<?php

namespace App\Mail;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Product $product,
        public string $type = 'created',
        public ?User $admin = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = match($this->type) {
            'created' => 'สินค้าใหม่ถูกเพิ่มเข้าสู่ระบบ',
            'updated' => 'สินค้าถูกแก้ไข',
            'deleted' => 'สินค้าถูกลบออกจากระบบ',
            default => 'แจ้งเตือนสินค้า'
        };

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.product-notification',
            with: [
                'product' => $this->product,
                'type' => $this->type,
                'admin' => $this->admin,
                'actionText' => $this->getActionText(),
                'actionUrl' => $this->getActionUrl(),
            ],
        );
    }

    /**
     * Get the action text based on notification type.
     */
    private function getActionText(): string
    {
        return match($this->type) {
            'created' => 'ดูสินค้า',
            'updated' => 'ดูการเปลี่ยนแปลง',
            'deleted' => 'ดูประวัติ',
            default => 'ดูรายละเอียด'
        };
    }

    /**
     * Get the action URL.
     */
    private function getActionUrl(): string
    {
        $baseUrl = config('app.url');

        return match($this->type) {
            'created', 'updated' => "{$baseUrl}/admin/products/{$this->product->product_id}",
            'deleted' => "{$baseUrl}/admin/products",
            default => "{$baseUrl}/admin/products"
        };
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}