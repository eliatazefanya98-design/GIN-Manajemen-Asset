@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Riwayat Peminjaman</h2>
            <p class="text-muted mb-0">Catatan riwayat lengkap peminjaman dan pengembalian aset.</p>
        </div>

        <div>
            <a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Peminjaman
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

    {{-- TABLE CARD --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong class="d-inline-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary"></i> Data Riwayat Peminjaman
                </strong>
                <small class="text-muted ms-2">Total: {{ $loans->total() }} riwayat</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Aset</th>
                            @if(auth()->user()->role !== 'peminjam')
                                <th>Peminjam</th>
                            @endif
                            <th>Tanggal Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th>Tanggal Kembali</th>
                            <th>Kondisi Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                            <tr>
                                <td>
                                    <strong>{{ $loan->asset?->kode_asset }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $loan->asset?->nama_asset }}</small>
                                </td>
                                @if(auth()->user()->role !== 'peminjam')
                                    <td>
                                        <span class="d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-person text-muted"></i>
                                            {{ $loan->user?->name ?? '-' }}
                                        </span>
                                    </td>
                                @endif
                                <td>{{ $loan->tanggal_pinjam?->format('d-m-Y') }}</td>
                                <td>{{ $loan->tanggal_rencana_kembali?->format('d-m-Y') ?? '-' }}</td>
                                <td>{{ $loan->tanggal_kembali?->format('d-m-Y') ?? '-' }}</td>
                                <td>
                                    @if($loan->kondisi_sesudah === 'Baik')
                                        <span class="badge bg-success">Baik</span>
                                    @elseif($loan->kondisi_sesudah === 'Rusak Ringan')
                                        <span class="badge bg-warning">Rusak Ringan</span>
                                    @elseif($loan->kondisi_sesudah === 'Rusak Berat')
                                        <span class="badge bg-danger">Rusak Berat</span>
                                    @else
                                        <span class="text-muted">{{ $loan->kondisi_sesudah ?? '-' }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($loan->status === 'dikembalikan')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle-fill me-1"></i> Dikembalikan
                                        </span>
                                    @elseif($loan->status === 'dipinjam')
                                        <span class="badge bg-info">
                                            <i class="bi bi-clock me-1"></i> Dipinjam
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($loan->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->role !== 'peminjam' ? 7 : 6 }}" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>Belum ada riwayat peminjaman.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($loans->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $loans->firstItem() }} - {{ $loans->lastItem() }} dari {{ $loans->total() }} data
                    </small>
                    <div>{{ $loans->links('pagination::bootstrap-5') }}</div>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection