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
        Schema::create('variant_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
            $table->string('size_name'); // e.g., "1kg", "2kg", "300g", "500g"
            $table->string('size_display_name')->nullable(); // e.g., "1 Kilogram", "300 Grams"
            $table->decimal('price', 10, 2); // Price for this specific size
            $table->decimal('original_price', 10, 2)->nullable(); // Original price before discount
            $table->integer('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // Default size for the variant
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['product_variant_id', 'is_active']);
            $table->index(['product_variant_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_sizes');
    }
};

