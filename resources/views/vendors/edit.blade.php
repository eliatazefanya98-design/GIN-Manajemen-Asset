@extends('layouts.app')

@section('title', 'Edit Vendor')

@section('content')
<div class="container-fluid" style="max-width: 900px">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Edit Vendor</h2>
            <p class="text-muted mb-0">Perbarui data informasi mitra penyedia <strong>{{ $vendor->nama }}</strong></p>
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
                <i class="bi bi-pencil-square text-primary"></i> Formulir Perubahan Vendor
            </strong>
        </div>

        <div class="card-body">
            <form action="{{ route('vendors.update', $vendor) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            Nama Vendor <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama', $vendor->nama) }}"
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
                            class="form-control"
                            value="{{ old('kontak', $vendor->kontak) }}"
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label">Email Vendor</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email', $vendor->email) }}"
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label">Alamat Kantor / Workshop</label>
                        <textarea
                            name="alamat"
                            rows="4"
                            class="form-control"
                        >{{ old('alamat', $vendor->alamat) }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
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