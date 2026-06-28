<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportAttachmentController extends Controller
{
    public function __invoke(Report $report, Request $request): StreamedResponse
    {
        Gate::authorize('view', $report);

        $path = $report->attachment_path;

        abort_if(blank($path), 404);

        $disk = (string) config('reports.attachment_disk', 'local');

        abort_unless(Storage::disk($disk)->exists($path), 404);

        $filename = basename($path);
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return Storage::disk($disk)->response($path, $filename, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('%s; filename="%s"', $disposition, $filename),
        ]);
    }
}
