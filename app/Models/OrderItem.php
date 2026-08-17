<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SupplierStock;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'qty',
        'price',
        'subtotal',
        'buy_price',
        'weight'
    ];

    public function order()
    {
    return $this->belongsTo(
        Order::class
    );
    }

    public function product()
    {
        return $this->belongsTo(
            SupplierStock::class, 'product_id'
        );
    }
    
}