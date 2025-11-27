<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

/**
 * SweetAlert 2 Service for beautiful notifications
 * Provides centralized SweetAlert 2 integration with Laravel
 */
class SweetAlertService
{
    /**
     * Success notification
     */
    public function success(string $title, string $message = '', array $options = []): void
    {
        $this->flash('success', $title, $message, $options);
    }

    /**
     * Error notification
     */
    public function error(string $title, string $message = '', array $options = []): void
    {
        $this->flash('error', $title, $message, $options);
    }

    /**
     * Warning notification
     */
    public function warning(string $title, string $message = '', array $options = []): void
    {
        $this->flash('warning', $title, $message, $options);
    }

    /**
     * Info notification
     */
    public function info(string $title, string $message = '', array $options = []): void
    {
        $this->flash('info', $title, $message, $options);
    }

    /**
     * Question/Confirm notification
     */
    public function question(string $title, string $message = '', array $options = []): void
    {
        $this->flash('question', $title, $message, $options);
    }

    /**
     * Confirmation dialog for delete operations
     */
    public function confirmDelete(string $title = 'คุณแน่ใจหรือไม่?', string $message = 'การดำเนินการนี้ไม่สามารถยกเลิกได้', array $options = []): void
    {
        $defaultOptions = [
            'showCancelButton' => true,
            'confirmButtonText' => 'ใช่, ลบเลย!',
            'cancelButtonText' => 'ยกเลิก',
            'confirmButtonColor' => '#d33',
            'cancelButtonColor' => '#3085d6',
            'reverseButtons' => true,
        ];

        $this->flash('warning', $title, $message, array_merge($defaultOptions, $options));
    }

    /**
     * Success message for create operations
     */
    public function created(string $item = 'รายการ', array $options = []): void
    {
        $this->success("เพิ่ม{$item}สำเร็จ", "ข้อมูลได้ถูกบันทึกเรียบร้อยแล้ว", $options);
    }

    /**
     * Success message for update operations
     */
    public function updated(string $item = 'รายการ', array $options = []): void
    {
        $this->success("อัปเดต{$item}สำเร็จ", "ข้อมูลได้ถูกแก้ไขเรียบร้อยแล้ว", $options);
    }

    /**
     * Success message for delete operations
     */
    public function deleted(string $item = 'รายการ', array $options = []): void
    {
        $this->success("ลบ{$item}สำเร็จ", "ข้อมูลได้ถูกลบเรียบร้อยแล้ว", $options);
    }

    /**
     * Auto redirect with SweetAlert
     */
    public function successRedirect(string $title, string $message, string $route, array $routeParams = [], array $options = []): void
    {
        $options = array_merge($options, [
            'redirect' => route($route, $routeParams),
            'timer' => 2000,
        ]);
        $this->success($title, $message, $options);
    }

    /**
     * Toast notification (smaller, auto-dismiss)
     */
    public function toast(string $title, string $type = 'success', array $options = []): void
    {
        $defaultOptions = [
            'toast' => true,
            'position' => 'top-end',
            'showConfirmButton' => false,
            'timer' => 3000,
            'timerProgressBar' => true,
        ];

        $this->flash($type, $title, '', array_merge($defaultOptions, $options));
    }

    /**
     * Custom SweetAlert configuration
     */
    public function custom(array $config): void
    {
        Session::flash('sweetalert', $config);
    }

    /**
     * Flash SweetAlert data to session
     */
    private function flash(string $type, string $title, string $message = '', array $options = []): void
    {
        $config = array_merge([
            'icon' => $type,
            'title' => $title,
            'text' => $message,
            'confirmButtonText' => 'ตกลง',
            'customClass' => [
                'confirmButton' => 'btn btn-primary',
                'cancelButton' => 'btn btn-secondary'
            ]
        ], $options);

        Session::flash('sweetalert', $config);
    }

    /**
     * Get SweetAlert configuration from session
     */
    public static function getConfig(): ?array
    {
        return Session::get('sweetalert');
    }

    /**
     * Clear SweetAlert from session
     */
    public static function clear(): void
    {
        Session::forget('sweetalert');
    }
}