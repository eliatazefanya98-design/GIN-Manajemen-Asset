@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Asset History</h3>
            <p class="text-muted mb-0">
                Riwayat aktivitas aset
            </p>
        </div>

    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    {{-- SEARCH --}}
    <div class="card mb-4">
        <div class="card-body">

            <form method="GET" action="{{ route('asset-histories.index') }}">

                <div class="row g-2">

                    <div class="col-md-10">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Cari kode asset, nama asset, aktivitas, atau deskripsi..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn btn-secondary">
                            Cari
                        </button>
                    </div>

                </div>

            </form>

        </div>
    </div>

    {{-- TABLE --}}
    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Asset</th>
                            <th>Aktivitas</th>
                            <th>Deskripsi</th>
                            <th>Tanggal</th>
                            <th width="12%">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($histories as $history)

                            <tr>

                                {{-- NO --}}
                                <td>
                                    {{ $histories->firstItem() + $loop->index }}
                                </td>

                                {{-- ASSET --}}
                                <td>

                                    @if($history->asset)

                                        <strong>
                                            {{ $history->asset->kode_asset }}
                                        </strong>

                                        <br>

                                        <small class="text-muted">
                                            {{ $history->asset->nama_asset }}
                                        </small>

                                    @else

                                        <span class="text-muted">
                                            Asset tidak ditemukan
                                        </span>

                                    @endif

                                </td>

                                {{-- AKTIVITAS --}}
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $history->aktivitas }}
                                    </span>
                                </td>

                                {{-- DESKRIPSI --}}
                                <td>
                                    {{ $history->deskripsi ?? '-' }}
                                </td>

                                {{-- TANGGAL --}}
                                <td>
                                    {{ $history->tanggal?->format('d-m-Y H:i') ?? '-' }}
                                </td>

                                {{-- AKSI --}}
                                <td>

                                    <div class="d-flex gap-1">

                                        <a
                                            href="{{ route('asset-histories.show', $history) }}"
                                            class="btn btn-sm btn-info text-white">
                                            Detail
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <span class="text-muted">
                                        Belum ada asset history.
                                    </span>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            @if($histories->hasPages())
                <div class="asset-history-pagination d-flex justify-content-center mt-3">
                    {{ $histories->links('pagination::bootstrap-5') }}
                </div>
            @endif

        </div>

    </div>

</div>
@endsection