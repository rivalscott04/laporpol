<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\SettingService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ReportPhotoService
{
    public function __construct(
        private readonly SettingService $settingService,
    ) {}

    /** @var list<string> */
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    public function store(string $uploadedPath): string
    {
        $disk = (string) config('reports.photo_disk', 'local');
        $directory = (string) config('reports.photo_directory', 'reports/photos');
        $uploadDirectory = (string) config('reports.photo_upload_directory', 'reports/photos/uploads');

        if (
            Storage::disk($disk)->exists($uploadedPath)
            && str_starts_with($uploadedPath, $directory.'/')
            && ! str_starts_with($uploadedPath, $uploadDirectory.'/')
        ) {
            return $uploadedPath;
        }

        if (! Storage::disk($disk)->exists($uploadedPath)) {
            throw ValidationException::withMessages([
                'photo' => 'File foto tidak ditemukan.',
            ]);
        }

        $this->assertWithinSizeLimit($uploadedPath, $disk, 'photo');

        $absolutePath = Storage::disk($disk)->path($uploadedPath);
        $mimeType = mime_content_type($absolutePath);

        if (! is_string($mimeType) || ! in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            Storage::disk($disk)->delete($uploadedPath);

            throw ValidationException::withMessages([
                'photo' => 'Foto harus berformat JPG/JPEG, PNG, atau WEBP.',
            ]);
        }

        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => throw new RuntimeException("Unsupported mime type [{$mimeType}]."),
        };

        $finalPath = $directory.'/'.uniqid('photo_', true).'.'.$extension;
        Storage::disk($disk)->move($uploadedPath, $finalPath);

        return $finalPath;
    }

    public function delete(?string $photoPath): void
    {
        if ($photoPath === null) {
            return;
        }

        $disk = (string) config('reports.photo_disk', 'local');

        if (Storage::disk($disk)->exists($photoPath)) {
            Storage::disk($disk)->delete($photoPath);
        }
    }

    private function assertWithinSizeLimit(string $path, string $disk, string $field): void
    {
        $maxKilobytes = $this->settingService->photoMaxKb();
        $size = Storage::disk($disk)->size($path);

        if ($size > ($maxKilobytes * 1024)) {
            Storage::disk($disk)->delete($path);

            throw ValidationException::withMessages([
                $field => sprintf(
                    'Ukuran foto maksimal %s.',
                    $this->settingService->formatMaxSize($maxKilobytes),
                ),
            ]);
        }
    }
}
