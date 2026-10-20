<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan {{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #202833; font-size: 10px; }
        h1 { font-size: 18px; margin: 0 0 5px; }
        p { color: #647080; margin: 0 0 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #cbd2da; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #edf2f7; }
        tr:nth-child(even) td { background: #f8fafc; }
    </style>
</head>
<body>
    <h1>Laporan {{ $title }}</h1>
    <p>Periode {{ $filters['start_date'] }} sampai {{ $filters['end_date'] }}</p>
    <table>
        <thead><tr>@foreach($headers as $header)<th>{{ $header }}</th>@endforeach</tr></thead>
        <tbody>
            @forelse($rows as $row)<tr>@foreach($row as $value)<td>{{ $value ?? '-' }}</td>@endforeach</tr>@empty<tr><td colspan="{{ count($headers) }}">Tidak ada data pada periode ini.</td></tr>@endforelse
        </tbody>
    </table>
</body>
</html>
