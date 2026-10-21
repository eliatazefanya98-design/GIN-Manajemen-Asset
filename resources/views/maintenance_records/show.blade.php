@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Detail Maintenance Record</h2>
            <p class="text-muted mb-0">
                Informasi lengkap aktivitas pemeliharaan dan perbaikan aset
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('maintenance-records.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>

            @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
            <a href="{{ route('maintenance-records.edit', $maintenanceRecord) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i> Edit Record
            </a>
            @endif
        </div>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- KOLOM KIRI: Informasi Utama & Hasil Tindakan --}}
        <div class="col-lg-8">
            {{-- INFORMASI MAINTENANCE --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-tools text-primary"></i>
                    <strong>Informasi Pemeliharaan</strong>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-4">
                            <label class="text-muted small d-block">Tanggal</label>
                            <div class="fw-semibold">{{ $maintenanceRecord->tanggal?->format('d-m-Y') ?? '-' }}</div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <label class="text-muted small d-block">Jenis Maintenance</label>
                            <div class="fw-semibold">{{ $maintenanceRecord->jenis ?? '-' }}</div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <label class="text-muted small d-block">Teknisi / Vendor</label>
                            <div class="fw-semibold">{{ $maintenanceRecord->teknisi ?? '-' }}</div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <label class="text-muted small d-block">Estimasi / Biaya</label>
                            <div class="fw-bold text-primary">Rp {{ number_format($maintenanceRecord->biaya ?? 0, 0, ',', '.') }}</div>
                        </div>

                        <div class="col-sm-6 col-md-4">
                            <label class="text-muted small d-block">Status</label>
                            <div class="mt-1">
                                @php
                                    $statusClass = match($maintenanceRecord->status) {
                                        'Terjadwal' => 'badge-status-draft',
                                        'Proses' => 'badge-status-pending',
                                        'Selesai' => 'badge-status-approved',
                                        'Dibatalkan' => 'badge-status-rejected',
                                        default => 'badge-status-draft',
                                    };
                                @endphp
                                <span class="badge-status {{ $statusClass }}">
                                    {{ $maintenanceRecord->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KELUHAN & KERUSAKAN --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle text-warning"></i>
                    <strong>Keluhan dan Kerusakan</strong>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small d-block mb-1">Keluhan Pengguna</label>
                            <div class="p-3 bg-light rounded border text-secondary" style="min-height: 80px;">
                                {{ $maintenanceRecord->keluhan ?? '-' }}
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="text-muted small d-block mb-1">Identifikasi Kerusakan</label>
                            <div class="p-3 bg-light rounded border text-secondary" style="min-height: 80px;">
                                {{ $maintenanceRecord->kerusakan ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- HASIL TINDAKAN --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-clipboard-check text-success"></i>
                    <strong>Hasil Tindakan & Catatan Akhir</strong>
                </div>

                <div class="card-body">
                    <div class="p-3 bg-light rounded border text-secondary" style="min-height: 90px;">
                        {{ $maintenanceRecord->hasil_tindakan ?? 'Belum ada catatan hasil tindakan perbaikan.' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Informasi Aset & Kondisi --}}
        <div class="col-lg-4">
            {{-- INFORMASI ASSET --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-box-seam text-primary"></i>
                        <strong>Informasi Aset</strong>
                    </div>
                    @if($maintenanceRecord->asset)
                    <a href="{{ route('assets.show', $maintenanceRecord->asset) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 11px;">
                        Lihat Aset
                    </a>
                    @endif
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small d-block">Kode Asset</label>
                        <div class="fw-bold fs-6 text-primary">
                            {{ $maintenanceRecord->asset->kode_asset ?? '-' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block">Nama Asset</label>
                        <div class="fw-semibold">
                            {{ $maintenanceRecord->asset->nama_asset ?? '-' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small d-block">Kategori</label>
                        <div>{{ $maintenanceRecord->asset->category->nama_kategori ?? '-' }}</div>
                    </div>

                    <div>
                        <label class="text-muted small d-block">Lokasi Saat Ini</label>
                        <div>{{ $maintenanceRecord->asset->location->nama_lokasi ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- KONDISI ASSET --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-heart-pulse text-info"></i>
                    <strong>Perubahan Kondisi Aset</strong>
                </div>

                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small d-block mb-1">Kondisi Sebelum</label>
                        <div>
                            <span class="badge bg-secondary px-3 py-2">
                                {{ $maintenanceRecord->kondisi_sebelum ?? '-' }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="text-muted small d-block mb-1">Kondisi Sesudah</label>
                        <div>
                            <span class="badge bg-primary px-3 py-2">
                                {{ $maintenanceRecord->kondisi_sesudah ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection