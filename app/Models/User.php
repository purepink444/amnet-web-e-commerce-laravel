<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ✅ ต้องระบุ Primary Key ให้ชัดเจน
    protected $primaryKey = 'user_id';
    
    // ✅ ถ้า user_id ไม่ใช่ auto-increment ต้องเพิ่มบรรทัดนี้ด้วย
    // public $incrementing = true;
    // protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id', // ✅ เปลี่ยนเป็น role_id
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * ✅ Relationship: User belongsTo Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    /**
     * ✅ Accessor: ดึงชื่อ role ออกมาใช้งาน
     */
    public function getRoleNameAttribute(): ?string
    {
        return $this->role?->role_name;
    }

    /**
     * ✅ Helper Methods สำหรับตรวจสอบ Role
     */
    public function isAdmin(): bool
    {
        return strtolower($this->role?->role_name ?? '') === 'admin';
    }

    public function isMember(): bool
    {
        return strtolower($this->role?->role_name ?? '') === 'member';
    }

    // Alias สำหรับ backward compatibility
    public function isCustomer(): bool
    {
        return $this->isMember();
    }
}