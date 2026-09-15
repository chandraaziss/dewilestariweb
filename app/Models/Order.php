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
            return \Carbon\Carbon::parse($this->estimated_delivery_date)->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') . ' WIB';
        }

        $baseTimestamp = $this->shipped_at ?? $this->paid_at ?? $this->created_at;
        $baseDate = $baseTimestamp ? \Carbon\Carbon::parse($baseTimestamp)->setTimezone('Asia/Jakarta') : \Carbon\Carbon::now('Asia/Jakarta');
        $option = strtolower($this->delivery_option ?? '');
        $formattedDate = $baseDate->translatedFormat('d M Y');

        if (str_contains($option, 'ambil') || str_contains($option, 'pickup')) {
            $startEst = $baseDate->copy()->addMinutes(30)->format('H:i');
            $endEst = $baseDate->copy()->addHours(1)->format('H:i');
            return "Hari Ini, {$formattedDate} (Estimasi Jam {$startEst} - {$endEst} WIB - Siap Ambil)";
        }

        if (str_contains($option, 'ekspedisi') || str_contains($option, 'expedition') || str_contains($option, 'jne') || str_contains($option, 'jnt') || str_contains($option, 'pos')) {
            $dateMin = $baseDate->copy()->addDays(1)->translatedFormat('d M Y');
            $dateMax = $baseDate->copy()->addDays(2)->translatedFormat('d M Y');
            return "{$dateMin} s/d {$dateMax} (Estimasi Jam 10:00 - 17:00 WIB)";
        }

        if (str_contains($option, 'instan') || str_contains($option, 'express') || str_contains($option, '1-2')) {
            $startEst = $baseDate->copy()->addHours(1)->format('H:i');
            $endEst = $baseDate->copy()->addHours(2)->format('H:i');
            return "Hari Ini, {$formattedDate} (Estimasi Jam {$startEst} - {$endEst} WIB - Instan)";
        }

        // Pengiriman Lokal Kurir Toko (Kota Cimahi & Kota Bandung) -> Sameday 2-4 Jam
        $startEst = $baseDate->copy()->addHours(2)->format('H:i');
        $endEst = $baseDate->copy()->addHours(4)->format('H:i');
        return "Hari Ini, {$formattedDate} (Estimasi Jam {$startEst} - {$endEst} WIB - Sameday)";
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