<?php

use App\Enums\PaymentMethodEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatus;
use App\Enums\ShippingMethodEnum;
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
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shipping_address_id')->nullable();
            $table->unsignedBigInteger('billing_address_id')->nullable();
            
            // Order Details
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->nullable();
            $table->decimal('shipping_cost', 10, 2);
            $table->decimal('discount_amount', 10, 2)->nullable()->comment('Discount amount applied by coupon');
            $table->decimal('total_amount', 10, 2);
            
            // Payment Information
            $table->string('transaction_number')->nullable()->comment('bKash | Rocket Transaction number for outside Dhaka area');
            $table->string('payment_method')->default(PaymentMethodEnum::COD->value);
            $table->string('payment_status')->default(PaymentStatus::UNPAID->value);
            $table->string('order_status')->default(OrderStatusEnum::PENDING->value);

            $table->boolean('is_inside_dhaka')->default(false);
            $table->text('notes')->nullable();
            
            // Order tracking Information
            $table->timestamps();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('processing_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            //shipping information
            $table->string('shipping_method')->default(ShippingMethodEnum::STANDARD->value);
            $table->string('shipping_tracking_number')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_area')->nullable();
            $table->string('shipping_address')->nullable();
            $table->string('shipping_notes')->nullable();

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
