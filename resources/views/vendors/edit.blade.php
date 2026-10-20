@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <h3 class="mb-1">Edit Vendor</h3>
        <p class="text-muted mb-0">
            Perbarui informasi vendor.
        </p>
    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form action="{{ route('vendors.update', $vendor) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">

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
                            class="form-control"
                            value="{{ old('kontak', $vendor->kontak) }}"
                        >

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $vendor->email) }}"
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
                            class="form-control"
                        >{{ old('alamat', $vendor->alamat) }}</textarea>

                    </div>

                </div>

                <div class="d-flex gap-2">

                    <a href="{{ route('vendors.index') }}"
                       class="btn btn-secondary">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        Update Vendor
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
@endsection