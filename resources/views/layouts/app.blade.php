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

<body class="role-{{ auth()->user()->role }}" data-theme="light">

<div class="main-wrapper">

    {{-- SIDEBAR --}}
    @include('components.sidebar')

    {{-- SIDEBAR BACKDROP FOR MOBILE --}}
    <div class="sidebar-backdrop" id="sidebar-backdrop" aria-hidden="true"></div>

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

</style>

@stack('styles')

<style>
    /* Desktop Collapsed */
    @media (min-width: 992px) {
        body.sidebar-collapsed .sidebar {
            width: 72px !important;
        }

        body.sidebar-collapsed .main-content {
            margin-left: 72px !important;
        }

        body.sidebar-collapsed .sidebar-brand {
            justify-content: center;
            padding-left: 8px;
            padding-right: 8px;
        }

        body.sidebar-collapsed .sidebar-brand .brand-info,
        body.sidebar-collapsed .sidebar-brand .sidebar-close-btn,
        body.sidebar-collapsed .sidebar .menu-title,
        body.sidebar-collapsed .sidebar .menu-section-label,
        body.sidebar-collapsed .sidebar .menu-group-header,
        body.sidebar-collapsed .sidebar .menu-item span,
        body.sidebar-collapsed .sidebar .menu-item small {
            display: none !important;
        }

        body.sidebar-collapsed .submenu-list {
            max-height: none !important;
            opacity: 1 !important;
            pointer-events: auto !important;
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
            font-size: 18px;
        }
    }

    /* Mobile & Tablet Drawer (< 992px) */
    @media (max-width: 991.98px) {
        .sidebar {
            width: 270px !important;
            max-width: 85vw !important;
            transform: translateX(-100%);
            position: fixed !important;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1050 !important;
            box-shadow: none;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        body.sidebar-mobile-open .sidebar {
            transform: translateX(0) !important;
            box-shadow: 6px 0 28px rgba(0, 0, 0, 0.2) !important;
        }

        .main-content {
            margin-left: 0 !important;
            width: 100% !important;
        }
    }

    .sidebar-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }

    body.sidebar-mobile-open .sidebar-backdrop {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .sidebar-close-btn {
        margin-left: auto;
        border: none;
        background: transparent;
        color: #64748b;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .sidebar-close-btn:hover {
        background: #fee2e2;
        color: #c81e28;
    }

    .sidebar,
    .main-content {
        transition: width .25s ease, margin-left .25s ease, transform .25s ease;
    }
</style>

<script>
    (() => {
        const toggle = document.getElementById('sidebar-toggle');
        const closeBtn = document.getElementById('sidebar-close-btn');
        const backdrop = document.getElementById('sidebar-backdrop');
        const storageKey = 'gin-sidebar-collapsed';

        const isMobile = () => window.innerWidth < 992;

        const setDesktopCollapsed = (collapsed) => {
            document.body.classList.toggle('sidebar-collapsed', collapsed);
            if (toggle) toggle.setAttribute('aria-expanded', String(!collapsed));
            localStorage.setItem(storageKey, collapsed ? '1' : '0');
        };

        const setMobileOpen = (open) => {
            document.body.classList.toggle('sidebar-mobile-open', open);
            if (toggle) toggle.setAttribute('aria-expanded', String(open));
            if (open) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        };

        // Initialize desktop collapsed state from localStorage
        if (!isMobile()) {
            const savedCollapsed = localStorage.getItem(storageKey) === '1';
            setDesktopCollapsed(savedCollapsed);
        }

        // Submenu accordion toggle handler
        document.addEventListener('click', (e) => {
            const header = e.target.closest('.menu-group-header');
            if (!header) return;

            e.preventDefault();
            const group = header.closest('.menu-group');
            if (!group) return;

            const isOpen = group.classList.contains('open');
            group.classList.toggle('open', !isOpen);
            header.setAttribute('aria-expanded', String(!isOpen));
        });

        // Toggle click
        if (toggle) {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                if (isMobile()) {
                    const isOpen = document.body.classList.contains('sidebar-mobile-open');
                    setMobileOpen(!isOpen);
                } else {
                    const isCollapsed = document.body.classList.contains('sidebar-collapsed');
                    setDesktopCollapsed(!isCollapsed);
                }
            });
        }

        // Mobile close button
        if (closeBtn) {
            closeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                setMobileOpen(false);
            });
        }

        // Backdrop click to close
        if (backdrop) {
            backdrop.addEventListener('click', () => {
                setMobileOpen(false);
            });
        }

        // Close sidebar on link click on mobile
        document.querySelectorAll('.sidebar-menu a').forEach((link) => {
            link.addEventListener('click', () => {
                if (isMobile()) {
                    setMobileOpen(false);
                }
            });
        });

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && document.body.classList.contains('sidebar-mobile-open')) {
                setMobileOpen(false);
            }
        });

        // Responsive resize listener
        window.addEventListener('resize', () => {
            if (!isMobile()) {
                document.body.classList.remove('sidebar-mobile-open');
                document.body.style.overflow = '';
                const savedCollapsed = localStorage.getItem(storageKey) === '1';
                setDesktopCollapsed(savedCollapsed);
            } else {
                document.body.classList.remove('sidebar-collapsed');
            }
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