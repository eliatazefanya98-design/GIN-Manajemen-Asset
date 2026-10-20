@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Data Vendor</h3>
            <p class="text-muted mb-0">
                Kelola data vendor atau supplier asset.
            </p>
        </div>

        <a href="{{ route('vendors.create') }}" class="btn btn-primary">
            + Tambah Vendor
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

    {{-- SEARCH --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="GET"
                  action="{{ route('vendors.index') }}">

                <div class="row g-2">

                    <div class="col-md-10">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ $search }}"
                            placeholder="Cari nama vendor, kontak, email, atau alamat..."
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
                            <th>Nama Vendor</th>
                            <th>Kontak</th>
                            <th>Email</th>
                            <th>Alamat</th>
                            <th class="text-center">Jumlah Asset</th>
                            <th width="220">Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($vendors as $vendor)

                            <tr>

                                <td>
                                    {{ $vendors->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $vendor->nama }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $vendor->kontak ?? '-' }}
                                </td>

                                <td>
                                    {{ $vendor->email ?? '-' }}
                                </td>

                                <td>
                                    {{ $vendor->alamat ?? '-' }}
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-secondary">
                                        {{ $vendor->assets_count }}
                                    </span>
                                </td>

                                <td>

                                    <div class="d-flex gap-1">

                                        <a href="{{ route('vendors.show', $vendor) }}"
                                           class="btn btn-sm btn-info text-white">
                                            Detail
                                        </a>

                                        <a href="{{ route('vendors.edit', $vendor) }}"
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('vendors.destroy', $vendor) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus vendor ini?')"
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
                                        Belum ada data vendor.
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
    @if($vendors->hasPages())

        <div class="d-flex justify-content-center mt-4">
            {{ $vendors->links() }}
        </div>

    @endif

</div>
@endsection