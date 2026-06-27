<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Report\CreateReportData;
use App\DTOs\Report\UpdateReportData;
use App\Http\Requests\Report\StoreReportRequest;
use App\Http\Requests\Report\UpdateReportRequest;
use App\Models\Report;
use App\Repositories\ReportRepository;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function __construct(
        private readonly ReportRepository $reportRepository,
        private readonly ReportPhotoService $reportPhotoService,
        private readonly ReportAttachmentService $reportAttachmentService,
    ) {}

    public function create(CreateReportData $data): Report
    {
        StoreReportRequest::validatePayload([
            'reported_at' => $data->reportedAt->toDateString(),
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'location_name' => $data->locationName,
            'notes' => $data->notes,
            'photo' => $data->photoPath,
            'attachment' => $data->attachmentPath,
        ]);

        return DB::transaction(function () use ($data): Report {
            return $this->reportRepository->create([
                'user_id' => $data->userId,
                'reported_at' => $data->reportedAt->toDateString(),
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
                'location_name' => $data->locationName,
                'photo_path' => $this->reportPhotoService->store($data->photoPath),
                'attachment_path' => $this->reportAttachmentService->store($data->attachmentPath),
                'notes' => $data->notes,
            ]);
        });
    }

    public function update(Report $report, UpdateReportData $data): Report
    {
        UpdateReportRequest::validatePayload([
            'reported_at' => $data->reportedAt->toDateString(),
            'latitude' => $data->latitude,
            'longitude' => $data->longitude,
            'location_name' => $data->locationName,
            'notes' => $data->notes,
            'photo' => $data->photoPath,
            'attachment' => $data->attachmentPath,
        ]);

        return DB::transaction(function () use ($report, $data): Report {
            $attributes = [
                'reported_at' => $data->reportedAt->toDateString(),
                'latitude' => $data->latitude,
                'longitude' => $data->longitude,
                'location_name' => $data->locationName,
                'notes' => $data->notes,
            ];

            if ($data->photoPath !== null && $data->photoPath !== $report->photo_path) {
                $attributes['photo_path'] = $this->replacePhoto($report, $data->photoPath);
            }

            if ($data->attachmentPath !== $report->attachment_path) {
                $attributes['attachment_path'] = $this->replaceAttachment($report, $data->attachmentPath);
            }

            return $this->reportRepository->update($report, $attributes);
        });
    }

    public function delete(Report $report): void
    {
        DB::transaction(function () use ($report): void {
            $this->reportRepository->delete($report);
        });
    }

    public function restore(Report $report): Report
    {
        return DB::transaction(function () use ($report): Report {
            return $this->reportRepository->restore($report);
        });
    }

    public function forceDelete(Report $report): void
    {
        DB::transaction(function () use ($report): void {
            $this->reportPhotoService->delete($report->photo_path);
            $this->reportAttachmentService->delete($report->attachment_path);
            $this->reportRepository->forceDelete($report);
        });
    }

    private function replacePhoto(Report $report, string $photoPath): string
    {
        $storedPath = $this->reportPhotoService->store($photoPath);

        if ($storedPath !== $report->photo_path) {
            $this->reportPhotoService->delete($report->photo_path);
        }

        return $storedPath;
    }

    private function replaceAttachment(Report $report, ?string $attachmentPath): ?string
    {
        $storedPath = $this->reportAttachmentService->store($attachmentPath);

        if ($storedPath !== $report->attachment_path) {
            $this->reportAttachmentService->delete($report->attachment_path);
        }

        return $storedPath;
    }
}
