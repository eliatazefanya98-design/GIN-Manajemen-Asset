@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- =========================================================
     HEADER
========================================================= --}}

<div class="dashboard-header">

    <div>
        <h2>Dashboard</h2>

        <p class="text-muted mb-0">
            Ringkasan Sistem Manajemen Aset
        </p>
    </div>

    <div class="date-info">

        <i class="bi bi-calendar3"></i>

        {{ now()->translatedFormat('l, d F Y') }}

    </div>

</div>


{{-- =========================================================
     STATISTIK ASSET
========================================================= --}}

<div class="row g-3 mb-4">

    {{-- TOTAL ASSET --}}
    <div class="col-xl-3 col-md-6">

        <div class="stat-card">

            <div class="stat-icon blue">
                <i class="bi bi-pc-display"></i>
            </div>

            <div>

                <span>Total Asset</span>

                <h3>
                    {{ $totalAssets }}
                </h3>

                <small>
                    Semua aset terdaftar
                </small>

            </div>

        </div>

    </div>


    {{-- ASSET AKTIF --}}
    <div class="col-xl-3 col-md-6">

        <div class="stat-card">

            <div class="stat-icon green">
                <i class="bi bi-check-circle-fill"></i>
            </div>

            <div>

                <span>Asset Aktif</span>

                <h3>
                    {{ $activeAssets }}
                </h3>

                <small class="text-success">

                    @if($totalAssets > 0)

                        {{ number_format(($activeAssets / $totalAssets) * 100, 1) }}%
                        dari total asset

                    @else

                        0% dari total asset

                    @endif

                </small>

            </div>

        </div>

    </div>


    {{-- MAINTENANCE --}}
    <div class="col-xl-3 col-md-6">

        <div class="stat-card">

            <div class="stat-icon orange">
                <i class="bi bi-tools"></i>
            </div>

            <div>

                <span>Maintenance</span>

                <h3>
                    {{ $maintenanceAssets }}
                </h3>

                <small>
                    Asset dalam maintenance
                </small>

            </div>

        </div>

    </div>


    {{-- RUSAK --}}
    <div class="col-xl-3 col-md-6">

        <div class="stat-card">

            <div class="stat-icon red">
                <i class="bi bi-exclamation-circle-fill"></i>
            </div>

            <div>

                <span>Asset Rusak</span>

                <h3>
                    {{ $damagedAssets }}
                </h3>

                <small>
                    Perlu perhatian
                </small>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     STATISTIK MAINTENANCE
========================================================= --}}

<div class="row g-3 mb-4">

    {{-- TOTAL MAINTENANCE --}}
    <div class="col-xl-3 col-md-6">

        <div class="stat-card">

            <div class="stat-icon blue">
                <i class="bi bi-wrench-adjustable-circle"></i>
            </div>

            <div>

                <span>Total Maintenance</span>

                <h3>
                    {{ $totalMaintenance }}
                </h3>

                <small>
                    Semua catatan maintenance
                </small>

            </div>

        </div>

    </div>


    {{-- TERJADWAL --}}
    <div class="col-xl-3 col-md-6">

        <div class="stat-card">

            <div class="stat-icon orange">
                <i class="bi bi-calendar-event"></i>
            </div>

            <div>

                <span>Terjadwal</span>

                <h3>
                    {{ $scheduledMaintenance }}
                </h3>

                <small>
                    Menunggu proses
                </small>

            </div>

        </div>

    </div>


    {{-- PROSES --}}
    <div class="col-xl-3 col-md-6">

        <div class="stat-card">

            <div class="stat-icon orange">
                <i class="bi bi-arrow-repeat"></i>
            </div>

            <div>

                <span>Dalam Proses</span>

                <h3>
                    {{ $processMaintenance }}
                </h3>

                <small>
                    Sedang ditangani
                </small>

            </div>

        </div>

    </div>


    {{-- SELESAI --}}
    <div class="col-xl-3 col-md-6">

        <div class="stat-card">

            <div class="stat-icon green">
                <i class="bi bi-check2-circle"></i>
            </div>

            <div>

                <span>Selesai</span>

                <h3>
                    {{ $completedMaintenance }}
                </h3>

                <small>
                    Maintenance selesai
                </small>

            </div>

        </div>

    </div>

</div>

@if(in_array(auth()->user()->role, ['admin', 'pimpinan'], true))
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-4">
        <span><strong>{{ $pendingApprovals }}</strong> pengajuan transfer menunggu approval</span>
        <a href="{{ route('approvals.index') }}" class="btn btn-sm btn-outline-primary">Tinjau Approval</a>
    </div>
@endif


{{-- =========================================================
     GRAFIK
========================================================= --}}

<div class="row g-3 mb-4">

    {{-- KATEGORI --}}
    <div class="col-xl-6">

        <div class="dashboard-card">

            <div class="card-header-custom">

                <div>
                    <h5>Distribusi Asset per Kategori</h5>

                    <small class="text-muted">
                        Jumlah asset berdasarkan kategori
                    </small>
                </div>

            </div>

            <div class="chart-container">

                <canvas id="categoryChart"></canvas>

            </div>

        </div>

    </div>


    {{-- STATUS --}}
    <div class="col-xl-6">

        <div class="dashboard-card">

            <div class="card-header-custom">

                <div>
                    <h5>Status Asset</h5>

                    <small class="text-muted">
                        Distribusi status seluruh asset
                    </small>
                </div>

            </div>

            <div class="chart-container">

                <canvas id="statusChart"></canvas>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     BOTTOM
