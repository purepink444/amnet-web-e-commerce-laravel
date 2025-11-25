@extends('layouts.admin')

@section('title', 'รายงานสินค้า')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">รายงานสินค้า</h3>
        <div class="card-tools">
            <a href="{{ route('admin.reports.export', ['type' => 'products'] + request()->query()) }}" class="btn btn-success btn-sm">
                <i class="fas fa-download me-1"></i>ส่งออก Excel
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <label for="search" class="form-label">ค้นหาสินค้า</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="ชื่อสินค้า...">
                </div>
                <div class="col-md-3">
                    <label for="category_id" class="form-label">หมวดหมู่</label>
                    <select class="form-control" id="category_id" name="category_id">
                        <option value="">ทั้งหมด</option>
                        @foreach(\App\Models\Category::all() as $category)
                            <option value="{{ $category->category_id }}" {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="brand_id" class="form-label">แบรนด์</label>
                    <select class="form-control" id="brand_id" name="brand_id">
                        <option value="">ทั้งหมด</option>
                        @foreach(\App\Models\Brand::all() as $brand)
                            <option value="{{ $brand->brand_id }}" {{ request('brand_id') == $brand->brand_id ? 'selected' : '' }}>
                                {{ $brand->brand_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>ค้นหา
                        </button>
                        <a href="{{ route('admin.reports.products') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>ล้าง
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Products Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>รูปภาพ</th>
                        <th>ชื่อสินค้า</th>
                        <th>หมวดหมู่</th>
                        <th>แบรนด์</th>
                        <th>ราคา</th>
                        <th>จำนวนในสต็อก</th>
                        <th>ขายได้ทั้งหมด</th>
                        <th>รายได้รวม</th>
                        <th>สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->image_url)
                                    <img src="{{ asset('storage/' . $product->image_url) }}"
                                         alt="{{ $product->product_name }}"
                                         class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center"
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $product->product_name }}</td>
                            <td>{{ $product->category ? $product->category->category_name : 'N/A' }}</td>
                            <td>{{ $product->brand ? $product->brand->brand_name : 'N/A' }}</td>
                            <td>฿{{ number_format($product->price, 2) }}</td>
                            <td>{{ $product->stock_quantity }}</td>
                            <td>{{ $product->total_sold ?? 0 }}</td>
                            <td>฿{{ number_format($product->total_revenue ?? 0, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $product->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ $product->status == 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">ไม่พบข้อมูลสินค้า</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="d-flex justify-content-center">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection