<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SettingKey;
use App\Repositories\SettingRepository;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $repository = app(SettingRepository::class);

        $defaults = [
            SettingKey::PhotoMaxKb->value => (string) config('reports.photo_max_kb', 1024),
            SettingKey::AttachmentMaxKb->value => (string) config('reports.attachment_max_kb', 1024),
        ];

        foreach ($defaults as $key => $value) {
            if ($repository->get($key) === null) {
                $repository->set($key, $value);
            }
        }
    }
}
