<?php

namespace App\Models;
use App\Models\OrderItem;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'image_path',
        'category',
        'stock',
        'stock_entry_date',
        'expiry_date',
        'is_active'
    ];

    protected $casts = [
        'stock_entry_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}
}