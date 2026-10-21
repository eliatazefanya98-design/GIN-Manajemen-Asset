@extends('layouts.app')

@section('title', 'Detail Vendor - ' . $vendor->nama)

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Detail Vendor: {{ $vendor->nama }}</h2>
            <p class="text-muted mb-0">Informasi kontak mitra penyedia dan daftar aset yang disuplai.</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>

            @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
            <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i> Edit Vendor
            </a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        {{-- INFORMASI VENDOR --}}
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <strong class="d-inline-flex align-items-center gap-2">
                        <i class="bi bi-building text-primary"></i> Data Vendor
                    </strong>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small d-block">Nama Vendor</label>
                        <div class="fw-bold fs-5 text-dark">{{ $vendor->nama }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block">Kontak / No. Telp</label>
                        <div class="fw-semibold">{{ $vendor->kontak ?? '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block">Email</label>
                        <div class="fw-semibold">{{ $vendor->email ?? '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block">Alamat</label>
                        <div class="p-2 bg-light rounded text-muted small mt-1">
                            {{ $vendor->alamat ?: 'Tidak ada alamat.' }}
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Total Aset Disuplai</span>
                        <span class="badge bg-info fs-6">
                            <i class="bi bi-box-seam me-1"></i> {{ $vendor->assets->count() }} aset
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ASSET DARI VENDOR --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong class="d-inline-flex align-items-center gap-2">
                            <i class="bi bi-pc-display text-primary"></i> Aset dari Vendor Ini
                        </strong>
                        <small class="text-muted ms-2">Total: {{ $vendor->assets->count() }} aset</small>
                    </div>
                </div>

                <div class="card-body p-0">
                    @if($vendor->assets->count())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Kode Asset</th>
                                        <th>Nama Asset</th>
                                        <th>Kondisi</th>
                                        <th>Status</th>
                                        <th width="100" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vendor->assets as $asset)
                                        <tr>
                                            <td><strong>{{ $asset->kode_asset }}</strong></td>
                                            <td>{{ $asset->nama_asset }}</td>
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
                            <p>Belum ada aset yang terkait dengan vendor ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection