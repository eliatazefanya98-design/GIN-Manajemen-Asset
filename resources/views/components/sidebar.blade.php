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

        <div class="brand-info">
            <h5>{{ $systemInitials }}</h5>
            <span>{{ $systemSetting->organization_name }}</span>
        </div>

        <button type="button" class="sidebar-close-btn d-lg-none" id="sidebar-close-btn" aria-label="Tutup menu sidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="sidebar-menu">
        {{-- ==========================================
            OVERVIEW
        ========================================== --}}
        @if(auth()->user()->isLeader() || auth()->user()->isAdmin())
            <div class="menu-section-label">OVERVIEW</div>
            <a href="{{ route('dashboard') }}" class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
            </a>
        @endif

        {{-- ==========================================
            MASTER DATA (COLLAPSIBLE SUBMENU)
        ========================================== --}}
        @if(auth()->user()->isAdmin())
            @php
                $isMasterDataActive = request()->routeIs('categories.*', 'locations.*', 'vendors.*');
                $isMasterDataOpen = $isMasterDataActive || !request()->routeIs('ai.*', 'settings.*', 'users.*');
            @endphp
            <div class="menu-group {{ $isMasterDataOpen ? 'open' : '' }}" id="group-master-data">
                <button type="button" class="menu-group-header" aria-expanded="{{ $isMasterDataOpen ? 'true' : 'false' }}">
                    <span class="group-title">MASTER DATA</span>
                    <i class="bi bi-chevron-down group-chevron"></i>
                </button>
                <div class="submenu-list">
                    {{-- KATEGORI --}}
                    <a href="{{ route('categories.index') }}"
                       class="menu-item submenu-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags-fill"></i>
                        <span>Kategori</span>
                    </a>

                    {{-- LOKASI --}}
                    <a href="{{ route('locations.index') }}"
                       class="menu-item submenu-item {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Lokasi</span>
                    </a>

                    {{-- VENDOR --}}
                    <a href="{{ route('vendors.index') }}"
                       class="menu-item submenu-item {{ request()->routeIs('vendors.*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i>
                        <span>Vendor</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- ==========================================
            MANAJEMEN ASSET (COLLAPSIBLE SUBMENU)
        ========================================== --}}
        @if(in_array(auth()->user()->role, ['admin', 'staff', 'pimpinan', 'peminjam'], true))
            @php
                $isAssetMgmtActive = request()->routeIs('loans.*', 'assets.*', 'transfers.*', 'maintenance-records.*', 'approvals.*', 'reports.*', 'asset-histories.*');
                $isAssetMgmtOpen = $isAssetMgmtActive || !request()->routeIs('ai.*', 'settings.*', 'users.*');
            @endphp
            <div class="menu-group {{ $isAssetMgmtOpen ? 'open' : '' }}" id="group-manajemen-asset">
                <button type="button" class="menu-group-header" aria-expanded="{{ $isAssetMgmtOpen ? 'true' : 'false' }}">
                    <span class="group-title">MANAJEMEN ASSET</span>
                    <i class="bi bi-chevron-down group-chevron"></i>
                </button>
                <div class="submenu-list">
                    @if(auth()->user()->role === 'peminjam')
                        <a href="{{ route('loans.index') }}" class="menu-item submenu-item {{ request()->routeIs('loans.index', 'loans.create', 'loans.return') ? 'active' : '' }}">
                            <i class="bi bi-box-arrow-in-down"></i>
                            <span>Peminjaman</span>
                        </a>
                        <a href="{{ route('loans.history') }}" class="menu-item submenu-item {{ request()->routeIs('loans.history') ? 'active' : '' }}">
                            <i class="bi bi-clock-history"></i>
                            <span>Riwayat Peminjaman</span>
                        </a>
                    @else
                        <a href="{{ route('assets.index') }}" class="menu-item submenu-item {{ request()->routeIs('assets.*') ? 'active' : '' }}">
                            <i class="bi bi-pc-display"></i>
                            <span>Data Asset</span>
                        </a>

                        @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
                            <a href="{{ route('transfers.index') }}" class="menu-item submenu-item {{ request()->routeIs('transfers.*') ? 'active' : '' }}">
                                <i class="bi bi-arrow-left-right"></i>
                                <span>Transfer</span>
                            </a>
                            <a href="{{ route('loans.index') }}" class="menu-item submenu-item {{ request()->routeIs('loans.index', 'loans.history') ? 'active' : '' }}">
                                <i class="bi bi-box-seam"></i>
                                <span>Data Peminjaman</span>
                            </a>
                        @endif

                        @if(in_array(auth()->user()->role, ['admin', 'staff', 'pimpinan'], true))
                            <a href="{{ route('maintenance-records.index') }}"
                               class="menu-item submenu-item {{ request()->routeIs('maintenance-records.*') ? 'active' : '' }}">
                                <i class="bi bi-tools"></i>
                                <span>Maintenance</span>
                            </a>
                        @endif

                        @if(in_array(auth()->user()->role, ['admin', 'pimpinan'], true))
                            <a href="{{ route('approvals.index') }}" class="menu-item submenu-item {{ request()->routeIs('approvals.*') ? 'active' : '' }}">
                                <i class="bi bi-check2-square"></i>
                                <span>Approval</span>
                            </a>
                            <a href="{{ route('reports.index') }}" class="menu-item submenu-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                                <span>Laporan</span>
                            </a>
                        @endif

                        @if(in_array(auth()->user()->role, ['admin', 'staff', 'pimpinan'], true))
                            <a href="{{ route('asset-histories.index') }}"
                               class="menu-item submenu-item {{ request()->routeIs('asset-histories.*') ? 'active' : '' }}">
                                <i class="bi bi-clock-history"></i>
                                <span>Asset History</span>
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        @endif

        {{-- ==========================================
            AI & PREDIKSI (COLLAPSIBLE SUBMENU)
        ========================================== --}}
        @if(in_array(auth()->user()->role, ['admin', 'staff', 'pimpinan'], true))
            @php
                $isAiActive = request()->routeIs('ai.*');
            @endphp
            <div class="menu-group {{ $isAiActive ? 'open' : '' }}" id="group-ai">
                <button type="button" class="menu-group-header" aria-expanded="{{ $isAiActive ? 'true' : 'false' }}">
                    <span class="group-title">AI & PREDIKSI</span>
                    <i class="bi bi-chevron-down group-chevron"></i>
                </button>
                <div class="submenu-list">
                    <a href="{{ route('ai.index') }}"
                       class="menu-item submenu-item {{ request()->routeIs('ai.*') ? 'active' : '' }}">
                        <i class="bi bi-robot"></i>
                        <span>AI Assistant</span>
                    </a>
                </div>
            </div>
        @endif

        {{-- ==========================================
            PENGATURAN (COLLAPSIBLE SUBMENU)
        ========================================== --}}
        @if(auth()->user()->isAdmin())
            @php
                $isSettingsActive = request()->routeIs('settings.*', 'users.*');
            @endphp
            <div class="menu-group {{ $isSettingsActive ? 'open' : '' }}" id="group-pengaturan">
                <button type="button" class="menu-group-header" aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}">
                    <span class="group-title">PENGATURAN</span>
                    <i class="bi bi-chevron-down group-chevron"></i>
                </button>
                <div class="submenu-list">
                    <a href="{{ route('settings.index') }}"
                       class="menu-item submenu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="bi bi-gear"></i>
                        <span>Pengaturan Sistem</span>
                    </a>
                    <a href="{{ route('users.index') }}"
                       class="menu-item submenu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Manajemen Role</span>
                    </a>
                </div>
            </div>
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

    background: #ffffff;

    color: #63708a;

    border-right: 1px solid var(--app-border, #edf0f5);

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

    border-bottom: 1px solid var(--app-border, #edf0f5);

}


.brand-logo {

    width: 42px;
    height: 42px;

    display: flex;

    align-items: center;
    justify-content: center;

    border-radius: 10px;

    background: var(--gin-red-soft, #fdebed);
    color: var(--gin-red, #c81e28);

    font-size: 21px;

}


.sidebar-brand h5 {

    margin: 0;

    font-size: 18px;

    font-weight: 700;

    color: #1f2c45;

}


.sidebar-brand span {

    display: block;

    margin-top: 2px;

    font-size: 8px;

    color: #8a96a8;

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

    color: #9aa8bb;

}


.menu-item {

    display: flex;

    align-items: center;

    gap: 13px;

    padding: 10px 13px;

    margin-bottom: 3px;

    color: #627189;

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

    background: #fff2f4;

    color: var(--gin-red, #c81e28);

}


/* ==========================================
   ACTIVE MENU
========================================== */

.menu-item.active {

    background: var(--gin-red-soft, #fdebed);

    color: var(--gin-red, #c81e28);

    box-shadow: none;
    font-weight: 600;

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




</style>