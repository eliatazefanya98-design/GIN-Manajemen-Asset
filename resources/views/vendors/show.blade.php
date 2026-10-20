@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Detail Vendor</h3>
            <p class="text-muted mb-0">
                Informasi vendor dan asset yang terkait.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('vendors.edit', $vendor) }}"
               class="btn btn-warning">
                Edit
            </a>

            <a href="{{ route('vendors.index') }}"
               class="btn btn-secondary">
                Kembali
            </a>
        </div>
    </div>

    <div class="row">

        {{-- INFORMASI VENDOR --}}
        <div class="col-md-5 mb-4">

            <div class="card shadow-sm">

                <div class="card-header">
                    <strong>Informasi Vendor</strong>
                </div>

                <div class="card-body">

                    <table class="table table-borderless mb-0">

                        <tr>
                            <th width="40%">Nama</th>
                            <td>{{ $vendor->nama }}</td>
                        </tr>

                        <tr>
                            <th>Kontak</th>
                            <td>{{ $vendor->kontak ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Email</th>
                            <td>{{ $vendor->email ?? '-' }}</td>
                        </tr>

                        <tr>
                            <th>Alamat</th>
                            <td>{{ $vendor->alamat ?? '-' }}</td>
                        </tr>

                    </table>

                </div>

            </div>

        </div>

        {{-- ASSET --}}
        <div class="col-md-7 mb-4">

            <div class="card shadow-sm">

                <div class="card-header">
                    <strong>Asset dari Vendor Ini</strong>
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

                                @forelse($vendor->assets as $asset)

                                    <tr>
                                        <td>{{ $asset->kode_asset }}</td>
                                        <td>{{ $asset->nama_asset }}</td>
                                        <td>{{ $asset->kondisi }}</td>
                                        <td>{{ $asset->status }}</td>
                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="4"
                                            class="text-center text-muted py-4">
                                            Belum ada asset dari vendor ini.
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