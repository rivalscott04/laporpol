<?php

declare(strict_types=1);

namespace App\Support;

final class UploadedFilePath
{
    public static function resolve(mixed $value): ?string
    {
        if (is_array($value)) {
            $value = $value[array_key_first($value)] ?? null;
        }

        return filled($value) ? (string) $value : null;
    }
}
