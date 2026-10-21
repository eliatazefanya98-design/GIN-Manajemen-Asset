@extends('layouts.app')

@section('title', 'Data Vendor')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Data Vendor</h2>
            <p class="text-muted mb-0">Kelola data mitra, supplier, dan penyedia pengadaan aset.</p>
        </div>

        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
        <div>
            <a href="{{ route('vendors.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Vendor
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

    {{-- SEARCH --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('vendors.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-9 col-lg-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input
                                type="text"
                                name="search"
                                class="form-control border-start-0 ps-0"
                                value="{{ $search }}"
                                placeholder="Cari nama vendor, kontak, email, atau alamat..."
                            >
                        </div>
                    </div>

                    <div class="col-md-3 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                        @if($search)
                            <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary" title="Reset filter">
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
                    <i class="bi bi-building text-primary"></i> Data Vendor
                </strong>
                <small class="text-muted ms-2">Total: {{ $vendors->total() }} vendor</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">#</th>
                            <th>Nama Vendor</th>
                            <th>Kontak</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th width="140" class="text-center">Jumlah Asset</th>
                            <th width="160" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                            <tr>
                                <td class="text-center text-muted">
                                    {{ $vendors->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <strong>{{ $vendor->nama }}</strong>
                                </td>
                                <td>{{ $vendor->kontak ?? '-' }}</td>
                                <td>{{ $vendor->email ?? '-' }}</td>
                                <td>{{ $vendor->alamat ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info">
                                        <i class="bi bi-box-seam me-1"></i> {{ $vendor->assets_count }} aset
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('vendors.show', $vendor) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Detail Vendor">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
                                        <a href="{{ route('vendors.edit', $vendor) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit Vendor">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('vendors.destroy', $vendor) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus vendor ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus Vendor">
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
                                                Tidak ditemukan vendor dengan kata kunci "<strong>{{ $search }}</strong>".
                                            @else
                                                Belum ada data vendor yang terdaftar.
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
        @if($vendors->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $vendors->firstItem() }} - {{ $vendors->lastItem() }} dari {{ $vendors->total() }} data
                    </small>
                    <div>
                        {{ $vendors->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection