@extends('layouts.app')

@section('title', 'Tambah Lokasi')

@section('content')
<div class="container-fluid" style="max-width: 900px">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Tambah Lokasi</h2>
            <p class="text-muted mb-0">Tambahkan lokasi fisik baru untuk penempatan dan pelacakan aset.</p>
        </div>

        <div>
            <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <strong class="d-inline-flex align-items-center gap-2">
                <i class="bi bi-geo-alt-fill text-primary"></i> Formulir Data Lokasi
            </strong>
        </div>

        <div class="card-body">
            <form action="{{ route('locations.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            Nama Lokasi <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama') }}"
                            placeholder="Contoh: Laboratorium Komputer"
                            required
                        >
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Gedung</label>
                        <input
                            type="text"
                            name="gedung"
                            class="form-control @error('gedung') is-invalid @enderror"
                            value="{{ old('gedung') }}"
                            placeholder="Contoh: Gedung A"
                        >
                        @error('gedung')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Lantai</label>
                        <input
                            type="text"
                            name="lantai"
                            class="form-control @error('lantai') is-invalid @enderror"
                            value="{{ old('lantai') }}"
                            placeholder="Contoh: Lantai 2"
                        >
                        @error('lantai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ruangan</label>
                        <input
                            type="text"
                            name="ruangan"
                            class="form-control @error('ruangan') is-invalid @enderror"
                            value="{{ old('ruangan') }}"
                            placeholder="Contoh: R. 201"
                        >
                        @error('ruangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea
                            name="deskripsi"
                            rows="4"
                            class="form-control @error('deskripsi') is-invalid @enderror"
                            placeholder="Deskripsi atau patokan spesifik lokasi..."
                        >{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Lokasi
                    </button>
                    <a href="{{ route('locations.index') }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection