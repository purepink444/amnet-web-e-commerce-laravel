@extends('layouts.default')

@section('title', 'ข้อตกลงและเงื่อนไข')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">ข้อตกลงและเงื่อนไข</h1>
                <p class="lead text-muted">กรุณาอ่านข้อตกลงและเงื่อนไขในการใช้บริการอย่างละเอียด</p>
                <small class="text-muted">อัปเดตล่าสุด: {{ date('d/m/Y') }}</small>
            </div>

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="mb-5">
                        <h3 class="h4 text-primary mb-4">
                            <i class="bi bi-file-text me-2"></i>1. การยอมรับข้อตกลง
                        </h3>
                        <p class="mb-3">
                            การเข้าถึงและใช้งานเว็บไซต์นี้ถือว่าท่านได้ยอมรับและตกลงที่จะปฏิบัติตามข้อตกลงและเงื่อนไข
                            การใช้บริการ การซื้อสินค้า และการให้บริการต่างๆ ที่ระบุไว้ในข้อตกลงนี้
                        </p>
                        <p>
                            หากท่านไม่เห็นด้วยกับข้อตกลงและเงื่อนไขใดๆ ท่านไม่ควรใช้งานเว็บไซต์นี้
                        </p>
                    </div>

                    <div class="mb-5">
                        <h3 class="h4 text-primary mb-4">
                            <i class="bi bi-cart me-2"></i>2. การสั่งซื้อสินค้า
                        </h3>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <strong>2.1 การสั่งซื้อ:</strong> การสั่งซื้อสินค้าผ่านเว็บไซต์ถือเป็นการเสนอซื้อ
                                และจะสมบูรณ์เมื่อทางร้านได้รับการยืนยันและส่งใบเสร็จรับเงินให้แก่ท่าน
                            </li>
                            <li class="mb-3">
                                <strong>2.2 ราคาสินค้า:</strong> ราคาสินค้าที่ระบุไว้เป็นราคาสุทธิ
                                รวมภาษีมูลค่าเพิ่มแล้ว และอาจมีการเปลี่ยนแปลงได้โดยไม่ต้องแจ้งให้ทราบล่วงหน้า
                            </li>
                            <li class="mb-3">
                                <strong>2.3 การชำระเงิน:</strong> ท่านสามารถชำระเงินด้วยวิธีการต่างๆ
                                ที่ทางร้านกำหนด และการชำระเงินจะถือเป็นที่สิ้นสุดเมื่อทางร้านได้รับเงินครบถ้วน
                            </li>
                        </ul>
                    </div>

                    <div class="mb-5">
                        <h3 class="h4 text-primary mb-4">
                            <i class="bi bi-truck me-2"></i>3. การจัดส่งสินค้า
                        </h3>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <strong>3.1 ระยะเวลา:</strong> ทางร้านจะจัดส่งสินค้าให้ภายในระยะเวลาที่กำหนด
                                โดยขึ้นอยู่กับพื้นที่จัดส่งและความพร้อมของสินค้า
                            </li>
                            <li class="mb-3">
                                <strong>3.2 ค่าจัดส่ง:</strong> ค่าจัดส่งสินค้าจะคำนวณตามน้ำหนัก
                                ขนาด และพื้นที่จัดส่งของสินค้า
                            </li>
                            <li class="mb-3">
                                <strong>3.3 ความเสียหาย:</strong> หากสินค้าได้รับความเสียหายระหว่างการขนส่ง
                                ท่านต้องแจ้งทางร้านภายใน 24 ชั่วโมงหลังได้รับสินค้า
                            </li>
                        </ul>
                    </div>

                    <div class="mb-5">
                        <h3 class="h4 text-primary mb-4">
                            <i class="bi bi-arrow-repeat me-2"></i>4. การคืนสินค้าและการเปลี่ยนสินค้า
                        </h3>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <strong>4.1 สิทธิ์ในการคืน:</strong> ท่านมีสิทธิ์คืนสินค้าได้ภายใน 7 วัน
                                นับจากวันที่ได้รับสินค้า โดยสินค้าต้องอยู่ในสภาพเดิม
                            </li>
                            <li class="mb-3">
                                <strong>4.2 เงื่อนไขการคืน:</strong> สินค้าต้องไม่มีการใช้งาน
                                มีบรรจุภัณฑ์ครบถ้วน และมีใบเสร็จรับเงิน
                            </li>
                            <li class="mb-3">
                                <strong>4.3 ค่าดำเนินการ:</strong> ทางร้านอาจมีค่าดำเนินการในการคืนสินค้า
                                ขึ้นอยู่กับประเภทสินค้าและสาเหตุการคืน
                            </li>
                        </ul>
                    </div>

                    <div class="mb-5">
                        <h3 class="h4 text-primary mb-4">
                            <i class="bi bi-shield-check me-2"></i>5. ความรับผิดชอบ
                        </h3>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <strong>5.1 ข้อจำกัดความรับผิด:</strong> ทางร้านจะไม่รับผิดชอบต่อความเสียหาย
                                ที่เกิดจากเหตุสุดวิสัยหรือการใช้งานที่ไม่ถูกต้อง
                            </li>
                            <li class="mb-3">
                                <strong>5.2 การใช้งานที่ถูกต้อง:</strong> ท่านต้องใช้งานสินค้าตามคำแนะนำ
                                และไม่นำไปใช้ในทางที่ผิดกฎหมาย
                            </li>
                        </ul>
                    </div>

                    <div class="mb-5">
                        <h3 class="h4 text-primary mb-4">
                            <i class="bi bi-lock me-2"></i>6. ความเป็นส่วนตัว
                        </h3>
                        <p class="mb-3">
                            ทางร้าน尊重และปกป้องข้อมูลส่วนบุคคลของท่าน
                            โดยจะใช้ข้อมูลเฉพาะเพื่อการให้บริการและปรับปรุงคุณภาพการให้บริการเท่านั้น
                        </p>
                        <p>
                            ท่านสามารถอ่านนโยบายความเป็นส่วนตัวได้
                            <a href="{{ route('pages.privacy') }}" class="text-primary">ที่นี่</a>
                        </p>
                    </div>

                    <div class="mb-5">
                        <h3 class="h4 text-primary mb-4">
                            <i class="bi bi-gavel me-2"></i>7. กฎหมายที่ใช้บังคับ
                        </h3>
                        <p class="mb-3">
                            ข้อตกลงและเงื่อนไขนี้อยู่ภายใต้กฎหมายไทย
                            และการตีความข้อพิพาทใดๆ จะอยู่ภายใต้เขตอำนาจศาลไทย
                        </p>
                    </div>

                    <div class="alert alert-info">
                        <h5 class="alert-heading">
                            <i class="bi bi-info-circle me-2"></i>ติดต่อเรา
                        </h5>
                        <p class="mb-0">
                            หากท่านมีคำถามหรือข้อสงสัยเกี่ยวกับข้อตกลงและเงื่อนไขนี้
                            สามารถติดต่อเราได้ที่ <strong>support@example.com</strong> หรือโทร <strong>02-123-4567</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection