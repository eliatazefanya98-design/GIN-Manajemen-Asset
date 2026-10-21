@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Edit Maintenance Record</h2>
            <p class="text-muted mb-0">
                Perbarui catatan maintenance untuk aset <strong>{{ $maintenanceRecord->asset?->kode_asset }} · {{ $maintenanceRecord->asset?->nama_asset }}</strong>
            </p>
        </div>

        <div>
            <a href="{{ route('maintenance-records.show', $maintenanceRecord) }}" class="btn btn-outline-secondary">
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

    <form
        action="{{ route('maintenance-records.update', $maintenanceRecord) }}"
        method="POST">

        @csrf
        @method('PUT')

        {{-- ASSET & TANGGAL --}}
        <div class="card mb-4">
            <div class="card-header">
                <strong>Informasi Maintenance</strong>
            </div>

            <div class="card-body">

                <div class="row">

                    {{-- ASSET --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Asset <span class="text-danger">*</span>
                        </label>

                        <select
                            name="asset_id"
                            class="form-select @error('asset_id') is-invalid @enderror"
                            required>

                            <option value="">
                                -- Pilih Asset --
                            </option>

                            @foreach($assets as $asset)

                                <option
                                    value="{{ $asset->id }}"
                                    @selected(old('asset_id', $maintenanceRecord->asset_id) == $asset->id)>

                                    {{ $asset->kode_asset }} -
                                    {{ $asset->nama_asset }}

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
                            value="{{ old('tanggal', $maintenanceRecord->tanggal?->format('Y-m-d')) }}"
                            required>

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
                            class="form-select">

                            <option value="">
                                -- Pilih Jenis --
                            </option>

                            @foreach([
                                'Preventive',
                                'Corrective',
                                'Inspeksi',
                                'Perbaikan',
                                'Lainnya'
                            ] as $jenis)

                                <option
                                    value="{{ $jenis }}"
                                    @selected(old('jenis', $maintenanceRecord->jenis) == $jenis)>

                                    {{ $jenis }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- TEKNISI --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Teknisi
                        </label>

                        <input
                            type="text"
                            name="teknisi"
                            class="form-control"
                            value="{{ old('teknisi', $maintenanceRecord->teknisi) }}"
                            placeholder="Nama teknisi">

                    </div>

                </div>

            </div>
        </div>

        {{-- KELUHAN & KERUSAKAN --}}
        <div class="card mb-4">

            <div class="card-header">
                <strong>Keluhan dan Kerusakan</strong>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Keluhan
                        </label>

                        <textarea
                            name="keluhan"
                            class="form-control"
                            rows="4"
                            placeholder="Masukkan keluhan">{{ old('keluhan', $maintenanceRecord->keluhan) }}</textarea>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kerusakan
                        </label>

                        <textarea
                            name="kerusakan"
                            class="form-control"
                            rows="4"
                            placeholder="Masukkan kerusakan">{{ old('kerusakan', $maintenanceRecord->kerusakan) }}</textarea>

                    </div>

                </div>

            </div>

        </div>

        {{-- KONDISI --}}
        <div class="card mb-4">

            <div class="card-header">
                <strong>Kondisi Asset</strong>
            </div>

            <div class="card-body">

                <div class="row">

                    {{-- SEBELUM --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kondisi Sebelum
                        </label>

                        <select
                            name="kondisi_sebelum"
                            class="form-select">

                            <option value="">
                                -- Pilih Kondisi --
                            </option>

                            @foreach([
                                'Baik',
                                'Rusak Ringan',
                                'Rusak Berat'
                            ] as $kondisi)

                                <option
                                    value="{{ $kondisi }}"
                                    @selected(old('kondisi_sebelum', $maintenanceRecord->kondisi_sebelum) == $kondisi)>

                                    {{ $kondisi }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- SESUDAH --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Kondisi Sesudah
                        </label>

                        <select
                            name="kondisi_sesudah"
                            class="form-select">

                            <option value="">
                                -- Pilih Kondisi --
                            </option>

                            @foreach([
                                'Baik',
                                'Rusak Ringan',
                                'Rusak Berat'
                            ] as $kondisi)

                                <option
                                    value="{{ $kondisi }}"
                                    @selected(old('kondisi_sesudah', $maintenanceRecord->kondisi_sesudah) == $kondisi)>

                                    {{ $kondisi }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>

        </div>

        {{-- BIAYA & STATUS --}}
        <div class="card mb-4">

            <div class="card-header">
                <strong>Biaya dan Status</strong>
            </div>

            <div class="card-body">

                <div class="row">

                    {{-- BIAYA --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Biaya
                        </label>

                        <input
                            type="number"
                            name="biaya"
                            class="form-control"
                            value="{{ old('biaya', $maintenanceRecord->biaya) }}"
                            min="0"
                            step="0.01"
                            placeholder="0">

                    </div>

                    {{-- STATUS --}}
                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Status <span class="text-danger">*</span>
                        </label>

                        <select
                            name="status"
                            class="form-select"
                            required>

                            @foreach([
                                'Terjadwal',
                                'Proses',
                                'Selesai',
                                'Dibatalkan'
                            ] as $status)

                                <option
                                    value="{{ $status }}"
                                    @selected(old('status', $maintenanceRecord->status) == $status)>

                                    {{ $status }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>

        </div>

        {{-- HASIL TINDAKAN --}}
        <div class="card mb-4">

            <div class="card-header">
                <strong>Hasil Tindakan</strong>
            </div>

            <div class="card-body">

                <textarea
                    name="hasil_tindakan"
                    class="form-control"
                    rows="5"
                    placeholder="Jelaskan tindakan yang dilakukan">{{ old('hasil_tindakan', $maintenanceRecord->hasil_tindakan) }}</textarea>

            </div>

        </div>

        {{-- BUTTON --}}
        <div class="d-flex justify-content-end gap-2 mb-5">

            <a
                href="{{ route('maintenance-records.show', $maintenanceRecord) }}"
                class="btn btn-secondary">
                Batal
            </a>

            <button
                type="submit"
                class="btn btn-primary">
                Simpan Perubahan
            </button>

        </div>

    </form>

</div>
@endsection