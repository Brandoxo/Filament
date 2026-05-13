<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')
                  ->nullable()
                  ->after('delivery_mode')
                  ->constrained('coupons')
                  ->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0)->after('coupon_id');
            $table->json('coupon_snapshot')->nullable()->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropColumn(['discount_amount', 'coupon_snapshot']);
        });
    }
};
