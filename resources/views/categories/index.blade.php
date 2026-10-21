@extends('layouts.app')

@section('title', 'Kategori Asset')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Kategori Asset</h2>
            <p class="text-muted mb-0">Kelola kelompok dan klasifikasi aset dalam sistem.</p>
        </div>

        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
        <div>
            <a href="{{ route('categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
            </a>
        </div>
        @endif
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Validation Error --}}
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

    {{-- Search --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('categories.index') }}">
                <div class="row g-2 align-items-center">
                    <div class="col-md-9 col-lg-10">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input
                                type="text"
                                name="search"
                                class="form-control border-start-0 ps-0"
                                placeholder="Cari kategori..."
                                value="{{ $search }}"
                            >
                        </div>
                    </div>

                    <div class="col-md-3 col-lg-2 d-flex gap-2">
                        <button class="btn btn-primary flex-grow-1" type="submit">
                            <i class="bi bi-search me-1"></i> Cari
                        </button>
                        @if($search)
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary" title="Reset filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong class="d-inline-flex align-items-center gap-2">
                    <i class="bi bi-tags-fill text-primary"></i> Data Kategori
                </strong>
                <small class="text-muted ms-2">Total: {{ $categories->total() }} kategori</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="60" class="text-center">#</th>
                            <th>Nama Kategori</th>
                            <th>Deskripsi</th>
                            <th width="140" class="text-center">Jumlah Asset</th>
                            <th width="160" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="text-center text-muted">
                                    {{ $categories->firstItem() + $loop->index }}
                                </td>
                                <td>
                                    <strong>{{ $category->nama }}</strong>
                                </td>
                                <td>
                                    {{ $category->deskripsi ?: '-' }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">
                                        <i class="bi bi-box-seam me-1"></i> {{ $category->assets_count }} aset
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex gap-1">
                                        <a href="{{ route('categories.show', $category) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Detail Kategori">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
                                        <a href="{{ route('categories.edit', $category) }}"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Edit Kategori">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <form action="{{ route('categories.destroy', $category) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus Kategori">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>
                                            @if($search)
                                                Tidak ditemukan kategori dengan kata kunci "<strong>{{ $search }}</strong>".
                                            @else
                                                Belum ada kategori yang terdaftar.
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

        {{-- Pagination --}}
        @if($categories->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $categories->firstItem() }} - {{ $categories->lastItem() }} dari {{ $categories->total() }} data
                    </small>
                    <div>
                        {{ $categories->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

</div>
@endsection