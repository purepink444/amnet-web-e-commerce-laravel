<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Order, Payment};
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected QRCodeService $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * แสดงหน้าเลือกวิธีการชำระเงินสำหรับออเดอร์
     */
    public function show($orderId)
    {
        $order = Order::with(['orderItems.product'])->findOrFail($orderId);

        // ตรวจสอบว่าผู้ใช้เป็นเจ้าของออเดอร์
        if ($order->user_id !== auth()->id()) {
            abort(403, 'ไม่มีสิทธิ์เข้าถึงออเดอร์นี้');
        }

        // ตรวจสอบว่าออเดอร์ยังไม่ได้ชำระเงิน
        if ($order->payment && $order->payment->isCompleted()) {
            return redirect()->route('account.orders.show', $orderId)
                ->with('info', 'ออเดอร์นี้ได้ชำระเงินแล้ว');
        }

        // ถ้ายังไม่มี payment record ให้สร้างขึ้นมา
        if (!$order->payment) {
            $order->payment()->create([
                'amount' => $order->total_amount,
                'payment_method' => 'pending',
                'payment_status' => 'pending',
            ]);
        }

        return view('pages.payment', compact('order'));
    }

    /**
     * ประมวลผลการชำระเงิน
     */
    public function process(Request $request, $orderId)
    {
        $request->validate([
            'payment_method' => 'required|in:credit,qr,cod',
            'card_name' => 'required_if:payment_method,credit|string|max:255',
            'card_number' => 'required_if:payment_method,credit|string|digits_between:13,19',
            'card_exp' => 'required_if:payment_method,credit|string|size:5',
            'card_cvv' => 'required_if:payment_method,credit|string|digits_between:3,4',
        ]);

        $order = Order::findOrFail($orderId);

        // ตรวจสอบสิทธิ์
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            // สร้างหรืออัปเดต payment record
            $payment = Payment::updateOrCreate(
                ['order_id' => $order->order_id],
                [
                    'amount' => $order->total_amount,
                    'payment_method' => $request->payment_method,
                    'status' => 'pending',
                    'payment_data' => $this->getPaymentData($request),
                ]
            );

            // ประมวลผลการชำระเงินตามวิธี
            $result = $this->processPayment($request, $payment);

            if ($result['success']) {
                $payment->markAsCompleted();
                $order->update(['status' => 'paid']);

                DB::commit();

                return redirect()->route('account.checkout.success', $order->order_id)
                    ->with('success', 'การชำระเงินสำเร็จ');
            } else {
                // จัดการ payment ที่ล้มเหลว
                $payment->update([
                    'status' => 'failed',
                    'payment_data' => array_merge($payment->payment_data ?? [], [
                        'error_message' => $result['message'],
                        'failed_at' => now(),
                    ])
                ]);

                DB::rollBack();
                return back()->withErrors(['payment' => $result['message']])
                    ->withInput();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed: ' . $e->getMessage());
            return back()->withErrors(['payment' => 'เกิดข้อผิดพลาดในการชำระเงิน กรุณาลองใหม่อีกครั้ง']);
        }
    }

    /**
     * รับข้อมูลการชำระเงินสำหรับบันทึก
     */
    private function getPaymentData(Request $request): array
    {
        $data = [];

        if ($request->payment_method === 'credit') {
            // เข้ารหัสข้อมูลบัตรเครดิต (ในระบบจริงควรใช้ encryption ที่ปลอดภัย)
            $data = [
                'card_name' => encrypt($request->card_name),
                'card_number_masked' => '**** **** **** ' . substr($request->card_number, -4),
                'card_number_last4' => substr($request->card_number, -4),
                'card_exp' => encrypt($request->card_exp),
                'card_type' => $this->getCardType($request->card_number),
            ];
        }

        return $data;
    }

    /**
     * ตรวจสอบประเภทบัตรเครดิต
     */
    private function getCardType(string $cardNumber): string
    {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);

        if (preg_match('/^4/', $cardNumber)) {
            return 'Visa';
        } elseif (preg_match('/^5[1-5]/', $cardNumber)) {
            return 'MasterCard';
        } elseif (preg_match('/^3[47]/', $cardNumber)) {
            return 'American Express';
        } elseif (preg_match('/^6(?:011|5)/', $cardNumber)) {
            return 'Discover';
        }

        return 'Unknown';
    }

    /**
     * ประมวลผลการชำระเงินตามวิธีการ
     */
    private function processPayment(Request $request, Payment $payment): array
    {
        switch ($request->payment_method) {
            case 'credit':
                return $this->processCreditCard($request, $payment);

            case 'qr':
                return $this->processQRPayment($request, $payment);

            case 'cod':
                return $this->processCOD($request, $payment);

            default:
                return ['success' => false, 'message' => 'วิธีการชำระเงินไม่ถูกต้อง'];
        }
    }

    /**
     * ประมวลผลบัตรเครดิต (จำลอง)
     */
    private function processCreditCard(Request $request, Payment $payment): array
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

        // ตรวจสอบ CVV
        if (!preg_match('/^\d{3,4}$/', $request->card_cvv)) {
            return ['success' => false, 'message' => 'รหัส CVV ไม่ถูกต้อง'];
        }

        // จำลองการเรียก API
        sleep(1); // จำลอง delay

        // ในระบบจริง: เรียก API ของ payment gateway
        $success = rand(1, 10) > 2; // 80% success rate สำหรับ demo

        if ($success) {
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
     * ประมวลผล QR Payment
     */
    private function processQRPayment(Request $request, Payment $payment): array
    {
        try {
            // ตรวจสอบว่ามี QR code อยู่แล้วหรือไม่
            $existingQrCode = $payment->payment_data['qr_code'] ?? null;

            if (!$existingQrCode) {
                // สร้าง QR code สำหรับ PromptPay ถ้ายังไม่มี
                $qrCodeData = $this->qrCodeService->generatePromptPayQR(
                    $payment->amount,
                    (string)$payment->order_id,
                    '1234567890' // Merchant ID สำหรับ demo
                );
            } else {
                $qrCodeData = $existingQrCode;
            }

            // สร้าง transaction_id
            $transactionId = $payment->payment_data['transaction_id'] ?? 'QR_' . time() . '_' . rand(1000, 9999);

            // อัปเดต payment data
            $payment->update([
                'payment_data' => array_merge($payment->payment_data ?? [], [
                    'transaction_id' => $transactionId,
                    'qr_code' => $qrCodeData,
                    'qr_generated_at' => now(),
                    'merchant_id' => '1234567890',
                    'processed_at' => now(),
                ])
            ]);

            return [
                'success' => true,
                'message' => 'QR Code สำหรับชำระเงินพร้อมใช้งานแล้ว',
                'transaction_id' => $transactionId,
                'qr_code' => $qrCodeData
            ];
        } catch (\Exception $e) {
            Log::error('QR Code processing failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'ไม่สามารถประมวลผล QR Code ได้ กรุณาลองใหม่อีกครั้ง'
            ];
        }
    }

    /**
     * ประมวลผล COD
     */
    private function processCOD(Request $request, Payment $payment): array
    {
        // COD ไม่ต้องชำระเงินทันที
        return [
            'success' => true,
            'message' => 'เลือกชำระเงินปลายทางสำเร็จ'
        ];
    }

    /**
     * ตรวจสอบสถานะการชำระเงิน (สำหรับ webhook หรือ callback)
     */
    public function verify(Request $request)
    {
        // สำหรับรับ callback จาก payment gateway
        // ในระบบจริงควร validate signature และ update payment status

        $transactionId = $request->input('transaction_id');
        $status = $request->input('status');

        // ค้นหา payment จาก transaction_id
        $payment = Payment::where('payment_data->transaction_id', $transactionId)->first();

        if ($payment && $status === 'completed') {
            $payment->markAsCompleted();
            $payment->order->update(['status' => 'paid']);

            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'failed'], 400);
    }

    /**
     * สร้าง QR Code สำหรับการชำระเงิน
     */
    public function generateQRCode($orderId)
    {
        $order = Order::findOrFail($orderId);

        // ตรวจสอบสิทธิ์
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            // สร้าง QR code สำหรับ PromptPay
            $qrCodeData = $this->qrCodeService->generatePromptPayQR(
                $order->total_amount,
                (string)$order->order_id,
                '1234567890' // Merchant ID สำหรับ demo
            );

            return response()->json([
                'success' => true,
                'qr_code' => $qrCodeData
            ]);
        } catch (\Exception $e) {
            Log::error('QR Code generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถสร้าง QR Code ได้'
            ], 500);
        }
    }

    /**
     * จำลองการยืนยันการชำระเงิน QR (สำหรับ demo)
     */
    public function confirmQRPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        // ตรวจสอบสิทธิ์
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $payment = $order->payment;

        if ($payment && $payment->payment_method === 'qr' && $payment->status === 'pending') {
            $payment->markAsCompleted();
            $order->update(['status' => 'paid']);

            return redirect()->route('account.checkout.success', $order->order_id)
                ->with('success', 'การชำระเงินด้วย QR สำเร็จแล้ว');
        }

        return redirect()->route('payment.show', $orderId)
            ->with('error', 'ไม่สามารถยืนยันการชำระเงินได้');
    }

    /**
     * ลองชำระเงินใหม่สำหรับ payment ที่ล้มเหลว
     */
    public function retryPayment($orderId)
    {
        $order = Order::findOrFail($orderId);

        // ตรวจสอบสิทธิ์
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $payment = $order->payment;

        if (!$payment || !$payment->canRetry()) {
            return redirect()->route('payment.show', $orderId)
                ->with('error', 'ไม่สามารถลองชำระเงินใหม่ได้');
        }

        // Reset payment status และ increment retry count
        $payment->incrementRetryCount();
        $payment->update(['status' => 'pending']);

        return redirect()->route('payment.show', $orderId)
            ->with('info', 'กรุณาลองชำระเงินใหม่อีกครั้ง');
    }
}