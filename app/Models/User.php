<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // บอก Laravel ว่า primary key คือ user_id
    protected $primaryKey = 'user_id';

    // ถ้าใช้ sequence/auto-increment
    public $incrementing = true;

    // ถ้า primary key เป็น integer (BIGINT)
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
}
