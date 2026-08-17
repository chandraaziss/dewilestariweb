<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add columns to supplier_stocks
        Schema::table('supplier_stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('supplier_stocks', 'price')) {
                $table->decimal('price', 15, 2)->default(0)->after('weight');
            }
            if (!Schema::hasColumn('supplier_stocks', 'description')) {
                $table->text('description')->nullable()->after('price');
            }
            if (!Schema::hasColumn('supplier_stocks', 'image_path')) {
                $table->string('image_path')->nullable()->after('description');
            }
            if (!Schema::hasColumn('supplier_stocks', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('image_path');
            }
        });

        // 2. Drop the foreign key referencing products in order_items and reference supplier_stocks instead
        try {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign('order_items_product_id_foreign');
            });
        } catch (\Exception $e) {
            // Ignore if foreign key doesn't exist
        }

        try {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreign('product_id')
                    ->references('id')
                    ->on('supplier_stocks')
                    ->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // Ignore if foreign key can't be added or exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
            });
        } catch (\Exception $e) {
        }

        try {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->onDelete('cascade');
            });
        } catch (\Exception $e) {
        }

        Schema::table('supplier_stocks', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('supplier_stocks', 'price')) $columns[] = 'price';
            if (Schema::hasColumn('supplier_stocks', 'description')) $columns[] = 'description';
            if (Schema::hasColumn('supplier_stocks', 'image_path')) $columns[] = 'image_path';
            if (Schema::hasColumn('supplier_stocks', 'is_active')) $columns[] = 'is_active';
            
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
