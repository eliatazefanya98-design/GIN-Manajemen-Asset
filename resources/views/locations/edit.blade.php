@extends('layouts.app')

@section('title', 'Edit Lokasi')

@section('content')
<div class="container-fluid" style="max-width: 900px">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Edit Lokasi</h2>
            <p class="text-muted mb-0">Perbarui data lokasi penempatan <strong>{{ $location->nama }}</strong></p>
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
                <i class="bi bi-pencil-square text-primary"></i> Formulir Perubahan Lokasi
            </strong>
        </div>

        <div class="card-body">
            <form action="{{ route('locations.update', $location) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">
                            Nama Lokasi <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            name="nama"
                            class="form-control @error('nama') is-invalid @enderror"
                            value="{{ old('nama', $location->nama) }}"
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
                            class="form-control"
                            value="{{ old('gedung', $location->gedung) }}"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Lantai</label>
                        <input
                            type="text"
                            name="lantai"
                            class="form-control"
                            value="{{ old('lantai', $location->lantai) }}"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Ruangan</label>
                        <input
                            type="text"
                            name="ruangan"
                            class="form-control"
                            value="{{ old('ruangan', $location->ruangan) }}"
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea
                            name="deskripsi"
                            rows="4"
                            class="form-control"
                        >{{ old('deskripsi', $location->deskripsi) }}</textarea>
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan
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