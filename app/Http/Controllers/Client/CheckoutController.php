<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{CartItem, Order, OrderItem, Payment};
use App\Services\QRCodeService;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    protected QRCodeService $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * แสดงหน้า checkout
     */
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        $member = $user->member;

        if (!$member) {
            // สร้าง member record ถ้ายังไม่มี
            $member = $user->member()->create([
                'first_name' => $user->firstname ?? 'Unknown',
                'last_name' => $user->lastname ?? 'User',
                'membership_level' => 'bronze',
                'points' => 0,
            ]);
        }

        $cartItems = CartItem::with('product')->where('member_id', $member->member_id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('account.cart.index')
                ->with('error', 'ตะกร้าของคุณว่างเปล่า กรุณาเพิ่มสินค้าก่อน');
        }

        // Create a cart-like object for the view
        $cart = (object) [
            'items' => $cartItems,
            'total_items' => $cartItems->sum('quantity'),
            'total_price' => $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            }),
        ];

        // เตรียมข้อมูลที่อยู่สำหรับแสดงผล
        $memberAddress = null;
        if ($member) {
            $provinceName = is_numeric($member->province) ? $this->getProvinceName($member->province) : $member->province;
            $districtName = is_numeric($member->district) ? $this->getDistrictName($member->district) : $member->district;

            $memberAddress = [
                'full_name' => $member->first_name . ' ' . $member->last_name,
                'address' => $member->address,
                'subdistrict' => $member->subdistrict,
                'district' => $districtName,
                'province' => $provinceName,
                'postal_code' => $member->postal_code,
                'formatted' => trim($member->first_name . ' ' . $member->last_name . "\n" .
                                   $member->address . "\n" .
                                   ($member->subdistrict ? $member->subdistrict . ' ' : '') .
                                   ($districtName ? $districtName . ' ' : '') .
                                   ($provinceName ? $provinceName . ' ' : '') .
                                   ($member->postal_code ?: ''))
            ];
        }

        return view('checkout.index', compact('cart', 'memberAddress'));
    }

    /**
     * ประมวลผลการสั่งซื้อและการชำระเงิน
     */
    public function process(Request $request): RedirectResponse
    {
        \Log::info('Checkout process started', ['method' => $request->method(), 'all' => $request->all()]);

        // Validation พื้นฐาน
        $rules = [
            'payment_method' => 'required|in:credit,qr,cod',
            'address_type' => 'required|in:registered,new',
            'shipping_company' => 'required|string|max:150',
        ];

        // Validation สำหรับที่อยู่ใหม่
        if ($request->address_type === 'new') {
            $rules = array_merge($rules, [
                'new_first_name' => 'required|string|max:255',
                'new_last_name' => 'required|string|max:255',
                'new_address' => 'required|string|max:500',
                'new_subdistrict' => 'nullable|string|max:255',
                'new_district' => 'nullable|string|max:255',
                'new_province' => 'required|string|max:255',
                'new_postal_code' => 'required|string|size:5',
            ]);
        }

        // Validation ตามวิธีการชำระเงิน
        if ($request->payment_method === 'credit') {
            $rules = array_merge($rules, [
                'card_name' => 'required|string|max:255',
                'card_number' => 'required|string|digits_between:13,19',
                'card_exp' => 'required|string|size:5',
                'card_cvv' => 'required|string|digits_between:3,4',
            ]);
        }

        // Clean card number by removing spaces if credit payment
        if ($request->payment_method === 'credit' && $request->has('card_number')) {
            $original = $request->card_number;
            $request->card_number = str_replace(' ', '', $request->card_number);
            \Log::info('Card number cleaned', ['original' => $original, 'cleaned' => $request->card_number]);
        }

        \Log::info('Validation rules prepared', ['rules' => $rules, 'request_data' => $request->all()]);

        try {
            $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors(), 'input' => $request->all()]);
            throw $e;
        }

        \Log::info('Checkout validation passed', $request->all());

        $user = auth()->user();
        \Log::info('User retrieved', ['user_id' => $user->user_id ?? null]);
        $member = $user->member;
        \Log::info('Member retrieved', ['member_id' => $member->member_id ?? null]);
        if (!$member) {
            \Log::warning('No member found for user', ['user_id' => $user->user_id]);
            return redirect()->route('account.cart.index')
                ->with('error', 'ไม่พบข้อมูลสมาชิก');
        }

        $cartItems = CartItem::with('product')->where('member_id', $member->member_id)->get();
        \Log::info('Cart items retrieved', ['count' => $cartItems->count()]);

        if ($cartItems->isEmpty()) {
            \Log::warning('Cart is empty for member', ['member_id' => $member->member_id]);
            return redirect()->route('account.cart.index')
                ->with('error', 'ตะกร้าของคุณว่างเปล่า');
        }

        // จัดการที่อยู่จัดส่ง
        $shippingAddress = '';
        if ($request->address_type === 'registered') {
            // ใช้ที่อยู่ที่ลงทะเบียนไว้ - แปลงรหัสเป็นชื่อถ้าจำเป็น
            $provinceName = is_numeric($member->province) ? $this->getProvinceName($member->province) : $member->province;
            $districtName = is_numeric($member->district) ? $this->getDistrictName($member->district) : $member->district;

            $shippingAddress = trim($member->first_name . ' ' . $member->last_name . "\n" .
                                   $member->address . "\n" .
                                   ($member->subdistrict ? $member->subdistrict . ' ' : '') .
                                   ($districtName ? $districtName . ' ' : '') .
                                   ($provinceName ? $provinceName . ' ' : '') .
                                   ($member->postal_code ?: ''));
        } else {
            // ใช้ที่อยู่ใหม่ - แปลงรหัสจังหวัดและอำเภอเป็นชื่อ
            $provinceName = $this->getProvinceName($request->new_province);
            $districtName = $this->getDistrictName($request->new_district);

            $shippingAddress = trim($request->new_first_name . ' ' . $request->new_last_name . "\n" .
                                   $request->new_address . "\n" .
                                   ($request->new_subdistrict ? $request->new_subdistrict . ' ' : '') .
                                   ($districtName ? $districtName . ' ' : '') .
                                   ($provinceName ? $provinceName . ' ' : '') .
                                   ($request->new_postal_code ?: ''));

            // อัปเดตข้อมูล member ด้วยที่อยู่ใหม่ (ถ้าต้องการ)
            $member->update([
                'first_name' => $request->new_first_name,
                'last_name' => $request->new_last_name,
                'address' => $request->new_address,
                'subdistrict' => $request->new_subdistrict,
                'district' => $districtName,
                'province' => $provinceName,
                'postal_code' => $request->new_postal_code,
            ]);
        }

        // คำนวณราคารวม
        $totalPrice = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        // ตรวจสอบ stock อีกครั้ง
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock_quantity) {
                return redirect()->route('account.cart.index')
                    ->with('error', "สินค้า {$item->product->product_name} มีจำนวนไม่เพียงพอ");
            }
        }

        DB::beginTransaction();

        try {
            \Log::info('Starting database transaction');

            // หา member_id ของ user ถ้าไม่มีให้สร้าง
            $member = $user->member;
            if (!$member) {
                \Log::info('Creating member record');
                // สร้าง member record ถ้ายังไม่มี
                $member = \App\Models\Member::create([
                    'user_id' => $user->user_id,
                    'first_name' => $user->firstname ?: 'Unknown',
                    'last_name' => $user->lastname ?: 'User',
                    'address' => $user->address,
                    'district' => $user->district,
                    'province' => $user->province,
                    'postal_code' => $user->zipcode,
                ]);
                \Log::info('Member created: ' . $member->member_id);
            }

            // สร้างคำสั่งซื้อ
            $orderData = [
                'user_id' => $user->user_id,
                'member_id' => $member->member_id,
                'total_amount' => $totalPrice,
                'order_status' => 'pending',
                'shipping_method' => $request->payment_method, // ใช้ shipping_method แทน payment_method
                'shipping_address' => $shippingAddress,
            ];

            \Log::info('Creating order', $orderData);
            $order = Order::create($orderData);
            \Log::info('Order created: ' . $order->order_id);

            // สร้างรายการสินค้าในคำสั่งซื้อ
            foreach ($cartItems as $item) {
                $subtotal = $item->quantity * $item->product->price;
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->product_name,
                    'quantity' => $item->quantity,
                    'price_at_purchase' => $item->product->price,
                    'subtotal' => $subtotal,
                ]);

                // ลดจำนวนสินค้าในสต็อก
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // จัดการการชำระเงิน
            Log::info('Starting payment processing for order: ' . $order->order_id);
            $paymentResult = $this->processPayment($request, $order);
            Log::info('Payment result: ' . json_encode($paymentResult));

            if (!$paymentResult['success']) {
                Log::warning('Payment failed for order: ' . $order->order_id . ' - ' . $paymentResult['message']);
                DB::rollBack();
                return redirect()->back()
                    ->withErrors(['payment' => $paymentResult['message']])
                    ->withInput();
            }

            // สร้าง shipping record
            \App\Models\Shipping::create([
                'order_id' => $order->order_id,
                'shipping_company' => $request->shipping_company,
                'shipping_status' => 'pending',
            ]);

            // ล้างตะกร้า
            CartItem::where('member_id', $member->member_id)->delete();

            DB::commit();

            // ส่งอีเมลยืนยันคำสั่งซื้อ
            try {
                Mail::to($user->email)->send(new OrderConfirmation($order));
                Log::info('Order confirmation email sent successfully', ['order_id' => $order->order_id, 'email' => $user->email]);
            } catch (\Exception $e) {
                Log::error('Failed to send order confirmation email', [
                    'order_id' => $order->order_id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
                // ไม่ต้อง rollback เพราะคำสั่งซื้อสำเร็จแล้ว แค่ส่งเมลไม่สำเร็จ
            }

            // สำเร็จ - redirect ไป success page
            return redirect()->route('account.checkout.success', $order->order_id)
                ->with('success', 'คำสั่งซื้อและการชำระเงินสำเร็จ!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout process failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'เกิดข้อผิดพลาดในการสั่งซื้อ กรุณาลองใหม่อีกครั้ง')
                ->withInput();
        }
    }

    /**
     * จัดการการชำระเงินตามวิธีการ
     */
    private function processPayment(Request $request, Order $order): array
    {
        // แปลง payment_method ให้ตรงกับฐานข้อมูล
        $paymentMethodMap = [
            'credit' => 'credit_card',
            'qr' => 'qr_code',
            'cod' => 'cod'
        ];

        $dbPaymentMethod = $paymentMethodMap[$request->payment_method] ?? $request->payment_method;

        // สร้าง payment record
        $payment = Payment::create([
            'order_id' => $order->order_id,
            'amount' => $order->total_amount,
            'payment_method' => $dbPaymentMethod,
            'payment_status' => 'pending',
        ]);

        switch ($request->payment_method) {
            case 'credit':
                return $this->processCreditCardPayment($request, $payment);

            case 'qr':
                return $this->processQRPayment($request, $payment);

            case 'cod':
                return $this->processCODPayment($request, $payment);

            default:
                return ['success' => false, 'message' => 'วิธีการชำระเงินไม่ถูกต้อง'];
        }
    }

    /**
     * จัดการการชำระเงินด้วยบัตรเครดิต
     */
    private function processCreditCardPayment(Request $request, Payment $payment): array
    {
        // ตรวจสอบวันหมดอายุ
        $expParts = explode('/', $request->card_exp);
        $expMonth = (int) $expParts[0];
        $expYear = (int) ('20' . $expParts[1]);
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('m');

        if ($expYear < $currentYear || ($expYear === $currentYear && $expMonth < $currentMonth)) {
            return ['success' => false, 'message' => 'บัตรเครดิตหมดอายุแล้ว'];
        }

        // ตรวจสอบเบื้องต้น
        if (!preg_match('/^\d{13,19}$/', $request->card_number)) {
            return ['success' => false, 'message' => 'หมายเลขบัตรเครดิตไม่ถูกต้อง'];
        }

        // จำลองการเรียก API ของ payment gateway
        sleep(1); // จำลอง delay

        $success = true; // สำหรับ demo ให้สำเร็จเสมอ

        if ($success) {
            $payment->markAsCompleted();
            return [
                'success' => true,
                'message' => 'การชำระเงินด้วยบัตรเครดิตสำเร็จ',
                'transaction_id' => 'TXN_' . time() . '_' . rand(1000, 9999)
            ];
        } else {
            return ['success' => false, 'message' => 'การชำระเงินล้มเหลว กรุณาตรวจสอบข้อมูลบัตรหรือลองใหม่อีกครั้ง'];
        }
    }

    /**
     * จัดการการชำระเงินด้วย QR
     */
    private function processQRPayment(Request $request, Payment $payment): array
    {
        try {
            // สร้าง QR code สำหรับ PromptPay
            $qrCodeData = $this->qrCodeService->generatePromptPayQR(
                $payment->amount,
                (string)$payment->order_id,
                '1234567890' // Merchant ID สำหรับ demo
            );

            // อัปเดต payment data
            $payment->update([
                'payment_data' => array_merge($payment->payment_data ?? [], [
                    'qr_code' => $qrCodeData,
                    'qr_generated_at' => now(),
                    'merchant_id' => '1234567890',
                ])
            ]);

            // สำหรับ demo ให้ mark เป็น completed ทันที (ในระบบจริงต้องรอการ confirm)
            $payment->markAsCompleted();

            return [
                'success' => true,
                'message' => 'QR Code สำหรับชำระเงินถูกสร้างแล้ว',
                'qr_code' => $qrCodeData
            ];
        } catch (\Exception $e) {
            Log::error('QR Code generation failed: ' . $e->getMessage());

            // สำหรับ demo ถ้า QR generation ล้มเหลว ให้สร้าง placeholder และ mark completed
            $placeholderQR = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+CiAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkFSIENPREU8L3RleHQ+Cjwvc3ZnPg==';

            $payment->update([
                'payment_data' => array_merge($payment->payment_data ?? [], [
                    'qr_code' => $placeholderQR,
                    'qr_generated_at' => now(),
                    'merchant_id' => '1234567890',
                    'qr_error' => $e->getMessage(),
                ])
            ]);

            $payment->markAsCompleted();

            return [
                'success' => true,
                'message' => 'QR Code สำหรับชำระเงินถูกสร้างแล้ว (Demo)',
                'qr_code' => $placeholderQR
            ];
        }
    }

    /**
     * จัดการการชำระเงินปลายทาง
     */
    private function processCODPayment(Request $request, Payment $payment): array
    {
        // COD ไม่ต้องชำระเงินทันที
        $payment->markAsCompleted();

        return [
            'success' => true,
            'message' => 'เลือกชำระเงินปลายทางสำเร็จ'
        ];
    }


    /**
     * แสดงหน้า success หลังสั่งซื้อ
     */
    public function success(int $orderId): View
    {
        $user = auth()->user();
        $order = Order::with(['orderItems.product', 'payment', 'shipping'])
            ->where('user_id', $user->user_id)
            ->where('order_id', $orderId)
            ->firstOrFail();

        return view('checkout.success', compact('order'));
    }

    /**
     * แปลงรหัสจังหวัดเป็นชื่อจังหวัด
     */
    private function getProvinceName($provinceCode)
    {
        $provinces = json_decode(file_get_contents(public_path('json/src/provinces.json')), true);
        $province = collect($provinces)->firstWhere('provinceCode', $provinceCode);
        return $province ? $province['provinceNameTh'] : $provinceCode;
    }

    /**
     * แปลงรหัสอำเภอเป็นชื่ออำเภอ
     */
    private function getDistrictName($districtCode)
    {
        $districts = json_decode(file_get_contents(public_path('json/src/districts.json')), true);
        $district = collect($districts)->firstWhere('districtCode', $districtCode);
        return $district ? $district['districtNameTh'] : $districtCode;
    }
}
