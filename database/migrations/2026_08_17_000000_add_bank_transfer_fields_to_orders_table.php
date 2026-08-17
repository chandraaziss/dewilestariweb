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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->default('midtrans')->after('payment_status');
            }
            if (!Schema::hasColumn('orders', 'transfer_proof')) {
                $table->string('transfer_proof')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('orders', 'payment_verified_at')) {
                $table->timestamp('payment_verified_at')->nullable()->after('transfer_proof');
            }
            if (!Schema::hasColumn('orders', 'payment_verified_by')) {
                $table->unsignedBigInteger('payment_verified_by')->nullable()->after('payment_verified_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'transfer_proof', 'payment_verified_at', 'payment_verified_by']);
        });
    }
};
