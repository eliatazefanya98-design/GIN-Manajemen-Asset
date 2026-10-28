@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')

@php
    $backUrl = in_array(auth()->user()->role, ['admin', 'pimpinan'])
        ? route('dashboard')
        : (auth()->user()->role === 'staff' ? route('assets.index') : route('loans.index'));
    $hasPasswordErrors = $errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation');
@endphp

<div class="container-fluid">
    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Profil Saya</h2>
            <p class="text-muted mb-0">Kelola informasi profil, data diri, dan keamanan akun Anda</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- KOLOM KIRI: Ringkasan Pengguna --}}
        <div class="col-lg-4">
            <div class="card shadow-sm text-center p-4">
                <div class="d-flex justify-content-center mb-3">
                    <div class="avatar-circle-lg">
                        @if(auth()->user()->profile_photo_path)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Foto profil {{ auth()->user()->name }}">
                        @else
                            <i class="bi bi-person"></i>
                        @endif
                    </div>
                </div>

                <h5 class="fw-bold mb-1 text-dark">{{ auth()->user()->name }}</h5>
                <div class="text-muted small mb-3">{{ auth()->user()->email }}</div>

                <div class="mb-4">
                    <span class="badge bg-primary px-3 py-2" style="font-size: 11px;">
                        <i class="bi bi-shield-check me-1"></i> {{ ucfirst(auth()->user()->role) }}
                    </span>
                </div>

                <div class="list-group list-group-flush text-start border-top pt-2">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                        <span class="text-muted small">Nama Lengkap</span>
                        <span class="fw-semibold small text-dark">{{ auth()->user()->name }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                        <span class="text-muted small">Email</span>
                        <span class="fw-semibold small text-dark">{{ auth()->user()->email }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                        <span class="text-muted small">Role Sistem</span>
                        <span class="fw-semibold small text-dark">{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                        <span class="text-muted small">Unit Kerja</span>
                        <span class="fw-semibold small text-dark">
                            {{ auth()->user()->role === 'admin' ? 'Administrator' : (auth()->user()->role === 'staff' ? 'Staff Aset' : (auth()->user()->role === 'pimpinan' ? 'Pimpinan' : 'Peminjam / Pengguna')) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Formulir Edit Profil & Ubah Password --}}
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-transparent p-0 border-bottom">
                    <ul class="nav nav-tabs card-header-tabs m-0 px-3 border-0" id="profileTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link py-3 px-4 fw-semibold border-0 {{ !$hasPasswordErrors ? 'active text-primary border-bottom border-primary border-3' : 'text-muted' }}" id="tab-profile-btn" type="button" data-panel="profile-edit-panel">
                                <i class="bi bi-pencil-square me-2"></i> Edit Profil
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link py-3 px-4 fw-semibold border-0 {{ $hasPasswordErrors ? 'active text-primary border-bottom border-primary border-3' : 'text-muted' }}" id="tab-password-btn" type="button" data-panel="password-panel">
                                <i class="bi bi-shield-lock me-2"></i> Keamanan Akun
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    {{-- PANEL 1: EDIT PROFIL --}}
                    <div class="profile-form-panel" id="profile-edit-panel" {{ $hasPasswordErrors ? 'hidden' : '' }}>
                        <div class="alert alert-light border d-flex align-items-center gap-3 mb-4 p-3 rounded">
                            <div class="stat-icon" style="width: 42px; height: 42px; background: rgba(200,30,40,0.1); color: var(--app-blue);">
                                <i class="bi bi-person-check fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Informasi Profil</h6>
                                <p class="text-muted small mb-0">Perbarui nama lengkap, email, dan foto akun Anda.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="profile_photo" class="form-label fw-semibold">Foto Profil</label>
                                <input id="profile_photo" type="file" name="profile_photo" class="form-control @error('profile_photo') is-invalid @enderror" accept="image/jpeg,image/png,image/webp">
                                <div class="form-text">Format gambar: JPG, PNG, atau WEBP (Maksimal 2MB).</div>
                                @error('profile_photo')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Nama Lengkap</label>
                                <input id="name" type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">Alamat Email</label>
                                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-floppy me-1"></i> Simpan Profil
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- PANEL 2: PASSWORD --}}
                    <div class="profile-form-panel" id="password-panel" {{ !$hasPasswordErrors ? 'hidden' : '' }}>
                        <div class="alert alert-light border d-flex align-items-center gap-3 mb-4 p-3 rounded">
                            <div class="stat-icon" style="width: 42px; height: 42px; background: rgba(200,30,40,0.1); color: var(--app-blue);">
                                <i class="bi bi-shield-lock fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Ubah Password</h6>
                                <p class="text-muted small mb-0">Kelola dan perbarui kata sandi akun Anda untuk menjaga keamanan.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('profile.password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-semibold">Password Lama</label>
                                <div class="input-group">
                                    <input id="current_password" type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Masukkan password saat ini">
                                    <button type="button" class="btn btn-outline-secondary toggle-password" aria-label="Lihat password">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Password Baru</label>
                                <div class="input-group">
                                    <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter">
                                    <button type="button" class="btn btn-outline-secondary toggle-password" aria-label="Lihat password">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                                    <button type="button" class="btn btn-outline-secondary toggle-password" aria-label="Lihat password">
                                        <i class="bi bi-eye-slash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-floppy me-1"></i> Simpan Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-circle-lg {
        width: 104px;
        height: 104px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(200,30,40,0.15), rgba(200,30,40,0.05));
        border: 3px solid #ffffff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: var(--app-blue);
        font-size: 42px;
    }

    .avatar-circle-lg img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .nav-tabs .nav-link {
        border-bottom: 2px solid transparent !important;
        border-radius: 0;
        background: transparent;
        transition: color .15s ease, border-color .15s ease;
    }

    .nav-tabs .nav-link.active {
        color: var(--app-blue) !important;
        border-bottom: 2px solid var(--app-blue) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('#profileTabs button[data-panel]');
        const panels = document.querySelectorAll('.profile-form-panel');

        tabButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                tabButtons.forEach(function (btn) {
                    btn.classList.remove('active', 'text-primary');
                    btn.classList.add('text-muted');
                });
                this.classList.add('active', 'text-primary');
                this.classList.remove('text-muted');

                const targetPanelId = this.dataset.panel;
                panels.forEach(function (panel) {
                    panel.hidden = panel.id !== targetPanelId;
                });
            });
        });

        document.querySelectorAll('.toggle-password').forEach(function (button) {
            button.addEventListener('click', function () {
                const input = this.parentElement.querySelector('input');
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                }
            });
        });
    });
</script>
@endpush

