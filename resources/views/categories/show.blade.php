@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3>{{ $category->nama }}</h3>
            <p class="text-muted mb-0">
                Detail kategori asset
            </p>
        </div>

        <div>

            <a href="{{ route('categories.edit', $category) }}"
               class="btn btn-warning">
                Edit
            </a>

            <a href="{{ route('categories.index') }}"
               class="btn btn-secondary">
                Kembali
            </a>

        </div>

    </div>

    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <h5>Informasi Kategori</h5>

            <hr>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <strong>Nama</strong>

                    <div>
                        {{ $category->nama }}
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Jumlah Asset</strong>

                    <div>
                        {{ $category->assets->count() }}
                    </div>
                </div>

                <div class="col-12">
                    <strong>Deskripsi</strong>

                    <div>
                        {{ $category->deskripsi ?: '-' }}
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <h5>Asset dalam Kategori</h5>

            <hr>

            @if($category->assets->count())

                <div class="table-responsive">

                    <table class="table table-hover">

                        <thead>
                            <tr>
                                <th>Kode Asset</th>
                                <th>Nama Asset</th>
                                <th>Merk</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($category->assets as $asset)

                                <tr>

                                    <td>
                                        {{ $asset->kode_asset }}
                                    </td>

                                    <td>
                                        {{ $asset->nama_asset }}
                                    </td>

                                    <td>
                                        {{ $asset->merk ?: '-' }}
                                    </td>

                                    <td>
                                        {{ $asset->kondisi }}
                                    </td>

                                    <td>
                                        {{ $asset->status }}
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <p class="text-muted mb-0">
                    Belum ada asset dalam kategori ini.
                </p>

            @endif

        </div>

    </div>

</div>
@endsection