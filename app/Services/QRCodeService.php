<?php

namespace App\Services;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QRCodeService
{
    /**
     * สร้าง QR Code สำหรับ PromptPay
     */
    public function generatePromptPayQR(float $amount, string $orderId, string $merchantId = '1234567890'): string
    {
        // สร้างข้อมูล PromptPay ตามมาตรฐาน
        $qrData = $this->generatePromptPayData($amount, $orderId, $merchantId);

        // สร้าง QR Code
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($qrData);

        // แปลงเป็น base64 สำหรับแสดงใน HTML
        return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
    }

    /**
     * สร้างข้อมูล PromptPay ตามมาตรฐาน EMVCo
     */
    private function generatePromptPayData(float $amount, string $orderId, string $merchantId): string
    {
        // Format: 00020101021129370016A0000006770101110113[merchant_id]0213[order_id]5802TH5303764540[amount]5802TH6304[CRC]

        $amountStr = number_format($amount, 2, '.', '');
        $amountPadded = str_pad($amountStr, 13, '0', STR_PAD_LEFT);

        // Merchant ID (พร้อมเพย์ ID)
        $merchantIdPadded = str_pad($merchantId, 13, '0', STR_PAD_LEFT);

        // สร้างข้อมูลพื้นฐาน
        $data = "000201"; // Payload Format Indicator
        $data .= "010211"; // Point of Initiation Method (11 = static QR)

        // Merchant Account Information
        $data .= "29370016A000000677010111"; // AID for Thai QR
        $data .= "0113" . $merchantIdPadded; // Merchant ID
        $data .= "0213" . str_pad($orderId, 13, '0', STR_PAD_LEFT); // Reference/Order ID

        $data .= "5802TH"; // Country Code (TH)
        $data .= "5303764"; // Currency (764 = THB)
        $data .= "54" . strlen($amountPadded) . $amountPadded; // Transaction Amount
        $data .= "5802TH"; // Country Code again

        // คำนวณ CRC
        $crc = $this->calculateCRC($data . "6304");
        $data .= "6304" . $crc; // CRC

        return $data;
    }

    /**
     * คำนวณ CRC-16-CCITT สำหรับ QR Code
     */
    private function calculateCRC(string $data): string
    {
        $crc = 0xFFFF;
        $polynomial = 0x1021;

        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= (ord($data[$i]) << 8);

            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }

    /**
     * สร้าง QR Code ธรรมดา (ไม่ใช่ PromptPay)
     */
    public function generateSimpleQR(string $data): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($data);

        return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
    }
}