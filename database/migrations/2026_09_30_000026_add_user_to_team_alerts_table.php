<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lets a team alert point at the account it is about.
 *
 * An order alert points at its order. A registration alert needs the same for
 * the account, so the alert can be cleared for everybody the moment somebody
 * accepts or turns the account down — otherwise the bell would keep nagging
 * about a decision that has already been made.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_alerts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('order_id')
                ->constrained('users')->cascadeOnDelete();
            $table->index(['kind', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('team_alerts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });
    }
};
