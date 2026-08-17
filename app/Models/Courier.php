<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Courier extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'password',
        'phone',
    ];

    protected $hidden = [
        'password',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
