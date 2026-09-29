<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon', 60)->nullable();
            $table->string('image')->nullable();
            $table->string('color', 20)->default('emerald');
            $table->unsignedInteger('depth')->default(0);
            $table->string('path')->default('/');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['parent_id', 'position']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
