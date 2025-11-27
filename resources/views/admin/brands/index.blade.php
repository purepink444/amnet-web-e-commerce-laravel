@extends('layouts.admin')

@section('title', 'จัดการแบรนด์สินค้า')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">จัดการแบรนด์สินค้า</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> เพิ่มแบรนด์
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Search and Filter -->
                    <form method="GET" class="mb-3">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="ค้นหาแบรนด์..."
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">ทั้งหมด</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-search"></i> ค้นหา
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times"></i> ล้างการค้นหา
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Bulk Actions -->
                    <form id="bulkForm" method="POST" style="display: none;">
                        @csrf
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th width="80">โลโก้</th>
                                    <th>ชื่อแบรนด์</th>
                                    <th>คำอธิบาย</th>
                                    <th>จำนวนสินค้า</th>
                                    <th>สถานะ</th>
                                    <th width="150">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $brand)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="form-check-input brand-checkbox"
                                               name="ids[]" value="{{ $brand->brand_id }}"
                                               form="bulkForm">
                                    </td>
                                    <td>
                                        @if($brand->brand_logo)
                                            <img src="{{ asset('storage/' . $brand->brand_logo) }}"
                                                 alt="{{ $brand->brand_name }}"
                                                 class="img-thumbnail" width="50" height="50">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px; border-radius: 5px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $brand->brand_name }}</td>
                                    <td>
                                        {{ Str::limit($brand->description, 50) }}
                                        @if(strlen($brand->description) > 50)
                                            <a href="{{ route('admin.brands.show', $brand) }}"
                                               class="text-primary">...ดูเพิ่ม</a>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $brand->products()->count() }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $brand->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $brand->status === 'active' ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.brands.show', $brand) }}"
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.brands.edit', $brand) }}"
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}"
                                                  style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                        onclick="return confirm('คุณต้องการลบแบรนด์นี้หรือไม่?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <p>ไม่พบข้อมูลแบรนด์</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Bulk Actions Bar -->
                    <div id="bulkActions" class="d-none mt-3 p-3 bg-light rounded">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <span id="selectedCount" class="text-muted">เลือก 0 รายการ</span>
                            </div>
                            <div class="col-md-6 text-end">
                                <button type="button" class="btn btn-sm btn-success me-2"
                                        onclick="bulkUpdateStatus('active')">
                                    <i class="fas fa-check"></i> เปิดใช้งาน
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary me-2"
                                        onclick="bulkUpdateStatus('inactive')">
                                    <i class="fas fa-ban"></i> ปิดใช้งาน
                                </button>
                                <button type="button" class="btn btn-sm btn-danger"
                                        onclick="bulkDelete()">
                                    <i class="fas fa-trash"></i> ลบ
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    {{ $brands->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const brandCheckboxes = document.querySelectorAll('.brand-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        brandCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    // Individual checkbox change
    brandCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.brand-checkbox:checked');
            selectAllCheckbox.checked = checkedBoxes.length === brandCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < brandCheckboxes.length;
            updateBulkActions();
        });
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.brand-checkbox:checked');
        if (checkedBoxes.length > 0) {
            bulkActions.classList.remove('d-none');
            selectedCount.textContent = `เลือก ${checkedBoxes.length} รายการ`;
        } else {
            bulkActions.classList.add('d-none');
        }
    }
});

function bulkUpdateStatus(status) {
    const form = document.getElementById('bulkForm');
    form.action = '{{ route("admin.brands.index") }}';
    form.innerHTML = '@csrf <input type="hidden" name="status" value="' + status + '">';

    // Add selected IDs
    document.querySelectorAll('.brand-checkbox:checked').forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });

    form.submit();
}

function bulkDelete() {
    if (confirm('คุณต้องการลบแบรนด์ที่เลือกหรือไม่?')) {
        const form = document.getElementById('bulkForm');
        form.action = '{{ route("admin.brands.bulk-delete") }}';
        form.method = 'POST';
        form.submit();
    }
}
</script>
@endsection