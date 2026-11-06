<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomCakeRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'request_number',
        'user_id',
        'branch_id',
        'cake_size',
        'flavor',
        'design',
        'message',
        'needed_by',
        'quoted_price',
        'deposit_required_percentage',
        'deposit_paid',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'needed_by' => 'date',
        'quoted_price' => 'decimal:2',
        'deposit_required_percentage' => 'decimal:2',
        'deposit_paid' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    public function images()
    {
        return $this->hasMany(CustomCakeImage::class, 'request_id', 'request_id');
    }
}
