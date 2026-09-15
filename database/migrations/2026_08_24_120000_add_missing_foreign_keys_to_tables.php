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
        // 1. Bersihkan data yatim (orphaned records) yang merujuk ke ID yang tidak ada
        DB::table('ratings')
            ->whereNotIn('order_id', DB::table('orders')->pluck('id'))
            ->delete();

        DB::table('ratings')
            ->whereNotIn('product_id', DB::table('supplier_stocks')->pluck('id'))
            ->delete();

        DB::table('ratings')
            ->whereNotIn('user_id', DB::table('users')->pluck('id'))
            ->delete();

        DB::table('order_items')
            ->whereNotIn('order_id', DB::table('orders')->pluck('id'))
            ->delete();

        // 2. Samakan tipe data kolom agar cocok dengan Primary Key (BIGINT UNSIGNED)
        DB::statement("ALTER TABLE orders MODIFY id BIGINT UNSIGNED AUTO_INCREMENT");
        DB::statement("ALTER TABLE order_items MODIFY order_id BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE order_items MODIFY product_id BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE ratings MODIFY order_id BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE ratings MODIFY order_item_id BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE ratings MODIFY product_id BIGINT UNSIGNED NOT NULL");

        // 3. Pasang Foreign Key fisik pada tabel order_items
        Schema::table('order_items', function (Blueprint $table) {
            try {
                $table->foreign('order_id', 'fk_order_items_order_id')
                      ->references('id')
                      ->on('orders')
                      ->onDelete('cascade');
            } catch (\Exception $e) {}

            try {
                $table->foreign('product_id', 'fk_order_items_product_id')
                      ->references('id')
                      ->on('supplier_stocks')
                      ->onDelete('set null');
            } catch (\Exception $e) {}
        });

        // 4. Pasang Foreign Key fisik pada tabel ratings
        Schema::table('ratings', function (Blueprint $table) {
            try {
                $table->foreign('order_id', 'fk_ratings_order_id')
                      ->references('id')
                      ->on('orders')
                      ->onDelete('cascade');
            } catch (\Exception $e) {}

            try {
                $table->foreign('product_id', 'fk_ratings_product_id')
                      ->references('id')
                      ->on('supplier_stocks')
                      ->onDelete('cascade');
            } catch (\Exception $e) {}

            try {
                $table->foreign('user_id', 'fk_ratings_user_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('cascade');
            } catch (\Exception $e) {}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            try {
                $table->dropForeign('fk_order_items_order_id');
            } catch (\Exception $e) {}
            try {
                $table->dropForeign('fk_order_items_product_id');
            } catch (\Exception $e) {}
        });

        Schema::table('ratings', function (Blueprint $table) {
            try {
                $table->dropForeign('fk_ratings_order_id');
            } catch (\Exception $e) {}
            try {
                $table->dropForeign('fk_ratings_product_id');
            } catch (\Exception $e) {}
            try {
                $table->dropForeign('fk_ratings_user_id');
            } catch (\Exception $e) {}
        });
    }
};
