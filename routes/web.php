<?php

use App\Http\Controllers\PdfViewerController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/login');

Route::middleware(['auth'])
    ->get('/pdf-viewer', PdfViewerController::class)
    ->name('pdf.viewer');
