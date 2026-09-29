<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Added nullable first: on a populated table every existing row would
        // otherwise get '' and trip the unique index.
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 40)->nullable()->after('id');
        });

        $this->backfill();

        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE users MODIFY username VARCHAR(40) NOT NULL');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username', 40)->nullable(false)->change();
            });
        }
    }

    private function backfill(): void
    {
        $rows = DB::table('users')->select('id', 'email', 'name')->get();
        $used = [];

        foreach (DB::table('users')->pluck('username') as $existing) {
            if ($existing) {
                $used[Str::lower($existing)] = true;
            }
        }

        foreach ($rows as $row) {
            if ($row->username ?? null) {
                continue;
            }

            $base = Str::slug(Str::before((string) $row->email, '@'));
            $base = $base !== '' ? $base : 'user'.$row->id;

            $candidate = $base;
            $counter = 1;

            while (isset($used[Str::lower($candidate)])) {
                $candidate = $base.(++$counter);
            }

            $used[Str::lower($candidate)] = true;

            DB::table('users')->where('id', $row->id)->update(['username' => $candidate]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
