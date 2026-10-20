<aside class="sidebar">

    @php
        $systemSetting = \App\Models\SystemSetting::current();
        $systemLogoUrl = $systemSetting->logo_path ? asset('storage/'.$systemSetting->logo_path) : null;
        $systemInitials = $systemSetting->initials();
    @endphp

    {{-- ==========================================
        LOGO
    ========================================== --}}
    <div class="sidebar-brand">

        <div class="brand-logo">
            @if($systemLogoUrl)
                <img src="{{ $systemLogoUrl }}" alt="Logo {{ $systemSetting->organization_name }}">
            @else
                <i class="bi bi-box-seam"></i>
            @endif
        </div>

        <div>
            <h5>{{ $systemInitials }}</h5>
            <span>{{ $systemSetting->organization_name }}</span>
        </div>
    </div>

    <div class="sidebar-menu">
        @if(auth()->user()->isLeader() || auth()->user()->isAdmin())
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-fill"></i>
                <span>Dashboard</span>
            </a>
        @endif

        {{-- ==========================================
            MASTER DATA
        ========================================== --}}
        @if(auth()->user()->isAdmin())
            <div class="menu-title">MASTER DATA</div>


        {{-- KATEGORI --}}
        <a href="{{ route('categories.index') }}"
           class="menu-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">

            <i class="bi bi-tags-fill"></i>

            <span>Kategori</span>

        </a>


        {{-- LOKASI --}}
        <a href="{{ route('locations.index') }}"
           class="menu-item {{ request()->routeIs('locations.*') ? 'active' : '' }}">

            <i class="bi bi-geo-alt-fill"></i>

            <span>Lokasi</span>

        </a>


        {{-- VENDOR --}}
        <a href="{{ route('vendors.index') }}"
           class="menu-item {{ request()->routeIs('vendors.*') ? 'active' : '' }}">

            <i class="bi bi-building"></i>

            <span>Vendor</span>

        </a>
        @endif


        {{-- ==========================================
            MANAJEMEN ASSET
        ========================================== --}}
        @if(in_array(auth()->user()->role, ['admin', 'staff', 'pimpinan', 'peminjam'], true))
            <div class="menu-title">MANAJEMEN ASSET</div>


        @if(auth()->user()->role === 'peminjam')
            <a href="{{ route('loans.index') }}" class="menu-item {{ request()->routeIs('loans.index', 'loans.create', 'loans.return') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-in-down"></i>
                <span>Peminjaman</span>
            </a>
            <a href="{{ route('loans.history') }}" class="menu-item {{ request()->routeIs('loans.history') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i>
                <span>Riwayat Peminjaman</span>
            </a>
        @else
            <a href="{{ route('assets.index') }}" class="menu-item {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                <i class="bi bi-pc-display"></i>
                <span>Data Asset</span>
            </a>

            @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
                <a href="{{ route('transfers.index') }}" class="menu-item {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Transfer</span>
                </a>
                <a href="{{ route('loans.index') }}" class="menu-item {{ request()->routeIs('loans.index', 'loans.history') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i>
                    <span>Data Peminjaman</span>
                </a>
            @endif
        @endif
        @endif


        {{-- MAINTENANCE --}}
        @if(in_array(auth()->user()->role, ['admin', 'staff', 'pimpinan'], true))
        <a href="{{ route('maintenance-records.index') }}"
           class="menu-item {{ request()->routeIs('maintenance-records.*') ? 'active' : '' }}">

            <i class="bi bi-tools"></i>

            <span>Maintenance</span>

        </a>
        @endif

        @if(in_array(auth()->user()->role, ['admin', 'pimpinan'], true))
            <a href="{{ route('approvals.index') }}" class="menu-item {{ request()->routeIs('approvals.*') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i>
                <span>Approval</span>
            </a>
            <a href="{{ route('reports.index') }}" class="menu-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan</span>
            </a>
        @endif


        {{-- ASSET HISTORY --}}
        @if(in_array(auth()->user()->role, ['admin', 'staff', 'pimpinan'], true))
        <a href="{{ route('asset-histories.index') }}"
           class="menu-item {{ request()->routeIs('asset-histories.*') ? 'active' : '' }}">

            <i class="bi bi-clock-history"></i>

            <span>Asset History</span>

        </a>
        @endif


        {{-- ==========================================
            AI & PREDIKSI
        ========================================== --}}
        <div class="menu-title">
            AI & PREDIKSI
        </div>


          {{-- AI ASSISTANT --}}
          @if(in_array(auth()->user()->role, ['admin', 'staff', 'pimpinan'], true))
          <a href="{{ route('ai.index') }}"
              class="menu-item {{ request()->routeIs('ai.*') ? 'active' : '' }}">

            <i class="bi bi-robot"></i>

            <span>AI Assistant</span>

        </a>
        @endif


        {{-- ==========================================
            PENGATURAN
        ========================================== --}}
        <div class="menu-title">
            PENGATURAN
        </div>


        @if(auth()->user()->isAdmin())
        <a href="{{ route('settings.index') }}"
              class="menu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">

            <i class="bi bi-gear"></i>

            <span>Pengaturan Sistem</span>

        </a>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('users.index') }}"
               class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Manajemen Role</span>
            </a>
        @endif
        @endif


    </div>

