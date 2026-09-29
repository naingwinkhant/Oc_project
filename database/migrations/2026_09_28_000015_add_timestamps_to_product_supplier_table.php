<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The relation declares withTimestamps(), so the pivot has to carry them
        // or every read of a supplier's goods fails on a missing column.
        Schema::table('product_supplier', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->after('supplier_sku');
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });

        // Existing links predate the columns, so backfill rather than leave holes.
        DB::table('product_supplier')
            ->whereNull('created_at')
            ->update(['created_at' => now(), 'updated_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('product_supplier', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }
};
