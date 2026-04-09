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
        Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('shop_id')->constrained()->onDelete('cascade')->index();
        
        $table->string('title');
        $table->string('slug'); // URL amigable
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->decimal('original_price', 10, 2);
        $table->integer('stock')->default(0);
        $table->string('brand')->nullable();
        $table->string('size')->nullable();
        $table->foreignId('category_id')->nullable()->constrained();
        $table->string('image_url')->nullable();
        $table->string('image_url_2')->nullable();
        $table->string('image_url_3')->nullable();
        $table->string('image_url_4')->nullable();
        $table->boolean('is_new')->nullable();

        $table->string('currency', 3)->default('MXN');
        
        // Define qué variantes tendrá. 
        // Ej: ["Color", "Talla"] o ["Memoria RAM", "Disco Duro"]
        $table->json('variant_options')->nullable(); 
        
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        
        // El slug debe ser único SOLO dentro de esa tienda
        // $table->unique(['shop_id', 'slug']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
