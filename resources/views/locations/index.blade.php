@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Data Lokasi</h3>
            <p class="text-muted mb-0">
                Kelola lokasi penempatan asset.
            </p>
        </div>

        <a href="{{ route('locations.create') }}" class="btn btn-primary">
            + Tambah Lokasi
        </a>
    </div>

    {{-- ALERT SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ALERT ERROR --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- VALIDATION ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SEARCH --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="GET"
                  action="{{ route('locations.index') }}">

                <div class="row g-2">

                    <div class="col-md-10">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Cari nama, gedung, lantai, atau ruangan..."
                            value="{{ $search }}"
                        >
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit"
                                class="btn btn-outline-primary">
                            Cari
                        </button>
                    </div>

                </div>

            </form>

        </div>
    </div>

    {{-- TABLE --}}
    <div class="card shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th width="60">No</th>
                            <th>Nama Lokasi</th>
                            <th>Gedung</th>
                            <th>Lantai</th>
                            <th>Ruangan</th>
                            <th class="text-center">Jumlah Asset</th>
                            <th width="220">Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($locations as $location)

                            <tr>

                                <td>
                                    {{ $locations->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $location->nama }}
                                    </strong>

                                    @if($location->deskripsi)
                                        <br>
                                        <small class="text-muted">
                                            {{ $location->deskripsi }}
                                        </small>
                                    @endif
                                </td>

                                <td>
                                    {{ $location->gedung ?? '-' }}
                                </td>

                                <td>
                                    {{ $location->lantai ?? '-' }}
                                </td>

                                <td>
                                    {{ $location->ruangan ?? '-' }}
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-secondary">
                                        {{ $location->assets_count }}
                                    </span>
                                </td>

                                <td>

                                    <div class="d-flex gap-1">

                                        <a href="{{ route('locations.show', $location) }}"
                                           class="btn btn-sm btn-info text-white">
                                            Detail
                                        </a>

                                        <a href="{{ route('locations.edit', $location) }}"
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('locations.destroy', $location) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus lokasi ini?')"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger">
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7"
                                    class="text-center py-5">

                                    <div class="text-muted">
                                        Belum ada data lokasi.
                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- PAGINATION --}}
    @if($locations->hasPages())

        <div class="d-flex justify-content-center mt-4">

            {{ $locations->links() }}

        </div>

    @endif

</div>
@endsection