</aside>


<style>

/* ==========================================
   SIDEBAR
========================================== */

.sidebar {

    position: fixed;

    top: 0;
    left: 0;

    width: 250px;

    height: 100vh;

    background: #06264a;

    color: white;

    overflow-y: auto;

    z-index: 1000;

}


/* ==========================================
   LOGO
========================================== */

.sidebar-brand {

    display: flex;

    align-items: center;

    gap: 12px;

    padding: 22px 20px;

    border-bottom: 1px solid rgba(255,255,255,.08);

}


.brand-logo {

    width: 42px;
    height: 42px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: #0d6efd;

    font-size: 21px;

}


.sidebar-brand h5 {

    margin: 0;

    font-size: 18px;

    font-weight: 700;

}


.sidebar-brand span {

    display: block;

    margin-top: 2px;

    font-size: 8px;

    color: #aab9ce;

}


/* ==========================================
   MENU
========================================== */

.sidebar-menu {

    padding: 20px 12px;

}


.menu-title {

    margin: 22px 12px 9px;

    font-size: 11px;

    font-weight: 600;

    color: #9cafc6;

}


.menu-item {

    display: flex;

    align-items: center;

    gap: 13px;

    padding: 10px 13px;

    margin-bottom: 3px;

    color: #dbe5f2;

    text-decoration: none;

    border-radius: 8px;

    font-size: 13px;

    transition: .2s;

}


.menu-item i {

    width: 18px;

    font-size: 16px;

}


.menu-item:hover {

    background: rgba(255,255,255,.08);

    color: white;

}


/* ==========================================
   ACTIVE MENU
========================================== */

.menu-item.active {

    background: #0879f9;

    color: white;

    box-shadow: 0 5px 15px rgba(0,0,0,.15);

}


/* ==========================================
   DISABLED MENU
========================================== */

.menu-disabled {

    cursor: not-allowed;

    color: #7f94ad;

    opacity: .65;

}


.menu-disabled:hover {

    background: transparent;

    color: #7f94ad;

}


.menu-disabled small {

    margin-left: auto;

    padding: 3px 6px;

    border-radius: 5px;

    background: rgba(255,255,255,.08);

    color: #9cafc6;

    font-size: 8px;

}


/* ==========================================
   SCROLLBAR
========================================== */

.sidebar::-webkit-scrollbar {

    width: 5px;

}


.sidebar::-webkit-scrollbar-track {

    background: transparent;

}


.sidebar::-webkit-scrollbar-thumb {

    background: rgba(255,255,255,.15);

    border-radius: 10px;

}


/* ==========================================
   RESPONSIVE
========================================== */

@media (max-width: 991px) {

    .sidebar {

        display: none;

    }

}

</style>