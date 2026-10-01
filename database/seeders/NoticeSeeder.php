<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Notice;
use App\Models\User;
use Illuminate\Database\Seeder;

class NoticeSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('role', Role::Admin)->value('id');
        $manager = User::query()->where('role', Role::Manager)->value('id');

        Notice::query()->delete();

        $notices = [
            [
                'title' => 'Free delivery over 150,000 Ks',
                'body' => 'Fill a big basket and we will carry it for nothing, anywhere we deliver.',
                'tone' => 'brand',
                'user_id' => $manager,
                'is_active' => true,
                'show_on_shop' => true,
            ],
            [
                'title' => 'Same-day delivery inside Yangon',
                'body' => 'Order before 2pm and your goods reach the inner township the same day.',
                'tone' => 'info',
                'user_id' => $admin,
                'is_active' => true,
                'show_on_shop' => true,
            ],
            [
                'title' => 'Expired batches are withdrawn, not discounted',
                'body' => 'We take anything past its expiry date off the shelf the next morning, so what you see is what you can buy.',
                'tone' => 'warning',
                'user_id' => $manager,
                'is_active' => true,
                'show_on_shop' => false,
            ],
            [
                'title' => 'Ramadan opening hours',
                'body' => 'We open an hour later each day through the month of fasting.',
                'tone' => 'info',
                'user_id' => $admin,
                // Dated in the past, so it stays out of the way until somebody
                // publishes it again for next year.
                'is_active' => true,
                'show_on_shop' => false,
                'starts_on' => now()->subMonth()->toDateString(),
                'ends_on' => now()->subDays(2)->toDateString(),
            ],
        ];

        foreach ($notices as $notice) {
            Notice::create($notice);
        }
    }
}
