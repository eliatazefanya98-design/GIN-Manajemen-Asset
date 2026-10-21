@extends('layouts.app')

@section('title', 'Approval')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Approval Mutasi</h2>
            <p class="text-muted mb-0">Daftar pengajuan mutasi/transfer aset yang memerlukan persetujuan pimpinan.</p>
        </div>
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

    {{-- TABLE CARD --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong class="d-inline-flex align-items-center gap-2">
                    <i class="bi bi-check2-circle text-primary"></i> Menunggu Persetujuan
                </strong>
                <small class="text-muted ms-2">Total: {{ $transfers->total() }} pengajuan</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Aset</th>
                            <th>Lokasi Asal</th>
                            <th>Lokasi Tujuan</th>
                            <th>Pengaju</th>
                            <th>Alasan</th>
                            <th width="120" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                            <tr>
                                <td>
                                    <strong>{{ $transfer->asset?->kode_asset }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $transfer->asset?->nama_asset }}</small>
                                </td>
                                <td>
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-geo-alt text-muted"></i>
                                        {{ $transfer->originLocation?->name ?? $transfer->originLocation?->nama ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="d-inline-flex align-items-center gap-1 text-primary">
                                        <i class="bi bi-geo-alt-fill"></i>
                                        {{ $transfer->targetLocation?->name ?? $transfer->targetLocation?->nama ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-person text-muted"></i>
                                        {{ $transfer->requester?->name ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 250px;" title="{{ $transfer->reason }}">
                                        {{ $transfer->reason }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a class="btn btn-sm btn-primary" href="{{ route('approvals.show', $transfer) }}">
                                        <i class="bi bi-check-square me-1"></i> Tinjau
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-check2-all text-success"></i>
                                        <p>Semua beres! Tidak ada pengajuan yang menunggu persetujuan saat ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        @if($transfers->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $transfers->firstItem() }} - {{ $transfers->lastItem() }} dari {{ $transfers->total() }} data
                    </small>
                    <div>
                        {{ $transfers->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
