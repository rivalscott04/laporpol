<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportPdfExportService
{
    /**
     * @param  Builder<Report>  $query
     */
    public function download(Builder $query, string $title, string $filename = 'laporan.pdf'): StreamedResponse
    {
        /** @var Collection<int, Report> $reports */
        $reports = $query
            ->with('user')
            ->orderByDesc('reported_at')
            ->get();

        $pdf = Pdf::loadView('exports.reports-pdf', [
            'organizationName' => config('branding.full_name'),
            'title' => $title,
            'reports' => $reports,
            'generatedAt' => now()->timezone(config('app.timezone'))->format('d M Y H:i'),
        ]);

        return response()->streamDownload(
            function () use ($pdf): void {
                echo $pdf->output();
            },
            $filename,
            ['Content-Type' => 'application/pdf'],
        );
    }
}
