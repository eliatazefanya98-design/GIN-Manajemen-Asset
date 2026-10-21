@extends('layouts.app')

@section('title', 'Tambah Vendor')

@section('content')
<div class="container-fluid" style="max-width: 900px">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Tambah Vendor</h2>
            <p class="text-muted mb-0">Tambahkan data mitra penyedia atau supplier pengadaan aset baru.</p>
        </div>

        <div>
            <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <strong class="d-inline-flex align-items-center gap-2">
                <i class="bi bi-building text-primary"></i> Formulir Data Vendor
            </strong>
        </div>

        <div class="card-body">
            <form action="{{ route('vendors.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            Nama Vendor <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama') }}"
                            placeholder="Contoh: PT Teknologi Indonesia"
                            required
                        >
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Kontak / No. Telepon</label>
                        <input
                            type="text"
                            name="kontak"
                            class="form-control @error('kontak') is-invalid @enderror"
                            value="{{ old('kontak') }}"
                            placeholder="Contoh: 081234567890"
                        >
                        @error('kontak')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Email Vendor</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="Contoh: info@vendor.co.id"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Alamat Kantor / Workshop</label>
                        <textarea
                            name="alamat"
                            rows="4"
                            class="form-control @error('alamat') is-invalid @enderror"
                            placeholder="Alamat lengkap vendor..."
                        >{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Vendor
                    </button>
                    <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection