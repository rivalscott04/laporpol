<?php

declare(strict_types=1);

namespace App\Support;

use Symfony\Component\HttpFoundation\StreamedResponse;

final class UserImportTemplate
{
  /**
   * @return list<list<string>>
   */
    public static function rows(): array
    {
        return [
            ['name', 'username', 'email', 'password', 'role'],
            ['Bripka Ahmad Wijaya', '77010123', 'ahmad.wijaya@laporanpol.test', 'Password123!', 'user'],
            ['Ipda Budi Santoso', '77010124', 'budi.santoso@laporanpol.test', 'Password123!', 'admin'],
        ];
    }

    public static function contents(): string
    {
        $handle = fopen('php://temp', 'r+');

        if ($handle === false) {
            return '';
        }

        fwrite($handle, "\xEF\xBB\xBF");

        foreach (self::rows() as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        fclose($handle);

        return is_string($contents) ? $contents : '';
    }

    public static function download(): StreamedResponse
    {
        return response()->streamDownload(
            function (): void {
                echo self::contents();
            },
            'template-impor-akun.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8'],
        );
    }
}
