@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div><h3 class="mb-1">Laporan</h3><p class="text-muted mb-0">Filter dan unduh laporan operasional.</p></div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('reports.export', ['format' => 'pdf', ...$filters]) }}"><i class="bi bi-filetype-pdf me-1"></i>PDF</a>
            <a class="btn btn-outline-success" href="{{ route('reports.export', ['format' => 'xlsx', ...$filters]) }}"><i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel</a>
        </div>
    </div>

    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end mb-4">
        <div class="col-md-4"><label for="type" class="form-label">Jenis laporan</label><select id="type" name="type" class="form-select">@foreach($types as $value => $label)<option value="{{ $value }}" @selected($filters['type'] === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-3"><label for="start_date" class="form-label">Tanggal awal</label><input id="start_date" name="start_date" type="date" class="form-control" value="{{ $filters['start_date'] }}" required></div>
        <div class="col-md-3"><label for="end_date" class="form-label">Tanggal akhir</label><input id="end_date" name="end_date" type="date" class="form-control" value="{{ $filters['end_date'] }}" min="{{ $filters['start_date'] }}" required></div>
        <div class="col-md-2 d-grid"><button class="btn btn-primary" type="submit">Tampilkan</button></div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-2"><h5 class="mb-0">{{ $reportTitle }}</h5><span class="text-muted">{{ count($rows) }} baris</span></div>
    <div class="table-responsive bg-white border rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr>@foreach($headers as $header)<th>{{ $header }}</th>@endforeach</tr></thead>
            <tbody>
                @forelse($rows as $row)<tr>@foreach($row as $value)<td>{{ $value ?? '-' }}</td>@endforeach</tr>
                @empty<tr><td colspan="{{ count($headers) }}" class="text-center text-muted py-4">Tidak ada data pada periode ini.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
