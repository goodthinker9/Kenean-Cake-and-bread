<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomCakeImage extends Model
{
    use HasFactory;

    protected $primaryKey = 'image_id';

    protected $fillable = [
        'request_id',
        'image_url',
    ];

    public function request()
    {
        return $this->belongsTo(CustomCakeRequest::class, 'request_id', 'request_id');
    }
}
