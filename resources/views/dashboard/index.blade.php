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
     STYLE
========================================================= --}}

@push('styles')

<style>

/* ==========================================
   HEADER
========================================== */

.dashboard-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 20px;

}

.dashboard-header h2 {

    font-size: 25px;

    font-weight: 700;

    margin: 0;

}

.dashboard-header p {

    font-size: 12px;

    margin-top: 4px;

}

.date-info {

    color: #697586;

    font-size: 12px;

}


/* ==========================================
   STAT CARD
========================================== */

.stat-card {

    background: white;

    border: 1px solid #edf0f5;

    border-radius: 10px;

    padding: 20px;

    display: flex;

    align-items: center;

    gap: 16px;

    min-height: 120px;

}

.stat-icon {

    width: 56px;

    height: 56px;

    min-width: 56px;

    border-radius: 10px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 25px;

}

.stat-icon.blue {

    background: #e7f0ff;

    color: #0d6efd;

}

.stat-icon.green {

    background: #e4f8ed;

    color: #20b86b;

}

.stat-icon.orange {

    background: #fff0df;

    color: #f39c32;

}

.stat-icon.red {

    background: #ffe7e7;

    color: #e84c4c;

}

.stat-card span {

    display: block;

    color: #536174;

    font-size: 13px;

}

.stat-card h3 {

    margin: 3px 0;

    font-size: 27px;

    font-weight: 700;

}

.stat-card small {

    color: #8993a3;

    font-size: 10px;

}


/* ==========================================
   DASHBOARD CARD
========================================== */

.dashboard-card {

    background: white;

    border: 1px solid #edf0f5;

    border-radius: 10px;

    padding: 18px;

}

.card-header-custom {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-bottom: 15px;

}

.card-header-custom h5 {

    margin: 0;

    font-size: 14px;

    font-weight: 700;

}

.card-header-custom small {

    font-size: 10px;

}


/* ==========================================
   CHART
========================================== */

.chart-container {

    height: 260px;

    position: relative;

}


/* ==========================================
   TABLE
========================================== */

.table {

    font-size: 11px;

    margin-bottom: 0;

}

.table thead th {

    background: #f8fafc;

    color: #4d5969;

    font-size: 10px;

    white-space: nowrap;

}

.table tbody td {

    white-space: nowrap;

    color: #465365;

}


/* ==========================================
   STATUS
========================================== */

.badge-status {

    display: inline-block;

    padding: 5px 9px;

    border-radius: 6px;

    font-size: 10px;

    background: #f0f2f5;

    color: #687589;

}

.badge-status.active {

    background: #dff7e9;

    color: #1d9b58;

}

.badge-status.maintenance {

    background: #fff0dd;

    color: #dc8b23;

}

.badge-status.damaged {

    background: #ffe7e7;

    color: #e84c4c;

}

.badge-status.borrowed {

    background: #e7f0ff;

    color: #1670d8;

}

.badge-status.deleted {

    background: #eeeeee;

    color: #777777;

}


/* ==========================================
   MAINTENANCE
========================================== */

.maintenance-item {

    display: flex;

    align-items: center;

    gap: 10px;

    padding: 12px 0;

    border-bottom: 1px solid #f0f2f5;

}

.maintenance-item:last-child {

    border-bottom: none;

}

.item-icon {

    width: 38px;

    height: 38px;

    min-width: 38px;

    border-radius: 8px;

    background: #f3f6fa;

    display: flex;

    align-items: center;

    justify-content: center;

    color: #62738a;

}

.item-content {

    flex: 1;

    min-width: 0;

}

.item-content strong {

    display: block;

    font-size: 11px;

}

.item-content small {

    display: block;

    font-size: 9px;

    color: #8a94a3;

    margin-top: 3px;

}

.schedule {

    text-align: right;

}

.schedule strong {

    display: block;

    font-size: 10px;

    color: #1670d8;

}

.schedule small {

    display: block;

    font-size: 9px;

    color: #687589;

    margin-top: 3px;

}


/* ==========================================
   EMPTY STATE
========================================== */

.empty-state {

    text-align: center;

    padding: 30px 10px;

    color: #8993a3;

}

.empty-state i {

    font-size: 25px;

    display: block;

    margin-bottom: 8px;

}

.empty-state p {

    font-size: 11px;

    margin: 0;

}


/* ==========================================
   FOOTER
========================================== */

.dashboard-footer {

    text-align: center;

    color: #8993a3;

    font-size: 11px;

    padding: 30px 0 10px;

}


/* ==========================================
   RESPONSIVE
========================================== */

@media (max-width: 768px) {

    .dashboard-header {

        align-items: flex-start;

        flex-direction: column;

        gap: 10px;

    }

}

/* Dashboard visual system */
body {
    background: #f6f8fc;
    color: #23304a;
}

.content-wrapper {
    padding: 18px 22px 10px;
}

