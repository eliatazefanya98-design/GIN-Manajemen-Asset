@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="mb-4">
        <h3 class="mb-1">Edit Asset</h3>
        <p class="text-muted mb-0">
            Perbarui informasi asset yang sudah terdaftar.
        </p>
    </div>


    {{-- VALIDATION ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger">

            <strong>Terjadi kesalahan:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>
    @endif


    <div class="card shadow-sm">

        <div class="card-header">
            <strong>Informasi Asset</strong>
        </div>

        <div class="card-body">

            <form action="{{ route('assets.update', $asset) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="row">

                    {{-- KODE ASSET --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kode Asset <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="kode_asset"
                            class="form-control @error('kode_asset') is-invalid @enderror"
                            value="{{ old('kode_asset', $asset->kode_asset) }}"
                            required
                        >

                        @error('kode_asset')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- NAMA ASSET --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Nama Asset <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="nama_asset"
                            class="form-control @error('nama_asset') is-invalid @enderror"
                            value="{{ old('nama_asset', $asset->nama_asset) }}"
                            required
                        >

                        @error('nama_asset')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- KATEGORI --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Kategori <span class="text-danger">*</span>
                        </label>

                        <select
                            name="category_id"
                            class="form-select @error('category_id') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                -- Pilih Kategori --
                            </option>

                            @foreach($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    @selected(old('category_id', $asset->category_id) == $category->id)
                                >
                                    {{ $category->nama }}
                                </option>

                            @endforeach

                        </select>

                        @error('category_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- LOKASI --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Lokasi
                        </label>

                        <select
                            name="location_id"
                            class="form-select @error('location_id') is-invalid @enderror"
                        >

                            <option value="">
                                -- Pilih Lokasi --
                            </option>

                            @foreach($locations as $location)

                                <option
                                    value="{{ $location->id }}"
                                    @selected(old('location_id', $asset->location_id) == $location->id)
                                >
                                    {{ $location->nama }}

                                    @if($location->ruangan)
                                        - {{ $location->ruangan }}
                                    @endif
                                </option>

                            @endforeach

                        </select>

                        @error('location_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- VENDOR --}}
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Vendor
                        </label>

                        <select
                            name="vendor_id"
                            class="form-select @error('vendor_id') is-invalid @enderror"
                        >

                            <option value="">
                                -- Pilih Vendor --
                            </option>

                            @foreach($vendors as $vendor)

                                <option
                                    value="{{ $vendor->id }}"
                                    @selected(old('vendor_id', $asset->vendor_id) == $vendor->id)
                                >
                                    {{ $vendor->nama }}
                                </option>

                            @endforeach

                        </select>

                        @error('vendor_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- MERK --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Merk
                        </label>

                        <input
                            type="text"
                            name="merk"
                            class="form-control"
                            value="{{ old('merk', $asset->merk) }}"
                            placeholder="Contoh: Lenovo"
                        >

                    </div>


                    {{-- TIPE --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Tipe
                        </label>

                        <input
                            type="text"
                            name="tipe"
                            class="form-control"
                            value="{{ old('tipe', $asset->tipe) }}"
                            placeholder="Contoh: ThinkPad E14"
                        >

                    </div>


                    {{-- TANGGAL PEROLEHAN --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Tanggal Perolehan
                        </label>

                        <input
                            type="date"
                            name="tanggal_perolehan"
                            class="form-control"
                            value="{{ old('tanggal_perolehan', optional($asset->tanggal_perolehan)->format('Y-m-d')) }}"
                        >

                    </div>


                    {{-- HARGA --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Harga
                        </label>

                        <input
                            type="number"
                            name="harga"
                            class="form-control @error('harga') is-invalid @enderror"
                            value="{{ old('harga', $asset->harga) }}"
                            min="0"
                            step="0.01"
                            placeholder="Contoh: 12500000"
                        >

                        @error('harga')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- KONDISI --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kondisi <span class="text-danger">*</span>
                        </label>

                        <select
                            name="kondisi"
                            class="form-select @error('kondisi') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                -- Pilih Kondisi --
                            </option>

                            <option value="Baik"
                                @selected(old('kondisi', $asset->kondisi) === 'Baik')>
                                Baik
                            </option>

                            <option value="Rusak Ringan"
                                @selected(old('kondisi', $asset->kondisi) === 'Rusak Ringan')>
                                Rusak Ringan
                            </option>

                            <option value="Rusak Berat"
                                @selected(old('kondisi', $asset->kondisi) === 'Rusak Berat')>
                                Rusak Berat
                            </option>

                        </select>

                        @error('kondisi')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- STATUS --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Status <span class="text-danger">*</span>
                        </label>

                        <select
                            name="status"
                            class="form-select @error('status') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                -- Pilih Status --
                            </option>

                            <option value="Aktif"
                                @selected(old('status', $asset->status) === 'Aktif')>
                                Aktif
                            </option>

                            <option value="Dipinjam"
                                @selected(old('status', $asset->status) === 'Dipinjam')>
                                Dipinjam
                            </option>

                            <option value="Maintenance"
                                @selected(old('status', $asset->status) === 'Maintenance')>
                                Maintenance
                            </option>

                            <option value="Rusak"
                                @selected(old('status', $asset->status) === 'Rusak')>
                                Rusak
                            </option>

                            <option value="Dihapus"
                                @selected(old('status', $asset->status) === 'Dihapus')>
                                Dihapus
                            </option>

                        </select>

                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- PENANGGUNG JAWAB --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Penanggung Jawab
                        </label>

                        <input
                            type="text"
                            name="penanggung_jawab"
                            class="form-control"
                            value="{{ old('penanggung_jawab', $asset->penanggung_jawab) }}"
                            placeholder="Contoh: Admin Laboratorium"
                        >

                    </div>


                    {{-- DESKRIPSI --}}
                    <div class="col-12 mb-3">

                        <label class="form-label">
                            Deskripsi
                        </label>

                        <textarea
                            name="deskripsi"
                            rows="4"
                            class="form-control"
                            placeholder="Keterangan tambahan mengenai asset..."
                        >{{ old('deskripsi', $asset->deskripsi) }}</textarea>

                    </div>

                </div>


                <hr>


                {{-- BUTTON --}}
                <div class="d-flex gap-2">

                    <a href="{{ route('assets.show', $asset) }}"
                       class="btn btn-secondary">
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
@endsection