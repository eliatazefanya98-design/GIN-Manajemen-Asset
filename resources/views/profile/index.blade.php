@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')

@php
    $backUrl = in_array(auth()->user()->role, ['admin', 'pimpinan'])
        ? route('dashboard')
        : (auth()->user()->role === 'staff' ? route('assets.index') : route('loans.index'));
    $hasPasswordErrors = $errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation');
@endphp

<div class="profile-page">
    <div class="profile-header">
        <a href="{{ $backUrl }}" class="back-link" aria-label="Kembali">
            <i class="bi bi-arrow-left"></i>
        </a>

        <div>
            <h1 class="profile-title">Profil Saya</h1>
            <p class="profile-subtitle">Kelola informasi profil dan data diri Anda</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-3" style="border-radius: 12px; font-size: 12px;">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="profile-shell">
        <div class="profile-card">
            <div class="avatar-wrap">
                <div class="avatar-circle">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="Foto profil {{ auth()->user()->name }}">
                    @else
                        <i class="bi bi-person"></i>
                    @endif
                </div>
            </div>

            <div class="profile-name">{{ auth()->user()->name }}</div>
            <div class="profile-role">{{ ucfirst(auth()->user()->role) }}</div>

            <div class="detail-list">
                <div class="detail-row">
                    <span class="label">Nama Lengkap</span>
                    <span class="value">{{ auth()->user()->name }}</span>
                </div>

                <div class="detail-row">
                    <span class="label">Email</span>
                    <span class="value">{{ auth()->user()->email }}</span>
                </div>

                <div class="detail-row">
                    <span class="label">Role</span>
                    <span class="value">{{ ucfirst(auth()->user()->role) }}</span>
                </div>

                <div class="detail-row">
                    <span class="label">Unit Kerja</span>
                    <span class="value">{{ auth()->user()->role === 'admin' ? 'Administrator' : (auth()->user()->role === 'staff' ? 'Staff Aset' : (auth()->user()->role === 'pimpinan' ? 'Pimpinan' : 'Peminjam / Pengguna')) }}</span>
                </div>
            </div>
        </div>

        <div class="profile-panel">
            <div class="profile-tabs" role="tablist" aria-label="Akun profil">
                <button class="tab-button {{ !$hasPasswordErrors ? 'active' : '' }}" type="button" data-panel="profile-edit-panel" aria-selected="{{ !$hasPasswordErrors ? 'true' : 'false' }}">
                    <i class="bi bi-pencil-square"></i>
                    Edit Profil
                </button>

                <button class="tab-button {{ $hasPasswordErrors ? 'active' : '' }}" type="button" data-panel="password-panel" aria-selected="{{ $hasPasswordErrors ? 'true' : 'false' }}">
                    <i class="bi bi-shield-lock"></i>
                    Keamanan Akun
                </button>
            </div>

            <div class="profile-form-panel" id="profile-edit-panel" {{ $hasPasswordErrors ? 'hidden' : '' }}>
                <div class="security-header">
                    <div class="security-icon">
                        <i class="bi bi-person-check"></i>
                    </div>

                    <div>
                        <h3>Informasi Profil</h3>
                        <p>Perbarui nama dan email akun Anda.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="password-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="profile_photo">Foto Profil</label>
                        <div class="input-wrap">
                            <input id="profile_photo" type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <small class="text-muted" style="font-size: 10px;">Format gambar: JPG, PNG, atau WEBP (Maksimal 2MB).</small>
                        @error('profile_photo')
                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <div class="input-wrap">
                            <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                        </div>
                        @error('name')
                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-wrap">
                            <input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                        </div>
                        @error('email')
                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary submit-btn">
                            <i class="bi bi-floppy"></i>
                            Simpan Profil
                        </button>
                    </div>
                </form>
            </div>

            <div class="profile-form-panel" id="password-panel" {{ !$hasPasswordErrors ? 'hidden' : '' }}>
                <div class="security-header">
                    <div class="security-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>

                    <div>
                        <h3>Ubah Password</h3>
                        <p>Kelola password akun Anda.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.password.update') }}" class="password-form">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="current_password">Password Lama</label>
                        <div class="input-wrap">
                            <input id="current_password" type="password" name="current_password" placeholder="Syarat Password" value="{{ old('current_password') }}">
                            <button type="button" class="toggle-password" aria-label="Lihat password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <div class="input-wrap @error('password') has-error @enderror">
                            <input id="password" type="password" name="password" placeholder="Rahasia12" value="{{ old('password') }}">
                            <button type="button" class="toggle-password" aria-label="Lihat password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                        @error('password')
                            <small class="text-danger d-block mt-2">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <div class="input-wrap">
                            <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Konfirmasi password" value="{{ old('password_confirmation') }}">
                            <button type="button" class="toggle-password" aria-label="Lihat password">
                                <i class="bi bi-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary submit-btn">
                            <i class="bi bi-floppy"></i>
                            Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .profile-page {
        min-height: calc(100vh - 120px);
        font-family: 'Poppins', sans-serif;
        color: #252b35;
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 18px;
    }

    .back-link {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #1f2430;
        text-decoration: none;
        background: transparent;
    }

    .back-link:hover {
        background: #f2f4f8;
    }

    .back-link i {
        font-size: 19px;
    }

    .profile-title {
        margin: 0;
        font-size: 21px;
        font-weight: 700;
        color: #1d2430;
    }

    .profile-subtitle {
        margin: 4px 0 0;
        color: #7a8390;
        font-size: 11px;
    }

    .profile-shell {
        display: grid;
        grid-template-columns: 0.9fr 1.3fr;
        gap: 26px;
        align-items: stretch;
    }

    .profile-card,
    .profile-panel {
        background: #ffffff;
        border: 1px solid #edf0f4;
        border-radius: 20px;
        box-shadow: 0 6px 18px rgba(18, 25, 38, 0.03);
    }

    .profile-card {
        padding: 28px 20px 18px;
        text-align: center;
    }

    .avatar-wrap {
        display: flex;
        justify-content: center;
        margin-bottom: 18px;
    }

    .avatar-circle {
        width: 132px;
        height: 132px;
        border-radius: 50%;
        background: linear-gradient(180deg, #d0c4ee, #c4baef);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 55px;
        box-shadow: inset 0 0 0 6px rgba(255,255,255,0.4);
    }

    .avatar-circle img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }

    .profile-name {
        font-size: 16px;
        font-weight: 700;
        color: #1f2430;
        margin-bottom: 6px;
    }

    .profile-role {
        font-size: 11px;
        color: #657082;
        margin-bottom: 26px;
    }

    .detail-list {
        text-align: left;
        width: min(100%, 360px);
        margin: 0 auto;
    }

    .detail-row {
        display: grid;
        grid-template-columns: 1fr minmax(130px, auto);
        align-items: center;
        gap: 20px;
        min-height: 48px;
        padding: 10px 0;
        border-bottom: 1px solid #f1f4f7;
        color: #1f2430;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-row .label {
        color: #7b8694;
        font-weight: 500;
        font-size: 10px;
        white-space: nowrap;
    }

    .detail-row .value {
        font-weight: 600;
        font-size: 10px;
        text-align: right;
        color: #252b35;
        overflow-wrap: anywhere;
    }

    .profile-panel {
        padding: 0 20px 20px;
    }

    .profile-tabs {
        display: flex;
        border-bottom: 1px solid #edf0f3;
        margin: 0 -20px;
        padding: 0 20px;
    }

    .tab-button {
        border: none;
        background: transparent;
        color: #5f6979;
        font-size: 11px;
        font-weight: 600;
        padding: 18px 18px 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-bottom: 3px solid transparent;
    }

    .tab-button.active {
        color: #d03847;
        border-bottom-color: #d03847;
    }

    .tab-button:not(.active) {
        opacity: 0.8;
    }

    .profile-form-panel {
        padding-top: 18px;
    }

    .security-header {
        display: flex;
        align-items: center;
        gap: 12px;
        background: #fff5f6;
        border: 1px solid #f6dfe2;
        border-radius: 14px;
        padding: 16px 18px;
        margin-bottom: 18px;
    }

    .security-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #ffe6ea;
        color: #d03847;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
    }

    .security-header h3 {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #1d2430;
    }

    .security-header p {
        margin: 4px 0 0;
        font-size: 10px;
        color: #778293;
    }

    .password-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-size: 11px;
        font-weight: 600;
        color: #2e3949;
    }

    .input-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-wrap input {
        width: 100%;
        border: 1px solid #dfe5eb;
        background: #f9fafc;
        border-radius: 12px;
        padding: 14px 44px 14px 16px;
        font-size: 11px;
        color: #1e2530;
        outline: none;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .input-wrap input:focus {
        border-color: #d03847;
        box-shadow: 0 0 0 3px rgba(208, 56, 71, 0.08);
        background: #fff;
    }

    .input-wrap.has-error input {
        border-color: #d03847;
    }

    .toggle-password {
        position: absolute;
        right: 12px;
        border: none;
        background: transparent;
        color: #8a94a4;
        font-size: 18px;
        padding: 0;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 14px;
        padding-top: 12px;
    }

    .cancel-btn {
        border: none;
        background: #f0f3f7;
        color: #2f3a4a;
        font-weight: 600;
        padding: 12px 22px;
        border-radius: 12px;
    }

    .submit-btn {
        border: none;
        background: linear-gradient(180deg, #d83b4a, #cf2d3e);
        color: #fff;
        font-weight: 700;
        padding: 13px 24px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 8px 16px rgba(214, 54, 74, 0.22);
    }

    @media (max-width: 991px) {
        .profile-shell {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 576px) {
        .profile-title {
            font-size: 25px;
        }

        .profile-subtitle {
            font-size: 13px;
        }

        .profile-header {
            align-items: flex-start;
        }

        .profile-panel {
            padding-left: 14px;
            padding-right: 14px;
        }

        .profile-tabs {
            margin-left: -14px;
            margin-right: -14px;
            padding-left: 14px;
            padding-right: 14px;
        }

        .tab-button {
            font-size: 13px;
            padding-left: 10px;
            padding-right: 10px;
        }

        .form-actions {
            flex-direction: column-reverse;
        }

        .cancel-btn,
        .submit-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.tab-button').forEach(function (button) {
            button.addEventListener('click', function () {
                document.querySelectorAll('.tab-button').forEach(function (tab) {
                    tab.classList.toggle('active', tab === button);
                    tab.setAttribute('aria-selected', tab === button ? 'true' : 'false');
                });

                document.querySelectorAll('.profile-form-panel').forEach(function (panel) {
                    panel.hidden = panel.id !== button.dataset.panel;
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
