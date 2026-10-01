<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * A new team account waits to be accepted.
 *
 * Creating somebody's username does not hand them the shop: an administrator or
 * manager accepts the account, and only then can it sign in. approved_at is null
 * while it waits, and records who accepted it and when once it does.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('is_active');
            $table->foreignId('approved_by')->nullable()->after('approved_at')
                ->constrained('users')->nullOnDelete();
        });

        // Accounts that already existed were created under the old rule, so
        // they are accepted here. Without this every current member of staff
        // would be locked out the moment this migration ran.
        DB::table('users')->whereNull('approved_at')->update([
            'approved_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn('approved_at');
        });
    }
};
