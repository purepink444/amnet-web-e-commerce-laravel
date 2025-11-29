@extends('layouts.admin')

@section('title', 'จัดการผู้ใช้')

@section('content')
<div style="margin: 0; padding: 40px 60px; font-family: 'Prompt', sans-serif; background: #ffffff; min-height: 100vh;">

    <!-- HEADER SECTION -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap; gap: 20px;">

        <!-- TITLE -->
        <div>
            <h1 style="font-size: 60px; font-weight: 700; margin-bottom: 5px; color: #1f2937;">จัดการผู้ใช้</h1>
            <p style="font-size: 24px; color: #6b7280; margin: 0; font-weight: 500;">จัดการผู้ใช้ทั้งหมดในระบบ</p>
        </div>

        <!-- ADD BUTTON -->
        <a href="{{ route('admin.users.create') }}" style="background: linear-gradient(135deg, #00d621 0%, #00b81f 100%); color: #fff; padding: 18px 50px; font-size: 24px; font-weight: 600; border: 0; border-radius: 15px; cursor: pointer; text-decoration: none; white-space: nowrap; box-shadow: 0 4px 15px rgba(0, 214, 33, 0.3); transition: all 0.3s ease;">
            <i class="fas fa-plus" style="margin-right: 10px;"></i>เพิ่มผู้ใช้
        </a>

    </div>

    <!-- SEARCH AND FILTER SECTION -->
    <div style="background: #f8f9fa; padding: 25px; border-radius: 15px; margin-bottom: 30px; border: 2px solid #e9ecef;">

        <form method="GET" action="{{ route('admin.users.index') }}" style="display: flex; flex-wrap: wrap; gap: 20px; align-items: end;">

            <!-- SEARCH BOX -->
            <div style="flex: 1; min-width: 250px;">
                <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #333;">
                    <i class="fas fa-search" style="margin-right: 8px;"></i>ค้นหาผู้ใช้
                </label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="ค้นหาชื่อผู้ใช้, อีเมล, หรือเบอร์โทร..."
                       style="width: 100%; padding: 12px 15px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 10px; outline: none;">
            </div>

            <!-- ROLE FILTER -->
            <div style="min-width: 200px;">
                <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #333;">
                    <i class="fas fa-user-tag" style="margin-right: 8px;"></i>บทบาท
                </label>
                <select name="role" style="width: 100%; padding: 12px 15px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 10px; outline: none; background: white;">
                    <option value="">ทั้งหมด</option>
                    <option value="1" {{ request('role') == '1' ? 'selected' : '' }}>Admin</option>
                    <option value="2" {{ request('role') == '2' ? 'selected' : '' }}>Member</option>
                </select>
            </div>

            <!-- STATUS FILTER -->
            <div style="min-width: 200px;">
                <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #333;">
                    <i class="fas fa-toggle-on" style="margin-right: 8px;"></i>สถานะ
                </label>
                <select name="status" style="width: 100%; padding: 12px 15px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 10px; outline: none; background: white;">
                    <option value="">ทั้งหมด</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>ใช้งาน</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>ปิดใช้งาน</option>
                </select>
            </div>

            <!-- BUTTONS -->
            <div style="display: flex; gap: 10px; align-items: end;">
                <button type="submit" style="background: #ff8b26; color: white; padding: 12px 25px; font-size: 18px; border: none; border-radius: 10px; cursor: pointer;">
                    <i class="fas fa-search" style="margin-right: 8px;"></i>ค้นหา
                </button>
                <a href="{{ route('admin.users.index') }}" style="background: #6c757d; color: white; padding: 12px 25px; font-size: 18px; border: none; border-radius: 10px; text-decoration: none; display: inline-block;">
                    <i class="fas fa-times" style="margin-right: 8px;"></i>ล้าง
                </a>
            </div>

        </form>

        <!-- RESULTS COUNT -->
        @if(request()->hasAny(['search', 'role', 'status']))
            <div style="margin-top: 15px; font-size: 16px; color: #666;">
                <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                พบผู้ใช้ {{ $users->total() }} คน
                @if(request('search'))
                    ที่ตรงกับ "{{ request('search') }}"
                @endif
            </div>
        @endif

    </div>

    <!-- TABLE -->
    <div style="width: 100%; border: 4px solid #ff8b26; border-radius: 20px; overflow: hidden; margin-top: 40px; box-shadow: 0 8px 25px rgba(255, 139, 38, 0.2);">
        <table style="width: 100%; border-collapse: collapse; font-size: 24px; font-weight: 500;">
            <thead>
                <tr style="background: linear-gradient(135deg, #ff8b26 0%, #e67e22 100%); color: white; height: 80px; font-weight: 700; font-size: 26px;">
                    <th style="padding: 20px; border-right: 4px solid #fff; text-align: center; text-transform: uppercase; letter-spacing: 1px;">ID</th>
                    <th style="padding: 20px; border-right: 4px solid #fff; text-align: center; text-transform: uppercase; letter-spacing: 1px;">ข้อมูลผู้ใช้</th>
                    <th style="padding: 20px; border-right: 4px solid #fff; text-align: center; text-transform: uppercase; letter-spacing: 1px;">ที่อยู่</th>
                    <th style="padding: 20px; border-right: 4px solid #fff; text-align: center; text-transform: uppercase; letter-spacing: 1px;">บทบาท</th>
                    <th style="padding: 20px; border-right: 4px solid #fff; text-align: center; text-transform: uppercase; letter-spacing: 1px;">สถานะ</th>
                    <th style="padding: 20px; text-align: center; text-transform: uppercase; letter-spacing: 1px;">จัดการผู้ใช้</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td style="padding: 22px; text-align: center; border-top: 3px solid #ff8b26; font-weight: 500;">{{ $user->getDisplayId() }}</td>
                        <td style="padding: 22px; text-align: center; border-top: 3px solid #ff8b26; font-weight: 500;">
                            {{ $user->prefix }} {{ $user->first_name }} {{ $user->last_name }}<br>
                            <small style="color: #666; font-size: 18px;">{{ $user->username }}</small>
                        </td>
                        <td style="padding: 22px; text-align: center; border-top: 3px solid #ff8b26; font-weight: 500;">
                            @if($user->province)
                                {{ $user->province }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 22px; text-align: center; border-top: 3px solid #ff8b26;">
                            <span style="background: {{ $user->role_id == 1 ? 'linear-gradient(135deg, #0044ff 0%, #0033cc 100%)' : 'linear-gradient(135deg, #00d621 0%, #00b81f 100%)' }}; color: white; padding: 8px 18px; border-radius: 20px; font-size: 20px; font-weight: 600; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">
                                {{ $user->role?->role_name ?? 'ปกติ' }}
                            </span>
                        </td>
                        <td style="padding: 22px; text-align: center; border-top: 3px solid #ff8b26;">
                            <span style="background: {{ $user->is_active ? 'linear-gradient(135deg, #00d621 0%, #00b81f 100%)' : 'linear-gradient(135deg, #ff1d1d 0%, #cc0000 100%)' }}; color: white; padding: 8px 18px; border-radius: 20px; font-size: 20px; font-weight: 600; box-shadow: 0 3px 10px rgba(0,0,0,0.2);">
                                {{ $user->is_active ? 'ใช้งาน' : 'ปิดใช้งาน' }}
                            </span>
                        </td>
                        <td style="padding: 18px; text-align: center; border-top: 2px solid #ff8b26;">
                            <a href="{{ route('admin.users.edit', $user->user_id) }}" style="background: linear-gradient(135deg, #0044ff 0%, #0033cc 100%); color: white; padding: 12px 25px; border-radius: 25px; font-size: 20px; font-weight: 600; cursor: pointer; margin-right: 12px; text-decoration: none; box-shadow: 0 4px 15px rgba(0, 68, 255, 0.3); transition: all 0.3s ease;">แก้ไข</a>
                            <form action="{{ route('admin.users.destroy', $user->user_id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        style="background: linear-gradient(135deg, #ff1d1d 0%, #cc0000 100%); color: white; padding: 12px 25px; border-radius: 25px; font-size: 20px; font-weight: 600; cursor: pointer; border: none; box-shadow: 0 4px 15px rgba(255, 29, 29, 0.3); transition: all 0.3s ease;"
                                        data-confirm-delete
                                        data-confirm-title="ต้องการลบผู้ใช้นี้หรือไม่?"
                                        data-confirm-text="ข้อมูลผู้ใช้และข้อมูลที่เกี่ยวข้องทั้งหมดจะถูกลบอย่างถาวร">
                                    ลบ
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; border-top: 2px solid #ff8b26; color: #666;">
                            <i class="fas fa-users fa-3x mb-3"></i><br>
                            ไม่มีผู้ใช้ในระบบ
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    @if($users->hasPages())
        <div style="text-align: center; margin-top: 30px;">
            {{ $users->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
