<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Report;
use App\Models\User;

final class ActivityLogLabels
{
    public static function event(?string $event): string
    {
        return match ($event) {
            'created' => 'Baru ditambahkan',
            'updated' => 'Diubah',
            'deleted' => 'Dihapus',
            default => $event ?? '-',
        };
    }

    public static function subjectType(?string $type): string
    {
        return match ($type) {
            User::class => 'Akun pengguna',
            Report::class => 'Laporan',
            default => '-',
        };
    }

    public static function description(string $eventName, string $subject): string
    {
        return match ($subject) {
            User::class => match ($eventName) {
                'created' => 'Akun pengguna baru ditambahkan',
                'updated' => 'Data akun pengguna diubah',
                'deleted' => 'Akun pengguna dihapus',
                default => self::event($eventName),
            },
            Report::class => match ($eventName) {
                'created' => 'Laporan baru dibuat',
                'updated' => 'Laporan diubah',
                'deleted' => 'Laporan dihapus',
                default => self::event($eventName),
            },
            default => self::event($eventName),
        };
    }
}
