<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cleared per person, so one member of staff dismissing an order alert does not
 * hide it from the rest of the team.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_alert_dismissals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_alert_id')->constrained()->cascadeOnDelete();
            $table->timestamp('dismissed_at');

            $table->unique(['user_id', 'team_alert_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_alert_dismissals');
    }
};
