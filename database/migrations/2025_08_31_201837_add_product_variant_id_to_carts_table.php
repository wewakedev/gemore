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
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade')->after('product_id');
            
            // Drop the old unique constraint
            $table->dropUnique(['cart_token', 'product_id']);
            
            // Add new unique constraint that includes variant
            $table->unique(['cart_token', 'product_id', 'product_variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique(['cart_token', 'product_id', 'product_variant_id']);
            
            // Restore the old unique constraint
            $table->unique(['cart_token', 'product_id']);
            
            // Drop the variant column
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });
    }
};
