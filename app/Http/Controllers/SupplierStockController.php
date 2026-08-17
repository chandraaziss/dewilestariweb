<?php

namespace App\Http\Controllers;

use App\Models\SupplierStock;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierStockController extends Controller
{
    public static function consolidateDuplicateStocks()
    {
        $activeStocks = SupplierStock::where('is_active', 1)->get();
        $grouped = $activeStocks->groupBy(function ($item) {
            return strtolower(trim($item->item_name));
        });

        foreach ($grouped as $itemName => $group) {
            if ($group->count() <= 1) {
                continue;
            }

            $primary = $group->first();
            $primaryVariants = $primary->variants ?? [];
            if (!is_array($primaryVariants)) {
                $primaryVariants = json_decode($primaryVariants, true) ?: [];
            }

            $duplicates = $group->slice(1);
            foreach ($duplicates as $dup) {
                $dupVariants = $dup->variants ?? [];
                if (!is_array($dupVariants)) {
                    $dupVariants = json_decode($dupVariants, true) ?: [];
                }

                foreach ($dupVariants as $dv) {
                    $dGrams = OrderController::parseWeightToGrams($dv['weight'] ?? '');
                    $matchedIndex = -1;

                    foreach ($primaryVariants as $idx => $pv) {
                        $pGrams = OrderController::parseWeightToGrams($pv['weight'] ?? '');
                        if ($pGrams === $dGrams) {
                            $matchedIndex = $idx;
                            break;
                        }
                    }

                    if ($matchedIndex >= 0) {
                        $pvAvailable = (int) ($primaryVariants[$matchedIndex]['available_quantity'] ?? 0);
                        $dvAvailable = (int) ($dv['available_quantity'] ?? 0);

                        if ($pvAvailable <= 0 && $dvAvailable > 0) {
                            $primaryVariants[$matchedIndex]['initial_quantity'] = (int) ($dv['initial_quantity'] ?? $dvAvailable);
                            $primaryVariants[$matchedIndex]['available_quantity'] = $dvAvailable;
                            $primaryVariants[$matchedIndex]['price'] = (float) ($dv['price'] ?? $primaryVariants[$matchedIndex]['price']);
                            $primaryVariants[$matchedIndex]['buy_price'] = (float) ($dv['buy_price'] ?? $primaryVariants[$matchedIndex]['buy_price']);
                            if (!empty($dv['entry_date'])) $primaryVariants[$matchedIndex]['entry_date'] = $dv['entry_date'];
                            if (!empty($dv['expiry_date'])) $primaryVariants[$matchedIndex]['expiry_date'] = $dv['expiry_date'];
                        } elseif ($pvAvailable > 0 && $dvAvailable > 0) {
                            $primaryVariants[$matchedIndex]['initial_quantity'] = (int)($primaryVariants[$matchedIndex]['initial_quantity'] ?? 0) + (int)($dv['initial_quantity'] ?? $dvAvailable);
                            $primaryVariants[$matchedIndex]['available_quantity'] += $dvAvailable;
                            if (!empty($dv['price']) && (float)$dv['price'] > 0) {
                                $primaryVariants[$matchedIndex]['price'] = (float) $dv['price'];
                            }
                        }
                    } else {
                        $primaryVariants[] = $dv;
                    }
                }

                $dup->delete();
            }

            $primary->variants = $primaryVariants;
            $primary->save();
        }
    }

    public function index()
    {
        self::consolidateDuplicateStocks();

        $stocks = SupplierStock::with('supplier')->latest()->get();
        $activeStocks = SupplierStock::with('supplier')->where('is_active', 1)->latest()->get();
        $queuedStocks = SupplierStock::with('supplier')
            ->where('is_active', 0)
            ->orderBy('created_at', 'asc')
            ->get();
        $stockLogs = \App\Models\StockLog::with(['supplierStock.supplier'])->latest()->limit(50)->get();
        $completedOrders = \App\Models\SupplierOrder::with('supplier')->where('status', 'completed')->latest()->get();

        return view('admin.supplier_stocks.index', compact('stocks', 'activeStocks', 'queuedStocks', 'stockLogs', 'completedOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('admin.supplier_stocks.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'item_name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'nullable',
            'entry_date' => 'nullable|date',
            'variants' => 'required|array|min:1',
            'variants.*.weight' => 'required|string',
            'variants.*.initial_quantity' => 'required|integer|min:1',
            'variants.*.buy_price' => 'required|numeric|min:0',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.expiry_date' => 'nullable|date',
        ]);

        $itemName = trim($request->item_name);

        $existingActiveStock = SupplierStock::whereRaw('LOWER(item_name) = ?', [strtolower($itemName)])
            ->where('is_active', 1)
            ->first();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $filename = time().'_'.$request->image->getClientOriginalName();
            $request->image->move(
                public_path('images/products'),
                $filename
            );
            $imagePath = 'images/products/'.$filename;
        }

        if ($existingActiveStock) {
            $activeVariants = $existingActiveStock->variants ?? [];
            if (!is_array($activeVariants)) {
                $activeVariants = json_decode($activeVariants, true) ?: [];
            }

            foreach ($request->input('variants', []) as $v) {
                $weight = trim($v['weight']);
                $initialQty = (int) $v['initial_quantity'];
                $buyPrice = (float) ($v['buy_price'] ?? 0);
                $sellPrice = !empty($v['price']) && (float) $v['price'] > 0
                    ? (float) $v['price']
                    : round($buyPrice * 1.30);
                $targetGrams = OrderController::parseWeightToGrams($weight);

                $matchedIndex = -1;
                foreach ($activeVariants as $idx => $av) {
                    if (OrderController::parseWeightToGrams($av['weight'] ?? '') === $targetGrams) {
                        $matchedIndex = $idx;
                        break;
                    }
                }

                if ($matchedIndex >= 0) {
                    $avAvailable = (int) ($activeVariants[$matchedIndex]['available_quantity'] ?? 0);
                    if ($avAvailable <= 0) {
                        $activeVariants[$matchedIndex]['initial_quantity'] = $initialQty;
                        $activeVariants[$matchedIndex]['available_quantity'] = $initialQty;
                    } else {
                        $activeVariants[$matchedIndex]['initial_quantity'] = (int)($activeVariants[$matchedIndex]['initial_quantity'] ?? 0) + $initialQty;
                        $activeVariants[$matchedIndex]['available_quantity'] += $initialQty;
                    }
                    $activeVariants[$matchedIndex]['price'] = $sellPrice;
                    $activeVariants[$matchedIndex]['buy_price'] = $buyPrice;
                    $activeVariants[$matchedIndex]['entry_date'] = $v['entry_date'] ?: now()->format('Y-m-d');
                    $activeVariants[$matchedIndex]['expiry_date'] = $v['expiry_date'] ?: null;
                } else {
                    $activeVariants[] = [
                        'weight' => $weight,
                        'initial_quantity' => $initialQty,
                        'available_quantity' => $initialQty,
                        'buy_price' => $buyPrice,
                        'price' => $sellPrice,
                        'entry_date' => $v['entry_date'] ?: now()->format('Y-m-d'),
                        'expiry_date' => $v['expiry_date'] ?: null,
                    ];
                }
            }

            $existingActiveStock->variants = $activeVariants;
            if ($imagePath) {
                $existingActiveStock->image_path = $imagePath;
            }
            if (!empty($request->description)) {
                $existingActiveStock->description = $request->description;
            }
            $existingActiveStock->save();

            \App\Models\StockLog::create([
                'supplier_stock_id' => $existingActiveStock->id,
                'weight' => $request->input('variants')[0]['weight'] ?? '',
                'type' => 'in',
                'quantity' => (int)($request->input('variants')[0]['initial_quantity'] ?? 1),
                'description' => 'Memperbarui data produk utama (tanpa membuat produk baru)',
            ]);

            return redirect('/admin/supplier-stocks')->with('success', 'Data produk `' . $itemName . '` berhasil diperbarui pada data yang sudah ada.');
        }

        // New product record if no existing record found
        $variants = [];
        foreach ($request->input('variants', []) as $v) {
            $buyPrice = (float) ($v['buy_price'] ?? 0);
            $sellPrice = !empty($v['price']) && (float) $v['price'] > 0
                ? (float) $v['price']
                : round($buyPrice * 1.30);
            $variants[] = [
                'weight' => trim($v['weight']),
                'initial_quantity' => (int) $v['initial_quantity'],
                'available_quantity' => (int) $v['initial_quantity'],
                'buy_price' => $buyPrice,
                'price' => $sellPrice,
                'entry_date' => $v['entry_date'] ?: now()->format('Y-m-d'),
                'expiry_date' => $v['expiry_date'] ?: null,
            ];
        }

        $stock = SupplierStock::create([
            'supplier_id' => $request->supplier_id,
            'item_name' => $itemName,
            'description' => $request->description,
            'image_path' => $imagePath,
            'is_active' => 1,
            'entry_date' => $variants[0]['entry_date'] ?? now()->format('Y-m-d'),
            'variants' => $variants,
        ]);

        return redirect('/admin/supplier-stocks')->with('success', 'Produk baru `' . $itemName . '` berhasil ditambahkan.');
    }

    public function storeBatchQueue(Request $request)
    {
        $request->validate([
            'parent_stock_id' => 'required|exists:supplier_stocks,id',
            'weight' => 'required|string',
            'initial_quantity' => 'required|integer|min:1',
            'buy_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'entry_date' => 'required|date',
            'expiry_date' => 'nullable|date',
            'supplier_order_id' => 'nullable|integer',
            'order_item_index' => 'nullable|integer',
        ]);

        if ($request->filled('supplier_order_id') && $request->has('order_item_index')) {
            $co = \App\Models\SupplierOrder::find($request->supplier_order_id);
            if ($co && is_array($co->items)) {
                $items = $co->items;
                $idx = (int) $request->order_item_index;
                if (isset($items[$idx])) {
                    $items[$idx]['processed'] = true;
                    $co->items = $items;
                    $co->save();
                }
            }
        }

        $parentStock = SupplierStock::findOrFail($request->parent_stock_id);
        $itemName = trim($parentStock->item_name);
        $supplierId = $parentStock->supplier_id;
        $description = $parentStock->description;
        $imagePath = $parentStock->image_path;

        $weight = trim($request->weight);
        $initialQty = (int) $request->initial_quantity;
        $buyPrice = (float) $request->buy_price;
        $sellPrice = !empty($request->price) && (float) $request->price > 0
            ? (float) $request->price
            : round($buyPrice * 1.30);
        $entryDate = $request->entry_date;
        $expiryDate = $request->expiry_date ?: null;

        $targetGrams = OrderController::parseWeightToGrams($weight);

        $existingActiveStock = SupplierStock::whereRaw('LOWER(item_name) = ?', [strtolower($itemName)])
            ->where('is_active', 1)
            ->first();

        $activeHasStock = false;
        if ($existingActiveStock && is_array($existingActiveStock->variants)) {
            foreach ($existingActiveStock->variants as $av) {
                if (OrderController::parseWeightToGrams($av['weight'] ?? '') === $targetGrams) {
                    if ((int)($av['available_quantity'] ?? 0) > 0) {
                        $activeHasStock = true;
                        break;
                    }
                }
            }
        }

        if ($existingActiveStock && !$activeHasStock) {
            // Update existing active product record directly!
            $activeVariants = $existingActiveStock->variants ?? [];
            if (!is_array($activeVariants)) {
                $activeVariants = json_decode($activeVariants, true) ?: [];
            }

            $matchedIndex = -1;
            foreach ($activeVariants as $idx => $av) {
                if (OrderController::parseWeightToGrams($av['weight'] ?? '') === $targetGrams) {
                    $matchedIndex = $idx;
                    break;
                }
            }

            if ($matchedIndex >= 0) {
                $activeVariants[$matchedIndex]['initial_quantity'] = $initialQty;
                $activeVariants[$matchedIndex]['available_quantity'] = $initialQty;
                $activeVariants[$matchedIndex]['buy_price'] = $buyPrice;
                $activeVariants[$matchedIndex]['price'] = $sellPrice;
                $activeVariants[$matchedIndex]['entry_date'] = $entryDate;
                $activeVariants[$matchedIndex]['expiry_date'] = $expiryDate;
            } else {
                $activeVariants[] = [
                    'weight' => $weight,
                    'initial_quantity' => $initialQty,
                    'available_quantity' => $initialQty,
                    'buy_price' => $buyPrice,
                    'price' => $sellPrice,
                    'entry_date' => $entryDate,
                    'expiry_date' => $expiryDate,
                ];
            }

            $existingActiveStock->variants = $activeVariants;
            $existingActiveStock->save();

            \App\Models\StockLog::create([
                'supplier_stock_id' => $existingActiveStock->id,
                'weight' => $weight,
                'type' => 'in',
                'quantity' => $initialQty,
                'description' => 'Memperbarui stok produk habis pada data produk yang sudah ada',
            ]);

            return redirect('/admin/supplier-stocks')->with('success', "Stok produk '{$itemName}' ({$weight}) berhasil diperbarui pada data produk utama.");
        }

        // Place in queue if active stock still has available_quantity > 0
        $variantObj = [
            'weight' => $weight,
            'initial_quantity' => $initialQty,
            'available_quantity' => $initialQty,
            'buy_price' => $buyPrice,
            'price' => $sellPrice,
            'entry_date' => $entryDate,
            'expiry_date' => $expiryDate,
        ];

        $queuedStock = SupplierStock::create([
            'supplier_id' => $supplierId,
            'item_name' => $itemName,
            'description' => $description,
            'image_path' => $imagePath,
            'is_active' => 0, // QUEUED
            'entry_date' => $entryDate,
            'variants' => [$variantObj],
        ]);

        \App\Models\StockLog::create([
            'supplier_stock_id' => $queuedStock->id,
            'weight' => $weight,
            'type' => 'in',
            'quantity' => $initialQty,
            'description' => 'Input batch baru ke antrean (Menunggu batch lama habis)',
        ]);

        return redirect('/admin/supplier-stocks?tab=queue')->with('success', "Batch baru untuk '{$itemName}' ({$weight}) berhasil masuk ke Antrean Batch (Menunggu stok lama habis).");
    }

    public function launchBatch($id)
    {
        $queuedStock = SupplierStock::findOrFail($id);
        $itemName = trim($queuedStock->item_name);

        $activeStock = SupplierStock::whereRaw('LOWER(item_name) = ?', [strtolower($itemName)])
            ->where('is_active', 1)
            ->first();

        if ($activeStock && $activeStock->id !== $queuedStock->id) {
            $activeVariants = $activeStock->variants ?? [];
            if (!is_array($activeVariants)) {
                $activeVariants = json_decode($activeVariants, true) ?: [];
            }

            foreach ($queuedStock->variants ?? [] as $qv) {
                $qGrams = OrderController::parseWeightToGrams($qv['weight'] ?? '');
                $matchedIndex = -1;
                foreach ($activeVariants as $idx => $av) {
                    if (OrderController::parseWeightToGrams($av['weight'] ?? '') === $qGrams) {
                        $matchedIndex = $idx;
                        break;
                    }
                }

                if ($matchedIndex >= 0) {
                    $activeVariants[$matchedIndex]['initial_quantity'] = (int)($qv['initial_quantity'] ?? 1);
                    $activeVariants[$matchedIndex]['available_quantity'] = (int)($qv['available_quantity'] ?? $qv['initial_quantity'] ?? 1);
                    $activeVariants[$matchedIndex]['buy_price'] = (float)($qv['buy_price'] ?? 0);
                    $activeVariants[$matchedIndex]['price'] = (float)($qv['price'] ?? 0);
                    if (!empty($qv['entry_date'])) $activeVariants[$matchedIndex]['entry_date'] = $qv['entry_date'];
                    if (!empty($qv['expiry_date'])) $activeVariants[$matchedIndex]['expiry_date'] = $qv['expiry_date'];
                } else {
                    $activeVariants[] = $qv;
                }
            }

            $activeStock->variants = $activeVariants;
            $activeStock->save();
            $queuedStock->delete();

            return redirect('/admin/supplier-stocks')->with('success', 'Batch ' . $itemName . ' berhasil diperbarui ke data produk utama toko.');
        }

        $queuedStock->is_active = 1;
        $queuedStock->save();
        return redirect('/admin/supplier-stocks')->with('success', 'Batch ' . $itemName . ' berhasil diluncurkan ke toko.');
    }

    public static function autoProcessBatchQueue($itemName, $weight = null)
    {
        $activeStock = SupplierStock::whereRaw('LOWER(item_name) = ?', [strtolower(trim($itemName))])
            ->where('is_active', 1)
            ->first();

        $queuedStocks = SupplierStock::whereRaw('LOWER(item_name) = ?', [strtolower(trim($itemName))])
            ->where('is_active', 0)
            ->orderBy('entry_date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();

        if ($queuedStocks->isEmpty() || !$activeStock) {
            return;
        }

        $activeVariants = $activeStock->variants ?? [];
        if (!is_array($activeVariants)) {
            $activeVariants = json_decode($activeVariants, true) ?: [];
        }

        foreach ($queuedStocks as $queuedStock) {
            $queuedVariants = $queuedStock->variants ?? [];
            if (!is_array($queuedVariants)) continue;

            $updatedAny = false;
            foreach ($queuedVariants as $qv) {
                $qWeight = $qv['weight'] ?? '';
                $qGrams = OrderController::parseWeightToGrams($qWeight);

                if ($weight !== null && $qGrams !== OrderController::parseWeightToGrams($weight)) {
                    continue;
                }

                $matchedIndex = -1;
                foreach ($activeVariants as $idx => $av) {
                    if (OrderController::parseWeightToGrams($av['weight'] ?? '') === $qGrams) {
                        $matchedIndex = $idx;
                        break;
                    }
                }

                if ($matchedIndex >= 0 && (int)($activeVariants[$matchedIndex]['available_quantity'] ?? 0) <= 0) {
                    $activeVariants[$matchedIndex]['initial_quantity'] = (int)($qv['initial_quantity'] ?? 1);
                    $activeVariants[$matchedIndex]['available_quantity'] = (int)($qv['available_quantity'] ?? $qv['initial_quantity'] ?? 1);
                    $activeVariants[$matchedIndex]['buy_price'] = (float)($qv['buy_price'] ?? 0);
                    $activeVariants[$matchedIndex]['price'] = (float)($qv['price'] ?? 0);
                    if (!empty($qv['entry_date'])) $activeVariants[$matchedIndex]['entry_date'] = $qv['entry_date'];
                    if (!empty($qv['expiry_date'])) $activeVariants[$matchedIndex]['expiry_date'] = $qv['expiry_date'];
                    $updatedAny = true;
                }
            }

            if ($updatedAny) {
                $activeStock->variants = $activeVariants;
                $activeStock->save();
                $queuedStock->delete();
            }
        }
    }

    public function edit($id)
    {
        $stock = SupplierStock::findOrFail($id);
        $suppliers = Supplier::all();
        return view('admin.supplier_stocks.edit', compact('stock', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $stock = SupplierStock::findOrFail($id);

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'item_name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'nullable',
            'entry_date' => 'nullable|date',
            'variants' => 'required|array|min:1',
            'variants.*.weight' => 'required|string',
            'variants.*.initial_quantity' => 'required|integer|min:1',
            'variants.*.buy_price' => 'required|numeric|min:0',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.expiry_date' => 'nullable|date',
        ]);

        $imagePath = $stock->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath && file_exists(public_path($imagePath))) {
                @unlink(public_path($imagePath));
            }
            $filename = time().'_'.$request->image->getClientOriginalName();
            $request->image->move(
                public_path('images/products'),
                $filename
            );
            $imagePath = 'images/products/'.$filename;
        }

        $activeVariants = [];
        $queuedVariants = [];
        $oldVariants = $stock->variants ?? [];

        foreach ($request->input('variants', []) as $v) {
            $weight = trim($v['weight']);
            $initialQty = (int) $v['initial_quantity'];
            $price = (float) $v['price'];
            $buyPrice = (float) $v['buy_price'];
            $entryDate = !empty($v['entry_date']) ? $v['entry_date'] : ($stock->entry_date ? $stock->entry_date->format('Y-m-d') : date('Y-m-d'));
            $expiryDate = $v['expiry_date'] ?: null;
            $targetGrams = OrderController::parseWeightToGrams($weight);

            $mergedActive = false;
            foreach ($activeVariants as &$av) {
                if (OrderController::parseWeightToGrams($av['weight'] ?? '') === $targetGrams) {
                    if (abs((float)$av['price'] - $price) < 0.01) {
                        $av['initial_quantity'] += $initialQty;
                        $av['available_quantity'] += $initialQty;
                        $mergedActive = true;
                        break;
                    } else {
                        $queuedVariants[] = [
                            'weight' => $weight,
                            'initial_quantity' => $initialQty,
                            'available_quantity' => $initialQty,
                            'buy_price' => $buyPrice,
                            'price' => $price,
                            'entry_date' => $entryDate,
                            'expiry_date' => $expiryDate,
                        ];
                        $mergedActive = true;
                        break;
                    }
                }
            }

            if ($mergedActive) {
                continue;
            }

            $availableQty = $initialQty;
            $foundOld = false;
            foreach ($oldVariants as $oldV) {
                if (OrderController::parseWeightToGrams($oldV['weight'] ?? '') === $targetGrams) {
                    $oldPrice = (float) ($oldV['price'] ?? 0);
                    $oldAvailable = (int) ($oldV['available_quantity'] ?? 0);

                    if (abs($oldPrice - $price) < 0.01) {
                        $oldInitialQty = (int) ($oldV['initial_quantity'] ?? 0);
                        $qtyDiff = $initialQty - $oldInitialQty;
                        $availableQty = $oldAvailable + $qtyDiff;
                        if ($availableQty < 0) {
                            $availableQty = 0;
                        }
                        $foundOld = true;
                        if ($qtyDiff != 0) {
                            \App\Models\StockLog::create([
                                'supplier_stock_id' => $stock->id,
                                'weight' => $weight,
                                'type' => 'adjustment',
                                'quantity' => abs($qtyDiff),
                                'description' => 'Penyesuaian stok awal oleh admin (' . ($qtyDiff > 0 ? '+' : '-') . abs($qtyDiff) . ')',
                            ]);
                        }
                        break;
                    }
                }
            }

            if (!$foundOld && empty($activeVariants)) {
                \App\Models\StockLog::create([
                    'supplier_stock_id' => $stock->id,
                    'weight' => $weight,
                    'type' => 'in',
                    'quantity' => $initialQty,
                    'description' => 'Varian baru ditambahkan saat edit',
                ]);
            }

            $activeVariants[] = [
                'weight' => $weight,
                'initial_quantity' => $initialQty,
                'available_quantity' => $availableQty,
                'buy_price' => $buyPrice,
                'price' => $price,
                'entry_date' => $entryDate,
                'expiry_date' => $expiryDate,
            ];
        }

        $firstEntryDate = $activeVariants[0]['entry_date'] ?? ($stock->entry_date ? $stock->entry_date->format('Y-m-d') : date('Y-m-d'));

        $stock->update([
            'supplier_id' => $request->supplier_id,
            'item_name' => $request->item_name,
            'description' => $request->description,
            'image_path' => $imagePath,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'entry_date' => $firstEntryDate,
            'variants' => $activeVariants,
        ]);

        if (!empty($queuedVariants)) {
            $queuedEntryDate = $queuedVariants[0]['entry_date'] ?? date('Y-m-d');
            SupplierStock::create([
                'supplier_id' => $request->supplier_id,
                'item_name' => $request->item_name,
                'description' => $request->description,
                'image_path' => $imagePath,
                'is_active' => 0, // QUEUED
                'entry_date' => $queuedEntryDate,
                'variants' => $queuedVariants,
            ]);

            return redirect('/admin/supplier-stocks')->with('success', 'Perubahan disimpan. Varian beda harga masuk ke antrean (Menunggu batch lama habis).');
        }

        return redirect('/admin/supplier-stocks')->with('success', 'Data barang masuk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $stock = SupplierStock::findOrFail($id);
        $stock->delete();

        return redirect('/admin/supplier-stocks')->with('success', 'Data barang masuk berhasil dihapus.');
    }

    public function expiredForm()
    {
        $stocks = SupplierStock::with('supplier')->where('is_active', 1)->get();
        return view('admin.supplier_stocks.expired', compact('stocks'));
    }

    public function storeExpired(Request $request)
    {
        $request->validate([
            'variant_key' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $parts = explode('|', $request->variant_key);
        if (count($parts) !== 2) {
            return back()->with('error', 'Pilihan produk tidak valid.');
        }

        $stockId = $parts[0];
        $weight = $parts[1];
        $quantity = (int)$request->quantity;

        $stock = SupplierStock::findOrFail($stockId);

        $variants = $stock->variants;
        $updated = false;
        $available = 0;
        foreach ($variants as &$v) {
            if (($v['weight'] ?? '') === $weight) {
                $available = (int)($v['available_quantity'] ?? 0);
                if ($available < $quantity) {
                    return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $available . ' pcs.');
                }
                $v['available_quantity'] = $available - $quantity;
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            return back()->with('error', 'Varian produk tidak ditemukan.');
        }

        // Save updated variants
        $stock->variants = $variants;
        $stock->save();

        $loggedDate = $request->created_at ? \Carbon\Carbon::parse($request->created_at) : now();

        // Create Stock Log
        \App\Models\StockLog::create([
            'supplier_stock_id' => $stock->id,
            'weight' => $weight,
            'type' => 'expired',
            'quantity' => $quantity,
            'description' => $request->description ?? 'Manually logged expired',
            'created_at' => $loggedDate,
            'updated_at' => now(),
        ]);

        return redirect('/admin/supplier-stocks/expired-list')->with('success', 'Barang kadaluwarsa berhasil dicatat.');
    }

    public function expiredList()
    {
        $logs = \App\Models\StockLog::with('supplierStock.supplier')
            ->where('type', 'expired')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        foreach ($logs as $log) {
            $buyPrice = 0;
            if ($log->supplierStock) {
                $variants = $log->supplierStock->variants;
                if (!is_array($variants)) {
                    $variants = json_decode($variants, true) ?: [];
                }
                foreach ($variants as $v) {
                    if (($v['weight'] ?? '') === $log->weight || count($variants) === 1) {
                        $buyPrice = (float)($v['buy_price'] ?? 0);
                        break;
                    }
                }
            }
            $log->buy_price = $buyPrice;
            $log->loss_value = $log->quantity * $buyPrice;
        }

        return view('admin.supplier_stocks.expired_list', compact('logs'));
    }

    public function editExpired($id)
    {
        $log = \App\Models\StockLog::with('supplierStock.supplier')
            ->where('type', 'expired')
            ->findOrFail($id);

        $stocks = SupplierStock::with('supplier')->where('is_active', 1)->get();

        $buyPrice = 0;
        if ($log->supplierStock) {
            $variants = $log->supplierStock->variants;
            if (!is_array($variants)) {
                $variants = json_decode($variants, true) ?: [];
            }
            foreach ($variants as $v) {
                if (($v['weight'] ?? '') === $log->weight || count($variants) === 1) {
                    $buyPrice = (float)($v['buy_price'] ?? 0);
                    break;
                }
            }
        }
        $log->buy_price = $buyPrice;

        return view('admin.supplier_stocks.expired_edit', compact('log', 'stocks'));
    }

    public function updateExpired(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        $log = \App\Models\StockLog::where('type', 'expired')->findOrFail($id);
        $newQty = (int)$request->quantity;
        $oldQty = (int)$log->quantity;
        $diff = $newQty - $oldQty;

        if ($diff !== 0 && $log->supplierStock) {
            $stock = $log->supplierStock;
            $variants = $stock->variants;
            if (!is_array($variants)) {
                $variants = json_decode($variants, true) ?: [];
            }
            $updated = false;
            foreach ($variants as &$v) {
                if (($v['weight'] ?? '') === $log->weight || count($variants) === 1) {
                    $currentAvailable = (int)($v['available_quantity'] ?? 0);
                    if ($diff > 0 && $currentAvailable < $diff) {
                        return back()->with('error', 'Stok tidak cukup untuk menaikkan jumlah kadaluarsa. Stok tersedia saat ini: ' . $currentAvailable . ' pcs.');
                    }
                    $v['available_quantity'] = max(0, $currentAvailable - $diff);
                    $updated = true;
                    break;
                }
            }
            if ($updated) {
                $stock->variants = $variants;
                $stock->save();
            }
        }

        $log->quantity = $newQty;
        $log->description = $request->description;
        if ($request->created_at) {
            $log->created_at = \Carbon\Carbon::parse($request->created_at);
        }
        $log->save();

        return redirect('/admin/supplier-stocks/expired-list')->with('success', 'Catatan barang kadaluarsa berhasil diperbarui.');
    }

    public function destroyExpired($id)
    {
        $log = \App\Models\StockLog::where('type', 'expired')->findOrFail($id);

        if ($log->supplierStock) {
            $stock = $log->supplierStock;
            $variants = $stock->variants;
            if (!is_array($variants)) {
                $variants = json_decode($variants, true) ?: [];
            }
            foreach ($variants as &$v) {
                if (($v['weight'] ?? '') === $log->weight || count($variants) === 1) {
                    $v['available_quantity'] = (int)($v['available_quantity'] ?? 0) + (int)$log->quantity;
                    break;
                }
            }
            $stock->variants = $variants;
            $stock->save();
        }

        $log->delete();

        return redirect('/admin/supplier-stocks/expired-list')->with('success', 'Catatan barang kadaluarsa berhasil dihapus dan stok dipulihkan.');
    }
}
