<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alerts the shop raises for itself, such as a new order.
 *
 * These are deliberately not notices: a notice is written by an administrator
 * and can be edited or withdrawn, whereas this is something that happened. It
 * cannot be edited, only read and cleared by each member of staff.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('kind', 40)->default('order');
            $table->string('title');
            $table->string('body', 300)->nullable();
            $table->string('link')->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['kind', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_alerts');
    }
};
