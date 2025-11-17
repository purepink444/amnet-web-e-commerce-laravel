<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'username',
        'email',
        'password',
        'firstname',
        'lastname',
        'address',
        'subdistrict',
        'district',
        'province',
        'zipcode',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Override getAuthIdentifierName เพื่อบอก Laravel ว่าใช้ user_id แทน id
     */
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    /**
     * Get the wishlist items for the user.
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class, 'user_id', 'user_id');
    }

    /**
     * Get the role of the user.
     * ระบุ foreign key และ owner key ให้ชัดเจน
     */
    public function role()
    {
        // ตรวจสอบว่าตาราง roles ใช้ primary key ชื่ออะไร
        // ถ้าใช้ role_id ให้เปลี่ยนเป็น
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
        
        // ถ้าใช้ id ให้ใช้
        // return $this->belongsTo(Role::class, 'role_id', 'id');
    }
}