@extends('layouts.default')

@section('title', 'เกี่ยวกับเรา')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">เกี่ยวกับเรา</h1>
                <p class="lead text-muted">เรียนรู้เกี่ยวกับแบรนด์และความมุ่งมั่นของเราในการนำเสนอสินค้าคุณภาพ</p>
            </div>

            <div class="card shadow-lg border-0 rounded-4 mb-5">
                <div class="card-body p-5">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <h2 class="h3 mb-4">เรื่องราวของเรา</h2>
                            <p class="mb-4">
                                เราก่อตั้งขึ้นด้วยความหลงใหลในการนำเสนอสินค้าคุณภาพสูงให้กับลูกค้าของเรา
                                ด้วยประสบการณ์กว่า 10 ปีในอุตสาหกรรมอีคอมเมิร์ซ เรามุ่งมั่นที่จะเป็นผู้นำ
                                ในการให้บริการที่ยอดเยี่ยมและนวัตกรรมที่ไม่หยุดยั้ง
                            </p>
                            <p class="mb-4">
                                ทีมงานของเราประกอบด้วยผู้เชี่ยวชาญที่มีความรู้และประสบการณ์
                                ที่พร้อมให้คำปรึกษาและสนับสนุนลูกค้าทุกท่าน
                            </p>
                        </div>
                        <div class="col-lg-6">
                            <img src="https://via.placeholder.com/500x400?text=Our+Story" alt="Our Story" class="img-fluid rounded-3 shadow">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="bi bi-shield-check text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="card-title">คุณภาพที่เชื่อถือได้</h5>
                            <p class="card-text text-muted">สินค้าทุกชิ้นผ่านการตรวจสอบคุณภาพอย่างเข้มงวด</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="bi bi-truck text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="card-title">จัดส่งรวดเร็ว</h5>
                            <p class="card-text text-muted">บริการจัดส่งที่รวดเร็วและปลอดภัยทั่วประเทศ</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <i class="bi bi-headset text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <h5 class="card-title">บริการลูกค้า</h5>
                            <p class="card-text text-muted">ทีมงานพร้อมให้คำปรึกษาและช่วยเหลือตลอด 24 ชั่วโมง</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <h3 class="h4 mb-4 text-center">วิสัยทัศน์และพันธกิจ</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h5 class="text-primary mb-3">
                                    <i class="bi bi-eye me-2"></i>วิสัยทัศน์
                                </h5>
                                <p class="text-muted">
                                    ต้องการเป็นผู้นำด้านอีคอมเมิร์ซในประเทศไทย
                                    ที่ลูกค้าทุกคนไว้วางใจและเลือกใช้บริการ
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h5 class="text-primary mb-3">
                                    <i class="bi bi-target me-2"></i>พันธกิจ
                                </h5>
                                <p class="text-muted">
                                    มอบประสบการณ์การช็อปปิ้งออนไลน์ที่ยอดเยี่ยม
                                    ด้วยสินค้าคุณภาพและบริการที่เป็นเลิศ
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection