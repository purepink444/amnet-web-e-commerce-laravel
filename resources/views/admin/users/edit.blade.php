@extends('layouts.admin')

@section('title', 'แก้ไขผู้ใช้')

@section('content')
<div style="margin: 0; padding: 40px 60px; font-family: 'Prompt', sans-serif; background: #ffffff; min-height: 100vh;">

    <!-- HEADER SECTION -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap; gap: 20px;">

        <!-- TITLE -->
        <div>
            <h1 style="font-size: 60px; font-weight: 700; margin-bottom: 5px; color: #1f2937;">แก้ไขผู้ใช้</h1>
            <p style="font-size: 24px; color: #6b7280; margin: 0; font-weight: 500;">แก้ไขข้อมูลผู้ใช้ในระบบ</p>
        </div>

        <!-- BACK BUTTON -->
        <a href="{{ route('admin.users.index') }}" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: #fff; padding: 18px 40px; font-size: 20px; font-weight: 600; border: 0; border-radius: 15px; cursor: pointer; text-decoration: none; white-space: nowrap; box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3); transition: all 0.3s ease;">
            <i class="fas fa-arrow-left" style="margin-right: 10px;"></i>กลับไปหน้าหลัก
        </a>

    </div>

    <!-- EDIT FORM -->
    <div style="background: white; border-radius: 20px; box-shadow: 0 8px 25px rgba(255, 139, 38, 0.15); border: 3px solid #ff8b26; overflow: hidden;">

        <!-- FORM HEADER -->
        <div style="background: linear-gradient(135deg, #ff8b26 0%, #e67e22 100%); padding: 25px 40px; color: white;">
            <h2 style="font-size: 28px; font-weight: 700; margin: 0;">
                <i class="fas fa-user-edit" style="margin-right: 15px;"></i>ข้อมูลผู้ใช้
            </h2>
            <p style="font-size: 18px; margin: 5px 0 0 0; opacity: 0.9;">แก้ไขข้อมูลส่วนตัวของผู้ใช้</p>
        </div>

        <!-- FORM CONTENT -->
        <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST" style="padding: 40px;">
            @csrf
            @method('PUT')

            <!-- BASIC INFO SECTION -->
            <div style="margin-bottom: 40px;">
                <h3 style="font-size: 24px; font-weight: 600; color: #1f2937; margin-bottom: 25px; border-bottom: 2px solid #ff8b26; padding-bottom: 10px;">
                    <i class="fas fa-id-card" style="margin-right: 10px;"></i>ข้อมูลพื้นฐาน
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">

                    <!-- PREFIX -->
                    <div>
                        <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <i class="fas fa-user-tag" style="margin-right: 8px; color: #ff8b26;"></i>คำนำหน้า
                        </label>
                        <select name="prefix" style="width: 100%; padding: 15px 18px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 12px; outline: none; background: white; font-family: 'Prompt', sans-serif;">
                            <option value="">เลือกคำนำหน้า</option>
                            <option value="นาย" {{ old('prefix', $user->prefix) == 'นาย' ? 'selected' : '' }}>นาย</option>
                            <option value="นาง" {{ old('prefix', $user->prefix) == 'นาง' ? 'selected' : '' }}>นาง</option>
                            <option value="นางสาว" {{ old('prefix', $user->prefix) == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                            <option value="ดร." {{ old('prefix', $user->prefix) == 'ดร.' ? 'selected' : '' }}>ดร.</option>
                        </select>
                        @error('prefix')
                            <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- FIRST NAME -->
                    <div>
                        <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <i class="fas fa-user" style="margin-right: 8px; color: #ff8b26;"></i>ชื่อ
                        </label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" placeholder="กรอกชื่อ" style="width: 100%; padding: 15px 18px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 12px; outline: none; font-family: 'Prompt', sans-serif;">
                        @error('first_name')
                            <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- LAST NAME -->
                    <div>
                        <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <i class="fas fa-user" style="margin-right: 8px; color: #ff8b26;"></i>นามสกุล
                        </label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" placeholder="กรอกนามสกุล" style="width: 100%; padding: 15px 18px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 12px; outline: none; font-family: 'Prompt', sans-serif;">
                        @error('last_name')
                            <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- USERNAME -->
                    <div>
                        <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <i class="fas fa-at" style="margin-right: 8px; color: #ff8b26;"></i>ชื่อผู้ใช้
                        </label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" placeholder="กรอกชื่อผู้ใช้" style="width: 100%; padding: 15px 18px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 12px; outline: none; font-family: 'Prompt', sans-serif;">
                        @error('username')
                            <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- ACCOUNT INFO SECTION -->
            <div style="margin-bottom: 40px;">
                <h3 style="font-size: 24px; font-weight: 600; color: #1f2937; margin-bottom: 25px; border-bottom: 2px solid #ff8b26; padding-bottom: 10px;">
                    <i class="fas fa-shield-alt" style="margin-right: 10px;"></i>ข้อมูลบัญชี
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">

                    <!-- EMAIL -->
                    <div>
                        <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <i class="fas fa-envelope" style="margin-right: 8px; color: #ff8b26;"></i>อีเมล
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="กรอกอีเมล" style="width: 100%; padding: 15px 18px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 12px; outline: none; font-family: 'Prompt', sans-serif;">
                        @error('email')
                            <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- PHONE -->
                    <div>
                        <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <i class="fas fa-phone" style="margin-right: 8px; color: #ff8b26;"></i>เบอร์โทรศัพท์
                        </label>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="กรอกเบอร์โทรศัพท์" style="width: 100%; padding: 15px 18px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 12px; outline: none; font-family: 'Prompt', sans-serif;">
                        @error('phone')
                            <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ROLE -->
                    <div>
                        <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <i class="fas fa-user-cog" style="margin-right: 8px; color: #ff8b26;"></i>บทบาท
                        </label>
                        <select name="role_id" style="width: 100%; padding: 15px 18px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 12px; outline: none; background: white; font-family: 'Prompt', sans-serif;">
                            @foreach($roles as $role)
                                <option value="{{ $role->role_id }}" {{ old('role_id', $user->role_id) == $role->role_id ? 'selected' : '' }}>
                                    {{ $role->role_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- PASSWORD -->
                    <div>
                        <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <i class="fas fa-lock" style="margin-right: 8px; color: #ff8b26;"></i>รหัสผ่านใหม่ (เว้นว่างหากไม่ต้องการเปลี่ยน)
                        </label>
                        <input type="password" name="password" placeholder="กรอกรหัสผ่านใหม่" style="width: 100%; padding: 15px 18px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 12px; outline: none; font-family: 'Prompt', sans-serif;">
                        @error('password')
                            <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- ADDRESS SECTION -->
            <div style="margin-bottom: 40px;">
                <h3 style="font-size: 24px; font-weight: 600; color: #1f2937; margin-bottom: 25px; border-bottom: 2px solid #ff8b26; padding-bottom: 10px;">
                    <i class="fas fa-map-marker-alt" style="margin-right: 10px;"></i>ข้อมูลที่อยู่
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">

                    <!-- ADDRESS -->
                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <i class="fas fa-home" style="margin-right: 8px; color: #ff8b26;"></i>ที่อยู่
                        </label>
                        <textarea name="address" rows="3" placeholder="กรอกที่อยู่" style="width: 100%; padding: 15px 18px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 12px; outline: none; font-family: 'Prompt', sans-serif; resize: vertical;">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- PROVINCE -->
                    <div>
                        <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <i class="fas fa-city" style="margin-right: 8px; color: #ff8b26;"></i>จังหวัด
                        </label>
                        <input type="text" name="province" value="{{ old('province', $user->province) }}" placeholder="กรอกจังหวัด" style="width: 100%; padding: 15px 18px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 12px; outline: none; font-family: 'Prompt', sans-serif;">
                        @error('province')
                            <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- DISTRICT -->
                    <div>
                        <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <i class="fas fa-building" style="margin-right: 8px; color: #ff8b26;"></i>อำเภอ
                        </label>
                        <input type="text" name="district" value="{{ old('district', $user->district) }}" placeholder="กรอกอำเภอ" style="width: 100%; padding: 15px 18px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 12px; outline: none; font-family: 'Prompt', sans-serif;">
                        @error('district')
                            <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- SUBDISTRICT -->
                    <div>
                        <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <i class="fas fa-map" style="margin-right: 8px; color: #ff8b26;"></i>ตำบล
                        </label>
                        <input type="text" name="subdistrict" value="{{ old('subdistrict', $user->subdistrict) }}" placeholder="กรอกตำบล" style="width: 100%; padding: 15px 18px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 12px; outline: none; font-family: 'Prompt', sans-serif;">
                        @error('subdistrict')
                            <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ZIPCODE -->
                    <div>
                        <label style="display: block; font-size: 18px; font-weight: 600; margin-bottom: 8px; color: #374151;">
                            <i class="fas fa-mailbox" style="margin-right: 8px; color: #ff8b26;"></i>รหัสไปรษณีย์
                        </label>
                        <input type="text" name="zipcode" value="{{ old('zipcode', $user->zipcode) }}" placeholder="กรอกรหัสไปรษณีย์" style="width: 100%; padding: 15px 18px; font-size: 18px; border: 2px solid #ff8b26; border-radius: 12px; outline: none; font-family: 'Prompt', sans-serif;">
                        @error('zipcode')
                            <div style="color: #dc3545; font-size: 14px; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- FORM ACTIONS -->
            <div style="border-top: 2px solid #e5e7eb; padding-top: 30px; display: flex; justify-content: flex-end; gap: 20px;">
                <a href="{{ route('admin.users.index') }}" style="background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%); color: white; padding: 15px 30px; font-size: 18px; font-weight: 600; border: none; border-radius: 12px; text-decoration: none; box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3); transition: all 0.3s ease;">
                    <i class="fas fa-times" style="margin-right: 8px;"></i>ยกเลิก
                </a>
                <button type="submit" style="background: linear-gradient(135deg, #00d621 0%, #00b81f 100%); color: white; padding: 15px 40px; font-size: 18px; font-weight: 600; border: none; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 15px rgba(0, 214, 33, 0.3); transition: all 0.3s ease;">
                    <i class="fas fa-save" style="margin-right: 8px;"></i>บันทึกการเปลี่ยนแปลง
                </button>
            </div>

        </form>
    </div>
</div>
@endsection