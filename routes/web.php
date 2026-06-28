<?php

use App\Http\Controllers\ReportAttachmentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin/login');

Route::middleware(['auth'])
    ->get('/reports/{report}/attachment', ReportAttachmentController::class)
    ->name('reports.attachment');
