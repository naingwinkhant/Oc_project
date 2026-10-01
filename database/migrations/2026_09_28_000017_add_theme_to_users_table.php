<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // light | dark | system — remembered per person, and mirrored into
        // localStorage so guests and signed-in shoppers get the same behaviour.
        Schema::table('users', function (Blueprint $table) {
            $table->string('theme', 10)->default('system')->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('theme');
        });
    }
};
