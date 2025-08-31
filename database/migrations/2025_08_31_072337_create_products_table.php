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
            $table->string('name');
            $table->text('description');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('subcategory')->nullable();
            $table->string('brand')->default('Ge More Nutralife');
            $table->string('sku')->unique();
            $table->json('images')->nullable(); // Array of image URLs
            $table->json('tags')->nullable(); // Array of tags
            $table->json('features')->nullable(); // Array of features
            $table->json('specifications')->nullable(); // Nutrition facts, serving size, etc.
            $table->decimal('ratings_average', 3, 2)->default(0);
            $table->integer('ratings_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->json('seo')->nullable(); // Meta title, description, keywords
            $table->timestamps();
            
            $table->index(['category_id', 'is_active']);
            $table->index(['is_featured', 'is_active']);
            $table->index('sku');
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