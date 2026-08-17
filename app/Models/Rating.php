<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'order_id',
        'order_item_id',
        'product_id',
        'user_id',
        'rating',
        'review',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(SupplierStock::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
