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
        Schema::table('carts', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique(['cart_token', 'product_id', 'product_variant_id']);
            
            // Add variant_size_id column
            $table->foreignId('variant_size_id')->nullable()->constrained()->onDelete('cascade')->after('product_variant_id');
            
            // Add new unique constraint that includes size
            $table->unique(['cart_token', 'product_id', 'product_variant_id', 'variant_size_id'], 'cart_unique_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('cart_unique_item');
            
            // Drop the variant_size_id column
            $table->dropForeign(['variant_size_id']);
            $table->dropColumn('variant_size_id');
            
            // Restore the old unique constraint
            $table->unique(['cart_token', 'product_id', 'product_variant_id']);
        });
    }
};

