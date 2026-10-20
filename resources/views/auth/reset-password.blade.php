@extends('layouts.auth')

@section('title', 'Atur Ulang Kata Sandi')

@section('content')
<section class="auth-card login-card">
    <div class="auth-heading">
        <span class="eyebrow">PEMULIHAN AKUN</span>
        <h1>Atur Ulang Kata Sandi</h1>
        <p>Buat kata sandi baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="auth-form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label for="email">Email korporat <em>*</em></label>
        <div class="input-wrap"><span class="input-icon">@</span><input id="email" name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required autofocus></div>
        @error('email')<small class="field-error">{{ $message }}</small>@enderror

        <label for="password">Kata sandi baru <em>*</em></label>
        <div class="input-wrap"><span class="input-icon">*</span><input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required></div>
        @error('password')<small class="field-error">{{ $message }}</small>@enderror

        <label for="password_confirmation">Konfirmasi kata sandi <em>*</em></label>
        <div class="input-wrap"><span class="input-icon">*</span><input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="8" required></div>

        <button class="primary-button" type="submit">Simpan kata sandi baru</button>
    </form>
</section>
@endsection