<?php

namespace App\Models;
use App\Models\OrderItem;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
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
        'transfer_proof',
        'payment_verified_at',
        'payment_verified_by',
        'midtrans_order_id',
        'snap_token',
        'paid_at',
        'tracking_ticket_id',
        'tracking_status',
        'shipped_at',
        'almost_arrived_at',
        'delivered_at',
        'delivery_proof',
        'return_reason',
        'return_proof',
        'return_requested_at',
        'return_status',
        'estimated_delivery_date'
    ];

    /**
     * Get human-friendly estimated delivery string.
     */
    public function getEstimatedDeliveryAttribute()
    {
        if ($this->tracking_status === 'completed' || $this->delivered_at) {
            $date = $this->delivered_at ? \Carbon\Carbon::parse($this->delivered_at) : null;
            return 'Selesai' . ($date ? ' (' . $date->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB)' : '');
        }

        if ($this->tracking_status === 'returned') {
            return 'Dalam Proses Pengembalian';
        }

        if ($this->estimated_delivery_date) {
            return \Carbon\Carbon::parse($this->estimated_delivery_date)->setTimezone('Asia/Jakarta')->translatedFormat('d M Y') . ' (Tiba Hari Ini)';
        }

        $baseTimestamp = $this->shipped_at ?? $this->paid_at ?? $this->created_at;
        $baseDate = $baseTimestamp ? \Carbon\Carbon::parse($baseTimestamp)->setTimezone('Asia/Jakarta') : \Carbon\Carbon::now('Asia/Jakarta');
        $option = strtolower($this->delivery_option ?? '');
        $formattedDate = $baseDate->translatedFormat('d M Y');

        if (str_contains($option, 'ambil') || str_contains($option, 'pickup')) {
            return "Hari Ini, {$formattedDate} (Estimasi 1 - 2 Jam)";
        }

        // Pengiriman Lokal Kurir Toko (Kota Cimahi & Kota Bandung) -> Tiba di hari yang sama
        return "Hari Ini, {$formattedDate} (Estimasi 2 - 4 Jam - Sameday)";
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
}