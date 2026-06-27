<?php

declare(strict_types=1);

return [
    'photo_disk' => env('REPORT_PHOTO_DISK', 'local'),
    'photo_directory' => 'reports/photos',
    'photo_upload_directory' => 'reports/photos/uploads',
    'photo_max_kb' => 1024,
    'attachment_disk' => env('REPORT_ATTACHMENT_DISK', 'local'),
    'attachment_directory' => 'reports/attachments',
    'attachment_upload_directory' => 'reports/attachments/uploads',
    'attachment_max_kb' => 1024,
];
