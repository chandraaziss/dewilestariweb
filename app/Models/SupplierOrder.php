<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierOrder extends Model
{
    use HasFactory;

    protected $table = 'supplier_orders';

    protected $fillable = [
        'invoice_number',
        'supplier_id',
        'items',
        'notes',
        'status',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}
