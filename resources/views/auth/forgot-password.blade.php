@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi')

@section('content')
<section class="auth-card login-card">
    <div class="auth-heading">
        <span class="eyebrow">PEMULIHAN AKUN</span>
        <h1>Lupa Kata Sandi</h1>
        <p>Masukkan email akun untuk menerima tautan pengaturan ulang kata sandi.</p>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf
        <label for="email">Email korporat <em>*</em></label>
        <div class="input-wrap"><span class="input-icon">@</span><input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus></div>
        @error('email')<small class="field-error">{{ $message }}</small>@enderror
        <button class="primary-button" type="submit">Kirim tautan reset</button>
    </form>

    <div class="auth-switch"><a href="{{ route('login') }}">Kembali ke login</a></div>
</section>
@endsection