.sidebar {
    width: 220px;
    background: #ffffff;
    color: #63708a;
    border-right: 1px solid #e8edf5;
    box-shadow: 4px 0 18px rgba(43, 63, 94, .025);
}

.sidebar-brand {
    height: 63px;
    padding: 13px 17px;
    border-bottom: 1px solid #edf1f6;
}

.brand-logo {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: #eaf2ff;
    color: #2563eb;
    font-size: 15px;
}

.sidebar-brand h5 {
    color: #1f2c45;
    font-size: 14px;
    letter-spacing: .02em;
}

.sidebar-brand span {
    color: #9aa6b8;
    font-size: 6px;
}

.sidebar-menu {
    padding: 14px 9px;
}

.menu-title {
    margin: 17px 10px 7px;
    color: #a1adbd;
    font-size: 8px;
    letter-spacing: .06em;
}

.menu-item {
    gap: 10px;
    padding: 8px 10px;
    color: #77849a;
    border-radius: 6px;
    font-size: 10px;
}

.menu-item i {
    width: 16px;
    color: #9aa8bb;
    font-size: 12px;
}

.menu-item:hover {
    background: #f3f7ff;
    color: #2563eb;
}

.menu-item.active {
    background: #eaf2ff;
    color: #2563eb;
    box-shadow: none;
    font-weight: 600;
}

.menu-item.active i {
    color: #2563eb;
}

.main-content {
    margin-left: 220px;
}

.top-navbar {
    height: 63px;
    padding: 0 22px;
    border-bottom-color: #e8edf5;
}

.sidebar-toggle {
    font-size: 17px;
    color: #7c899c;
}

.navbar-right {
    gap: 14px;
}

.search-box {
    width: 280px;
    height: 32px;
    border-color: #e5eaf2;
    border-radius: 6px;
}

.search-box input {
    font-size: 10px;
}

.notification-button {
    width: 30px;
    height: 30px;
    border: 0;
    color: #66758d;
}

.notification-button span {
    width: 13px;
    height: 13px;
    top: -2px;
    right: -2px;
    font-size: 7px;
}

.user-avatar {
    width: 28px;
    height: 28px;
    background: #e8ddff;
    color: #7b51d2;
    font-size: 12px;
}

.user-info strong {
    color: #34415a;
    font-size: 10px;
}

.user-info small {
    font-size: 8px;
}

.logout-button {
    border: 0;
    padding: 5px;
    color: #8190a4;
}

.logout-button:hover {
    background: #f4f7fb;
    color: #2563eb;
}

.dashboard-header {
    margin-bottom: 15px;
}

.dashboard-header h2 {
    color: #23304a;
    font-size: 17px;
    letter-spacing: -.02em;
}

.dashboard-header p,
.date-info {
    color: #8b98aa;
    font-size: 9px;
}

.date-info {
    padding: 7px 10px;
    background: #ffffff;
    border: 1px solid #e7ecf3;
    border-radius: 5px;
}

.row.g-3 {
    --bs-gutter-x: .7rem;
    --bs-gutter-y: .7rem;
}

.stat-card {
    min-height: 82px;
    padding: 13px;
    gap: 11px;
    border-color: #e9eef5;
    border-radius: 7px;
    box-shadow: 0 3px 12px rgba(48, 67, 96, .025);
}

.stat-icon {
    width: 31px;
    height: 31px;
    min-width: 31px;
    border-radius: 6px;
    font-size: 14px;
}

.stat-card span {
    color: #60708a;
    font-size: 9px;
}

.stat-card h3 {
    margin: 2px 0;
    color: #273650;
    font-size: 18px;
}

.stat-card small {
    color: #a0aaba;
    font-size: 7px;
}

.dashboard-card {
    padding: 13px;
    border-color: #e9eef5;
    border-radius: 7px;
    box-shadow: 0 3px 12px rgba(48, 67, 96, .025);
}

.card-header-custom {
    margin-bottom: 9px;
}

.card-header-custom h5 {
    color: #2b3953;
    font-size: 11px;
}

.card-header-custom small {
    color: #9aa6b7 !important;
    font-size: 8px;
}

.card-header-custom .btn {
    padding: 3px 7px;
    border-radius: 4px;
    font-size: 8px;
}

.chart-container {
    height: 175px;
}

.table {
    font-size: 9px;
}

.table thead th {
    padding: 8px 7px;
    color: #8491a5;
    background: #fbfcfe;
    font-size: 8px;
    font-weight: 600;
    border-bottom-color: #edf1f6;
}

.table tbody td {
    padding: 8px 7px;
    color: #5f6d82;
    border-bottom-color: #f0f3f7;
}

.table tbody tr:last-child td {
    border-bottom: 0;
}

.badge-status {
    padding: 3px 6px;
    border-radius: 4px;
    font-size: 8px;
}

.maintenance-item {
    gap: 8px;
    padding: 9px 0;
}

.item-icon {
    width: 28px;
    height: 28px;
    min-width: 28px;
    border-radius: 6px;
    font-size: 11px;
}

