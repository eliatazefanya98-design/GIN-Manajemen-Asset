@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-1">Detail Asset</h3>
            <p class="text-muted mb-0">
                Informasi lengkap mengenai asset.
            </p>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('assets.index') }}"
               class="btn btn-secondary">
                Kembali
            </a>

            <a href="{{ route('assets.edit', $asset) }}"
               class="btn btn-warning">
                Edit
            </a>

        </div>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- INFORMASI UTAMA --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <strong>Informasi Asset</strong>
        </div>

        <div class="card-body">

            <div class="row">

                {{-- KODE --}}
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">
                        Kode Asset
                    </label>

                    <div class="fw-bold">
                        {{ $asset->kode_asset }}
                    </div>
                </div>


                {{-- NAMA --}}
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">
                        Nama Asset
                    </label>

                    <div class="fw-bold">
                        {{ $asset->nama_asset }}
                    </div>
                </div>


                {{-- KATEGORI --}}
                <div class="col-md-4 mb-3">
                    <label class="text-muted small">
                        Kategori
                    </label>

                    <div>
                        {{ $asset->category?->nama ?? '-' }}
                    </div>
                </div>


                {{-- LOKASI --}}
                <div class="col-md-4 mb-3">
                    <label class="text-muted small">
                        Lokasi
                    </label>

                    <div>
                        {{ $asset->location?->nama ?? '-' }}

                        @if($asset->location?->ruangan)
                            <br>
                            <small class="text-muted">
                                Ruangan: {{ $asset->location->ruangan }}
                            </small>
                        @endif
                    </div>
                </div>


                {{-- VENDOR --}}
                <div class="col-md-4 mb-3">
                    <label class="text-muted small">
                        Vendor
                    </label>

                    <div>
                        {{ $asset->vendor?->nama ?? '-' }}
                    </div>
                </div>


                {{-- MERK --}}
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">
                        Merk
                    </label>

                    <div>
                        {{ $asset->merk ?? '-' }}
                    </div>
                </div>


                {{-- TIPE --}}
                <div class="col-md-6 mb-3">
                    <label class="text-muted small">
                        Tipe
                    </label>

                    <div>
                        {{ $asset->tipe ?? '-' }}
                    </div>
                </div>


                {{-- TANGGAL PEROLEHAN --}}
                <div class="col-md-4 mb-3">
                    <label class="text-muted small">
                        Tanggal Perolehan
                    </label>

                    <div>
                        {{ $asset->tanggal_perolehan?->format('d-m-Y') ?? '-' }}
                    </div>
                </div>


                {{-- HARGA --}}
                <div class="col-md-4 mb-3">
                    <label class="text-muted small">
                        Harga
                    </label>

                    <div>
                        @if($asset->harga !== null)
                            Rp {{ number_format((float) $asset->harga, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </div>
                </div>


                {{-- PENANGGUNG JAWAB --}}
                <div class="col-md-4 mb-3">
                    <label class="text-muted small">
                        Penanggung Jawab
                    </label>

                    <div>
                        {{ $asset->penanggung_jawab ?? '-' }}
                    </div>
                </div>


                {{-- KONDISI --}}
                <div class="col-md-6 mb-3">

                    <label class="text-muted small">
                        Kondisi
                    </label>

                    <div>
                        @if($asset->kondisi === 'Baik')
                            <span class="badge bg-success">
                                Baik
                            </span>
                        @elseif($asset->kondisi === 'Rusak Ringan')
                            <span class="badge bg-warning text-dark">
                                Rusak Ringan
                            </span>
                        @elseif($asset->kondisi === 'Rusak Berat')
                            <span class="badge bg-danger">
                                Rusak Berat
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                {{ $asset->kondisi }}
                            </span>
                        @endif
                    </div>

                </div>


                {{-- STATUS --}}
                <div class="col-md-6 mb-3">

                    <label class="text-muted small">
                        Status
                    </label>

                    <div>
                        @if($asset->status === 'Aktif')
                            <span class="badge bg-success">
                                Aktif
                            </span>
                        @elseif($asset->status === 'Maintenance')
                            <span class="badge bg-warning text-dark">
                                Maintenance
                            </span>
                        @elseif($asset->status === 'Dipinjam')
                            <span class="badge bg-info text-dark">
                                Dipinjam
                            </span>
                        @elseif($asset->status === 'Rusak')
                            <span class="badge bg-danger">
                                Rusak
                            </span>
                        @elseif($asset->status === 'Dihapus')
                            <span class="badge bg-secondary">
                                Dihapus
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                {{ $asset->status }}
                            </span>
                        @endif
                    </div>

                </div>


                {{-- DESKRIPSI --}}
                <div class="col-12 mb-3">

                    <label class="text-muted small">
                        Deskripsi
                    </label>

                    <div>
                        {{ $asset->deskripsi ?? '-' }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- RIWAYAT MAINTENANCE --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <strong>Riwayat Maintenance</strong>
        </div>

        <div class="card-body">

            @if($asset->maintenanceRecords->count())

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th>Teknisi</th>
                                <th>Biaya</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($asset->maintenanceRecords as $maintenance)

                                <tr>

                                    <td>
                                        {{ $maintenance->tanggal?->format('d-m-Y') ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $maintenance->jenis ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $maintenance->status }}
                                    </td>

                                    <td>
                                        {{ $maintenance->teknisi ?? '-' }}
                                    </td>

                                    <td>
                                        Rp {{ number_format((float) $maintenance->biaya, 0, ',', '.') }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <p class="text-muted mb-0">
                    Belum ada riwayat maintenance.
                </p>

            @endif

        </div>

    </div>


    {{-- RIWAYAT ASSET --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <strong>Riwayat Asset</strong>
        </div>

        <div class="card-body">

            @if($asset->histories->count())

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Aktivitas</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($asset->histories as $history)

                                <tr>

                                    <td>
                                        {{ $history->tanggal?->format('d-m-Y H:i') ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $history->aktivitas }}
                                    </td>

                                    <td>
                                        {{ $history->deskripsi ?? '-' }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <p class="text-muted mb-0">
                    Belum ada riwayat asset.
                </p>

            @endif

        </div>

    </div>


    {{-- AI PREDICTIONS --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <strong>Prediksi AI</strong>
        </div>

        <div class="card-body">

            @if($asset->aiPredictions->count())

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead>
                            <tr>
                                <th>Prediksi</th>
                                <th>Risk Score</th>
                                <th>Kategori Risiko</th>
                                <th>Model</th>
                                <th>Waktu Prediksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($asset->aiPredictions as $prediction)

                                <tr>

                                    <td>
                                        {{ $prediction->prediction ? 'Perlu Maintenance' : 'Tidak Perlu Maintenance' }}
                                    </td>

                                    <td>
                                        {{ $prediction->risk_score }}
                                    </td>

                                    <td>
                                        {{ $prediction->risk_category }}
                                    </td>

                                    <td>
                                        {{ $prediction->model_name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $prediction->predicted_at?->format('d-m-Y H:i') ?? '-' }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <p class="text-muted mb-0">
                    Belum ada hasil prediksi AI untuk asset ini.
                </p>

            @endif

        </div>

    </div>

</div>
@endsection