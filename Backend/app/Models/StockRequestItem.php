<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'product_id',
        'quantity_requested',
    ];

    public function request()
    {
        return $this->belongsTo(StockRequest::class, 'request_id', 'request_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
