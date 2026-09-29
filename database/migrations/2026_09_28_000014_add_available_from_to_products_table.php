<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // A date in the future means the batch has not landed yet: it is
            // listed as "coming soon" and cannot be bought.
            $table->date('available_from')->nullable()->after('expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('available_from');
        });
    }
};
