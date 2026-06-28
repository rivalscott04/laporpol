<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PdfViewerController extends Controller
{
    public function __invoke(Request $request): View
    {
        $file = $request->query('file');

        if (! is_string($file) || ! $this->isAllowedFileUrl($file)) {
            abort(400, 'URL PDF tidak valid.');
        }

        return view('pdf.viewer', [
            'file' => $file,
        ]);
    }

    private function isAllowedFileUrl(string $url): bool
    {
        $parsed = parse_url($url);

        if (! isset($parsed['scheme'], $parsed['host']) || ! in_array($parsed['scheme'], ['http', 'https'], true)) {
            return false;
        }

        $allowedHosts = collect([
            parse_url((string) config('app.url'), PHP_URL_HOST),
            parse_url((string) config('filesystems.disks.public.url'), PHP_URL_HOST),
            parse_url((string) config('filesystems.disks.s3.url'), PHP_URL_HOST),
            parse_url((string) config('filesystems.disks.s3.endpoint'), PHP_URL_HOST),
            parse_url(request()->getSchemeAndHttpHost(), PHP_URL_HOST),
        ])->filter()->unique()->values()->all();

        return in_array($parsed['host'], $allowedHosts, true);
    }
}
