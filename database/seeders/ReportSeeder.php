<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('username', '96000003')->first();
        $admin = User::query()->where('username', '96000002')->first();

        if ($user === null || $admin === null) {
            return;
        }

        $today = CarbonImmutable::today();

        $reports = [
            [
                'user_id' => $user->id,
                'reported_at' => $today->toDateString(),
                'latitude' => '-6.2000000',
                'longitude' => '106.8166667',
                'location_name' => 'Monas, Jakarta Pusat',
                'notes' => 'Patroli pagi area Monas.',
            ],
            [
                'user_id' => $user->id,
                'reported_at' => $today->toDateString(),
                'latitude' => '-6.1753924',
                'longitude' => '106.8271528',
                'location_name' => 'Bundaran HI, Jakarta',
                'notes' => 'Pengaturan lalu lintas pagi.',
            ],
            [
                'user_id' => $user->id,
                'reported_at' => $today->subDay()->toDateString(),
                'latitude' => '-6.2614930',
                'longitude' => '106.8106000',
                'location_name' => 'Blok M, Jakarta Selatan',
                'notes' => 'Patroli malam kawasan Blok M.',
            ],
            [
                'user_id' => $admin->id,
                'reported_at' => $today->subDays(3)->toDateString(),
                'latitude' => '-6.1275280',
                'longitude' => '106.6537000',
                'location_name' => 'Bandara Soekarno-Hatta',
                'notes' => 'Koordinasi pengamanan bandara.',
            ],
            [
                'user_id' => $admin->id,
                'reported_at' => $today->subWeek()->toDateString(),
                'latitude' => '-6.4024840',
                'longitude' => '106.7942400',
                'location_name' => 'Cibinong, Bogor',
                'notes' => 'Operasi rutin mingguan.',
            ],
            [
                'user_id' => $user->id,
                'reported_at' => $today->subMonth()->toDateString(),
                'latitude' => '-6.9147440',
                'longitude' => '107.6098100',
                'location_name' => 'Bandung Kota',
                'notes' => 'Dukungan operasi luar kota.',
            ],
        ];

        foreach ($reports as $attributes) {
            Report::query()->updateOrCreate(
                [
                    'user_id' => $attributes['user_id'],
                    'reported_at' => $attributes['reported_at'],
                    'location_name' => $attributes['location_name'],
                ],
                $attributes,
            );
        }
    }
}
