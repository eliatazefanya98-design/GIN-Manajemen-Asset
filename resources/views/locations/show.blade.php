@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Detail Lokasi</h3>
            <p class="text-muted mb-0">
                Informasi lokasi dan asset yang ditempatkan.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('locations.edit', $location) }}"
               class="btn btn-warning">
                Edit
            </a>

            <a href="{{ route('locations.index') }}"
               class="btn btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="row">

        {{-- INFORMASI LOKASI --}}
        <div class="col-md-5 mb-4">

            <div class="card shadow-sm">

                <div class="card-header">
                    <strong>Informasi Lokasi</strong>
                </div>

                <div class="card-body">

                    <table class="table table-borderless mb-0">

                        <tr>
                            <th width="40%">Nama</th>
                            <td>{{ $location->nama }}</td>
                        </tr>

                        <tr>
                            <th>Gedung</th>
                            <td>{{ $location->gedung ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Lantai</th>
                            <td>{{ $location->lantai ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Ruangan</th>
                            <td>{{ $location->ruangan ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $location->deskripsi ?? '-' }}</td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

        {{-- ASSET --}}
        <div class="col-md-7 mb-4">

            <div class="card shadow-sm">

                <div class="card-header">
                    <strong>Asset di Lokasi Ini</strong>
                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-hover mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th>Kode Asset</th>
                                    <th>Nama Asset</th>
                                    <th>Kondisi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($location->assets as $asset)

                                    <tr>
                                        <td>
                                            {{ $asset->kode_asset }}
                                        </td>

                                        <td>
                                            {{ $asset->nama_asset }}
                                        </td>

                                        <td>
                                            {{ $asset->kondisi }}
                                        </td>

                                        <td>
                                            {{ $asset->status }}
                                        </td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="4"
                                            class="text-center text-muted py-4">
                                            Belum ada asset di lokasi ini.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
@endsection