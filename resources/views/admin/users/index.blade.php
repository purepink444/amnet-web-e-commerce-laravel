@extends('layouts.admin')

@section('title', 'จัดการผู้ใช้')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
        <h3 class="card-title mb-2 mb-sm-0 h5 h-sm-4">จัดการผู้ใช้</h3>
        <div class="card-tools">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i>
                <span class="d-none d-sm-inline">เพิ่มผู้ใช้</span>
                <span class="d-sm-none">เพิ่ม</span>
            </a>
        </div>
    </div>
    <div class="card-body p-0 p-sm-3">
        <!-- Desktop Table View -->
        <div class="d-none d-md-block">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">#</th>
                            <th>ข้อมูลผู้ใช้</th>
                            <th>ข้อมูลติดต่อ</th>
                            <th>ที่อยู่</th>
                            <th>บทบาท</th>
                            <th>วันที่สร้าง</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="text-center fw-bold">{{ $user->user_id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-2" style="width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                            {{ strtoupper(substr($user->firstname, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $user->prefix }} {{ $user->firstname }} {{ $user->lastname }}</div>
                                            <small class="text-muted">{{ $user->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div><i class="fas fa-envelope text-primary me-1"></i>{{ $user->email }}</div>
                                    @if($user->phone)
                                        <div><i class="fas fa-phone text-success me-1"></i>{{ $user->phone }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($user->address)
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $user->address }}">
                                            {{ Str::limit($user->address, 30) }}
                                        </div>
                                        @if($user->province)
                                            <small class="text-muted">{{ $user->province }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->role_id == 1 ? 'primary' : 'success' }} fs-6">
                                        {{ $user->role?->role_name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.users.show', $user->user_id) }}" class="btn btn-outline-info btn-sm" title="ดูรายละเอียด">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->user_id) }}" class="btn btn-outline-primary btn-sm" title="แก้ไข">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    data-confirm-delete
                                                    data-confirm-title="ต้องการลบผู้ใช้นี้หรือไม่?"
                                                    data-confirm-text="ข้อมูลผู้ใช้และข้อมูลที่เกี่ยวข้องทั้งหมดจะถูกลบอย่างถาวร"
                                                    title="ลบ">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-2x mb-2 text-muted"></i>
                                    <div>ไม่มีผู้ใช้ในระบบ</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="d-md-none">
            <div class="list-group list-group-flush">
                @forelse($users as $user)
                    <div class="list-group-item px-3 py-3">
                        <div class="d-flex align-items-start">
                            <!-- Avatar -->
                            <div class="avatar-circle bg-primary text-white me-3 flex-shrink-0" style="width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem;">
                                {{ strtoupper(substr($user->firstname, 0, 1)) }}
                            </div>

                            <!-- User Info -->
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $user->prefix }} {{ $user->firstname }} {{ $user->lastname }}</h6>
                                        <small class="text-muted">{{ $user->username }}</small>
                                    </div>
                                    <span class="badge bg-{{ $user->role_id == 1 ? 'primary' : 'success' }} ms-2">
                                        {{ $user->role?->role_name ?? 'N/A' }}
                                    </span>
                                </div>

                                <div class="mb-2">
                                    <div class="small text-truncate">
                                        <i class="fas fa-envelope text-primary me-1"></i>{{ $user->email }}
                                    </div>
                                    @if($user->phone)
                                        <div class="small">
                                            <i class="fas fa-phone text-success me-1"></i>{{ $user->phone }}
                                        </div>
                                    @endif
                                </div>

                                @if($user->province)
                                    <div class="small text-muted mb-2">
                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $user->province }}
                                    </div>
                                @endif

                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>สมัครเมื่อ {{ $user->created_at->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-1 mt-3">
                            <a href="{{ route('admin.users.show', $user->user_id) }}" class="btn btn-outline-info btn-sm flex-fill">
                                <i class="fas fa-eye me-1"></i>ดู
                            </a>
                            <a href="{{ route('admin.users.edit', $user->user_id) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="fas fa-edit me-1"></i>แก้ไข
                            </a>
                            <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" class="flex-fill">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                        data-confirm-delete
                                        data-confirm-title="ต้องการลบผู้ใช้นี้หรือไม่?"
                                        data-confirm-text="ข้อมูลผู้ใช้และข้อมูลที่เกี่ยวข้องทั้งหมดจะถูกลบอย่างถาวร">
                                    <i class="fas fa-trash me-1"></i>ลบ
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="fas fa-users fa-3x mb-3 text-muted"></i>
                        <h5 class="text-muted">ไม่มีผู้ใช้ในระบบ</h5>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>เพิ่มผู้ใช้คนแรก
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="card-footer d-flex justify-content-center">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
