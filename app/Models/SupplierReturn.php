<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierReturn extends Model
{
    protected $table = 'supplier_returns';

    protected $fillable = [
        'return_number',
        'supplier_id',
        'supplier_order_id',
        'item_name',
        'weight',
        'quantity',
        'reason',
        'proof_image',
        'status',
        'admin_notes',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function supplierOrder()
    {
        return $this->belongsTo(SupplierOrder::class, 'supplier_order_id');
    }
}
