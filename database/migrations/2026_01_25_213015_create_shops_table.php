<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // create_shops_table.php
public function up()
{
    Schema::create('shops', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        
        // El dominio es tu identificador principal. Ej: "tienda1.com"
        $table->string('domain')->unique()->index(); 
        
        // AQUÍ vive la configuración visual (Data-Driven UI)
        // Guardarás: {"primary_color": "#ff0000", "font": "Inter", "logo": "url..."}
        $table->json('theme_config')->nullable(); 
        
        // Configuración de negocio (Moneda, Lenguaje)
        $table->string('currency', 3)->default('MXN');
        $table->string('status')->default('active'); // active, suspended, trial
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