.item-content strong,
.schedule strong {
    font-size: 9px;
}

.item-content small,
.schedule small {
    font-size: 8px;
}

.dashboard-footer {
    padding: 18px 0 3px;
    color: #9ba7b8;
    font-size: 8px;
}

/* Reference palette */
:root {
    --dashboard-bg: #f5f7fb;
    --dashboard-blue: #2f6fed;
    --dashboard-blue-soft: #eaf2ff;
    --dashboard-border: #e7ecf3;
    --dashboard-green: #18b88a;
    --dashboard-orange: #f2a52b;
    --dashboard-red: #f04b68;
}

body {
    background: var(--dashboard-bg);
}

.brand-logo {
    background: var(--dashboard-blue-soft);
    color: var(--dashboard-blue);
}

.menu-item.active {
    background: var(--dashboard-blue-soft);
    color: var(--dashboard-blue);
}

.menu-item.active i,
.menu-item:hover {
    color: var(--dashboard-blue);
}

.stat-card,
.dashboard-card,
.date-info {
    border-color: var(--dashboard-border);
}

.stat-icon.blue {
    background: var(--dashboard-blue-soft);
    color: var(--dashboard-blue);
}

.stat-icon.green {
    background: #e3f8f1;
    color: var(--dashboard-green);
}

.stat-icon.orange {
    background: #fff3df;
    color: var(--dashboard-orange);
}

.stat-icon.red {
    background: #ffebef;
    color: var(--dashboard-red);
}

.badge-status.active {
    background: #e3f8f1;
    color: #139b75;
}

.badge-status.maintenance {
    background: #fff3df;
    color: #cb8510;
}

.badge-status.damaged {
    background: #ffebef;
    color: #d63b59;
}

.badge-status.borrowed {
    background: var(--dashboard-blue-soft);
    color: #2864cd;
}

.card-header-custom .btn-outline-primary {
    color: var(--dashboard-blue);
    border-color: #cbdcff;
}

.card-header-custom .btn-outline-primary:hover {
    color: #fff;
    background: var(--dashboard-blue);
    border-color: var(--dashboard-blue);
}

@media (max-width: 991px) {
    .sidebar {
        display: block;
        width: 180px;
    }

    .main-content {
        margin-left: 180px;
    }
}

@media (max-width: 768px) {
    .sidebar {
        display: block;
        width: 96px;
    }

    .main-content {
        margin-left: 96px;
    }

    .content-wrapper {
        padding: 9px;
    }

    .search-box {
        display: none;
    }

    .user-info {
        display: none;
    }

    .sidebar-brand {
        height: 48px;
        gap: 4px;
        padding: 8px 6px;
    }

    .brand-logo {
        width: 22px;
        height: 22px;
        font-size: 11px;
    }

    .sidebar-brand h5 {
        font-size: 10px;
    }

    .sidebar-brand span {
        font-size: 3px;
    }

    .sidebar-menu {
        padding: 8px 4px;
    }

    .menu-title {
        margin: 10px 4px 4px;
        font-size: 6px;
    }

    .menu-item {
        gap: 4px;
        padding: 5px 4px;
        font-size: 7px;
    }

    .menu-item i {
        width: 11px;
        font-size: 8px;
    }

    .menu-disabled small {
        font-size: 5px;
        padding: 1px 2px;
    }

    .top-navbar {
        height: 48px;
        padding: 0 9px;
    }

    .sidebar-toggle {
        font-size: 14px;
    }

    .navbar-right {
        gap: 4px;
    }

    .notification-button {
        width: 24px;
        height: 24px;
    }

    .user-avatar {
        width: 22px;
        height: 22px;
        font-size: 9px;
    }

    .logout-button {
        font-size: 9px;
        padding: 3px;
    }

    .row.g-3 > .col-md-6 {
        width: 25%;
    }

    .row.g-3 > .col-xl-6 {
        width: 50%;
    }

    .row.g-3 > .col-xl-8 {
        width: 66.666667%;
    }

    .row.g-3 > .col-xl-4 {
        width: 33.333333%;
    }

    .stat-card {
        min-height: 58px;
        padding: 7px;
        gap: 5px;
    }

    .stat-icon {
        width: 20px;
        height: 20px;
        min-width: 20px;
        font-size: 9px;
    }

    .stat-card span {
        font-size: 6px;
    }

    .stat-card h3 {
        font-size: 12px;
    }

    .stat-card small {
        font-size: 5px;
    }

    .dashboard-card {
        padding: 7px;
    }

    .card-header-custom h5 {
        font-size: 7px;
    }

    .card-header-custom small {
        font-size: 5px;
    }

    .chart-container {
        height: 100px;
    }

    .table {
        font-size: 6px;
    }

    .table thead th,
    .table tbody td {
        padding: 4px 3px;
        font-size: 5px;
    }

    .dashboard-header {
        margin-bottom: 12px;
    }
}

</style>

@endpush


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