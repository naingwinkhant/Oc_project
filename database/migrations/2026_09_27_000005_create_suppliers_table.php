<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 40)->unique();
            $table->string('contact_name', 120)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email', 190)->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->string('supplier_sku', 60)->nullable();

            $table->unique(['product_id', 'supplier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
        Schema::dropIfExists('suppliers');
    }
};
