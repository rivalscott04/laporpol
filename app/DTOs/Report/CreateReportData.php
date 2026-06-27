<?php

declare(strict_types=1);

namespace App\DTOs\Report;

use App\Support\UploadedFilePath;
use Carbon\CarbonImmutable;

final readonly class CreateReportData
{
    public function __construct(
        public int $userId,
        public CarbonImmutable $reportedAt,
        public string $latitude,
        public string $longitude,
        public string $locationName,
        public string $photoPath,
        public ?string $attachmentPath,
        public ?string $notes,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data, int $userId): self
    {
        $photoPath = UploadedFilePath::resolve($data['photo'] ?? null);

        if ($photoPath === null) {
            throw new \InvalidArgumentException('Photo is required.');
        }

        return new self(
            userId: $userId,
            reportedAt: CarbonImmutable::parse((string) $data['reported_at']),
            latitude: (string) $data['latitude'],
            longitude: (string) $data['longitude'],
            locationName: (string) $data['location_name'],
            photoPath: $photoPath,
            attachmentPath: UploadedFilePath::resolve($data['attachment'] ?? null),
            notes: isset($data['notes']) ? (string) $data['notes'] : null,
        );
    }
}
