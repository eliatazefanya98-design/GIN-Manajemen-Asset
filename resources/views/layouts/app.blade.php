<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Dashboard') - GIN Asset Management
    </title>

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #f5f7fb;
            color: #172033;
        }

        .main-wrapper {
            min-height: 100vh;
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }

        .content-wrapper {
            padding: 25px;
        }

        @media (max-width: 991px) {

            .main-content {
                margin-left: 0;
            }

        }

    </style>

</head>

<body class="role-{{ auth()->user()->role }}" data-theme="{{ \App\Models\SystemSetting::current()->theme ?? 'light' }}">

<div class="main-wrapper">

    {{-- SIDEBAR --}}
    @include('components.sidebar')

    {{-- MAIN CONTENT --}}
    <main class="main-content">

        {{-- NAVBAR --}}
        @include('components.navbar')

        {{-- PAGE CONTENT --}}
        <div class="content-wrapper">

            @yield('content')

        </div>

    </main>

</div>

<style>
{!! file_get_contents(resource_path('css/app-ui.css')) !!}
{!! file_get_contents(resource_path('css/theme.css')) !!}
</style>

@stack('styles')

<style>
    body.sidebar-collapsed .sidebar {
        width: 68px !important;
    }

    body.sidebar-collapsed .main-content {
        margin-left: 68px !important;
    }

    body.sidebar-collapsed .sidebar-brand {
        justify-content: center;
        padding-left: 8px;
        padding-right: 8px;
    }

    body.sidebar-collapsed .sidebar-brand > div:last-child,
    body.sidebar-collapsed .sidebar .menu-title,
    body.sidebar-collapsed .sidebar .menu-item span,
    body.sidebar-collapsed .sidebar .menu-item small {
        display: none;
    }

    body.sidebar-collapsed .sidebar-menu {
        padding-left: 7px;
        padding-right: 7px;
    }

    body.sidebar-collapsed .menu-item {
        justify-content: center;
        padding-left: 6px;
        padding-right: 6px;
    }

    body.sidebar-collapsed .menu-item i {
        margin: 0;
    }

    @media (max-width: 768px) {
        body.sidebar-collapsed .sidebar {
            transform: translateX(-100%);
            width: 96px !important;
        }

        body.sidebar-collapsed .main-content {
            margin-left: 0 !important;
        }
    }

    .sidebar,
    .main-content {
        transition: width .2s ease, margin-left .2s ease, transform .2s ease;
    }
</style>

<script>
    (() => {
        const toggle = document.getElementById('sidebar-toggle');
        const storageKey = 'gin-sidebar-collapsed';

        if (!toggle) return;

        const setSidebarState = (collapsed) => {
            document.body.classList.toggle('sidebar-collapsed', collapsed);
            toggle.setAttribute('aria-expanded', String(!collapsed));
            localStorage.setItem(storageKey, collapsed ? '1' : '0');
        };

        setSidebarState(localStorage.getItem(storageKey) === '1');
        toggle.addEventListener('click', () => {
            setSidebarState(!document.body.classList.contains('sidebar-collapsed'));
        });
    })();
</script>

<!-- Bootstrap JS -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@stack('scripts')

</body>

</html>