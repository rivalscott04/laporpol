<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 14px; margin-bottom: 2px; text-align: center; }
        h2 { font-size: 13px; margin-bottom: 4px; font-weight: normal; color: #333; text-align: center; }
        .meta { color: #555; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; }
        .empty { text-align: center; color: #666; padding: 24px; }
    </style>
</head>
<body>
    <h1>{{ $organizationName }}</h1>
    <h2>{{ $title }}</h2>
    <div class="meta">Dicetak: {{ $generatedAt }} · Total: {{ $reports->count() }} laporan</div>

    @if ($reports->isEmpty())
        <p class="empty">Tidak ada data laporan.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Petugas</th>
                    <th>NRP/NIP</th>
                    <th>Garis Lintang</th>
                    <th>Garis Bujur</th>
                    <th>Catatan</th>
                    <th>Lampiran</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $report)
                    <tr>
                        <td>{{ $report->reported_at?->format('d/m/Y') }}</td>
                        <td>{{ $report->location_name }}</td>
                        <td>{{ $report->user?->name }}</td>
                        <td>{{ $report->user?->username }}</td>
                        <td>{{ $report->latitude }}</td>
                        <td>{{ $report->longitude }}</td>
                        <td>{{ $report->notes }}</td>
                        <td>{{ $report->attachment_path ? 'Ada' : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
