@extends('layouts.app')

@section('title', auth()->user()->role === 'peminjam' ? 'Aset Tersedia' : 'Daftar Asset')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>{{ auth()->user()->role === 'peminjam' ? 'Aset Tersedia' : 'Daftar Asset' }}</h2>
            <p class="text-muted mb-0">
                {{ auth()->user()->role === 'peminjam' ? 'Aset yang dapat diajukan untuk dipinjam.' : 'Kelola seluruh asset yang terdaftar dalam sistem.' }}
            </p>
        </div>

        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
        <div>
            <a href="{{ route('assets.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Asset
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

    {{-- SEARCH --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('assets.index') }}" method="GET">
                <div class="row g-2 align-items-center">
                    <div class="col-md-9 col-lg-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input
                                type="text"
                                name="search"
                                class="form-control border-start-0 ps-0"
                                value="{{ $search }}"
                                placeholder="Cari kode, nama, merk, tipe, kategori, lokasi, vendor..."
                            >
                        </div>
                    </div>

                    <div class="col-md-3 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                        @if($search)
                            <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary" title="Reset filter">
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
                    <i class="bi bi-pc-display text-primary"></i> Data Asset
                </strong>
                <small class="text-muted ms-2">Total: {{ $assets->total() }} asset</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">#</th>
                            <th>Kode Asset</th>
                            <th>Nama Asset</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Vendor</th>
                            <th>Kondisi</th>
                            <th>Status</th>
                            <th width="160" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                            <tr>
                                <td class="text-center text-muted">
                                    {{ $assets->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <strong>{{ $asset->kode_asset }}</strong>
                                </td>
                                <td>
                                    {{ $asset->nama_asset }}
                                </td>
                                <td>
                                    {{ $asset->category?->nama ?? '-' }}
                                </td>
                                <td>
                                    {{ $asset->location?->nama ?? '-' }}
                                    @if($asset->location?->ruangan)
                                        <br>
                                        <small class="text-muted">{{ $asset->location->ruangan }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ $asset->vendor?->nama ?? '-' }}
                                </td>

                                {{-- KONDISI --}}
                                <td>
                                    @if($asset->kondisi === 'Baik')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle-fill"></i> Baik
                                        </span>
                                    @elseif($asset->kondisi === 'Rusak Ringan')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-exclamation-circle-fill"></i> Rusak Ringan
                                        </span>
                                    @elseif($asset->kondisi === 'Rusak Berat')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle-fill"></i> Rusak Berat
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            {{ $asset->kondisi }}
                                        </span>
                                    @endif
                                </td>

                                {{-- STATUS --}}
                                <td>
                                    @if($asset->status === 'Aktif')
                                        <span class="badge-status active">
                                            <i class="bi bi-check-circle-fill"></i> Aktif
                                        </span>
                                    @elseif($asset->status === 'Dipinjam')
                                        <span class="badge-status borrowed">
                                            <i class="bi bi-person-check-fill"></i> Dipinjam
                                        </span>
                                    @elseif($asset->status === 'Maintenance')
                                        <span class="badge-status maintenance">
                                            <i class="bi bi-tools"></i> Maintenance
                                        </span>
                                    @elseif($asset->status === 'Rusak')
                                        <span class="badge-status damaged">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Rusak
                                        </span>
                                    @elseif($asset->status === 'Dihapus')
                                        <span class="badge-status deleted">
                                            <i class="bi bi-trash-fill"></i> Dihapus
                                        </span>
                                    @else
                                        <span class="badge-status secondary">
                                            {{ $asset->status }}
                                        </span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('assets.show', $asset) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Detail Asset">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
                                        <a href="{{ route('assets.edit', $asset) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit Asset">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('assets.destroy', $asset) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus asset ini?');"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus Asset">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>
                                            @if($search)
                                                Tidak ditemukan asset dengan kata kunci "<strong>{{ $search }}</strong>".
                                            @else
                                                Belum ada data asset yang terdaftar.
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
        @if($assets->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $assets->firstItem() }} - {{ $assets->lastItem() }} dari {{ $assets->total() }} data
                    </small>
                    <div>
                        {{ $assets->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection