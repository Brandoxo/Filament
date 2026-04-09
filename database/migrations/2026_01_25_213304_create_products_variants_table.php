<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('product_variants', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->onDelete('cascade');
        
        $table->string('sku')->nullable();
        $table->integer('stock')->default(0);
        $table->decimal('price', 10, 2); // Precio final de esta variante
        
        // LA MAGIA: Aquí guardas la combinación específica
        // Ej: {"Color": "Rojo", "Talla": "M"}
        $table->json('attributes'); 
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_variants');
    }
};
