<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'category_id'; // PK จริงของตาราง
    public $timestamps = false; // ถ้าไม่มี created_at/updated_at
    protected $fillable = ['name']; // ปรับตาม column จริง
}
