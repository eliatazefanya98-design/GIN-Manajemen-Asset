@extends('layouts.app')

@section('title', 'Transfer Aset')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Transfer Aset</h2>
            <p class="text-muted mb-0">Daftar pengajuan pemindahan dan mutasi lokasi aset.</p>
        </div>

        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
        <div>
            <a href="{{ route('transfers.create') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left-right me-1"></i> Ajukan Transfer
            </a>
        </div>
        @endif
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong class="d-inline-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left-right text-primary"></i> Data Pengajuan Transfer
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
                            <th>Status</th>
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
                                    @if($transfer->status === 'menunggu')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-clock-history me-1"></i> Menunggu
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
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('transfers.show', $transfer) }}"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Detail Pengajuan">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>Belum ada pengajuan transfer yang terdaftar.</p>
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