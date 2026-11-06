<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'branch_id',
        'status',
        'requested_by',
        'converted_transfer_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'user_id');
    }

    public function convertedTransfer()
    {
        return $this->belongsTo(StockTransfer::class, 'converted_transfer_id', 'transfer_id');
    }

    public function items()
    {
        return $this->hasMany(StockRequestItem::class, 'request_id', 'request_id');
    }
}
