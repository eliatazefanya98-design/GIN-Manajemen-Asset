@extends('layouts.app')

@section('title', 'Data Lokasi')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Data Lokasi</h2>
            <p class="text-muted mb-0">Kelola lokasi fisik penempatan dan persebaran aset.</p>
        </div>

        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
        <div>
            <a href="{{ route('locations.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Lokasi
            </a>
        </div>
        @endif
    </div>

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ALERT ERROR --}}
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
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-1 ps-3">
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
            <form method="GET" action="{{ route('locations.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-9 col-lg-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input
                                type="text"
                                name="search"
                                class="form-control border-start-0 ps-0"
                                placeholder="Cari nama, gedung, lantai, atau ruangan..."
                                value="{{ $search }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-3 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                        @if($search)
                            <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary" title="Reset filter">
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
                    <i class="bi bi-geo-alt-fill text-primary"></i> Data Lokasi
                </strong>
                <small class="text-muted ms-2">Total: {{ $locations->total() }} lokasi</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">#</th>
                            <th>Nama Lokasi</th>
                            <th>Gedung</th>
                            <th>Lantai</th>
                            <th>Ruangan</th>
                            <th width="140" class="text-center">Jumlah Asset</th>
                            <th width="160" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($locations as $location)
                            <tr>
                                <td class="text-center text-muted">
                                    {{ $locations->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <strong>{{ $location->nama }}</strong>
                                    @if($location->deskripsi)
                                        <br>
                                        <small class="text-muted">{{ $location->deskripsi }}</small>
                                    @endif
                                </td>
                                <td>{{ $location->gedung ?? '-' }}</td>
                                <td>{{ $location->lantai ?? '-' }}</td>
                                <td>{{ $location->ruangan ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info">
                                        <i class="bi bi-box-seam me-1"></i> {{ $location->assets_count }} aset
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('locations.show', $location) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Detail Lokasi">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
                                        <a href="{{ route('locations.edit', $location) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit Lokasi">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('locations.destroy', $location) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus lokasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus Lokasi">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>
                                            @if($search)
                                                Tidak ditemukan lokasi dengan kata kunci "<strong>{{ $search }}</strong>".
                                            @else
                                                Belum ada data lokasi yang terdaftar.
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
        @if($locations->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $locations->firstItem() }} - {{ $locations->lastItem() }} dari {{ $locations->total() }} data
                    </small>
                    <div>
                        {{ $locations->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection