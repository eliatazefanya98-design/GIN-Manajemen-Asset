@extends('layouts.app')

@section('title', 'Maintenance Records')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Maintenance Records</h2>
            <p class="text-muted mb-0">Daftar riwayat pemeliharaan, servis, dan perbaikan aset.</p>
        </div>

        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
        <div>
            <a href="{{ route('maintenance-records.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Maintenance
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

    {{-- ERROR MESSAGE --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- VALIDATION ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- SEARCH --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('maintenance-records.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-9 col-lg-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input
                                type="text"
                                name="search"
                                class="form-control border-start-0 ps-0"
                                placeholder="Cari asset, jenis, keluhan, teknisi, atau status..."
                                value="{{ request('search') }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-3 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                        @if(request('search'))
                            <a href="{{ route('maintenance-records.index') }}" class="btn btn-outline-secondary" title="Reset filter">
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
                    <i class="bi bi-tools text-primary"></i> Data Maintenance
                </strong>
                <small class="text-muted ms-2">Total: {{ $maintenanceRecords->total() }} catatan</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">#</th>
                            <th>Asset</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Teknisi</th>
                            <th>Biaya</th>
                            <th>Status</th>
                            <th width="160" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($maintenanceRecords as $record)
                            <tr>
                                <td class="text-center text-muted">
                                    {{ $maintenanceRecords->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    @if($record->asset)
                                        <strong>{{ $record->asset->kode_asset }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $record->asset->nama_asset }}</small>
                                    @else
                                        <span class="text-muted">Asset tidak ditemukan</span>
                                    @endif
                                </td>
                                <td>{{ $record->tanggal?->format('d-m-Y') }}</td>
                                <td>{{ $record->jenis ?? '-' }}</td>
                                <td>{{ $record->teknisi ?? '-' }}</td>
                                <td>Rp {{ number_format($record->biaya ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    @if($record->status === 'Terjadwal')
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-calendar-event me-1"></i> Terjadwal
                                        </span>
                                    @elseif($record->status === 'Proses')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-arrow-repeat me-1"></i> Proses
                                        </span>
                                    @elseif($record->status === 'Selesai')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                        </span>
                                    @elseif($record->status === 'Dibatalkan')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle-fill me-1"></i> Dibatalkan
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">{{ $record->status }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('maintenance-records.show', $record) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Detail Maintenance">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
                                        <a href="{{ route('maintenance-records.edit', $record) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit Maintenance">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('maintenance-records.destroy', $record) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus maintenance record ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus Maintenance">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>
                                            @if(request('search'))
                                                Tidak ditemukan catatan maintenance dengan kata kunci "<strong>{{ request('search') }}</strong>".
                                            @else
                                                Belum ada catatan maintenance yang terdaftar.
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
        @if($maintenanceRecords->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $maintenanceRecords->firstItem() }} - {{ $maintenanceRecords->lastItem() }} dari {{ $maintenanceRecords->total() }} data
                    </small>
                    <div>
                        {{ $maintenanceRecords->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection