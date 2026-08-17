<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'slug',
        'phone',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function orders()
    {
        return $this->hasMany(SupplierOrder::class, 'supplier_id');
    }

    public function returns()
    {
        return $this->hasMany(SupplierReturn::class, 'supplier_id');
    }
}
