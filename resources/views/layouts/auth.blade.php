<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Gin Asset</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/auth.css'])
    @else
        <style>{!! file_get_contents(resource_path('css/auth.css')) !!}</style>
    @endif
</head>
<body class="auth-page">
    <header class="auth-header">
        <a href="{{ route('login') }}" class="brand" aria-label="Gin Asset">
            <span class="brand-mark"><i></i><i></i><i></i></span>
            <span><strong>GIN ASSET</strong><small>PT GIN GLOBAL INTERNASIONAL NUSANTARA</small></span>
        </a>
        <span class="header-rule"></span>
    </header>

    <main class="auth-main">
        @yield('content')
    </main>

    <footer class="auth-footer">
        <span class="security-note"><b>+</b> Enkripsi AES-256 aktif</span>
        <span>© 2026 PT Global Internasional Nusantara. Seluruh hak cipta dilindungi.</span>
    </footer>
</body>
</html>