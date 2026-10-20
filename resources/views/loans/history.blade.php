@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start mb-4"><div><h3 class="mb-1">Riwayat Peminjaman</h3><p class="text-muted mb-0">Daftar peminjaman dan pengembalian aset.</p></div><a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">Kembali</a></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="table-responsive bg-white border rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Aset</th>@if(auth()->user()->role !== 'peminjam')<th>Peminjam</th>@endif<th>Tanggal Pinjam</th><th>Rencana Kembali</th><th>Tanggal Kembali</th><th>Kondisi Kembali</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($loans as $loan)
                    <tr><td>{{ $loan->asset?->kode_asset }} · {{ $loan->asset?->nama_asset }}</td>@if(auth()->user()->role !== 'peminjam')<td>{{ $loan->user?->name ?? '-' }}</td>@endif<td>{{ $loan->tanggal_pinjam?->format('d-m-Y') }}</td><td>{{ $loan->tanggal_rencana_kembali?->format('d-m-Y') ?? '-' }}</td><td>{{ $loan->tanggal_kembali?->format('d-m-Y') ?? '-' }}</td><td>{{ $loan->kondisi_sesudah ?? '-' }}</td><td>{{ $loan->status }}</td></tr>
                @empty<tr><td colspan="7" class="text-center text-muted py-4">Belum ada riwayat peminjaman.</td></tr>@endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $loans->links('pagination::bootstrap-5') }}</div>
</div>
@endsection