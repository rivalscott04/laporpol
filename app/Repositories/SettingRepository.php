<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\AppSetting;

class SettingRepository
{
    public function get(string $key): ?string
    {
        $setting = AppSetting::query()->find($key);

        return $setting?->value;
    }

    public function set(string $key, string $value): void
    {
        AppSetting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }
}