========================================================= --}}

<div class="row g-3">


    {{-- =====================================================
         ASSET TERBARU
    ====================================================== --}}

    <div class="col-xl-8">

        <div class="dashboard-card">

            <div class="card-header-custom">

                <div>

                    <h5>Asset Terbaru</h5>

                    <small class="text-muted">
                        5 asset terakhir yang ditambahkan
                    </small>

                </div>

                <a
                    href="{{ route('assets.index') }}"
                    class="btn btn-sm btn-outline-primary"
                >
                    Lihat Semua
                </a>

            </div>


            <div class="table-responsive">

                <table class="table align-middle">

                    <thead>

                        <tr>

                            <th>Kode</th>

                            <th>Nama Asset</th>

                            <th>Kategori</th>

                            <th>Lokasi</th>

                            <th>Status</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($latestAssets as $asset)

                            <tr>

                                <td>

                                    <strong>
                                        {{ $asset->kode_asset }}
                                    </strong>

                                </td>


                                <td>
                                    {{ $asset->nama_asset }}
                                </td>


                                <td>
                                    {{ $asset->category->nama ?? '-' }}
                                </td>


                                <td>
                                    {{ $asset->location->nama ?? '-' }}
                                </td>


                                <td>

                                    @if($asset->status === 'Aktif')

                                        <span class="badge-status active">
                                            Aktif
                                        </span>

                                    @elseif($asset->status === 'Maintenance')

                                        <span class="badge-status maintenance">
                                            Maintenance
                                        </span>

                                    @elseif($asset->status === 'Rusak')

                                        <span class="badge-status damaged">
                                            Rusak
                                        </span>

                                    @elseif($asset->status === 'Dipinjam')

                                        <span class="badge-status borrowed">
                                            Dipinjam
                                        </span>

                                    @elseif($asset->status === 'Dihapus')

                                        <span class="badge-status deleted">
                                            Dihapus
                                        </span>

                                    @else

                                        <span class="badge-status">
                                            {{ $asset->status }}
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    <a
                                        href="{{ route('assets.show', $asset) }}"
                                        class="btn btn-sm btn-light"
                                        title="Lihat Asset"
                                    >

                                        <i class="bi bi-eye"></i>

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="6"
                                    class="text-center text-muted py-4"
                                >

                                    <i class="bi bi-inbox fs-4 d-block mb-2"></i>

                                    Belum ada data asset.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- =====================================================
         MAINTENANCE TERBARU
    ====================================================== --}}

    <div class="col-xl-4">

        <div class="dashboard-card">

            <div class="card-header-custom">

                <div>

                    <h5>Maintenance Terbaru</h5>

                    <small class="text-muted">
                        Catatan maintenance terakhir
                    </small>

                </div>

                <a
                    href="{{ route('maintenance-records.index') }}"
                    class="btn btn-sm btn-outline-primary"
                >
                    Semua
                </a>

            </div>


            @forelse($latestMaintenance as $maintenance)

                <div class="maintenance-item">

                    <div class="item-icon">

                        <i class="bi bi-tools"></i>

                    </div>


                    <div class="item-content">

                        <strong>

                            {{ $maintenance->asset->nama_asset ?? '-' }}

                        </strong>

                        <small>

                            {{ $maintenance->asset->kode_asset ?? '-' }}

                        </small>

                        <small>

                            {{ $maintenance->jenis ?? 'Maintenance' }}

                        </small>

                    </div>


                    <div class="schedule">

                        <strong>

                            {{ $maintenance->tanggal->format('d/m/Y') }}

                        </strong>

                        <small>

                            {{ $maintenance->status }}

                        </small>

                    </div>

                </div>

            @empty

                <div class="empty-state">

                    <i class="bi bi-tools"></i>

                    <p>
                        Belum ada data maintenance.
                    </p>

                </div>

            @endforelse

        </div>

    </div>

</div>


{{-- =========================================================
     FOOTER
========================================================= --}}

<footer class="dashboard-footer">

    © {{ date('Y') }} PT. Global Intermedia Nusantara.

    All rights reserved.

</footer>


@endsection





{{-- =========================================================
     CHART SCRIPT
========================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {


    /* ==========================================
       GRAFIK KATEGORI
    ========================================== */

    const categoryCtx =
        document.getElementById('categoryChart');

    if (categoryCtx) {

        new Chart(categoryCtx, {

            type: 'doughnut',

            data: {

                labels: @json($categoryLabels),

                datasets: [{

                    data: @json($categoryData),

                    backgroundColor: ['#2f6fed', '#18b88a', '#f2a52b', '#8064d9', '#f04b68'],

                    borderWidth: 0

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {

                        position: 'right',

                        labels: {

                            font: {
                                size: 10
                            },

                            boxWidth: 10

                        }

                    }

                }

            }

        });

    }


    /* ==========================================
       GRAFIK STATUS
    ========================================== */

    const statusCtx =
        document.getElementById('statusChart');

    if (statusCtx) {

        new Chart(statusCtx, {

            type: 'bar',

            data: {

                labels: @json($statusLabels),

                datasets: [{

                    label: 'Jumlah Asset',

                    data: @json($statusData),

                    backgroundColor: ['#2f6fed', '#18b88a', '#f2a52b', '#f04b68', '#aeb8c7'],

                    borderRadius: 5,

                    barThickness: 30

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: false
                    }

                },

                scales: {

                    y: {

                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        }

                    }

                }

            }

        });

    }

});

</script>

@endpush