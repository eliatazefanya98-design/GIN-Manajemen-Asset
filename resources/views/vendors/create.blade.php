@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h3 class="mb-1">Tambah Vendor</h3>
        <p class="text-muted mb-0">
            Tambahkan data vendor atau supplier asset.
        </p>
    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form action="{{ route('vendors.store') }}"
                  method="POST">

                @csrf

                <div class="row">

                    <div class="col-md-6 mb-3">

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
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kontak
                        </label>

                        <input
                            type="text"
                            name="kontak"
                            class="form-control @error('kontak') is-invalid @enderror"
                            value="{{ old('kontak') }}"
                            placeholder="Contoh: 081234567890"
                        >

                        @error('kontak')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="vendor@example.com"
                        >

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-12 mb-3">

                        <label class="form-label">
                            Alamat
                        </label>

                        <textarea
                            name="alamat"
                            rows="4"
                            class="form-control @error('alamat') is-invalid @enderror"
                            placeholder="Alamat lengkap vendor..."
                        >{{ old('alamat') }}</textarea>

                        @error('alamat')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                <div class="d-flex gap-2">

                    <a href="{{ route('vendors.index') }}"
                       class="btn btn-secondary">
                        Kembali
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        Simpan Vendor
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
@endsection