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
            $table->text('return_reason')->nullable()->after('delivery_proof');
            $table->string('return_proof')->nullable()->after('return_reason');
            $table->timestamp('return_requested_at')->nullable()->after('return_proof');
            $table->string('return_status')->nullable()->default('pending')->after('return_requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'return_reason',
                'return_proof',
                'return_requested_at',
                'return_status',
            ]);
        });
    }
};
