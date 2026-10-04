<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'role')) {
            $table->string('role')->default('staff')->after('password');
        }

        if (!Schema::hasColumn('users', 'phone')) {
            $table->string('phone', 30)->nullable()->after('role');
        }

        if (!Schema::hasColumn('users', 'avatar')) {
            $table->string('avatar')->nullable()->after('phone');
        }

        if (!Schema::hasColumn('users', 'is_active')) {
            $table->boolean('is_active')->default(true)->after('avatar');
        }

        if (!Schema::hasColumn('users', 'last_login_at')) {
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        }
    });
}
};
