@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Edit Asset History</h3>
            <p class="text-muted mb-0">
                Perbarui informasi riwayat aktivitas aset
            </p>
        </div>

        <a
            href="{{ route('asset-histories.show', $history) }}"
            class="btn btn-secondary">
            Kembali
        </a>
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

    <form
        action="{{ route('asset-histories.update', $history) }}"
        method="POST">

        @csrf
        @method('PUT')

        {{-- INFORMASI HISTORY --}}
        <div class="card mb-4">

            <div class="card-header">
                <strong>Informasi History</strong>
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
                                    @selected(old('asset_id', $history->asset_id) == $asset->id)>

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
                            type="datetime-local"
                            name="tanggal"
                            class="form-control @error('tanggal') is-invalid @enderror"
                            value="{{ old('tanggal', $history->tanggal?->format('Y-m-d\TH:i')) }}"
                            required>

                        @error('tanggal')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- AKTIVITAS --}}
                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Aktivitas <span class="text-danger">*</span>
                        </label>

                        <select
                            name="aktivitas"
                            class="form-select @error('aktivitas') is-invalid @enderror"
                            required>

                            <option value="">
                                -- Pilih Aktivitas --
                            </option>

                            @foreach([
                                'Asset Dibuat',
                                'Asset Diperbarui',
                                'Asset Dipindahkan',
                                'Maintenance',
                                'Perubahan Kondisi',
                                'Perubahan Status',
                                'Asset Dihapus',
                                'Lainnya'
                            ] as $aktivitas)

                                <option
                                    value="{{ $aktivitas }}"
                                    @selected(old('aktivitas', $history->aktivitas) == $aktivitas)>

                                    {{ $aktivitas }}

                                </option>

                            @endforeach

                        </select>

                        @error('aktivitas')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Deskripsi
                        </label>

                        <textarea
                            name="deskripsi"
                            class="form-control @error('deskripsi') is-invalid @enderror"
                            rows="5"
                            placeholder="Jelaskan aktivitas yang dilakukan...">{{ old('deskripsi', $history->deskripsi) }}</textarea>

                        @error('deskripsi')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>

        {{-- BUTTON --}}
        <div class="d-flex justify-content-end gap-2 mb-5">

            <a
                href="{{ route('asset-histories.show', $history) }}"
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