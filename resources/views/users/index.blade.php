@extends('layouts.app')

@section('title', 'Manajemen Role')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Manajemen Role Pengguna</h2>
            <p class="text-muted mb-0">Tentukan dan kelola hak akses setiap pengguna dalam sistem manajemen aset.</p>
        </div>

        <div class="date-info">
            <i class="bi bi-shield-lock-fill text-primary"></i>
            <span>Hak Akses Berbasis Peran (RBAC)</span>
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ $errors->first() }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- TABLE CARD --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong class="d-inline-flex align-items-center gap-2">
                    <i class="bi bi-people-fill text-primary"></i> Daftar Pengguna Sistem
                </strong>
                <small class="text-muted ms-2">Total: {{ $users->count() }} pengguna</small>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th>Email</th>
                            <th>Role Saat Ini</th>
                            <th width="240">Atur Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar" style="width: 32px; height: 32px; font-size: 14px;">
                                            @if($user->profile_photo_path)
                                                <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}">
                                            @else
                                                <i class="bi bi-person-fill"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            <small class="d-block text-muted">Bergabung {{ $user->created_at?->format('d/m/Y') }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge {{ $user->role === 'admin' ? 'bg-primary' : ($user->role === 'pimpinan' ? 'bg-success' : ($user->role === 'staff' ? 'bg-warning' : 'bg-info')) }}">
                                        <i class="bi {{ $user->role === 'admin' ? 'bi-shield-fill-check' : ($user->role === 'pimpinan' ? 'bi-eye-fill' : 'bi-person-badge') }} me-1"></i>
                                        {{ \App\Models\User::ROLES[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('users.role.update', $user) }}" class="d-flex gap-2 align-items-center">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" class="form-select form-select-sm" style="min-width: 130px;">
                                            @foreach(\App\Models\User::ROLES as $role => $label)
                                                <option value="{{ $role }}" @selected($user->role === $role)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-primary btn-sm text-nowrap" type="submit">
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="bi bi-people"></i>
                                        <p>Belum ada pengguna terdaftar.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ROLE INFO CARDS --}}
    <div class="row g-3">
        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="bi bi-shield-fill-check"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; margin: 0 0 4px;">Admin</h3>
                    <small>Akses penuh: mengelola user, data master, dan konfigurasi sistem.</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="bi bi-tools"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; margin: 0 0 4px;">Staff</h3>
                    <small>Mengelola aset, mutasi/transfer, maintenance, dan riwayat aset.</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="bi bi-eye-fill"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; margin: 0 0 4px;">Pimpinan</h3>
                    <small>Memantau dashboard, risiko aset, laporan, serta approval mutasi.</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: #f1eafa; color: #7852a8;">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <h3 style="font-size: 16px; margin: 0 0 4px;">Peminjam</h3>
                    <small>Melihat daftar aset tersedia, mengajukan pinjaman dan pengembalian.</small>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection