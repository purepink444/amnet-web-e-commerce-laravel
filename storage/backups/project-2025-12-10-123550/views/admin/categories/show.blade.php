@extends('layouts.admin')

@section('title', 'รายละเอียดหมวดหมู่สินค้า')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">รายละเอียดหมวดหมู่สินค้า</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> แก้ไข
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> กลับ
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <!-- Category Image -->
                            <div class="text-center mb-4">
                                @if($category->category_image)
                                    <img src="{{ asset('storage/' . $category->category_image) }}"
                                         alt="{{ $category->category_name }}"
                                         class="img-fluid rounded shadow" style="max-width: 300px;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center rounded shadow"
                                         style="width: 300px; height: 200px; margin: 0 auto;">
                                        <div class="text-center">
                                            <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                            <p class="text-muted">ไม่มีรูปภาพ</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-8">
                            <!-- Category Info -->
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">ชื่อหมวดหมู่:</th>
                                    <td>{{ $category->category_name }}</td>
                                </tr>
                                <tr>
                                    <th>คำอธิบาย:</th>
                                    <td>{{ $category->description ?: 'ไม่มีคำอธิบาย' }}</td>
                                </tr>
                                <tr>
                                    <th>สถานะ:</th>
                                    <td>
                                        <span class="badge {{ $category->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $category->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>จำนวนสินค้า:</th>
                                    <td>
                                        <span class="badge bg-info">{{ $category->products()->count() }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>วันที่สร้าง:</th>
                                    <td>{{ $category->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>วันที่แก้ไขล่าสุด:</th>
                                    <td>{{ $category->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Products in this Category -->
                    <div class="mt-4">
                        <h4>สินค้าในหมวดหมู่นี้</h4>
                        @if($category->products->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="80">รูปภาพ</th>
                                            <th>ชื่อสินค้า</th>
                                            <th>ราคา</th>
                                            <th>จำนวนคงเหลือ</th>
                                            <th>สถานะ</th>
                                            <th width="100">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($category->products as $product)
                                        <tr>
                                            <td>
                                                @if($product->image_url)
                                                    <img src="{{ asset('storage/' . $product->image_url) }}"
                                                         alt="{{ $product->product_name }}"
                                                         class="img-thumbnail" width="50" height="50">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                                         style="width: 50px; height: 50px; border-radius: 5px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $product->product_name }}</td>
                                            <td>฿{{ number_format($product->price, 2) }}</td>
                                            <td>{{ $product->stock_quantity }}</td>
                                            <td>
                                                <span class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $product->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.products.show', $product) }}"
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p>ไม่มีสินค้าในหมวดหมู่นี้</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection