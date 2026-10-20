@extends('layouts.app')

@section('title', 'Manajemen Role')

@section('content')
<div class="container-fluid users-page">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div><h3 class="mb-1">Manajemen Role</h3><p class="text-muted mb-0">Tentukan hak akses setiap pengguna sistem.</p></div>
		<span class="role-help"><i class="bi bi-shield-lock"></i> Admin memiliki akses penuh</span>
	</div>
	@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
	@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
	<div class="card">
		<div class="card-header d-flex justify-content-between align-items-center"><strong>Daftar Pengguna</strong><span class="text-muted">{{ $users->count() }} pengguna</span></div>
		<div class="card-body p-0"><div class="table-responsive"><table class="table mb-0 align-middle"><thead><tr><th>Pengguna</th><th>Email</th><th>Role Aktif</th><th width="220">Atur Role</th></tr></thead><tbody>
			@forelse($users as $user)
				<tr><td><strong>{{ $user->name }}</strong><small class="d-block text-muted">Bergabung {{ $user->created_at?->format('d/m/Y') }}</small></td><td>{{ $user->email }}</td><td><span class="role-badge {{ $user->role }}"><i class="bi {{ $user->role === 'admin' ? 'bi-shield-fill-check' : ($user->role === 'pimpinan' ? 'bi-eye-fill' : 'bi-person-badge') }}"></i> {{ \App\Models\User::ROLES[$user->role] ?? ucfirst($user->role) }}</span></td><td><form method="POST" action="{{ route('users.role.update', $user) }}" class="role-form">@csrf @method('PUT')<select name="role" class="form-select form-select-sm">@foreach(\App\Models\User::ROLES as $role => $label)<option value="{{ $role }}" @selected($user->role === $role)>{{ $label }}</option>@endforeach</select><button class="btn btn-primary btn-sm" type="submit">Simpan</button></form></td></tr>
			@empty
				<tr><td colspan="4" class="text-center text-muted py-4">Belum ada pengguna.</td></tr>
			@endforelse
		</tbody></table></div></div>
	</div>
	<div class="role-info-grid"><div class="role-info admin"><i class="bi bi-shield-fill-check"></i><div><strong>Admin</strong><small>Mengelola user, data master, dan konfigurasi sistem.</small></div></div><div class="role-info staff"><i class="bi bi-tools"></i><div><strong>Staff</strong><small>Mengelola aset, penempatan, maintenance, pemeriksaan, dan riwayat aset.</small></div></div><div class="role-info pimpinan"><i class="bi bi-eye-fill"></i><div><strong>Pimpinan</strong><small>Memantau dashboard, data risiko, laporan, dan approval.</small></div></div><div class="role-info peminjam"><i class="bi bi-person-badge"></i><div><strong>User / Peminjam</strong><small>Mengajukan peminjaman dan pengembalian aset.</small></div></div></div>
</div>
@endsection

@push('styles')
<style>
.users-page { max-width: 1180px; margin: 0 auto; }.role-help { color: #2f6fed; background: #eaf2ff; border-radius: 6px; padding: 8px 10px; font-size: 10px; }.role-form { display: flex; gap: 6px; }.role-form .form-select { min-height: 31px; width: 145px; font-size: 10px; }.role-form .btn { padding: 6px 9px; font-size: 10px; }.role-badge { display: inline-flex; align-items: center; gap: 5px; border-radius: 5px; padding: 5px 8px; font-size: 9px; font-weight: 700; }.role-badge.admin { color: #2864cd; background: #eaf2ff; }.role-badge.staff { color: #a45c13; background: #fff1dc; }.role-badge.pimpinan { color: #139b75; background: #e3f8f1; }.role-badge.peminjam { color: #7852a8; background: #f1eafa; }.role-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px; }.role-info { display: flex; align-items: center; gap: 10px; padding: 13px; border: 1px solid #e7ecf3; border-radius: 8px; background: #fff; }.role-info > i { font-size: 20px; }.role-info strong, .role-info small { display: block; }.role-info strong { font-size: 11px; }.role-info small { margin-top: 4px; color: #8a96a8; font-size: 9px; line-height: 1.5; }.role-info.admin > i { color: #2f6fed; }.role-info.staff > i { color: #dc8a25; }.role-info.pimpinan > i { color: #18b88a; }.role-info.peminjam > i { color: #7852a8; }
@media (max-width: 600px) { .role-help { display: none; }.role-info-grid { grid-template-columns: 1fr; }.role-form .form-select { width: 100px; } }
</style>
@endpush