@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')
<div class="container-fluid" style="max-width: 900px">
    <div class="mb-4"><h3>Ajukan Peminjaman</h3><p class="text-muted mb-0">{{ $asset->kode_asset }} · {{ $asset->nama_asset }}</p></div>
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('loans.store') }}" class="card card-body gap-3">
        @csrf
        <input type="hidden" name="asset_id" value="{{ $asset->id }}">
        <div><label for="tanggal_pinjam" class="form-label">Tanggal pinjam</label><input id="tanggal_pinjam" name="tanggal_pinjam" type="date" class="form-control" value="{{ old('tanggal_pinjam', now()->toDateString()) }}" required></div>
        <div><label for="tanggal_rencana_kembali" class="form-label">Rencana kembali</label><input id="tanggal_rencana_kembali" name="tanggal_rencana_kembali" type="date" class="form-control" min="{{ old('tanggal_pinjam', now()->toDateString()) }}" value="{{ old('tanggal_rencana_kembali') }}" required></div>
        <div><label for="keperluan" class="form-label">Keperluan</label><textarea id="keperluan" name="keperluan" class="form-control" rows="3" maxlength="255" required>{{ old('keperluan') }}</textarea></div>
        <div class="d-flex gap-2"><button class="btn btn-primary" type="submit">Ajukan Peminjaman</button><a href="{{ route('loans.index') }}" class="btn btn-outline-secondary">Batal</a></div>
    </form>
</div>
@endsection