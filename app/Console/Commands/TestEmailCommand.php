<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ส่งอีเมลทดสอบไปยังอีเมลที่กำหนด';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info("กำลังส่งอีเมลทดสอบไปยัง: {$email}");

        try {
            Mail::raw('นี่คืออีเมลทดสอบจาก AMNET Laravel Application!

หากคุณได้รับอีเมลนี้ แสดงว่าการตั้งค่า SMTP สำเร็จแล้ว! 🎉

เวลาที่ส่ง: ' . now()->format('Y-m-d H:i:s'), function ($message) use ($email) {
                $message->to($email)
                        ->subject('ทดสอบอีเมล - AMNET Laravel');
            });

            $this->info('✅ ส่งอีเมลสำเร็จ!');
            $this->info('กรุณาตรวจสอบอีเมลใน Inbox และ Spam folder');

        } catch (\Exception $e) {
            $this->error('❌ ส่งอีเมลไม่สำเร็จ!');
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
