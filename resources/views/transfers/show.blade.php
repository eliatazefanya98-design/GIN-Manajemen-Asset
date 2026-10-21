@extends('layouts.app')

@section('title', 'Detail Transfer')

@section('content')
<div class="container-fluid" style="max-width: 1000px">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Detail Transfer Aset</h2>
            <p class="text-muted mb-0">Informasi pengajuan pemindahan aset <strong>{{ $transfer->asset?->kode_asset }} · {{ $transfer->asset?->nama_asset }}</strong></p>
        </div>

        <div>
            <a class="btn btn-outline-secondary" href="{{ route('transfers.index') }}">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- DETAIL CARD --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong class="d-inline-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-primary"></i> Data Rincian Transfer
            </strong>

            <div>
                @if($transfer->status === 'menunggu')
                    <span class="badge bg-warning">
                        <i class="bi bi-clock-history me-1"></i> Menunggu Persetujuan
                    </span>
                @elseif($transfer->status === 'disetujui')
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle-fill me-1"></i> Disetujui
                    </span>
                @elseif($transfer->status === 'ditolak')
                    <span class="badge bg-danger">
                        <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                    </span>
                @else
                    <span class="badge bg-secondary">{{ ucfirst($transfer->status) }}</span>
                @endif
            </div>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="text-muted small d-block">Aset</label>
                    <div class="fw-bold fs-6 text-dark">{{ $transfer->asset?->nama_asset ?? '-' }}</div>
                    <small class="text-muted">{{ $transfer->asset?->kode_asset }}</small>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small d-block">Pengaju Transfer</label>
                    <div class="fw-bold fs-6 text-dark">{{ $transfer->requester?->name ?? '-' }}</div>
                    <small class="text-muted">Diajukan pada {{ $transfer->created_at?->format('d F Y H:i') }}</small>
                </div>

                <div class="col-12"><hr class="my-2 border-light"></div>

                <div class="col-md-6">
                    <label class="text-muted small d-block">Lokasi Asal</label>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <div class="stat-icon orange" style="width: 36px; height: 36px; min-width: 36px; font-size: 16px;">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div>
                            <div class="fw-bold">{{ $transfer->originLocation?->name ?? $transfer->originLocation?->nama ?? '-' }}</div>
                            <small class="text-muted">{{ $transfer->originLocation?->gedung ?? '' }} {{ $transfer->originLocation?->ruangan ? '· '.$transfer->originLocation->ruangan : '' }}</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small d-block">Lokasi Tujuan</label>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        <div class="stat-icon blue" style="width: 36px; height: 36px; min-width: 36px; font-size: 16px;">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-primary">{{ $transfer->targetLocation?->name ?? $transfer->targetLocation?->nama ?? '-' }}</div>
                            <small class="text-muted">{{ $transfer->targetLocation?->gedung ?? '' }} {{ $transfer->targetLocation?->ruangan ? '· '.$transfer->targetLocation->ruangan : '' }}</small>
                        </div>
                    </div>
                </div>

                <div class="col-12"><hr class="my-2 border-light"></div>

                <div class="col-12">
                    <label class="text-muted small d-block">Alasan Transfer</label>
                    <div class="p-3 bg-light rounded text-dark mt-1">
                        {{ $transfer->reason }}
                    </div>
                </div>

                @if($transfer->decision_note)
                    <div class="col-12">
                        <label class="text-muted small d-block">Catatan Keputusan Pimpinan</label>
                        <div class="p-3 bg-light rounded text-dark mt-1 border-start border-3 {{ $transfer->status === 'disetujui' ? 'border-success' : 'border-danger' }}">
                            {{ $transfer->decision_note }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection