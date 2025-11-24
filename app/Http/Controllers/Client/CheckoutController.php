<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{CartItem, Order, OrderItem, Payment};
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        return view('checkout.index', compact('cart'));
    }

    /**
     * ประมวลผลการสั่งซื้อและการชำระเงิน
     */
    public function process(Request $request): RedirectResponse
    {
        // Validation ตามวิธีการชำระเงิน
        $rules = [
            'payment_method' => 'required|in:credit,qr,cod',
            'shipping_address' => 'required|string|max:500',
        ];

        if ($request->payment_method === 'credit') {
            $rules = array_merge($rules, [
                'card_name' => 'required|string|max:255',
                'card_number' => 'required|string|digits_between:13,19',
                'card_exp' => 'required|string|size:5',
                'card_cvv' => 'required|string|digits_between:3,4',
            ]);
        }

        $request->validate($rules);

        $user = auth()->user();
        $member = $user->member;
        if (!$member) {
            return redirect()->route('account.cart.index')
                ->with('error', 'ไม่พบข้อมูลสมาชิก');
        }

        $cartItems = CartItem::with('product')->where('member_id', $member->member_id)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('account.cart.index')
                ->with('error', 'ตะกร้าของคุณว่างเปล่า');
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
            // หา member_id ของ user ถ้าไม่มีให้สร้าง
            $member = $user->member;
            if (!$member) {
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
            }

            // สร้างคำสั่งซื้อ
            $orderData = [
                'user_id' => $user->user_id,
                'member_id' => $member->member_id,
                'total_amount' => $totalPrice,
                'order_status' => 'pending',
                'shipping_method' => $request->payment_method, // ใช้ shipping_method แทน payment_method
                'shipping_address' => $request->shipping_address,
            ];

            $order = Order::create($orderData);

            // สร้างรายการสินค้าในคำสั่งซื้อ
            foreach ($cartItems as $item) {
                $subtotal = $item->quantity * $item->product->price;
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item->product_id,
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

            // ล้างตะกร้า
            CartItem::where('member_id', $member->member_id)->delete();

            DB::commit();

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

        $success = rand(1, 10) > 2; // 80% success rate สำหรับ demo

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
            return [
                'success' => false,
                'message' => 'ไม่สามารถสร้าง QR Code ได้ กรุณาลองใหม่อีกครั้ง'
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
        $order = Order::with(['items.product', 'payment'])
            ->where('user_id', $user->user_id)
            ->where('order_id', $orderId)
            ->firstOrFail();

        return view('checkout.success', compact('order'));
    }
}
