<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password',
        'role',
        'branch_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function customCakeRequests()
    {
        return $this->hasMany(CustomCakeRequest::class, 'user_id', 'user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'Super Admin';
    }

    public function isMainManager(): bool
    {
        return $this->role === 'Main Branch Manager';
    }

    public function isBranchAdmin(): bool
    {
        return $this->role === 'Branch Admin';
    }

    public function isBranchStaff(): bool
    {
        return $this->role === 'Branch Staff';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'Customer';
    }
}
