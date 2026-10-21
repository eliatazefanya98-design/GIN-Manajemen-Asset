@extends('layouts.app')

@section('title', 'Ajukan Transfer')

@section('content')
<div class="container-fluid" style="max-width: 900px">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Ajukan Transfer Aset</h2>
            <p class="text-muted mb-0">Lokasi aset akan diperbarui secara otomatis setelah disetujui Pimpinan.</p>
        </div>

        <div>
            <a href="{{ route('transfers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ $errors->first() }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- FORM CARD --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <strong class="d-inline-flex align-items-center gap-2">
                <i class="bi bi-arrow-left-right text-primary"></i> Formulir Mutasi Lokasi
            </strong>
        </div>

        <div class="card-body">
            <form method="POST" action="{{ route('transfers.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="asset_id" class="form-label">Pilih Aset yang Akan Dimutasi <span class="text-danger">*</span></label>
                    <select id="asset_id" name="asset_id" class="form-select" required>
                        <option value="">-- Pilih aset --</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>
                                {{ $asset->kode_asset }} · {{ $asset->nama_asset }} (Lokasi Saat Ini: {{ $asset->location?->name ?? $asset->location?->nama ?? 'Tanpa lokasi' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="target_location_id" class="form-label">Lokasi Tujuan Mutasi <span class="text-danger">*</span></label>
                    <select id="target_location_id" name="target_location_id" class="form-select" required>
                        <option value="">-- Pilih lokasi tujuan --</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" @selected(old('target_location_id') == $location->id)>
                                {{ $location->name ?? $location->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="reason" class="form-label">Alasan Transfer / Pemindahan <span class="text-danger">*</span></label>
                    <textarea id="reason" name="reason" class="form-control" rows="3" maxlength="255" placeholder="Jelaskan kebutuhan operasional pemindahan aset ini..." required>{{ old('reason') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-send me-1"></i> Kirim Pengajuan
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('transfers.index') }}">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection