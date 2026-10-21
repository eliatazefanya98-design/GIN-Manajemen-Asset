@extends('layouts.app')

@section('title', 'Asset History')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Asset History</h2>
            <p class="text-muted mb-0">Jejak audit dan riwayat aktivitas perubahan seluruh aset.</p>
        </div>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- SEARCH --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('asset-histories.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-9 col-lg-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input
                                type="text"
                                name="search"
                                class="form-control border-start-0 ps-0"
                                placeholder="Cari kode asset, nama asset, aktivitas, atau deskripsi..."
                                value="{{ request('search') }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-3 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('asset-histories.index') }}" class="btn btn-outline-secondary" title="Reset filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong class="d-inline-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i> Data Riwayat Aktivitas
                </strong>
                <small class="text-muted ms-2">Total: {{ $histories->total() }} riwayat</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">#</th>
                            <th>Asset</th>
                            <th>Aktivitas</th>
                            <th>Deskripsi</th>
                            <th>Tanggal</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                            <tr>
                                <td class="text-center text-muted">
                                    {{ $histories->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    @if($history->asset)
                                        <strong>{{ $history->asset->kode_asset }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $history->asset->nama_asset }}</small>
                                    @else
                                        <span class="text-muted">Asset tidak ditemukan</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <i class="bi bi-activity me-1"></i> {{ $history->aktivitas }}
                                    </span>
                                </td>
                                <td>
                                    {{ $history->deskripsi ?? '-' }}
                                </td>
                                <td>
                                    {{ $history->tanggal?->format('d-m-Y H:i') ?? '-' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('asset-histories.show', $history) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Detail Riwayat">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>
                                            @if(request('search'))
                                                Tidak ditemukan riwayat aset dengan kata kunci "<strong>{{ request('search') }}</strong>".
                                            @else
                                                Belum ada riwayat aktivitas aset.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        @if($histories->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $histories->firstItem() }} - {{ $histories->lastItem() }} dari {{ $histories->total() }} data
                    </small>
                    <div>
                        {{ $histories->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection