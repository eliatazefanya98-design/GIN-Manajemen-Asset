@extends('layouts.app')

@section('title', 'Transfer Aset')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h3 class="mb-1">Transfer Aset</h3><p class="text-muted mb-0">Daftar pengajuan pemindahan aset.</p></div>
        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))<a href="{{ route('transfers.create') }}" class="btn btn-primary">Ajukan Transfer</a>@endif
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="table-responsive bg-white border rounded">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Aset</th><th>Asal</th><th>Tujuan</th><th>Pengaju</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($transfers as $transfer)
                    <tr>
                        <td>{{ $transfer->asset?->kode_asset }} · {{ $transfer->asset?->nama_asset }}</td>
                        <td>{{ $transfer->originLocation?->name ?? $transfer->originLocation?->nama ?? '-' }}</td>
                        <td>{{ $transfer->targetLocation?->name ?? $transfer->targetLocation?->nama ?? '-' }}</td>
                        <td>{{ $transfer->requester?->name ?? '-' }}</td>
                        <td><span class="badge {{ $transfer->status === 'menunggu' ? 'text-bg-warning' : ($transfer->status === 'disetujui' ? 'text-bg-success' : 'text-bg-secondary') }}">{{ ucfirst($transfer->status) }}</span></td>
                        <td><a href="{{ route('transfers.show', $transfer) }}" class="btn btn-sm btn-outline-primary">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pengajuan transfer.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $transfers->links('pagination::bootstrap-5') }}</div>
</div>
@endsection