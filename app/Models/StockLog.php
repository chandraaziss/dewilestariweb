<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    use HasFactory;

    protected $table = 'stock_logs';

    protected $fillable = [
        'supplier_stock_id',
        'weight',
        'type',
        'quantity',
        'description',
    ];

    public function supplierStock()
    {
        return $this->belongsTo(SupplierStock::class, 'supplier_stock_id');
    }
}
