<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReportAttachmentService
{
    public function __construct(
        private readonly SettingService $settingService,
    ) {}

    /** @var list<string> */
    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
    ];

    public function store(?string $uploadedPath): ?string
    {
        if ($uploadedPath === null) {
            return null;
        }

        $disk = (string) config('reports.attachment_disk', 'local');
        $directory = (string) config('reports.attachment_directory', 'reports/attachments');
        $uploadDirectory = (string) config('reports.attachment_upload_directory', 'reports/attachments/uploads');

        if (
            Storage::disk($disk)->exists($uploadedPath)
            && str_starts_with($uploadedPath, $directory.'/')
            && ! str_starts_with($uploadedPath, $uploadDirectory.'/')
        ) {
            return $uploadedPath;
        }

        if (! Storage::disk($disk)->exists($uploadedPath)) {
            throw ValidationException::withMessages([
                'attachment' => 'File lampiran tidak ditemukan.',
            ]);
        }

        $this->assertWithinSizeLimit($uploadedPath, $disk);

        $absolutePath = Storage::disk($disk)->path($uploadedPath);
        $mimeType = mime_content_type($absolutePath);

        if (! is_string($mimeType) || ! in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
            Storage::disk($disk)->delete($uploadedPath);

            throw ValidationException::withMessages([
                'attachment' => 'Lampiran harus berformat PDF.',
            ]);
        }

        $finalPath = $directory.'/'.uniqid('attachment_', true).'.pdf';
        Storage::disk($disk)->move($uploadedPath, $finalPath);

        return $finalPath;
    }

    public function delete(?string $attachmentPath): void
    {
        if ($attachmentPath === null) {
            return;
        }

        $disk = (string) config('reports.attachment_disk', 'local');

        if (Storage::disk($disk)->exists($attachmentPath)) {
            Storage::disk($disk)->delete($attachmentPath);
        }
    }

    private function assertWithinSizeLimit(string $path, string $disk): void
    {
        $maxKilobytes = $this->settingService->attachmentMaxKb();
        $size = Storage::disk($disk)->size($path);

        if ($size > ($maxKilobytes * 1024)) {
            Storage::disk($disk)->delete($path);

            throw ValidationException::withMessages([
                'attachment' => sprintf(
                    'Ukuran lampiran maksimal %s.',
                    $this->settingService->formatMaxSize($maxKilobytes),
                ),
            ]);
        }
    }
}
