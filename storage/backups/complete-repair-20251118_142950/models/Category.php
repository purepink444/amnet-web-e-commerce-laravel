<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    
    protected $fillable = [
        'category_name',
        'category_image',
        'description',
        'status',
    ];

    /**
     * Get products that belong to this category
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }
}