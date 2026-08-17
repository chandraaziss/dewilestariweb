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
        // 1. Add buy_price to order_items
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'buy_price')) {
                $table->decimal('buy_price', 15, 2)->default(0)->after('price');
            }
        });

        // 2. Create stock_logs table
        if (!Schema::hasTable('stock_logs')) {
            Schema::create('stock_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('supplier_stock_id');
                $table->string('weight')->nullable();
                $table->string('type'); // 'in', 'out', 'adjustment'
                $table->integer('quantity');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->foreign('supplier_stock_id')
                      ->references('id')
                      ->on('supplier_stocks')
                      ->onDelete('cascade');
            });
        }

        // 3. Migrate existing supplier_stocks JSON variants to include buy_price
        try {
            $stocks = DB::table('supplier_stocks')->get();
            foreach ($stocks as $stock) {
                if (!empty($stock->variants)) {
                    $variants = json_decode($stock->variants, true);
                    if (is_array($variants)) {
                        $updated = false;
                        foreach ($variants as &$v) {
                            if (!isset($v['buy_price'])) {
                                // Default buy price / HPP to 70% of sell price (supplier's share)
                                $v['buy_price'] = (float) (($v['price'] ?? 0) * 0.7);
                                $updated = true;
                            }
                        }
                        if ($updated) {
                            DB::table('supplier_stocks')
                                ->where('id', $stock->id)
                                ->update(['variants' => json_encode($variants)]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignore if tables are not ready or column missing
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'buy_price')) {
                $table->dropColumn('buy_price');
            }
        });

        Schema::dropIfExists('stock_logs');
    }
};
