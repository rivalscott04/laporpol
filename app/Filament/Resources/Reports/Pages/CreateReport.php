<?php

declare(strict_types=1);

namespace App\Filament\Resources\Reports\Pages;

use App\DTOs\Report\CreateReportData;
use App\Filament\Concerns\HasBackNavigation;
use App\Filament\Resources\Reports\ReportResource;
use App\Services\ReportService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateReport extends CreateRecord
{
    use HasBackNavigation;
    protected static string $resource = ReportResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Tambah Laporan';
    }

    public function getBreadcrumb(): string
    {
        return 'Tambah Laporan';
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->makeBackAction('Kembali ke Daftar'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return app(ReportService::class)->create(
            CreateReportData::fromArray($data, (int) Auth::id()),
        );
    }
}
