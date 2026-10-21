@extends('layouts.app')

@section('title', 'Detail Kategori - ' . $category->nama)

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Detail Kategori: {{ $category->nama }}</h2>
            <p class="text-muted mb-0">Informasi dan daftar seluruh aset yang masuk ke dalam kategori ini.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>

            @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
            <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i> Edit Kategori
            </a>
            @endif
        </div>
    </div>

    {{-- INFO CARD --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <strong class="d-inline-flex align-items-center gap-2">
                <i class="bi bi-info-circle text-primary"></i> Informasi Kategori
            </strong>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="text-muted small d-block">Nama Kategori</label>
                    <div class="fw-bold fs-5 text-dark">{{ $category->nama }}</div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted small d-block">Total Aset Terdaftar</label>
                    <div class="mt-1">
                        <span class="badge bg-info fs-6">
                            <i class="bi bi-box-seam me-1"></i> {{ $category->assets->count() }} aset
                        </span>
                    </div>
                </div>

                <div class="col-12"><hr class="my-2 border-light"></div>

                <div class="col-12">
                    <label class="text-muted small d-block">Deskripsi</label>
                    <div class="p-3 bg-light rounded text-dark mt-1">
                        {{ $category->deskripsi ?: 'Tidak ada deskripsi tambahan.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ASSETS LIST CARD --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong class="d-inline-flex align-items-center gap-2">
                    <i class="bi bi-pc-display text-primary"></i> Aset dalam Kategori Ini
                </strong>
                <small class="text-muted ms-2">Total: {{ $category->assets->count() }} aset</small>
            </div>
        </div>

        <div class="card-body p-0">
            @if($category->assets->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Kode Asset</th>
                                <th>Nama Asset</th>
                                <th>Merk</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->assets as $asset)
                                <tr>
                                    <td><strong>{{ $asset->kode_asset }}</strong></td>
                                    <td>{{ $asset->nama_asset }}</td>
                                    <td>{{ $asset->merk ?: '-' }}</td>
                                    <td>
                                        @if($asset->kondisi === 'Baik')
                                            <span class="badge bg-success">Baik</span>
                                        @elseif($asset->kondisi === 'Rusak Ringan')
                                            <span class="badge bg-warning">Rusak Ringan</span>
                                        @elseif($asset->kondisi === 'Rusak Berat')
                                            <span class="badge bg-danger">Rusak Berat</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $asset->kondisi }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($asset->status === 'Aktif')
                                            <span class="badge-status active">Aktif</span>
                                        @elseif($asset->status === 'Dipinjam')
                                            <span class="badge-status borrowed">Dipinjam</span>
                                        @elseif($asset->status === 'Maintenance')
                                            <span class="badge-status maintenance">Maintenance</span>
                                        @elseif($asset->status === 'Rusak')
                                            <span class="badge-status damaged">Rusak</span>
                                        @else
                                            <span class="badge-status secondary">{{ $asset->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-outline-primary" title="Detail Asset">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="bi bi-inbox"></i>
                    <p>Belum ada aset yang terdaftar dalam kategori ini.</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection