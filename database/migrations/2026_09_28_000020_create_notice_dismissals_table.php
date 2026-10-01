<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A notice is not deleted when a shopper clears it — it is cleared for
        // them, so the same notice can be published to everybody else and a
        // later re-publish can reach them again.
        Schema::create('notice_dismissals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('session_id', 40)->nullable()->index();
            $table->foreignId('notice_id')->constrained()->cascadeOnDelete();
            $table->timestamp('dismissed_at');

            $table->unique(['user_id', 'notice_id']);
            $table->unique(['session_id', 'notice_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notice_dismissals');
    }
};
