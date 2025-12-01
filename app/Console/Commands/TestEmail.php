<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-email {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ส่งอีเมลทดสอบเพื่อตรวจสอบการตั้งค่า Email Service';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?: 'test@example.com';

        $this->info('กำลังส่งอีเมลทดสอบไปยัง: ' . $email);

        try {
            Mail::raw('นี่คืออีเมลทดสอบจาก AMNET E-Commerce!

หากคุณได้รับอีเมลนี้ แสดงว่าการตั้งค่า Email Service สำเร็จแล้ว!

เวลาที่ส่ง: ' . now()->format('Y-m-d H:i:s'), function ($message) use ($email) {
                $message->to($email)
                        ->subject('ทดสอบการส่งอีเมล - AMNET E-Commerce');
            });

            $this->info('✅ ส่งอีเมลสำเร็จ! ตรวจสอบอีเมลของคุณ');
            $this->info('💡 หากไม่ได้รับอีเมล ให้ตรวจสอบ:');
            $this->info('   - Junk/Spam folder');
            $this->info('   - การตั้งค่า MAIL_* ใน .env');
            $this->info('   - การตั้งค่า App Password สำหรับ Gmail');

        } catch (\Exception $e) {
            $this->error('❌ ส่งอีเมลล้มเหลว: ' . $e->getMessage());
            $this->info('🔧 วิธีแก้ไข:');
            $this->info('   1. ตรวจสอบการตั้งค่า MAIL_* ใน .env');
            $this->info('   2. สำหรับ Gmail: เปิดใช้งาน 2FA และสร้าง App Password');
            $this->info('   3. ตรวจสอบไฟร์วอลล์และการเชื่อมต่ออินเทอร์เน็ต');
        }
    }
}
