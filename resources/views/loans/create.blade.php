@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')
<div class="container-fluid" style="max-width: 900px">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Ajukan Peminjaman</h2>
            <p class="text-muted mb-0">Isi formulir pengajuan peminjaman untuk aset terpilih.</p>
        </div>

        <div>
            <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ASSET SUMMARY CARD --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon blue" style="width: 48px; height: 48px; min-width: 48px; font-size: 20px;">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-dark">{{ $asset->nama_asset }}</h5>
                    <div class="text-muted small">
                        Kode: <strong>{{ $asset->kode_asset }}</strong> · Kategori: {{ $asset->category?->name ?? $asset->category?->nama ?? '-' }} · Lokasi: {{ $asset->location?->name ?? $asset->location?->nama ?? '-' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ $errors->first() }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- FORM CARD --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <strong class="d-inline-flex align-items-center gap-2">
                <i class="bi bi-calendar-plus text-primary"></i> Detail Rencana Peminjaman
            </strong>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('loans.store') }}">
                @csrf
                <input type="hidden" name="asset_id" value="{{ $asset->id }}">

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="tanggal_pinjam" class="form-label">Tanggal Mulai Pinjam <span class="text-danger">*</span></label>
                        <input id="tanggal_pinjam" name="tanggal_pinjam" type="date" class="form-control" value="{{ old('tanggal_pinjam', now()->toDateString()) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="tanggal_rencana_kembali" class="form-label">Rencana Tanggal Kembali <span class="text-danger">*</span></label>
                        <input id="tanggal_rencana_kembali" name="tanggal_rencana_kembali" type="date" class="form-control" min="{{ old('tanggal_pinjam', now()->toDateString()) }}" value="{{ old('tanggal_rencana_kembali') }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="keperluan" class="form-label">Keperluan / Alasan Peminjaman <span class="text-danger">*</span></label>
                    <textarea id="keperluan" name="keperluan" class="form-control" rows="3" maxlength="255" placeholder="Contoh: Digunakan untuk presentasi rapat divisi..." required>{{ old('keperluan') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check2-circle me-1"></i> Ajukan Peminjaman
                    </button>
                    <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection