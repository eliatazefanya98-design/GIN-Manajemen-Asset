@extends('layouts.auth')

@section('title', 'Masuk ke Akun')

@section('content')
<section class="auth-card login-card">
    <div class="auth-heading">
        <span class="eyebrow">PORTAL MANAJEMEN ASET</span>
        <h1>Masuk ke Akun</h1>
        <p>Masukkan kredensial resmi Anda untuk mengakses sistem manajemen inventaris aset perusahaan.</p>
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="auth-form">
        @csrf
        <label for="email">Email korporat <em>*</em></label>
        <div class="input-wrap"><span class="input-icon">@</span><input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="nama@gin.co.id" autocomplete="email" required autofocus></div>
        @error('email')<small class="field-error">{{ $message }}</small>@enderror

        <label for="password">Kata sandi <em>*</em></label>
        <div class="input-wrap"><span class="input-icon">*</span><input id="password" name="password" type="password" placeholder="Masukkan kata sandi Anda" autocomplete="current-password" required><button type="button" class="password-toggle" data-target="password" aria-label="Tampilkan kata sandi">Lihat</button></div>
        @error('password')<small class="field-error">{{ $message }}</small>@enderror

        <div class="form-options"><label class="check-label"><input type="checkbox" name="remember"> <span>Ingat sesi login ini</span></label><a href="{{ route('password.request') }}">Lupa kata sandi?</a></div>
        <button class="primary-button" type="submit">Masuk ke Sistem <span>→</span></button>
    </form>

    <div class="auth-switch">Belum memiliki hak akses? <a href="{{ route('register') }}">Daftar akun baru</a></div>
</section>
<script>
document.querySelectorAll('.password-toggle').forEach((button) => button.addEventListener('click', () => {
    const input = document.getElementById(button.dataset.target);
    input.type = input.type === 'password' ? 'text' : 'password';
    button.textContent = input.type === 'password' ? 'Lihat' : 'Sembunyikan';
}));
</script>
@endsection