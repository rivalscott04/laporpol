<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SettingKey;
use App\Repositories\SettingRepository;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    public function __construct(
        private readonly SettingRepository $settingRepository,
    ) {}

    public function photoMaxKb(): int
    {
        return $this->getInt(SettingKey::PhotoMaxKb, (int) config('reports.photo_max_kb', 1024));
    }

    public function attachmentMaxKb(): int
    {
        return $this->getInt(SettingKey::AttachmentMaxKb, (int) config('reports.attachment_max_kb', 1024));
    }

    /**
     * @param  array<string, int>  $settings
     */
    public function updateReportUploadSettings(array $settings): void
    {
        $this->setInt(SettingKey::PhotoMaxKb, $settings['photo_max_kb']);
        $this->setInt(SettingKey::AttachmentMaxKb, $settings['attachment_max_kb']);
    }

    public function formatMaxSize(int $kilobytes): string
    {
        if ($kilobytes >= 1024 && $kilobytes % 1024 === 0) {
            return ($kilobytes / 1024).' MB';
        }

        return $kilobytes.' KB';
    }

    private function getInt(SettingKey $key, int $default): int
    {
        $value = Cache::rememberForever(
            $this->cacheKey($key),
            fn (): ?string => $this->settingRepository->get($key->value),
        );

        if ($value === null || ! is_numeric($value)) {
            return $default;
        }

        return max(1, (int) $value);
    }

    private function setInt(SettingKey $key, int $value): void
    {
        $this->settingRepository->set($key->value, (string) max(1, $value));
        Cache::forget($this->cacheKey($key));
    }

    private function cacheKey(SettingKey $key): string
    {
        return 'app_setting.'.$key->value;
    }
}
