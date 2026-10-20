@extends('layouts.app')

@section('title', $isBorrower ? 'Peminjaman Aset' : 'Data Peminjaman')

@section('content')
<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-start mb-4">
		<div><h3 class="mb-1">{{ $isBorrower ? 'Peminjaman Aset' : 'Data Peminjaman' }}</h3><p class="text-muted mb-0">{{ $isBorrower ? 'Cari aset tersedia atau catat pengembalian.' : 'Pantau aset yang sedang dipinjam.' }}</p></div>
		@if(! $isBorrower)<a href="{{ route('loans.history') }}" class="btn btn-outline-primary">Riwayat Peminjaman</a>@endif
	</div>
	@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
	@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

	@if($isBorrower)
		<form method="GET" action="{{ route('loans.index') }}" class="row g-2 mb-3">
			<div class="col-md-10"><input name="search" class="form-control" value="{{ $search }}" placeholder="Cari kode atau nama aset"></div>
			<div class="col-md-2 d-grid"><button class="btn btn-primary" type="submit">Cari</button></div>
		</form>
		<h5 class="mb-3">Aset Tersedia</h5>
		<div class="table-responsive bg-white border rounded mb-4">
			<table class="table table-hover align-middle mb-0">
				<thead class="table-light"><tr><th>Kode</th><th>Nama Aset</th><th>Kategori</th><th>Lokasi</th><th></th></tr></thead>
				<tbody>
					@forelse($availableAssets as $asset)
						<tr><td>{{ $asset->kode_asset }}</td><td>{{ $asset->nama_asset }}</td><td>{{ $asset->category?->name ?? $asset->category?->nama ?? '-' }}</td><td>{{ $asset->location?->name ?? $asset->location?->nama ?? '-' }}</td><td><a class="btn btn-sm btn-primary" href="{{ route('loans.create', $asset) }}">Pinjam</a></td></tr>
					@empty<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada aset yang tersedia.</td></tr>@endforelse
				</tbody>
			</table>
		</div>
		<div class="mb-4">{{ $availableAssets->links('pagination::bootstrap-5') }}</div>
	@endif

	<h5 class="mb-3">{{ $isBorrower ? 'Peminjaman Aktif Saya' : 'Aset Sedang Dipinjam' }}</h5>
	<div class="table-responsive bg-white border rounded">
		<table class="table table-hover align-middle mb-0">
			<thead class="table-light"><tr><th>Aset</th>@if(! $isBorrower)<th>Peminjam</th>@endif<th>Tanggal Pinjam</th><th>Rencana Kembali</th>@if($isBorrower)<th>Aksi</th>@endif</tr></thead>
			<tbody>
				@forelse($activeLoans as $loan)
					<tr>
						<td>{{ $loan->asset?->kode_asset }} · {{ $loan->asset?->nama_asset }}</td>
						@if(! $isBorrower)<td>{{ $loan->user?->name ?? '-' }}</td>@endif
						<td>{{ $loan->tanggal_pinjam?->format('d-m-Y') }}</td><td>{{ $loan->tanggal_rencana_kembali?->format('d-m-Y') ?? '-' }}</td>
						@if($isBorrower)
							<td><form method="POST" action="{{ route('loans.return', $loan) }}" class="d-flex flex-wrap gap-2">@csrf @method('PUT')<input type="date" name="tanggal_kembali" class="form-control form-control-sm" value="{{ now()->toDateString() }}" required aria-label="Tanggal kembali"><select name="kondisi_sesudah" class="form-select form-select-sm" required aria-label="Kondisi aset"><option value="Baik">Baik</option><option value="Rusak Ringan">Rusak Ringan</option><option value="Rusak Berat">Rusak Berat</option></select><button class="btn btn-sm btn-outline-primary" type="submit">Kembalikan</button></form></td>
						@endif
					</tr>
				@empty
					<tr><td colspan="{{ $isBorrower ? 5 : 4 }}" class="text-center text-muted py-4">Tidak ada aset yang sedang dipinjam.</td></tr>
				@endforelse
			</tbody>
		</table>
	</div>
	<div class="mt-3">{{ $activeLoans->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
