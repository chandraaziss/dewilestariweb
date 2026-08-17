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
            $table->string('tracking_ticket_id')->nullable()->unique()->after('paid_at');
            $table->string('tracking_status')->default('pending')->after('tracking_ticket_id');
            $table->timestamp('shipped_at')->nullable()->after('tracking_status');
            $table->timestamp('almost_arrived_at')->nullable()->after('shipped_at');
            $table->timestamp('delivered_at')->nullable()->after('almost_arrived_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_ticket_id',
                'tracking_status',
                'shipped_at',
                'almost_arrived_at',
                'delivered_at'
            ]);
        });
    }
};
