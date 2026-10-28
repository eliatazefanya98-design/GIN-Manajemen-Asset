@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Detail Asset History</h2>
            <p class="text-muted mb-0">
                Informasi lengkap riwayat aktivitas dan jejak audit aset
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('asset-histories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
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

    <div class="row g-4">
        {{-- INFORMASI AKTIVITAS (Kiri) --}}
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i>
                    <strong>Informasi Aktivitas</strong>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small d-block">Jenis Aktivitas</label>
                            <div class="mt-1">
                                <span class="badge bg-info px-3 py-2">
                                    <i class="bi bi-activity me-1"></i> {{ $history->aktivitas }}
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small d-block">Waktu & Tanggal Kejadian</label>
                            <div class="fw-semibold mt-1">
                                <i class="bi bi-calendar3 me-1 text-muted"></i>
                                {{ $history->tanggal?->format('d-m-Y H:i:s') ?? '-' }}
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="text-muted small d-block mb-2">Deskripsi Aktivitas</label>
                            <div class="p-3 bg-light rounded border text-secondary" style="min-height: 100px; line-height: 1.6;">
                                {{ $history->deskripsi ?? 'Tidak ada catatan deskripsi.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ASSET & PENGGUNA (Kanan) --}}
        <div class="col-lg-4">
            {{-- INFORMASI ASSET --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam text-primary"></i>
                        <strong>Aset Terkait</strong>
                    </div>
                    @if($history->asset)
                    <a href="{{ route('assets.show', $history->asset) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 11px;">
                        Lihat Aset
                    </a>
                    @endif
                </div>

                <div class="card-body">
                    @if($history->asset)
                        <div class="mb-3">
                            <label class="text-muted small d-block">Kode Asset</label>
                            <div class="fw-bold fs-6 text-primary">
                                {{ $history->asset->kode_asset }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small d-block">Nama Asset</label>
                            <div class="fw-semibold">
                                {{ $history->asset->nama_asset }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="text-muted small d-block">Kategori</label>
                            <div>{{ $history->asset->category->nama_kategori ?? '-' }}</div>
                        </div>

                        <div>
                            <label class="text-muted small d-block">Lokasi Terakhir</label>
                            <div>{{ $history->asset->location->nama_lokasi ?? '-' }}</div>
                        </div>
                    @else
                        <div class="text-muted small">
                            <i class="bi bi-info-circle me-1"></i> Data aset tidak ditemukan atau telah dihapus.
                        </div>
                    @endif
                </div>
            </div>

            {{-- INFORMASI PENGGUNA --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-person-badge text-secondary"></i>
                    <strong>Perekam Aktivitas (User)</strong>
                </div>

                <div class="card-body">
                    @if($history->user)
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon" style="background: rgba(200,30,40,0.1); color: var(--app-blue); width: 44px; height: 44px;">
                                <i class="bi bi-person-circle fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $history->user->name }}</div>
                                <div class="small text-muted">{{ $history->user->email }}</div>
                                <span class="badge bg-light text-dark border mt-1" style="font-size: 10px;">
                                    {{ ucfirst($history->user->role ?? 'User') }}
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="text-muted small">
                            <i class="bi bi-robot me-1"></i> Tercatat otomatis oleh sistem.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection