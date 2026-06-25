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
        'is_active'
    ];

    public function orderItems()
{
    return $this->hasMany(OrderItem::class);
}
}