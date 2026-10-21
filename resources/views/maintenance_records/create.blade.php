@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Tambah Maintenance</h2>
            <p class="text-muted mb-0">
                Tambahkan catatan riwayat servis, pemeliharaan, atau perbaikan aset.
            </p>
        </div>

        <div>
            <a href="{{ route('maintenance-records.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">

        <div class="card-header">
            <strong class="d-inline-flex align-items-center gap-2">
                <i class="bi bi-tools text-primary"></i> Formulir Catatan Maintenance
            </strong>
        </div>

        <div class="card-body">

            <form action="{{ route('maintenance-records.store') }}"
                  method="POST">

                @csrf

                <div class="row">

                    {{-- ASSET --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Asset <span class="text-danger">*</span>
                        </label>

                        <select
                            name="asset_id"
                            class="form-select @error('asset_id') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                -- Pilih Asset --
                            </option>

                            @foreach($assets as $asset)

                                <option
                                    value="{{ $asset->id }}"
                                    @selected(old('asset_id') == $asset->id)
                                >
                                    {{ $asset->kode_asset }}
                                    - {{ $asset->nama_asset }}
                                </option>

                            @endforeach

                        </select>

                        @error('asset_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- TANGGAL --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Tanggal <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            class="form-control @error('tanggal') is-invalid @enderror"
                            value="{{ old('tanggal', date('Y-m-d')) }}"
                            required
                        >

                        @error('tanggal')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- JENIS --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Jenis Maintenance
                        </label>

                        <select
                            name="jenis"
                            class="form-select @error('jenis') is-invalid @enderror"
                        >

                            <option value="">
                                -- Pilih Jenis --
                            </option>

                            <option value="Preventive"
                                @selected(old('jenis') === 'Preventive')>
                                Preventive
                            </option>

                            <option value="Corrective"
                                @selected(old('jenis') === 'Corrective')>
                                Corrective
                            </option>

                            <option value="Inspeksi"
                                @selected(old('jenis') === 'Inspeksi')>
                                Inspeksi
                            </option>

                            <option value="Perbaikan"
                                @selected(old('jenis') === 'Perbaikan')>
                                Perbaikan
                            </option>

                            <option value="Lainnya"
                                @selected(old('jenis') === 'Lainnya')>
                                Lainnya
                            </option>

                        </select>

                        @error('jenis')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- TEKNISI --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Teknisi
                        </label>

                        <input
                            type="text"
                            name="teknisi"
                            class="form-control @error('teknisi') is-invalid @enderror"
                            value="{{ old('teknisi') }}"
                            placeholder="Contoh: Teknisi IT"
                        >

                        @error('teknisi')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- KELUHAN --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Keluhan
                        </label>

                        <textarea
                            name="keluhan"
                            rows="4"
                            class="form-control @error('keluhan') is-invalid @enderror"
                            placeholder="Jelaskan keluhan atau masalah asset..."
                        >{{ old('keluhan') }}</textarea>

                        @error('keluhan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- KERUSAKAN --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kerusakan
                        </label>

                        <textarea
                            name="kerusakan"
                            rows="4"
                            class="form-control @error('kerusakan') is-invalid @enderror"
                            placeholder="Jelaskan kerusakan yang ditemukan..."
                        >{{ old('kerusakan') }}</textarea>

                        @error('kerusakan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- KONDISI SEBELUM --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kondisi Sebelum
                        </label>

                        <select
                            name="kondisi_sebelum"
                            class="form-select @error('kondisi_sebelum') is-invalid @enderror"
                        >

                            <option value="">
                                -- Pilih Kondisi --
                            </option>

                            <option value="Baik"
                                @selected(old('kondisi_sebelum') === 'Baik')>
                                Baik
                            </option>

                            <option value="Rusak Ringan"
                                @selected(old('kondisi_sebelum') === 'Rusak Ringan')>
                                Rusak Ringan
                            </option>

                            <option value="Rusak Berat"
                                @selected(old('kondisi_sebelum') === 'Rusak Berat')>
                                Rusak Berat
                            </option>

                        </select>

                        @error('kondisi_sebelum')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- KONDISI SESUDAH --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kondisi Sesudah
                        </label>

                        <select
                            name="kondisi_sesudah"
                            class="form-select @error('kondisi_sesudah') is-invalid @enderror"
                        >

                            <option value="">
                                -- Pilih Kondisi --
                            </option>

                            <option value="Baik"
                                @selected(old('kondisi_sesudah') === 'Baik')>
                                Baik
                            </option>

                            <option value="Rusak Ringan"
                                @selected(old('kondisi_sesudah') === 'Rusak Ringan')>
                                Rusak Ringan
                            </option>

                            <option value="Rusak Berat"
                                @selected(old('kondisi_sesudah') === 'Rusak Berat')>
                                Rusak Berat
                            </option>

                        </select>

                        @error('kondisi_sesudah')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- BIAYA --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Biaya
                        </label>

                        <input
                            type="number"
                            name="biaya"
                            class="form-control @error('biaya') is-invalid @enderror"
                            value="{{ old('biaya', 0) }}"
                            min="0"
                            step="0.01"
                            placeholder="Contoh: 500000"
                        >

                        @error('biaya')
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

                            <option value="Terjadwal"
                                @selected(old('status') === 'Terjadwal')>
                                Terjadwal
                            </option>

                            <option value="Proses"
                                @selected(old('status') === 'Proses')>
                                Proses
                            </option>

                            <option value="Selesai"
                                @selected(old('status') === 'Selesai')>
                                Selesai
                            </option>

                            <option value="Dibatalkan"
                                @selected(old('status') === 'Dibatalkan')>
                                Dibatalkan
                            </option>

                        </select>

                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- HASIL TINDAKAN --}}
                    <div class="col-12 mb-3">

                        <label class="form-label">
                            Hasil Tindakan
                        </label>

                        <textarea
                            name="hasil_tindakan"
                            rows="4"
                            class="form-control @error('hasil_tindakan') is-invalid @enderror"
                            placeholder="Jelaskan tindakan yang dilakukan dan hasil maintenance..."
                        >{{ old('hasil_tindakan') }}</textarea>

                        @error('hasil_tindakan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                <hr>


                {{-- BUTTON --}}
                <div class="d-flex gap-2">

                    <a href="{{ route('maintenance-records.index') }}"
                       class="btn btn-secondary">
                        Kembali
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        Simpan Maintenance
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
@endsection