<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class ReportPdfExportService
{
    /**
     * @param  Builder<Report>  $query
     */
    public function download(Builder $query, string $title, string $filename = 'laporan.pdf'): Response
    {
        /** @var Collection<int, Report> $reports */
        $reports = $query
            ->with('user')
            ->orderByDesc('reported_at')
            ->get();

        return Pdf::loadView('exports.reports-pdf', [
            'organizationName' => config('branding.full_name'),
            'title' => $title,
            'reports' => $reports,
            'generatedAt' => now()->timezone(config('app.timezone'))->format('d M Y H:i'),
        ])->download($filename);
    }
}
