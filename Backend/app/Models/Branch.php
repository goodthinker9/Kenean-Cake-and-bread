<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $primaryKey = 'branch_id';

    protected $fillable = [
        'branch_name',
        'branch_code',
        'branch_type',
        'region',
        'city',
        'address',
        'phone',
        'opening_hours',
        'manager_id',
        'status',
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id', 'user_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'branch_id', 'branch_id');
    }

    public function deliveryZones()
    {
        return $this->hasMany(DeliveryZone::class, 'branch_id', 'branch_id');
    }

    public function inventory()
    {
        return $this->hasMany(BranchInventory::class, 'branch_id', 'branch_id');
    }

    public function productPrices()
    {
        return $this->hasMany(BranchProductPrice::class, 'branch_id', 'branch_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'branch_id', 'branch_id');
    }

    public function transfersSent()
    {
        return $this->hasMany(StockTransfer::class, 'source_branch_id', 'branch_id');
    }

    public function transfersReceived()
    {
        return $this->hasMany(StockTransfer::class, 'destination_branch_id', 'branch_id');
    }

    public function stockRequests()
    {
        return $this->hasMany(StockRequest::class, 'branch_id', 'branch_id');
    }
}
