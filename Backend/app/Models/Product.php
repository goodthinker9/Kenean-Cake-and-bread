<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'category_id',
        'product_name',
        'description',
        'base_price',
        'shelf_life_hours',
        'status',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'shelf_life_hours' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'product_id')->orderBy('sort_order');
    }

    public function branchPrices()
    {
        return $this->hasMany(BranchProductPrice::class, 'product_id', 'product_id');
    }

    public function branchInventory()
    {
        return $this->hasMany(BranchInventory::class, 'product_id', 'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id', 'product_id');
    }

    public function getPriceForBranch($branchId)
    {
        if (!$branchId) {
            return (float) $this->base_price;
        }

        $override = $this->branchPrices()
            ->where('branch_id', $branchId)
            ->where('active', true)
            ->first();

        return $override ? (float) $override->price : (float) $this->base_price;
    }
}
