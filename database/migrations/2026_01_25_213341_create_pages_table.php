<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // create_pages_table.php
    public function up()
    {
    Schema::create('pages', function (Blueprint $table) {
        $table->id();
        $table->foreignId('shop_id')->constrained()->onDelete('cascade');
        
        $table->string('title'); // Título para SEO y pestaña del navegador
        $table->string('slug');  // Ej: "ofertas-verano"
        
        // AQUÍ guardas el array de componentes para Vue
        // Ej: [{"component": "Hero", "props": {...}}, {"component": "Grid", ...}]
        $table->json('content_structure'); 
        
        $table->boolean('is_published')->default(true);
        $table->timestamps();
        
        $table->unique(['shop_id', 'slug']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
