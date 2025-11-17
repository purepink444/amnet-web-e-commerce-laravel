<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id'; // ถ้า PK ไม่ใช่ id
    public $timestamps = true; // หรือ false ถ้าไม่มี created_at/updated_at
    protected $fillable = [
        'product_name',
        'description',
        'specification',
        'price',
        'stock_quantity',
        'category_id',
        'brand_id',
        'image_url',
        'status',
    ];
    protected $keyType = 'int';

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }
}
