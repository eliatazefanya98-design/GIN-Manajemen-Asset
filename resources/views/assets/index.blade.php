@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h3 class="mb-1">{{ auth()->user()->role === 'peminjam' ? 'Aset Tersedia' : 'Daftar Asset' }}</h3>
            <p class="text-muted mb-0">
                {{ auth()->user()->role === 'peminjam' ? 'Aset yang dapat diajukan untuk dipinjam.' : 'Kelola seluruh asset yang terdaftar dalam sistem.' }}
            </p>
        </div>

        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
        <a href="{{ route('assets.create') }}"
           class="btn btn-primary">
            + Tambah Asset
        </a>
        @endif

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- ERROR MESSAGE --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    {{-- SEARCH --}}
    <div class="card shadow-sm mb-4">

        <div class="card-body">

            <form action="{{ route('assets.index') }}"
                  method="GET">

                <div class="row g-2">

                    <div class="col-md-10">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ $search }}"
                            placeholder="Cari kode, nama, merk, tipe, kategori, lokasi, vendor..."
                        >

                    </div>

                    <div class="col-md-2">

                        <button type="submit"
                                class="btn btn-primary w-100">
                            Cari
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- TABLE --}}
    <div class="card shadow-sm">

        <div class="card-header d-flex justify-content-between align-items-center">

            <strong>Data Asset</strong>

            <span class="text-muted">
                Total: {{ $assets->total() }} asset
            </span>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-bordered mb-0">

                    <thead class="table-light">

                        <tr>

                            <th width="50">
                                #
                            </th>

                            <th>
                                Kode Asset
                            </th>

                            <th>
                                Nama Asset
                            </th>

                            <th>
                                Kategori
                            </th>

                            <th>
                                Lokasi
                            </th>

                            <th>
                                Vendor
                            </th>

                            <th>
                                Kondisi
                            </th>

                            <th>
                                Status
                            </th>

                            <th width="180">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($assets as $asset)

                            <tr>

                                <td>
                                    {{ $assets->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $asset->kode_asset }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $asset->nama_asset }}
                                </td>

                                <td>
                                    {{ $asset->category?->nama ?? '-' }}
                                </td>

                                <td>
                                    {{ $asset->location?->nama ?? '-' }}

                                    @if($asset->location?->ruangan)
                                        <br>
                                        <small class="text-muted">
                                            {{ $asset->location->ruangan }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    {{ $asset->vendor?->nama ?? '-' }}
                                </td>

                                {{-- KONDISI --}}
                                <td>

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

                                </td>


                                {{-- STATUS --}}
                                <td>

                                    @if($asset->status === 'Aktif')

                                        <span class="badge bg-success">
                                            Aktif
                                        </span>

                                    @elseif($asset->status === 'Dipinjam')

                                        <span class="badge bg-info text-dark">
                                            Dipinjam
                                        </span>

                                    @elseif($asset->status === 'Maintenance')

                                        <span class="badge bg-warning text-dark">
                                            Maintenance
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

                                </td>


                                {{-- AKSI --}}
                                <td>

                                    <div class="d-flex gap-1">

                                        <a href="{{ route('assets.show', $asset) }}"
                                           class="btn btn-sm btn-info text-white">
                                            Detail
                                        </a>

                                        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
                                        <a href="{{ route('assets.edit', $asset) }}"
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        <form action="{{ route('assets.destroy', $asset) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus asset ini?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger">
                                                Hapus
                                            </button>

                                        </form>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9"
                                    class="text-center py-4 text-muted">

                                    @if($search)
                                        Tidak ditemukan asset dengan kata kunci
                                        "<strong>{{ $search }}</strong>".
                                    @else
                                        Belum ada data asset.
                                    @endif

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- PAGINATION --}}
        @if($assets->hasPages())

            <div class="card-footer">

                <div class="d-flex justify-content-center">

                    {{ $assets->links() }}

                </div>

            </div>

        @endif

    </div>

</div>
@endsection