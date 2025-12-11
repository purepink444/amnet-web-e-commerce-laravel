@extends('layouts.default')

@section('title', 'เกิดข้อผิดพลาด')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                    </div>
                    <h1 class="h3 mb-3">เกิดข้อผิดพลาด</h1>
                    <p class="text-muted mb-4">
                        ขออภัย เกิดข้อผิดพลาดบางอย่างในระบบ กรุณาลองใหม่อีกครั้ง
                    </p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>กลับ
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-primary">
                            <i class="bi bi-house me-2"></i>หน้าหลัก
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection