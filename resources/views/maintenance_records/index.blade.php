@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Maintenance Records</h3>
            <p class="text-muted mb-0">
                Daftar riwayat maintenance aset
            </p>
        </div>

        <a href="{{ route('maintenance-records.create') }}" class="btn btn-primary">
            + Tambah Maintenance
        </a>
    </div>

    {{-- SEARCH --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('maintenance-records.index') }}">
                <div class="row g-2">

                    <div class="col-md-10">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Cari asset, jenis, keluhan, teknisi, atau status..."
                            value="{{ request('search') }}"
                        >
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

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>
        </div>
    @endif

    {{-- ERROR MESSAGE --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>
        </div>
    @endif

    {{-- VALIDATION ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Asset</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Teknisi</th>
                            <th>Biaya</th>
                            <th>Status</th>
                            <th width="18%">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($maintenanceRecords as $record)

                            <tr>

                                {{-- NO --}}
                                <td>
                                    {{ $maintenanceRecords->firstItem() + $loop->index }}
                                </td>

                                {{-- ASSET --}}
                                <td>
                                    @if($record->asset)
                                        <strong>
                                            {{ $record->asset->kode_asset }}
                                        </strong>

                                        <br>

                                        <small class="text-muted">
                                            {{ $record->asset->nama_asset }}
                                        </small>
                                    @else
                                        <span class="text-muted">
                                            Asset tidak ditemukan
                                        </span>
                                    @endif
                                </td>

                                {{-- TANGGAL --}}
                                <td>
                                    {{ $record->tanggal?->format('d-m-Y') }}
                                </td>

                                {{-- JENIS --}}
                                <td>
                                    {{ $record->jenis ?? '-' }}
                                </td>

                                {{-- TEKNISI --}}
                                <td>
                                    {{ $record->teknisi ?? '-' }}
                                </td>

                                {{-- BIAYA --}}
                                <td>
                                    Rp {{ number_format($record->biaya ?? 0, 0, ',', '.') }}
                                </td>

                                {{-- STATUS --}}
                                <td>

                                    @php
                                        $statusClass = match($record->status) {
                                            'Terjadwal' => 'bg-secondary',
                                            'Proses' => 'bg-warning text-dark',
                                            'Selesai' => 'bg-success',
                                            'Dibatalkan' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp

                                    <span class="badge {{ $statusClass }}">
                                        {{ $record->status }}
                                    </span>

                                </td>

                                {{-- AKSI --}}
                                <td>

                                    <div class="d-flex gap-1">

                                        <a
                                            href="{{ route('maintenance-records.show', $record) }}"
                                            class="btn btn-sm btn-info text-white">
                                            Detail
                                        </a>

                                        <a
                                            href="{{ route('maintenance-records.edit', $record) }}"
                                            class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        <form
                                            action="{{ route('maintenance-records.destroy', $record) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus maintenance record ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-danger">
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        Belum ada maintenance record.
                                    </div>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            {{-- PAGINATION --}}
            @if($maintenanceRecords->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $maintenanceRecords->links() }}
                </div>
            @endif

        </div>
    </div>

</div>
@endsection