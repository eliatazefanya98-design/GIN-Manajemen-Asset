@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Detail Maintenance Record</h3>
            <p class="text-muted mb-0">
                Informasi lengkap aktivitas maintenance aset
            </p>
        </div>

        <div class="d-flex gap-2">
            <a
                href="{{ route('maintenance-records.edit', $maintenanceRecord) }}"
                class="btn btn-warning">
                Edit
            </a>

            <a
                href="{{ route('maintenance-records.index') }}"
                class="btn btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    {{-- INFORMASI ASSET --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>Informasi Asset</strong>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="text-muted">Kode Asset</label>
                    <div class="fw-bold">
                        {{ $maintenanceRecord->asset->kode_asset ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted">Nama Asset</label>
                    <div class="fw-bold">
                        {{ $maintenanceRecord->asset->nama_asset ?? '-' }}
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- INFORMASI MAINTENANCE --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>Informasi Maintenance</strong>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="text-muted">Tanggal</label>
                    <div>
                        {{ $maintenanceRecord->tanggal?->format('d-m-Y') ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="text-muted">Jenis Maintenance</label>
                    <div>
                        {{ $maintenanceRecord->jenis ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="text-muted">Teknisi</label>
                    <div>
                        {{ $maintenanceRecord->teknisi ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="text-muted">Biaya</label>
                    <div>
                        Rp {{ number_format($maintenanceRecord->biaya ?? 0, 0, ',', '.') }}
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="text-muted">Status</label>
                    <div>
                        @php
                            $statusClass = match($maintenanceRecord->status) {
                                'Terjadwal' => 'bg-secondary',
                                'Proses' => 'bg-warning text-dark',
                                'Selesai' => 'bg-success',
                                'Dibatalkan' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                        @endphp

                        <span class="badge {{ $statusClass }}">
                            {{ $maintenanceRecord->status }}
                        </span>
                    </div>
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
                    <label class="text-muted">Keluhan</label>

                    <div class="border rounded p-3">
                        {{ $maintenanceRecord->keluhan ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted">Kerusakan</label>

                    <div class="border rounded p-3">
                        {{ $maintenanceRecord->kerusakan ?? '-' }}
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- KONDISI ASSET --}}
    <div class="card mb-4">
        <div class="card-header">
            <strong>Kondisi Asset</strong>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">
                    <label class="text-muted">Kondisi Sebelum</label>

                    <div class="mt-1">
                        <span class="badge bg-secondary">
                            {{ $maintenanceRecord->kondisi_sebelum ?? '-' }}
                        </span>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-muted">Kondisi Sesudah</label>

                    <div class="mt-1">
                        <span class="badge bg-primary">
                            {{ $maintenanceRecord->kondisi_sesudah ?? '-' }}
                        </span>
                    </div>
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

            <div class="border rounded p-3">
                {{ $maintenanceRecord->hasil_tindakan ?? '-' }}
            </div>

        </div>
    </div>

</div>
@endsection