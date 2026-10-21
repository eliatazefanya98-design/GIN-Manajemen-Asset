@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Laporan Operasional</h2>
            <p class="text-muted mb-0">Filter, pratinjau, dan unduh data laporan inventaris & operasional aset.</p>
        </div>

        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('reports.export', ['format' => 'pdf', ...$filters]) }}">
                <i class="bi bi-file-earmark-pdf text-danger me-1"></i> Export PDF
            </a>
            <a class="btn btn-outline-secondary" href="{{ route('reports.export', ['format' => 'xlsx', ...$filters]) }}">
                <i class="bi bi-file-earmark-spreadsheet text-success me-1"></i> Export Excel
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ $errors->first() }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- FILTER CARD --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <strong class="d-inline-flex align-items-center gap-2">
                <i class="bi bi-funnel text-primary"></i> Parameter Filter Laporan
            </strong>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="type" class="form-label">Jenis Laporan</label>
                        <select id="type" name="type" class="form-select">
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}" @selected($filters['type'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Tanggal Awal</label>
                        <input id="start_date" name="start_date" type="date" class="form-control" value="{{ $filters['start_date'] }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input id="end_date" name="end_date" type="date" class="form-control" value="{{ $filters['end_date'] }}" min="{{ $filters['start_date'] }}" required>
                    </div>

                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="submit">
                            <i class="bi bi-search me-1"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- RESULT CARD --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong class="d-inline-flex align-items-center gap-2">
                    <i class="bi bi-table text-primary"></i> {{ $reportTitle }}
                </strong>
                <small class="text-muted ms-2">Total: {{ count($rows) }} baris data</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            @foreach($headers as $header)
                                <th>{{ $header }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr>
                                @foreach($row as $value)
                                    <td>{{ $value ?? '-' }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ count($headers) }}" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>Tidak ada data laporan yang ditemukan pada periode ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection
