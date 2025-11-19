<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $with = ['role'];

    protected $fillable = [
        'username',
        'email',
        'password',
        'role_id',
        'prefix',
        'firstname',
        'lastname',
        'phone',
        'address',
        'province',
        'district',
        'subdistrict',
        'zipcode',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    public function isAdmin(): bool
    {
        return strtolower($this->role?->role_name ?? '') === 'admin';
    }

    public function isMember(): bool
    {
        return strtolower($this->role?->role_name ?? '') === 'member';
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }
}
