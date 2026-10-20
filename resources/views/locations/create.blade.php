@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h3 class="mb-1">Tambah Lokasi</h3>
        <p class="text-muted mb-0">
            Tambahkan lokasi baru untuk penempatan asset.
        </p>
    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form action="{{ route('locations.store') }}"
                  method="POST">

                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">

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
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Gedung
                        </label>

                        <input
                            type="text"
                            name="gedung"
                            class="form-control @error('gedung') is-invalid @enderror"
                            value="{{ old('gedung') }}"
                            placeholder="Contoh: Gedung A"
                        >

                        @error('gedung')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Lantai
                        </label>

                        <input
                            type="text"
                            name="lantai"
                            class="form-control @error('lantai') is-invalid @enderror"
                            value="{{ old('lantai') }}"
                            placeholder="Contoh: Lantai 2"
                        >

                        @error('lantai')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Ruangan
                        </label>

                        <input
                            type="text"
                            name="ruangan"
                            class="form-control @error('ruangan') is-invalid @enderror"
                            value="{{ old('ruangan') }}"
                            placeholder="Contoh: R. 201"
                        >

                        @error('ruangan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-12 mb-3">

                        <label class="form-label">
                            Deskripsi
                        </label>

                        <textarea
                            name="deskripsi"
                            rows="4"
                            class="form-control @error('deskripsi') is-invalid @enderror"
                            placeholder="Deskripsi lokasi..."
                        >{{ old('deskripsi') }}</textarea>

                        @error('deskripsi')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                <div class="d-flex gap-2">

                    <a href="{{ route('locations.index') }}"
                       class="btn btn-secondary">
                        Kembali
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        Simpan Lokasi
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
@endsection