<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * An account is created with an email, a username and a role. The holder sets
 * their own password afterwards, so until they do there is nothing to store.
 *
 * The column is made nullable rather than given an empty string: the User model
 * casts `password` to `hashed`, so an empty string would be hashed into a real,
 * valid password and anyone could then sign in with a blank one. A null column
 * means "no password yet" and can never match a sign-in.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Cannot be reversed without inventing passwords, so any account still
        // without one is given an unusable value rather than a guessable one.
        Schema::table('users', function (Blueprint $table) {
            DB::table('users')
                ->whereNull('password')
                ->update(['password' => '!']);

            $table->string('password')->nullable(false)->change();
        });
    }
};
