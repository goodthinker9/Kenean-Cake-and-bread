<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'transfer_item_id';

    protected $fillable = [
        'transfer_id',
        'product_id',
        'quantity_sent',
        'quantity_received',
        'damaged_quantity',
        'missing_quantity',
        'discrepancy_reason',
    ];

    public function transfer()
    {
        return $this->belongsTo(StockTransfer::class, 'transfer_id', 'transfer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
