@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h3 class="mb-1">Edit Lokasi</h3>
        <p class="text-muted mb-0">
            Perbarui informasi lokasi.
        </p>
    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form action="{{ route('locations.update', $location) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">

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
                            class="form-control"
                            value="{{ old('gedung', $location->gedung) }}"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Lantai
                        </label>

                        <input
                            type="text"
                            name="lantai"
                            class="form-control"
                            value="{{ old('lantai', $location->lantai) }}"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Ruangan
                        </label>

                        <input
                            type="text"
                            name="ruangan"
                            class="form-control"
                            value="{{ old('ruangan', $location->ruangan) }}"
                        >

                    </div>

                    <div class="col-12 mb-3">

                        <label class="form-label">
                            Deskripsi
                        </label>

                        <textarea
                            name="deskripsi"
                            rows="4"
                            class="form-control"
                        >{{ old('deskripsi', $location->deskripsi) }}</textarea>

                    </div>

                </div>

                <div class="d-flex gap-2">

                    <a href="{{ route('locations.index') }}"
                       class="btn btn-secondary">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        Update Lokasi
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
@endsection