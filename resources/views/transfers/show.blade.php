@extends('layouts.app')

@section('title', 'Detail Transfer')

@section('content')
<div class="container-fluid" style="max-width: 1000px">
    <div class="d-flex justify-content-between align-items-start mb-4"><div><h3>Detail Transfer</h3><p class="text-muted mb-0">{{ $transfer->asset?->kode_asset }} · {{ $transfer->asset?->nama_asset }}</p></div><a class="btn btn-outline-secondary" href="{{ route('transfers.index') }}">Kembali</a></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card"><div class="card-body"><dl class="row mb-0">
        <dt class="col-sm-3">Lokasi asal</dt><dd class="col-sm-9">{{ $transfer->originLocation?->name ?? $transfer->originLocation?->nama ?? '-' }}</dd>
        <dt class="col-sm-3">Lokasi tujuan</dt><dd class="col-sm-9">{{ $transfer->targetLocation?->name ?? $transfer->targetLocation?->nama ?? '-' }}</dd>
        <dt class="col-sm-3">Pengaju</dt><dd class="col-sm-9">{{ $transfer->requester?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Alasan</dt><dd class="col-sm-9">{{ $transfer->reason }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{ ucfirst($transfer->status) }}</dd>
        @if($transfer->decision_note)<dt class="col-sm-3">Catatan keputusan</dt><dd class="col-sm-9">{{ $transfer->decision_note }}</dd>@endif
    </dl></div></div>
</div>
@endsection