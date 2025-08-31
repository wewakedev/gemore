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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // Shipping Address
            $table->json('shipping_address');
            
            // Billing Address
            $table->json('billing_address')->nullable();
            
            // Payment Information
            $table->json('payment'); // method, status, transaction_id, etc.
            
            // Pricing
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('shipping', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            // Coupon
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_discount', 10, 2)->default(0);
            
            // Status
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'])->default('pending');
            
            // Tracking
            $table->json('tracking')->nullable(); // provider, tracking_number, url, estimated_delivery
            
            // Status History
            $table->json('status_history')->nullable();
            
            // Notes
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Cancellation
            $table->json('cancellation')->nullable(); // reason, requested_at, processed_at, refund_status
            
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('order_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}; 