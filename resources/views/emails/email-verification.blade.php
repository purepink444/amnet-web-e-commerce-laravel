@component('mail::message')

# ยืนยันอีเมลสำหรับการสมัครสมาชิก

สวัสดี,

ขอบคุณที่สมัครสมาชิกกับ AMNET E-Commerce!

กรุณาคลิกลิงก์ด้านล่างเพื่อยืนยันอีเมลของคุณและเสร็จสิ้นการสมัครสมาชิก:

@component('mail::button', ['url' => $verificationUrl, 'color' => 'primary'])
ยืนยันอีเมล
@endcomponent

**ลิงก์ยืนยัน:** {{ $verificationUrl }}

ลิงก์นี้จะหมดอายุใน 24 ชั่วโมง

หากคุณไม่ได้สมัครสมาชิก กรุณาละเว้นอีเมลนี้

ขอบคุณที่เลือกใช้บริการ AMNET E-Commerce!

@endcomponent