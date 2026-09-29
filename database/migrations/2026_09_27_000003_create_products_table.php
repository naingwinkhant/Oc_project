<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku', 60)->unique();
            $table->string('barcode', 60)->nullable()->unique();
            $table->string('brand', 120)->nullable();
            $table->text('description')->nullable();
            $table->string('unit', 30)->default('pcs');
            $table->decimal('weight', 10, 3)->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('min_stock')->default(5);
            $table->string('image')->nullable();
            $table->json('attributes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'is_active']);
            $table->index('brand');

            if (Schema::getConnection()->getDriverName() === 'mysql') {
                $table->fullText(['name', 'brand', 'description'], 'products_search_index');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
