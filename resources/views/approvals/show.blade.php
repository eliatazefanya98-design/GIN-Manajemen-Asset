@extends('layouts.app')

@section('title', 'Tinjau Pengajuan')

@section('content')
<div class="container-fluid" style="max-width: 1000px">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Tinjau Pengajuan Transfer</h2>
            <p class="text-muted mb-0">Verifikasi perpindahan aset <strong>{{ $transfer->asset?->kode_asset }} · {{ $transfer->asset?->nama_asset }}</strong></p>
        </div>

        <div>
            <a class="btn btn-outline-secondary" href="{{ route('approvals.index') }}">
                <i class="bi bi-arrow-left me-1"></i> Kembali
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

    {{-- DETAIL CARD --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong class="d-inline-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-check text-primary"></i> Rincian Permohonan Transfer
            </strong>

            <div>
                @if($transfer->status === 'menunggu')
                    <span class="badge bg-warning">
                        <i class="bi bi-clock-history me-1"></i> Menunggu Keputusan
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
                    <label class="text-muted small d-block">Pengaju Permohonan</label>
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
                    <label class="text-muted small d-block">Lokasi Tujuan yang Dimohon</label>
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
            </div>
        </div>
    </div>

    {{-- DECISION PANEL --}}
    @if($transfer->status === 'menunggu')
        <div class="card shadow-sm border-primary">
            <div class="card-header bg-white">
                <strong class="d-inline-flex align-items-center gap-2 text-primary">
                    <i class="bi bi-shield-check"></i> Keputusan Pimpinan
                </strong>
            </div>

            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-lg-3">
                        <form method="POST" action="{{ route('approvals.approve', $transfer) }}">
                            @csrf
                            @method('PUT')
                            <button class="btn btn-success w-100" type="submit" onclick="return confirm('Setujui pengajuan pemindahan aset ini?')">
                                <i class="bi bi-check2-circle me-1"></i> Setujui Transfer
                            </button>
                        </form>
                    </div>

                    <div class="col-lg-9">
                        <form method="POST" action="{{ route('approvals.reject', $transfer) }}" class="d-flex gap-2">
                            @csrf
                            @method('PUT')
                            <input name="decision_note" class="form-control" maxlength="255" placeholder="Tuliskan alasan penolakan pengajuan..." required>
                            <button class="btn btn-danger text-nowrap" type="submit" onclick="return confirm('Tolak pengajuan pemindahan aset ini?')">
                                <i class="bi bi-x-circle me-1"></i> Tolak
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 text-muted">
                    <i class="bi bi-check-circle text-primary"></i>
                    <span>Pengajuan telah diproses pada <strong>{{ $transfer->decided_at?->format('d-m-Y H:i') }}</strong>.</span>
                </div>
                @if($transfer->decision_note)
                    <div class="mt-2 p-2 bg-light rounded small">
                        <strong>Catatan:</strong> {{ $transfer->decision_note }}
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection