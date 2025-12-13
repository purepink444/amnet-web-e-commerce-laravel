<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brands';
    protected $primaryKey = 'brand_id';
    
    protected $fillable = [
        'brand_name',
        'logo_url',
        'description',
        'is_active',
    ];

    /**
     * Get products that belong to this brand
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'brand_id');
    }
}