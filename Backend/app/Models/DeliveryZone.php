<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $primaryKey = 'zone_id';

    protected $fillable = [
        'branch_id',
        'zone_name',
        'delivery_fee',
        'status',
    ];

    protected $casts = [
        'delivery_fee' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }
}
