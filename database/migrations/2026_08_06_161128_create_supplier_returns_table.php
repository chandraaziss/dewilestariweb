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
        Schema::create('supplier_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number')->unique();
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->foreignId('supplier_order_id')->nullable()->constrained('supplier_orders')->onDelete('set null');
            $table->string('item_name');
            $table->string('weight')->nullable();
            $table->integer('quantity')->default(1);
            $table->text('reason');
            $table->string('proof_image')->nullable();
            $table->string('status')->default('pending'); // pending, approved, completed, rejected
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_returns');
    }
};
