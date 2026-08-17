<?php

namespace App\Http\Controllers;

use App\Models\SupplierStock;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function api(Request $request)
    {
        SupplierStockController::consolidateDuplicateStocks();

        $query = SupplierStock::where('is_active', 1);

        if ($request->filled('search')) {
            $search = strtolower(trim($request->search));
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(item_name) LIKE ?', ["%{$search}%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
            });
        }

        $products = $query->get()
            ->map(function ($product) {
                $variants = $product->variants;
                if (!is_array($variants)) {
                    $variants = json_decode($variants, true) ?: [];
                }

                $variantData = [];
                $seenGrams = [];
                $totalStock = 0;
                foreach ($variants as $v) {
                    $isExpired = false;
                    $availableQty = (int) ($v['available_quantity'] ?? 0);
                    $weightStr = $v['weight'] ?? '';
                    $grams = \App\Http\Controllers\OrderController::parseWeightToGrams($weightStr);

                    // Expiry check
                    if (!empty($v['expiry_date'])) {
                        $expiry = \Carbon\Carbon::parse($v['expiry_date']);
                        if ($expiry->isPast()) {
                            $availableQty = 0;
                            $isExpired = true;
                        }
                    }

                    if ($availableQty <= 0 || $isExpired) {
                        continue;
                    }

                    // Strict FIFO per weight size:
                    // Only show the first active variant of this weight size that has stock.
                    // Subsequent batches of the same weight size wait until the active batch is finished!
                    if ($grams > 0 && in_array($grams, $seenGrams)) {
                        continue;
                    }
                    if ($grams > 0) {
                        $seenGrams[] = $grams;
                    }

                    $totalStock += $availableQty;
                    $variantData[] = [
                        'id' => $product->id, // Use the product ID, so frontend adds correct product ID
                        'weight' => $weightStr,
                        'price' => (float) ($v['price'] ?? 0),
                        'stock' => $availableQty,
                        'is_expired' => $isExpired,
                        'expiry_date' => $v['expiry_date'] ?? null,
                    ];
                }

                return [
                    'name' => $product->item_name,
                    'description' => $product->description ?? '',
                    'image_path' => $product->image_path,
                    'variants' => $variantData,
                    'total_stock' => $totalStock,
                ];
            })
            ->filter(fn($p) => !empty($p['variants']))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}