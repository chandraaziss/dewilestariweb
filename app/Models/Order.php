<?php

namespace App\Models;
use App\Models\OrderItem;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    public $timestamps = false;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'delivery_option',
        'delivery_address',
        'delivery_cost',
        'total_amount',
        'notes',
        'status',
        'payment_method',
        'payment_status',
        'midtrans_order_id',
        'paid_at'
    ];

    public function items()
{
    return $this->hasMany(OrderItem::class);
}
}