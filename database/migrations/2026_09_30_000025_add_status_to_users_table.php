<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Where an account stands with the shop: waiting, accepted, or turned down.
 *
 * A newly registered person is pending and therefore cannot do anything. An
 * administrator or manager accepts or rejects them, and only an accepted
 * account may sign in.
 *
 * Every account that already existed is marked accepted here: they were created
 * before this rule existed, so making them wait would lock out the whole team.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status', 20)->default('approved')->after('role')->index();
            // Who made the call on a pending account, either way.
            $table->foreignId('decided_by')->nullable()->after('approved_by')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable()->after('decided_by');
        });

        DB::table('users')->update(['status' => 'approved']);

        // Keep the acceptance timestamp in step with the new column.
        DB::table('users')->whereNull('approved_at')->update(['approved_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('decided_by');
            $table->dropColumn(['status', 'decided_at']);
        });
    }
};
