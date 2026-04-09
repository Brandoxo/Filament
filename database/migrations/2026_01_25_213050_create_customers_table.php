<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // create_customers_table.php
    public function up()
    {
    Schema::create('customers', function (Blueprint $table) {
        $table->id();
        
        // Relación vital con la tienda
        $table->foreignId('shop_id')->constrained()->onDelete('cascade');
        
        $table->string('name');
        $table->string('email');
        $table->string('password');
        $table->string('phone')->nullable();
        
        // Direcciones en JSON para no crear 5 tablas extra por ahora
        $table->json('addresses')->nullable(); 
        
        $table->timestamps();
        
        // ÍNDICE COMPUESTO: El email puede repetirse en la BD, 
        // pero NO dentro de la misma tienda.
        $table->unique(['shop_id', 'email']); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
