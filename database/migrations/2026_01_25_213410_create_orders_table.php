<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // create_orders_table.php
    public function up()
    {
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('shop_id')->constrained()->onDelete('cascade');
        $table->foreignId('customer_id')->constrained();
        
        $table->string('order_number'); // Ej: ORD-001
        $table->decimal('total_amount', 10, 2);
        $table->string('status')->default('pending'); // pending, paid, shipped, cancelled
        
        // Snapshot de los datos del cliente al momento de la compra
        // Por si el cliente cambia su dirección 1 mes después, la orden histórica no cambia.
        $table->json('shipping_address_snapshot'); 
        
        // Lista de items comprados (Snapshot de precios y nombres)
        $table->json('items_snapshot'); 
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
