@extends('layouts.app')

@section('title', 'Ajukan Transfer')

@section('content')
<div class="container-fluid" style="max-width: 900px">
    <div class="mb-4"><h3>Ajukan Transfer</h3><p class="text-muted mb-0">Lokasi aset berubah setelah disetujui Pimpinan.</p></div>
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <form method="POST" action="{{ route('transfers.store') }}" class="card card-body gap-3">
        @csrf
        <div><label for="asset_id" class="form-label">Aset</label><select id="asset_id" name="asset_id" class="form-select" required><option value="">Pilih aset</option>@foreach($assets as $asset)<option value="{{ $asset->id }}" @selected(old('asset_id') == $asset->id)>{{ $asset->kode_asset }} · {{ $asset->nama_asset }} ({{ $asset->location?->name ?? $asset->location?->nama ?? 'Tanpa lokasi' }})</option>@endforeach</select></div>
        <div><label for="target_location_id" class="form-label">Lokasi tujuan</label><select id="target_location_id" name="target_location_id" class="form-select" required><option value="">Pilih lokasi</option>@foreach($locations as $location)<option value="{{ $location->id }}" @selected(old('target_location_id') == $location->id)>{{ $location->name ?? $location->nama }}</option>@endforeach</select></div>
        <div><label for="reason" class="form-label">Alasan transfer</label><textarea id="reason" name="reason" class="form-control" rows="3" maxlength="255" required>{{ old('reason') }}</textarea></div>
        <div class="d-flex gap-2"><button class="btn btn-primary" type="submit">Kirim Pengajuan</button><a class="btn btn-outline-secondary" href="{{ route('transfers.index') }}">Batal</a></div>
    </form>
</div>
@endsection