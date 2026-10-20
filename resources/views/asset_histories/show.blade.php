@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Detail Asset History</h3>
            <p class="text-muted mb-0">
                Informasi lengkap riwayat aktivitas aset
            </p>
        </div>

        <div class="d-flex gap-2">
            <a
                href="{{ route('asset-histories.index') }}"
                class="btn btn-secondary">
                Kembali
            </a>

        </div>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- INFORMASI ASSET --}}
    <div class="card mb-4">

        <div class="card-header">
            <strong>Informasi Asset</strong>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="text-muted">
                        Kode Asset
                    </label>

                    <div class="fw-bold">
                        {{ $history->asset->kode_asset ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="text-muted">
                        Nama Asset
                    </label>

                    <div class="fw-bold">
                        {{ $history->asset->nama_asset ?? '-' }}
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- INFORMASI AKTIVITAS --}}
    <div class="card mb-4">

        <div class="card-header">
            <strong>Informasi Aktivitas</strong>
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="text-muted">
                        Aktivitas
                    </label>

                    <div class="mt-1">

                        <span class="badge bg-primary">
                            {{ $history->aktivitas }}
                        </span>

                    </div>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="text-muted">
                        Tanggal
                    </label>

                    <div>
                        {{ $history->tanggal?->format('d-m-Y H:i:s') ?? '-' }}
                    </div>

                </div>

                <div class="col-md-12 mb-3">

                    <label class="text-muted">
                        Deskripsi
                    </label>

                    <div class="border rounded p-3 mt-1">
                        {{ $history->deskripsi ?? '-' }}
                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- USER --}}
    <div class="card mb-4">

        <div class="card-header">
            <strong>Informasi Pengguna</strong>
        </div>

        <div class="card-body">

            @if($history->user)

                <div>
                    {{ $history->user->name }}
                </div>

                @if($history->user->email)
                    <small class="text-muted">
                        {{ $history->user->email }}
                    </small>
                @endif

            @else

                <span class="text-muted">
                    Tidak ada informasi pengguna
                </span>

            @endif

        </div>

    </div>

</div>
@endsection