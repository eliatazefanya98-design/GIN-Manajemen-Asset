@extends('layouts.app')

@section('title', 'Tinjau Pengajuan')

@section('content')
<div class="container-fluid" style="max-width: 1000px">
    <div class="d-flex justify-content-between align-items-start mb-4"><div><h3>Tinjau Pengajuan Transfer</h3><p class="text-muted mb-0">{{ $transfer->asset?->kode_asset }} · {{ $transfer->asset?->nama_asset }}</p></div><a class="btn btn-outline-secondary" href="{{ route('approvals.index') }}">Kembali</a></div>
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="card mb-3"><div class="card-body"><dl class="row mb-0">
        <dt class="col-sm-3">Lokasi asal</dt><dd class="col-sm-9">{{ $transfer->originLocation?->name ?? $transfer->originLocation?->nama ?? '-' }}</dd>
        <dt class="col-sm-3">Lokasi tujuan</dt><dd class="col-sm-9">{{ $transfer->targetLocation?->name ?? $transfer->targetLocation?->nama ?? '-' }}</dd>
        <dt class="col-sm-3">Pengaju</dt><dd class="col-sm-9">{{ $transfer->requester?->name ?? '-' }}</dd>
        <dt class="col-sm-3">Alasan</dt><dd class="col-sm-9">{{ $transfer->reason }}</dd>
        <dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{ ucfirst($transfer->status) }}</dd>
    </dl></div></div>
    @if($transfer->status === 'menunggu')
        <div class="d-flex flex-wrap gap-3">
            <form method="POST" action="{{ route('approvals.approve', $transfer) }}">@csrf @method('PUT')<button class="btn btn-success" type="submit">Setujui</button></form>
            <form method="POST" action="{{ route('approvals.reject', $transfer) }}" class="d-flex gap-2 flex-grow-1">@csrf @method('PUT')<input name="decision_note" class="form-control" maxlength="255" placeholder="Alasan penolakan" required><button class="btn btn-danger" type="submit">Tolak</button></form>
        </div>
    @else
        <p class="text-muted">Pengajuan diproses {{ $transfer->decided_at?->format('d-m-Y H:i') }}.</p>
        @if($transfer->decision_note)<p>Catatan: {{ $transfer->decision_note }}</p>@endif
    @endif
</div>
@endsection