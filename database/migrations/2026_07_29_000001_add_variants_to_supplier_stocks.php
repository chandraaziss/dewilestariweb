<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add variants column to supplier_stocks
        Schema::table('supplier_stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_stocks', 'variants')) {
                $table->text('variants')->nullable()->after('price');
            }
        });

        // 2. Migrate existing records to variants JSON format
        try {
            $stocks = DB::table('supplier_stocks')->get();
            foreach ($stocks as $stock) {
                // If variants is already filled (e.g. if rerun), skip
                if (!empty($stock->variants)) {
                    continue;
                }
                
                $expiryStr = null;
                if (isset($stock->expiry_date)) {
                    $expiryStr = $stock->expiry_date;
                }

                $variants = [
                    [
                        'weight' => $stock->weight ?? '250 gram',
                        'initial_quantity' => (int) ($stock->initial_quantity ?? 0),
                        'available_quantity' => (int) ($stock->available_quantity ?? 0),
                        'price' => (float) ($stock->price ?? 0),
                        'expiry_date' => $expiryStr,
                    ]
                ];

                DB::table('supplier_stocks')
                    ->where('id', $stock->id)
                    ->update(['variants' => json_encode($variants)]);
            }
        } catch (\Exception $e) {
            // Ignore if tables are not ready or column missing
        }

        // 3. Drop columns from supplier_stocks
        Schema::table('supplier_stocks', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('supplier_stocks', 'weight')) $columnsToDrop[] = 'weight';
            if (Schema::hasColumn('supplier_stocks', 'initial_quantity')) $columnsToDrop[] = 'initial_quantity';
            if (Schema::hasColumn('supplier_stocks', 'available_quantity')) $columnsToDrop[] = 'available_quantity';
            if (Schema::hasColumn('supplier_stocks', 'expiry_date')) $columnsToDrop[] = 'expiry_date';
            if (Schema::hasColumn('supplier_stocks', 'price')) $columnsToDrop[] = 'price';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });

        // 4. Add weight column to order_items
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'weight')) {
                $table->string('weight')->nullable()->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_stocks', 'weight')) $table->string('weight')->nullable();
            if (!Schema::hasColumn('supplier_stocks', 'initial_quantity')) $table->integer('initial_quantity')->default(0);
            if (!Schema::hasColumn('supplier_stocks', 'available_quantity')) $table->integer('available_quantity')->default(0);
            if (!Schema::hasColumn('supplier_stocks', 'price')) $table->decimal('price', 15, 2)->default(0);
            if (!Schema::hasColumn('supplier_stocks', 'expiry_date')) $table->date('expiry_date')->nullable();
        });

        try {
            $stocks = DB::table('supplier_stocks')->get();
            foreach ($stocks as $stock) {
                if (!empty($stock->variants)) {
                    $variants = json_decode($stock->variants, true);
                    if (!empty($variants) && is_array($variants)) {
                        $first = $variants[0];
                        DB::table('supplier_stocks')
                            ->where('id', $stock->id)
                            ->update([
                                'weight' => $first['weight'] ?? null,
                                'initial_quantity' => $first['initial_quantity'] ?? 0,
                                'available_quantity' => $first['available_quantity'] ?? 0,
                                'price' => $first['price'] ?? 0,
                                'expiry_date' => $first['expiry_date'] ?? null,
                            ]);
                    }
                }
            }
        } catch (\Exception $e) {
        }

        Schema::table('supplier_stocks', function (Blueprint $table) {
            if (Schema::hasColumn('supplier_stocks', 'variants')) {
                $table->dropColumn('variants');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'weight')) {
                $table->dropColumn('weight');
            }
        });
    }
};
