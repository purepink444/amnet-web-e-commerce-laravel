@component('mail::message')

# ยืนยันการสั่งซื้อสินค้า

สวัสดี **{{ $order->member->first_name }} {{ $order->member->last_name }}**,

ขอบคุณที่สั่งซื้อสินค้าจาก AMNET E-Commerce! คำสั่งซื้อของคุณได้รับการยืนยันแล้ว

## รายละเอียดคำสั่งซื้อ

**หมายเลขคำสั่งซื้อ:** #{{ $order->order_id }}

**วันที่สั่งซื้อ:** {{ $order->created_at->format('d/m/Y H:i') }}

**สถานะ:** {{ $order->status }}

---

## รายการสินค้า

@foreach($order->orderItems as $item)
**{{ $item->product->product_name }}**
- จำนวน: {{ $item->quantity }} ชิ้น
- ราคาต่อหน่วย: ฿{{ number_format($item->price, 2) }}
- รวม: ฿{{ number_format($item->subtotal, 2) }}

@endforeach

---

**รวมทั้งสิ้น: ฿{{ number_format($order->total_amount, 2) }}**

## ข้อมูลการจัดส่ง

{{ $order->shipping_address }}

## ข้อมูลการชำระเงิน

วิธีการชำระ: {{ $order->payment->payment_method ?? 'รอการอัปเดต' }}

สถานะการชำระ: {{ $order->payment->status ?? 'รอการชำระ' }}

---

หากมีคำถามหรือต้องการความช่วยเหลือ กรุณาติดต่อเราได้ที่:

📧 support@amnet-web.com
📞 02-XXX-XXXX

ขอบคุณที่เลือกใช้บริการ AMNET E-Commerce!

@component('mail::button', ['url' => route('account.orders.show', $order->order_id), 'color' => 'primary'])
ดูรายละเอียดคำสั่งซื้อ
@endcomponent

@endcomponent