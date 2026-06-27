<?php

declare(strict_types=1);

namespace App\Enums;

enum SettingKey: string
{
    case PhotoMaxKb = 'photo_max_kb';
    case AttachmentMaxKb = 'attachment_max_kb';
}
