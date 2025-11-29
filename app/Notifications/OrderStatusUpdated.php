<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
{

    public Order $order;
    public string $oldStatus;
    public string $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = $this->getStatusLabel($this->newStatus);

        $member = $notifiable->member;
        $firstName = $member ? $member->first_name : 'ลูกค้า';
        $lastName = $member ? $member->last_name : '';

        return (new MailMessage)
            ->subject("อัปเดตสถานะคำสั่งซื้อ #{$this->order->order_id}")
            ->greeting("สวัสดี {$firstName} {$lastName}")
            ->line("สถานะคำสั่งซื้อของคุณได้เปลี่ยนจาก '{$this->getStatusLabel($this->oldStatus)}' เป็น '{$statusLabel}'")
            ->line("รายละเอียดคำสั่งซื้อ:")
            ->line("หมายเลขคำสั่งซื้อ: #{$this->order->order_id}")
            ->line("วันที่สั่งซื้อ: {$this->order->created_at->format('d/m/Y H:i')}")
            ->line("ยอดรวม: " . number_format($this->order->total_amount, 2) . " บาท")
            ->when($this->newStatus === 'shipped', function ($mail) {
                return $mail->action('ติดตามคำสั่งซื้อ', route('account.orders.show', $this->order->order_id));
            })
            ->when($this->newStatus === 'delivered', function ($mail) {
                return $mail->action('ให้คะแนนสินค้า', route('account.orders.show', $this->order->order_id));
            })
            ->line('ขอบคุณที่ใช้บริการของเรา!')
            ->salutation('ด้วยความเคารพ');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->order_id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'status_label' => $this->getStatusLabel($this->newStatus),
            'total_amount' => $this->order->total_amount,
            'updated_at' => now(),
        ];
    }

    /**
     * Get status label in Thai
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'รอดำเนินการ',
            'paid' => 'ชำระเงินแล้ว',
            'shipped' => 'จัดส่งแล้ว',
            'delivered' => 'ส่งถึงแล้ว',
            'cancelled' => 'ยกเลิก',
            default => $status,
        };
    }
}
