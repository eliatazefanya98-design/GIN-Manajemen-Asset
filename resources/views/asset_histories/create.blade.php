@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Tambah Asset History</h2>
            <p class="text-muted mb-0">
                Tambahkan riwayat aktivitas baru pada aset
            </p>
        </div>

        <a href="{{ route('asset-histories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    {{-- VALIDATION ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <strong>Terjadi kesalahan input:</strong>
            </div>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('asset-histories.store') }}" method="POST">
        @csrf

        {{-- INFORMASI HISTORY --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary"></i>
                <strong>Formulir Riwayat Aktivitas</strong>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    {{-- ASSET --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Asset <span class="text-danger">*</span>
                        </label>

                        <select name="asset_id" class="form-select @error('asset_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Asset --</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>
                                    {{ $asset->kode_asset }} - {{ $asset->nama_asset }}
                                </option>
                            @endforeach
                        </select>

                        @error('asset_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- TANGGAL --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Tanggal & Waktu <span class="text-danger">*</span>
                        </label>

                        <input type="datetime-local" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', now()->format('Y-m-d\TH:i')) }}" required>

                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- AKTIVITAS --}}
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            Jenis Aktivitas <span class="text-danger">*</span>
                        </label>

                        <select name="aktivitas" class="form-select @error('aktivitas') is-invalid @enderror" required>
                            <option value="">-- Pilih Aktivitas --</option>
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
                                <option value="{{ $aktivitas }}" @selected(old('aktivitas') == $aktivitas)>
                                    {{ $aktivitas }}
                                </option>
                            @endforeach
                        </select>

                        @error('aktivitas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- DESKRIPSI --}}
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            Deskripsi Kejadian / Keterangan
                        </label>

                        <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4" placeholder="Jelaskan detail aktivitas yang dilakukan pada aset ini...">{{ old('deskripsi') }}</textarea>

                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- BUTTON --}}
        <div class="d-flex justify-content-end gap-2 mb-5">
            <a href="{{ route('asset-histories.index') }}" class="btn btn-outline-secondary">
                Batal
            </a>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-floppy me-1"></i> Simpan History
            </button>
        </div>
    </form>

</div>
@endsection