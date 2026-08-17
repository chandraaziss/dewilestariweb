<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Supplier;

class SupplierStock extends Model
{
    use HasFactory;

    protected $table = 'supplier_stocks';

    protected $fillable = [
        'supplier_id',
        'item_name',
        'description',
        'image_path',
        'is_active',
        'entry_date',
        'variants'
    ];

    protected $casts = [
        'entry_date' => 'date',
        'is_active' => 'boolean',
        'variants' => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    // Accessors for compatibility with Product attributes
    public function getNameAttribute()
    {
        return $this->item_name;
    }

    public function getCategoryAttribute()
    {
        if (empty($this->variants) || !is_array($this->variants)) {
            return '';
        }
        return collect($this->variants)->pluck('weight')->implode(', ');
    }

    public function getStockAttribute()
    {
        if (empty($this->variants) || !is_array($this->variants)) {
            return 0;
        }
        return (int) collect($this->variants)->sum('available_quantity');
    }

    public function getStockEntryDateAttribute()
    {
        return $this->entry_date;
    }

    public function decrementStock($weight, $quantity, $orderNumber = '')
    {
        $variants = $this->variants;
        if (!is_array($variants)) {
            return false;
        }

        $targetGrams = \App\Http\Controllers\OrderController::parseWeightToGrams($weight);

        $updated = false;
        $vFinished = false;
        foreach ($variants as &$v) {
            $vGrams = \App\Http\Controllers\OrderController::parseWeightToGrams($v['weight'] ?? '');
            if ($vGrams === $targetGrams) {
                $v['available_quantity'] = (int) ($v['available_quantity'] ?? 0) - (int) $quantity;
                if ($v['available_quantity'] <= 0) {
                    $v['available_quantity'] = 0;
                    $vFinished = true;
                }
                $updated = true;

                // Create Stock Log
                \App\Models\StockLog::create([
                    'supplier_stock_id' => $this->id,
                    'weight' => $v['weight'] ?? $weight,
                    'type' => 'out',
                    'quantity' => (int) $quantity,
                    'description' => 'Penjualan' . ($orderNumber ? ' order ' . $orderNumber : ''),
                ]);
                break;
            }
        }

        if ($updated) {
            $this->variants = $variants;
            $this->save();

            if ($vFinished) {
                \App\Http\Controllers\SupplierStockController::autoProcessBatchQueue($this->item_name, $weight);
            }

            return true;
        }

        return false;
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'product_id');
    }
}
