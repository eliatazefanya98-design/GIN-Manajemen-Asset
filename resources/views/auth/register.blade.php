@extends('layouts.auth')

@section('title', 'Daftar Akun Baru')

@section('content')
<section class="auth-card register-card">
    <div class="auth-heading">
        <span class="eyebrow">AKSES PENGGUNA BARU</span>
        <h1>Daftar Akun Baru</h1>
        <p>Daftarkan akun resmi Anda untuk mengakses sistem manajemen dan inventaris aset perusahaan.</p>
    </div>

    <form method="POST" action="{{ route('register.store') }}" class="auth-form">
        @csrf
        <label for="name">Nama lengkap <em>*</em></label>
        <div class="input-wrap"><span class="input-icon">◉</span><input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Contoh: Rian Pratama" autocomplete="name" required autofocus></div>
        @error('name')<small class="field-error">{{ $message }}</small>@enderror

        <label for="email">Email korporat <em>*</em></label>
        <div class="input-wrap"><span class="input-icon">@</span><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@gin.co.id" autocomplete="email" required></div>
        @error('email')<small class="field-error">{{ $message }}</small>@enderror

        <div class="form-grid">
            <div><label for="password">Kata sandi <em>*</em></label><div class="input-wrap"><span class="input-icon">*</span><input id="password" name="password" type="password" placeholder="Min. 8 karakter" autocomplete="new-password" required></div></div>
            <div><label for="password_confirmation">Konfirmasi sandi <em>*</em></label><div class="input-wrap"><span class="input-icon">*</span><input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi kata sandi" autocomplete="new-password" required></div></div>
        </div>
        @error('password')<small class="field-error">{{ $message }}</small>@enderror

        <button class="primary-button" type="submit">Daftar Akun Sekarang <span>→</span></button>
    </form>

    <div class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></div>
</section>
@endsection