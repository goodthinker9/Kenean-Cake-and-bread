<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchInventory extends Model
{
    use HasFactory;

    protected $table = 'branch_inventory';
    protected $primaryKey = 'inventory_id';

    protected $fillable = [
        'branch_id',
        'product_id',
        'quantity_on_hand',
        'reserved_quantity',
        'reorder_level',
    ];

    protected $appends = [
        'available_quantity',
        'status',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function getAvailableQuantityAttribute(): int
    {
        return max(0, (int) $this->quantity_on_hand - (int) $this->reserved_quantity);
    }

    public function getStatusAttribute(): string
    {
        $avail = $this->available_quantity;
        if ($avail <= 0) {
            return 'Sold out';
        }
        if ($avail <= (int) $this->reorder_level) {
            return 'Low';
        }
        return 'Healthy';
    }
}
