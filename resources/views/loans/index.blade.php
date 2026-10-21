@extends('layouts.app')

@section('title', $isBorrower ? 'Peminjaman Aset' : 'Data Peminjaman')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>{{ $isBorrower ? 'Peminjaman Aset' : 'Data Peminjaman' }}</h2>
            <p class="text-muted mb-0">
                {{ $isBorrower ? 'Cari aset tersedia atau catat pengembalian aset yang dipinjam.' : 'Pantau dan kelola seluruh aset yang sedang dipinjam.' }}
            </p>
        </div>

        @if(! $isBorrower)
        <div>
            <a href="{{ route('loans.history') }}" class="btn btn-outline-primary">
                <i class="bi bi-clock-history me-1"></i> Riwayat Peminjaman
            </a>
        </div>
        @endif
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ $errors->first() }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- BORROWER AVAILABLE ASSETS --}}
    @if($isBorrower)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('loans.index') }}">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-9 col-lg-10">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                <input
                                    name="search"
                                    class="form-control border-start-0 ps-0"
                                    value="{{ $search }}"
                                    placeholder="Cari kode atau nama aset..."
                                >
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-2 d-flex gap-2">
                            <button class="btn btn-primary flex-grow-1" type="submit">
                                <i class="bi bi-search me-1"></i> Cari
                            </button>
                            @if($search)
                                <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary" title="Reset filter">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong class="d-inline-flex align-items-center gap-2">
                        <i class="bi bi-box-seam text-primary"></i> Aset Tersedia untuk Dipinjam
                    </strong>
                    <small class="text-muted ms-2">Total: {{ $availableAssets->total() }} aset</small>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Aset</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th width="120" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($availableAssets as $asset)
                                <tr>
                                    <td><strong>{{ $asset->kode_asset }}</strong></td>
                                    <td>{{ $asset->nama_asset }}</td>
                                    <td>{{ $asset->category?->name ?? $asset->category?->nama ?? '-' }}</td>
                                    <td>{{ $asset->location?->name ?? $asset->location?->nama ?? '-' }}</td>
                                    <td class="text-center">
                                        <a class="btn btn-sm btn-primary" href="{{ route('loans.create', $asset) }}">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Pinjam
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox"></i>
                                            <p>Tidak ada aset yang tersedia saat ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($availableAssets->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $availableAssets->firstItem() }} - {{ $availableAssets->lastItem() }} dari {{ $availableAssets->total() }} aset
                        </small>
                        <div>{{ $availableAssets->links('pagination::bootstrap-5') }}</div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- ACTIVE LOANS CARD --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong class="d-inline-flex align-items-center gap-2">
                    <i class="bi bi-person-check text-primary"></i> {{ $isBorrower ? 'Peminjaman Aktif Saya' : 'Aset Sedang Dipinjam' }}
                </strong>
                <small class="text-muted ms-2">Total: {{ $activeLoans->total() }} peminjaman</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Aset</th>
                            @if(! $isBorrower)
                                <th>Peminjam</th>
                            @endif
                            <th>Tanggal Pinjam</th>
                            <th>Rencana Kembali</th>
                            @if($isBorrower)
                                <th width="320">Pengembalian</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeLoans as $loan)
                            <tr>
                                <td>
                                    <strong>{{ $loan->asset?->kode_asset }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $loan->asset?->nama_asset }}</small>
                                </td>
                                @if(! $isBorrower)
                                    <td>
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-person text-muted"></i>
                                            {{ $loan->user?->name ?? '-' }}
                                        </span>
                                    </td>
                                @endif
                                <td>{{ $loan->tanggal_pinjam?->format('d-m-Y') }}</td>
                                <td>
                                    <span class="badge bg-warning">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        {{ $loan->tanggal_rencana_kembali?->format('d-m-Y') ?? '-' }}
                                    </span>
                                </td>
                                @if($isBorrower)
                                    <td>
                                        <form method="POST" action="{{ route('loans.return', $loan) }}" class="d-flex flex-wrap gap-2 align-items-center">
                                            @csrf
                                            @method('PUT')
                                            <input type="date" name="tanggal_kembali" class="form-control form-control-sm" style="width: 125px;" value="{{ now()->toDateString() }}" required aria-label="Tanggal kembali">
                                            <select name="kondisi_sesudah" class="form-select form-select-sm" style="width: 110px;" required aria-label="Kondisi aset">
                                                <option value="Baik">Baik</option>
                                                <option value="Rusak Ringan">Rusak Ringan</option>
                                                <option value="Rusak Berat">Rusak Berat</option>
                                            </select>
                                            <button class="btn btn-sm btn-outline-primary" type="submit">
                                                Kembalikan
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isBorrower ? 5 : 4 }}" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-check2-circle text-success"></i>
                                        <p>Tidak ada aset yang sedang dipinjam.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($activeLoans->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $activeLoans->firstItem() }} - {{ $activeLoans->lastItem() }} dari {{ $activeLoans->total() }} data
                    </small>
                    <div>{{ $activeLoans->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
