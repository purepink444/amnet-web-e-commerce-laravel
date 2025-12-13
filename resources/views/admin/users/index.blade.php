@extends('layouts.admin')

@section('title', 'จัดการผู้ใช้')

@section('content')
<div class="page-container">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
            <div class="flex-grow-1">
                <h1 class="title mb-1">จัดการผู้ใช้</h1>
                <p class="subtitle mb-0">จัดการผู้ใช้ทั้งหมดในระบบ</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.users.export', request()->query()) }}" class="btn btn-outline-primary">
                    <i class="bi bi-download me-1"></i>ส่งออก
                </a>
                <a href="{{ route('admin.users.create') }}" class="btn-add">
                    <i class="bi bi-plus-circle me-2"></i>เพิ่มผู้ใช้
                </a>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.users.index') }}" class="filters-form">
            <div class="filters-row">
                <div class="search-group">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ค้นหาชื่อผู้ใช้, อีเมล, หรือเบอร์โทร..." class="search-input">
                    <button type="submit" class="search-btn">
                        <i class="bi bi-search"></i>
                    </button>
                </div>

                <div class="filter-group">
                    <select name="role" class="filter-select">
                        <option value="">ทุกบทบาท</option>
                        @foreach(\App\Models\Role::all() as $role)
                            <option value="{{ $role->role_id }}" {{ request('role') == $role->role_id ? 'selected' : '' }}>
                                {{ $role->role_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="status" class="filter-select">
                        <option value="">ทุกสถานะ</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>ใช้งาน</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>ปิดใช้งาน</option>
                    </select>

                    <div class="date-filter">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="filter-select" placeholder="จากวันที่">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="filter-select" placeholder="ถึงวันที่">
                    </div>

                    <a href="{{ route('admin.users.index') }}" class="clear-filters">
                        <i class="bi bi-x-circle"></i> ล้างตัวกรอง
                    </a>
                </div>
            </div>
        </form>

        <!-- Results Summary -->
        @if(request()->hasAny(['search', 'role', 'status', 'date_from', 'date_to']))
            <div class="results-summary">
                <i class="bi bi-info-circle me-2"></i>
                พบผู้ใช้ <strong>{{ $users->total() }}</strong> คน
                @if(request('search'))
                    ที่ตรงกับ "<strong>{{ request('search') }}</strong>"
                @endif
            </div>
        @endif
    </div>

    <!-- Bulk Actions -->
    <form id="bulkForm" method="POST" action="{{ route('admin.users.bulk') }}">
        @csrf
        <div class="bulk-actions">
            <div class="bulk-select">
                <input type="checkbox" id="selectAll" class="bulk-checkbox">
                <label for="selectAll">เลือกทั้งหมด</label>
            </div>
            <div class="bulk-buttons" style="display: none;">
                <select name="action" class="bulk-action-select">
                    <option value="">เลือกการดำเนินการ</option>
                    <option value="activate">เปิดใช้งาน</option>
                    <option value="deactivate">ปิดใช้งาน</option>
                    <select name="new_role_id" id="newRoleSelect" style="display: none;">
                        @foreach(\App\Models\Role::all() as $role)
                            <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                        @endforeach
                    </select>
                    <option value="change_role">เปลี่ยนบทบาท</option>
                    <option value="delete">ลบ</option>
                </select>
                <button type="submit" class="bulk-submit">ดำเนินการ</button>
            </div>
        </div>
    </form>

    <!-- Content Area -->
    <div class="page-content">
        <!-- Users Table -->
        <div class="content-card">
            <div class="table-responsive-wrapper">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th class="checkbox-col">
                                <input type="checkbox" id="headerCheckbox" class="bulk-checkbox">
                            </th>
                            <th class="avatar-col">รูปภาพ</th>
                            <th>
                                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'user_id', 'direction' => (request('sort') == 'user_id' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" class="sort-link">
                                    ID
                                    @if(request('sort') == 'user_id')
                                        <i class="bi bi-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'username', 'direction' => (request('sort') == 'username' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" class="sort-link">
                                    ชื่อผู้ใช้
                                    @if(request('sort') == 'username')
                                        <i class="bi bi-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>ข้อมูลส่วนตัว</th>
                            <th>ติดต่อ</th>
                            <th>บทบาท</th>
                            <th>สถานะ</th>
                            <th>
                                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => (request('sort') == 'created_at' && request('direction') == 'asc') ? 'desc' : 'asc'])) }}" class="sort-link">
                                    วันที่สมัคร
                                    @if(request('sort') == 'created_at')
                                        <i class="bi bi-chevron-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="checkbox-col">
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->user_id }}" class="bulk-checkbox user-checkbox" form="bulkForm">
                                </td>
                                <td class="avatar-col">
                                    <div class="user-avatar">
                                        @if($user->member && $user->member->photo_path)
                                            <img src="{{ asset('storage/' . $user->member->photo_path) }}" alt="{{ $user->username }}" class="avatar-img">
                                        @else
                                            <div class="avatar-placeholder">
                                                {{ strtoupper(substr($user->username, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $user->getDisplayId() }}</td>
                                <td>
                                    <div class="user-info">
                                        <strong>{{ $user->username }}</strong>
                                        <div class="user-email">{{ $user->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-details">
                                        @if($user->member)
                                            <div class="user-name">{{ $user->member->prefix }} {{ $user->member->first_name }} {{ $user->member->last_name }}</div>
                                            <div class="user-location">
                                                @if($user->province)
                                                    <i class="bi bi-geo-alt me-1"></i>{{ $user->province }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">ไม่ระบุ</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-info">
                                        @if($user->phone)
                                            <div><i class="bi bi-telephone me-1"></i>{{ $user->phone }}</div>
                                        @endif
                                        <div class="text-muted small">{{ $user->email }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-badge {{ $user->role_id == 1 ? 'admin' : 'member' }}">
                                        {{ $user->role?->role_name ?? 'ปกติ' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}">
                                        <i class="bi bi-{{ $user->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                                        {{ $user->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="registration-date">
                                        <div>{{ $user->created_at?->format('d/m/Y') }}</div>
                                        <div class="text-muted small">{{ $user->created_at?->diffForHumans() }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.users.show', $user->user_id) }}" class="btn-view" title="ดูรายละเอียด">
                                            <i class="bi bi-eye"></i> ดู
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->user_id) }}" class="btn-edit" title="แก้ไข">
                                            <i class="bi bi-pencil"></i> แก้ไข
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete" title="ลบ"
                                                    onclick="return confirm('ต้องการลบผู้ใช้นี้หรือไม่?')">
                                                <i class="bi bi-trash"></i> ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="empty-state">
                                    <div class="text-center py-5">
                                        <i class="bi bi-people display-4 mb-3 text-muted"></i>
                                        <h5 class="text-muted">ไม่มีผู้ใช้</h5>
                                        <p class="text-muted mb-3">
                                            @if(request()->hasAny(['search', 'role', 'status', 'date_from', 'date_to']))
                                                ไม่พบผู้ใช้ที่ตรงกับเงื่อนไขการค้นหา
                                            @else
                                                ยังไม่มีผู้ใช้ในระบบ
                                            @endif
                                        </p>
                                        <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                                            <i class="bi bi-plus-circle me-2"></i>เพิ่มผู้ใช้แรก
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="pagination-wrapper">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
/* ===== ENHANCED ADMIN USERS PAGE STYLES ===== */

/* Filters Section */
.filters-section {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
}

.filters-form {
    width: 100%;
}

.filters-row {
    display: grid;
    grid-template-columns: 1fr auto auto auto;
    gap: 1rem;
    align-items: end;
}

.search-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 250px;
    flex: 1;
}

.search-input {
    flex: 1;
    padding: 0.5rem 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.9rem;
    transition: border-color 0.2s ease;
}

.search-input:focus {
    outline: none;
    border-color: var(--orange-primary);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.search-btn {
    padding: 0.5rem 0.75rem;
    background: var(--orange-primary);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.search-btn:hover {
    background: var(--orange-dark);
}

.filter-group {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.filter-select {
    padding: 0.5rem 0.75rem;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    font-size: 0.9rem;
    min-width: 120px;
    transition: border-color 0.2s ease;
}

.filter-select:focus {
    outline: none;
    border-color: var(--orange-primary);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.date-filter {
    display: flex;
    gap: 0.5rem;
}

.clear-filters {
    color: #64748b;
    text-decoration: none;
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.clear-filters:hover {
    color: var(--orange-primary);
    background: rgba(255, 107, 53, 0.05);
}

.results-summary {
    margin-top: 1rem;
    padding: 0.75rem 1rem;
    background: #f8fafc;
    border-radius: 6px;
    border-left: 4px solid var(--orange-primary);
    font-size: 0.9rem;
    color: #475569;
}

/* Bulk Actions */
.bulk-actions {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
}

.bulk-select {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #64748b;
}

.bulk-checkbox {
    width: 16px;
    height: 16px;
    accent-color: var(--orange-primary);
}

.bulk-buttons {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.bulk-action-select {
    padding: 0.375rem 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 0.85rem;
    min-width: 140px;
}

.bulk-submit {
    padding: 0.375rem 0.75rem;
    background: var(--orange-primary);
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.bulk-submit:hover {
    background: var(--orange-dark);
}

/* Table Styles */
.users-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
    margin: 0;
}

.users-table thead tr {
    background: linear-gradient(135deg, var(--orange-primary) 0%, var(--orange-dark) 100%);
    color: white;
    position: sticky;
    top: 0;
    z-index: 10;
}

.users-table thead th {
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 2px solid rgba(255,255,255,0.2);
}

.users-table tbody td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.users-table tbody tr:hover {
    background-color: rgba(255, 107, 53, 0.02);
}

.checkbox-col {
    width: 40px;
    text-align: center;
}

.avatar-col {
    width: 80px;
    text-align: center;
}

.user-avatar {
    display: inline-block;
}

.avatar-img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e2e8f0;
    transition: transform 0.2s ease;
}

.avatar-img:hover {
    transform: scale(1.1);
}

.avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--orange-primary), var(--orange-dark));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1rem;
}

.sort-link {
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-weight: 600;
}

.sort-link:hover {
    color: rgba(255,255,255,0.9);
}

.user-info strong {
    display: block;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.user-email {
    color: #64748b;
    font-size: 0.8rem;
}

.user-details {
    min-width: 150px;
}

.user-name {
    font-weight: 500;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.user-location {
    color: #64748b;
    font-size: 0.8rem;
}

.contact-info {
    min-width: 140px;
}

.contact-info div {
    margin-bottom: 0.25rem;
}

.role-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-align: center;
    display: inline-block;
    min-width: 80px;
}

.role-badge.admin {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
}

.role-badge.member {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.status-badge.active {
    background: #dcfce7;
    color: #166534;
}

.status-badge.inactive {
    background: #fef2f2;
    color: #dc2626;
}

.registration-date {
    min-width: 100px;
}

.registration-date div:first-child {
    font-weight: 500;
    margin-bottom: 0.125rem;
}

.users-table th:last-child {
    width: 140px;
    min-width: 140px;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-start;
}

.btn-view, .btn-edit, .btn-delete {
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    gap: 0.375rem;
    white-space: nowrap;
}

.btn-view {
    background: #0ea5e9;
    color: white;
}

.btn-view:hover {
    background: #0284c7;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(14, 165, 233, 0.3);
}

.btn-edit {
    background: #3b82f6;
    color: white;
}

.btn-edit:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.btn-delete {
    background: #ef4444;
    color: white;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
}

/* Pagination */
.pagination-wrapper {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

.pagination-wrapper .pagination {
    margin: 0;
}

.pagination-wrapper .page-link {
    color: var(--orange-primary);
    border-color: #dee2e6;
    padding: 0.5rem 0.75rem;
    margin: 0 0.125rem;
    border-radius: 6px;
    transition: all 0.2s ease;
}

.pagination-wrapper .page-link:hover {
    color: var(--orange-dark);
    background: rgba(255, 107, 53, 0.05);
    border-color: var(--orange-primary);
}

.pagination-wrapper .page-item.active .page-link {
    background: var(--orange-primary);
    border-color: var(--orange-primary);
    color: white;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .filters-row {
        flex-direction: column;
        align-items: stretch;
    }

    .search-group {
        min-width: auto;
    }

    .filter-group {
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .filter-select {
        min-width: 100px;
    }

    .date-filter {
        flex-direction: column;
        gap: 0.5rem;
    }
}

@media (max-width: 768px) {
    .filters-section {
        padding: 1rem;
    }

    .filters-row {
        gap: 0.75rem;
    }

    .search-group {
        flex-direction: column;
        gap: 0.5rem;
    }

    .search-input {
        width: 100%;
    }

    .filter-group {
        flex-direction: column;
        gap: 0.5rem;
        align-items: stretch;
    }

    .filter-select {
        width: 100%;
        min-width: auto;
    }

    .clear-filters {
        justify-content: center;
    }

    .bulk-actions {
        padding: 1rem;
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }

    .bulk-buttons {
        justify-content: center;
    }

    .users-table {
        font-size: 0.8rem;
    }

    .users-table thead th,
    .users-table tbody td {
        padding: 0.75rem 0.5rem;
    }

    /* Hide less important columns on mobile */
    .users-table thead th:not(.checkbox-col):not(.avatar-col):not(:nth-child(4)):not(:nth-child(10)) {
        display: none;
    }

    .users-table tbody td:not(.checkbox-col):not(.avatar-col):not(:nth-child(4)):not(:nth-child(10)) {
        display: none;
    }

    .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }

    .user-details, .contact-info, .registration-date {
        min-width: auto;
    }
}

@media (max-width: 576px) {
    .page-container {
        padding: 1rem;
    }

    .page-header {
        padding: 1rem 0;
    }

    .title {
        font-size: 1.75rem;
    }

    .filters-section {
        padding: 0.75rem;
    }

    .bulk-actions {
        padding: 0.75rem;
    }

    .users-table {
        font-size: 0.75rem;
    }

    .users-table thead th,
    .users-table tbody td {
        padding: 0.5rem 0.25rem;
    }

    .avatar-img, .avatar-placeholder {
        width: 35px;
        height: 35px;
    }

    .role-badge, .status-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bulk actions functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const headerCheckbox = document.getElementById('headerCheckbox');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkButtons = document.querySelector('.bulk-buttons');
    const bulkActionSelect = document.querySelector('.bulk-action-select');
    const newRoleSelect = document.getElementById('newRoleSelect');

    // Handle select all checkbox
    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const totalBoxes = userCheckboxes.length;

        selectAllCheckbox.checked = checkedBoxes.length === totalBoxes && totalBoxes > 0;
        headerCheckbox.checked = selectAllCheckbox.checked;

        // Show/hide bulk actions
        if (checkedBoxes.length > 0) {
            bulkButtons.style.display = 'flex';
        } else {
            bulkButtons.style.display = 'none';
            bulkActionSelect.value = '';
            newRoleSelect.style.display = 'none';
        }
    }

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        headerCheckbox.checked = this.checked;
        updateSelectAllState();
    });

    headerCheckbox.addEventListener('change', function() {
        selectAllCheckbox.checked = this.checked;
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectAllState();
    });

    // Individual checkbox changes
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectAllState);
    });

    // Handle bulk action selection
    bulkActionSelect.addEventListener('change', function() {
        if (this.value === 'change_role') {
            newRoleSelect.style.display = 'inline-block';
            newRoleSelect.name = 'new_role_id';
        } else {
            newRoleSelect.style.display = 'none';
            newRoleSelect.name = '';
        }
    });

    // Bulk action form submission
    const bulkForm = document.getElementById('bulkForm');
    bulkForm.addEventListener('submit', function(e) {
        const selectedUsers = document.querySelectorAll('.user-checkbox:checked');
        if (selectedUsers.length === 0) {
            e.preventDefault();
            showNotification('กรุณาเลือกผู้ใช้อย่างน้อยหนึ่งคน', 'warning');
            return;
        }

        if (!bulkActionSelect.value) {
            e.preventDefault();
            showNotification('กรุณาเลือกการดำเนินการ', 'warning');
            return;
        }

        let confirmMessage = '';
        switch (bulkActionSelect.value) {
            case 'activate':
                confirmMessage = `ต้องการเปิดใช้งานผู้ใช้ ${selectedUsers.length} คนหรือไม่?`;
                break;
            case 'deactivate':
                confirmMessage = `ต้องการปิดใช้งานผู้ใช้ ${selectedUsers.length} คนหรือไม่?`;
                break;
            case 'change_role':
                const selectedRole = newRoleSelect.options[newRoleSelect.selectedIndex].text;
                confirmMessage = `ต้องการเปลี่ยนบทบาทเป็น "${selectedRole}" ของผู้ใช้ ${selectedUsers.length} คนหรือไม่?`;
                break;
            case 'delete':
                confirmMessage = `ต้องการลบผู้ใช้ ${selectedUsers.length} คนหรือไม่? การดำเนินการนี้ไม่สามารถยกเลิกได้`;
                break;
        }

        if (!confirm(confirmMessage)) {
            e.preventDefault();
        }
    });

    // Clear filters link
    const clearFiltersLink = document.querySelector('.clear-filters');
    if (clearFiltersLink) {
        clearFiltersLink.addEventListener('click', function(e) {
            e.preventDefault();
            const url = new URL(window.location);
            url.searchParams.delete('search');
            url.searchParams.delete('role');
            url.searchParams.delete('status');
            url.searchParams.delete('date_from');
            url.searchParams.delete('date_to');
            window.location.href = url.toString();
        });
    }

    // Auto-submit search on enter
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    }

    // Utility function for notifications
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Initialize
    updateSelectAllState();
});
</script>
@endsection
