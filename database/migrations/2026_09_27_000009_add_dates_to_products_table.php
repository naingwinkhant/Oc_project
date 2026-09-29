<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->date('produced_at')->nullable()->after('image');
            $table->date('expires_at')->nullable()->after('produced_at');

            // Expiry lookups ("what is about to go off?") run on every goods list,
            // the low-stock report and the dashboard.
            $table->index('expires_at');
            $table->index(['produced_at', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['expires_at']);
            $table->dropIndex(['produced_at', 'expires_at']);
            $table->dropColumn(['produced_at', 'expires_at']);
        });
    }
};
