<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('shipping_rate_id')
                  ->nullable()
                  ->after('status')
                  ->constrained('shipping_rates')
                  ->nullOnDelete();
            $table->string('tracking_number')->nullable()->after('shipping_rate_id');
            $table->json('carrier_snapshot')->nullable()->after('tracking_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipping_rate_id');
            $table->dropColumn(['tracking_number', 'carrier_snapshot']);
        });
    }
};
