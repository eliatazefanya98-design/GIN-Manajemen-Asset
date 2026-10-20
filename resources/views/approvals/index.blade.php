@extends('layouts.app')

@section('title', 'Approval')

@section('content')
<div class="container-fluid">
	<div class="mb-4"><h3 class="mb-1">Approval</h3><p class="text-muted mb-0">Pengajuan transfer yang menunggu keputusan.</p></div>
	@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
	@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
	<div class="table-responsive bg-white border rounded">
		<table class="table table-hover align-middle mb-0">
			<thead class="table-light"><tr><th>Aset</th><th>Asal</th><th>Tujuan</th><th>Pengaju</th><th>Alasan</th><th></th></tr></thead>
			<tbody>
				@forelse($transfers as $transfer)
					<tr><td>{{ $transfer->asset?->kode_asset }} · {{ $transfer->asset?->nama_asset }}</td><td>{{ $transfer->originLocation?->name ?? $transfer->originLocation?->nama ?? '-' }}</td><td>{{ $transfer->targetLocation?->name ?? $transfer->targetLocation?->nama ?? '-' }}</td><td>{{ $transfer->requester?->name ?? '-' }}</td><td>{{ $transfer->reason }}</td><td><a class="btn btn-sm btn-primary" href="{{ route('approvals.show', $transfer) }}">Tinjau</a></td></tr>
				@empty
					<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada pengajuan yang menunggu persetujuan.</td></tr>
				@endforelse
			</tbody>
		</table>
	</div>
	<div class="mt-3">{{ $transfers->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
