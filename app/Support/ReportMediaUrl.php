<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Report;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ReportMediaUrl
{
    public static function photo(Report $report): ?string
    {
        return self::url(
            $report->photo_path,
            (string) config('reports.photo_disk', 'local'),
        );
    }

    public static function attachment(Report $report): ?string
    {
        return self::url(
            $report->attachment_path,
            (string) config('reports.attachment_disk', 'local'),
        );
    }

    public static function attachmentPreview(Report $report): ?string
    {
        $url = self::attachment($report);

        if ($url === null) {
            return null;
        }

        return route('pdf.viewer', ['file' => $url]);
    }

    public static function url(?string $path, string $disk): ?string
    {
        if ($path === null) {
            return null;
        }

        /** @var Filesystem $storage */
        $storage = Storage::disk($disk);

        try {
            return $storage->temporaryUrl($path, now()->addMinutes(30));
        } catch (Throwable) {
            return $storage->url($path);
        }
    }
}
