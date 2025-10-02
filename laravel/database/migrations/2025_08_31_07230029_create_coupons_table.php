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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 10, 2);
            $table->decimal('minimum_order_amount', 10, 2)->default(0);
            $table->decimal('maximum_discount', 10, 2)->nullable();
            
            // Usage Limits
            $table->integer('usage_limit_total')->nullable(); // null means unlimited
            $table->integer('usage_limit_per_user')->default(1);
            $table->integer('usage_count')->default(0);
            
            // Validity
            $table->datetime('valid_from');
            $table->datetime('valid_until');
            
            // Applicable Products/Categories
            $table->json('applicable_products')->nullable(); // Array of product IDs
            $table->json('applicable_categories')->nullable(); // Array of category IDs
            $table->json('exclude_products')->nullable(); // Array of product IDs to exclude
            $table->json('exclude_categories')->nullable(); // Array of category IDs to exclude
            
            $table->boolean('is_active')->default(true);
            $table->boolean('is_first_time_user')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['code', 'is_active']);
            $table->index(['valid_from', 'valid_until']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
}; 