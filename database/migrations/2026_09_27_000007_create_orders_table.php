<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->string('customer_name', 120);
            $table->string('phone', 30);
            $table->string('email', 190)->nullable();
            $table->text('delivery_address');
            $table->string('township', 80);
            $table->text('note')->nullable();

            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('delivery_fee')->default(0);
            $table->unsignedBigInteger('total');

            $table->string('status', 20)->default('pending');
            $table->string('payment_gateway', 20)->nullable();
            $table->string('payment_reference', 80)->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('placed_at');
            $table->index(['payment_gateway', 'payment_reference']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();

            // Snapshot of the catalogue at purchase time, so a later price or
            // product change never rewrites history.
            $table->string('name', 180);
            $table->string('sku', 60);
            $table->string('unit', 30)->default('pcs');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('line_total');
            $table->timestamps();

            $table->index('order_id');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway', 20);
            $table->string('gateway_reference', 120)->nullable();
            $table->unsignedBigInteger('amount');
            $table->string('status', 20)->default('pending');
            $table->json('payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'gateway_reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
