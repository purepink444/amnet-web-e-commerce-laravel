<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\{Order, Payment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
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
            'card_number' => 'required_if:payment_method,credit|string|size:16',
            'card_exp' => 'required_if:payment_method,credit|string|regex:/^\d{2}\/\d{2}$/',
            'card_cvv' => 'required_if:payment_method,credit|string|size:3',
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

                return redirect()->route('checkout.success', $order->order_id)
                    ->with('success', 'การชำระเงินสำเร็จ');
            } else {
                DB::rollBack();
                return back()->withErrors(['payment' => $result['message']]);
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
            $data = [
                'card_name' => $request->card_name,
                'card_number' => '**** **** **** ' . substr($request->card_number, -4),
                'card_exp' => $request->card_exp,
            ];
        }

        return $data;
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
        // จำลองการตรวจสอบบัตรเครดิต
        // ในระบบจริงควรเชื่อมต่อกับ payment gateway

        // ตรวจสอบเบื้องต้น
        if (!preg_match('/^\d{16}$/', $request->card_number)) {
            return ['success' => false, 'message' => 'หมายเลขบัตรไม่ถูกต้อง'];
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
            return ['success' => false, 'message' => 'การชำระเงินล้มเหลว กรุณาตรวจสอบข้อมูลบัตร'];
        }
    }

    /**
     * ประมวลผล QR Payment (จำลอง)
     */
    private function processQRPayment(Request $request, Payment $payment): array
    {
        // ในระบบจริงควรตรวจสอบการสแกน QR และยืนยันการโอนเงิน
        // สำหรับ demo ให้สำเร็จทันที

        return [
            'success' => true,
            'message' => 'การชำระเงินด้วย QR สำเร็จ',
            'transaction_id' => 'QR_' . time() . '_' . rand(1000, 9999)
        ];
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
}