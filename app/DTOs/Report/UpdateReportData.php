<?php

declare(strict_types=1);

namespace App\DTOs\Report;

use App\Support\UploadedFilePath;
use Carbon\CarbonImmutable;

final readonly class UpdateReportData
{
    public function __construct(
        public CarbonImmutable $reportedAt,
        public string $latitude,
        public string $longitude,
        public string $locationName,
        public ?string $photoPath,
        public ?string $attachmentPath,
        public ?string $notes,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            reportedAt: CarbonImmutable::parse((string) $data['reported_at']),
            latitude: (string) $data['latitude'],
            longitude: (string) $data['longitude'],
            locationName: (string) $data['location_name'],
            photoPath: UploadedFilePath::resolve($data['photo'] ?? null),
            attachmentPath: UploadedFilePath::resolve($data['attachment'] ?? null),
            notes: isset($data['notes']) ? (string) $data['notes'] : null,
        );
    }
}
