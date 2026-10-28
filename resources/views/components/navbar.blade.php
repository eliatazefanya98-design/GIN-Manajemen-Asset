<header class="top-navbar">

    <div class="navbar-left">

        <button class="sidebar-toggle" type="button" id="sidebar-toggle" aria-label="Buka atau tutup sidebar" aria-expanded="true">

            <i class="bi bi-list"></i>

        </button>

    </div>


    <div class="navbar-right">

        {{-- NOTIFICATION DROPDOWN --}}
        @php
            $unreadCount = $systemNotifications['unread_count'] ?? 0;
            $notificationItems = $systemNotifications['items'] ?? [];
        @endphp
        <div class="dropdown notification-dropdown">
            <button class="notification-button" type="button" id="notificationDropdownBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="Pusat Notifikasi">
                <i class="bi bi-bell"></i>
                <span class="notification-badge {{ $unreadCount === 0 ? 'd-none' : '' }}" id="notificationBadge">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            </button>

            <div class="dropdown-menu dropdown-menu-end notification-menu shadow" id="notificationMenu" aria-labelledby="notificationDropdownBtn">
                <div class="notification-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-bell-fill text-primary"></i>
                        <h6 class="mb-0 fw-bold fs-6">Notifikasi</h6>
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-2 py-1" id="notificationHeaderCount" style="font-size: 11px;">
                            {{ $unreadCount }} Baru
                        </span>
                    </div>
                    @if($unreadCount > 0)
                        <button type="button" class="btn btn-sm btn-link text-decoration-none p-0 text-muted" id="btnMarkAllRead" title="Tandai semua sudah dibaca" style="font-size: 11px;">
                            <i class="bi bi-check2-all"></i> Tandai Dibaca
                        </button>
                    @endif
                </div>

                <div class="notification-body" id="notificationListContainer">
                    @forelse($notificationItems as $item)
                        <a href="{{ $item['url'] }}" class="notification-item notification-type-{{ $item['type'] }}" data-notification-id="{{ $item['id'] }}">
                            <div class="notification-icon-wrapper bg-{{ $item['type'] }}-subtle text-{{ $item['type'] }}">
                                <i class="bi {{ $item['icon'] }}"></i>
                            </div>
                            <div class="notification-content">
                                <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                    <span class="notification-title">{{ $item['title'] }}</span>
                                    <span class="badge bg-{{ $item['type'] }}-subtle text-{{ $item['type'] }} border border-{{ $item['type'] }}-subtle notification-tag">
                                        {{ $item['badge'] }}
                                    </span>
                                </div>
                                <p class="notification-text">{{ $item['message'] }}</p>
                                <span class="notification-time">
                                    <i class="bi bi-clock me-1"></i>{{ $item['time'] }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="notification-empty" id="notificationEmptyState">
                            <div class="empty-icon-circle">
                                <i class="bi bi-bell-slash"></i>
                            </div>
                            <p class="mb-1 fw-medium text-dark">Tidak ada notifikasi baru</p>
                            <small class="text-muted">Semua peminjaman, mutasi, dan pemeliharaan aset dalam kondisi terpantau aman.</small>
                        </div>
                    @endforelse
                </div>

                <div class="notification-footer">
                    @if(auth()->user()->role === 'peminjam')
                        <a href="{{ route('loans.history') }}" class="notification-footer-link">
                            <span>Riwayat Peminjaman Saya</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @else
                        <a href="{{ route('asset-histories.index') }}" class="notification-footer-link">
                            <span>Lihat Seluruh Log Riwayat Aset</span>
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>


        {{-- USER --}}
        <div class="user-profile">

            <a href="{{ route('profile.index') }}" class="user-profile-link" title="Buka Profil Saya">
                <div class="user-avatar">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="navbar-avatar-img">
                    @else
                        <i class="bi bi-person-fill"></i>
                    @endif
                </div>

                <div class="user-info">
                    <strong>{{ auth()->user()->name }}</strong>
                    <small>{{ ucfirst(auth()->user()->role) }} · {{ auth()->user()->email }}</small>
                </div>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-button" title="Keluar dari sistem">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </form>

        </div>

    </div>

</header>


<style>

.top-navbar {

    height: 70px;

    background: white;

    border-bottom: 1px solid #e9edf3;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 0 25px;

}


.sidebar-toggle {

    border: none;

    background: transparent;

    font-size: 24px;

    color: #536174;

}


.navbar-right {

    display: flex;

    align-items: center;

    gap: 18px;

}



.notification-dropdown {
    position: relative;
}

.notification-button {
    position: relative;
    width: 40px;
    height: 40px;
    background: white;
    border: 1px solid #e3e7ee;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #536174;
    cursor: pointer;
    transition: all 0.2s ease;
}

.notification-button:hover,
.notification-button[aria-expanded="true"] {
    background: #fff2f4;
    border-color: #fecaca;
    color: var(--gin-red, #c81e28);
}

.notification-button .notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    background: var(--gin-red, #c81e28);
    color: white;
    border-radius: 9px;
    font-size: 9px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.35);
}

.notification-menu {
    width: 360px;
    max-width: calc(100vw - 32px);
    padding: 0;
    margin-top: 10px !important;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    background: #ffffff;
    overflow: hidden;
    z-index: 1060;
}

.notification-header {
    padding: 12px 16px;
    background: #f8fafc;
    border-bottom: 1px solid #edf2f7;
}

.notification-body {
    max-height: 380px;
    overflow-y: auto;
}

.notification-body::-webkit-scrollbar {
    width: 5px;
}
.notification-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid #f1f5f9;
    text-decoration: none;
    color: #1e293b;
    transition: background-color 0.15s ease;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background-color: #f8fafc;
}

.notification-item.is-read {
    opacity: 0.65;
    background-color: #fafbfc;
}

.notification-icon-wrapper {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.notification-content {
    flex: 1;
    min-width: 0;
}

.notification-title {
    font-size: 13px;
    font-weight: 600;
    color: #0f172a;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.notification-tag {
    font-size: 9px;
    padding: 2px 6px;
    font-weight: 600;
    border-radius: 4px;
    flex-shrink: 0;
}

.notification-text {
    font-size: 12px;
    color: #475569;
    margin: 0 0 4px 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.notification-time {
    font-size: 11px;
    color: #94a3b8;
    display: flex;
    align-items: center;
}

.notification-empty {
    padding: 32px 20px;
    text-align: center;
}

.empty-icon-circle {
    width: 48px;
    height: 48px;
    background: #f1f5f9;
    color: #94a3b8;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px auto;
    font-size: 20px;
}

.notification-footer {
    padding: 10px 16px;
    background: #f8fafc;
    border-top: 1px solid #edf2f7;
    text-align: center;
}

.notification-footer-link {
    font-size: 12px;
    font-weight: 600;
    color: var(--gin-red, #c81e28);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: color 0.15s ease;
}

.notification-footer-link:hover {
    color: var(--gin-red-hover, #a81620);
    text-decoration: underline;
}


.user-profile {

    display: flex;

    align-items: center;

    gap: 9px;

}

.user-profile-link {

    display: flex;

    align-items: center;

    gap: 9px;

    text-decoration: none;

    color: inherit;

    padding: 4px 6px;

    border-radius: 8px;

    transition: background-color 0.15s ease, transform 0.15s ease;

}

.user-profile-link:hover {

    background-color: #f3f5f9;

}

.user-profile-link:hover .user-avatar {

    transform: scale(1.04);

}


.user-avatar {

    width: 38px;

    height: 38px;

    background: #8064d9;

    border-radius: 50%;

    display: flex;

    align-items: center;

    justify-content: center;

    color: white;

    overflow: hidden;

    flex-shrink: 0;

    transition: transform 0.15s ease;

}

.navbar-avatar-img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    border-radius: 50%;

}


.user-info strong {

    display: block;

    font-size: 12px;

}


.user-info small {

    color: #7b8493;

    font-size: 10px;

}

.logout-form {
    margin-left: 6px;
}

.logout-button {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    border: 1px solid #f0d7d7;
    border-radius: 7px;
    padding: 8px 10px;
    color: #dc3545;
    background: #fff;
    font-size: 11px;
    cursor: pointer;
}

.logout-button:hover {
    background: #fff5f5;
}


@media (max-width: 768px) {


    .user-info {

        display: none;

    }

    .logout-button span {

        display: none;

    }

    .logout-button {

        padding: 8px 9px;

    }

}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnMarkAll = document.getElementById('btnMarkAllRead');
    const badge = document.getElementById('notificationBadge');
    const headerCount = document.getElementById('notificationHeaderCount');
    const items = document.querySelectorAll('.notification-item');
    const storageKey = 'gin_read_notifications_v1';

    // Ambil daftar id notifikasi yang sudah pernah ditandai dibaca oleh pengguna
    let readIds = [];
    try {
        readIds = JSON.parse(localStorage.getItem(storageKey) || '[]');
    } catch (e) {
        readIds = [];
    }

    // Terapkan status dibaca pada notifikasi yang sesuai
    let unreadCount = 0;
    items.forEach(function(item) {
        const id = item.getAttribute('data-notification-id');
        if (id && readIds.includes(id)) {
            item.classList.add('is-read');
        } else {
            unreadCount++;
        }
    });

    if (items.length > 0) {
        if (unreadCount === 0) {
            if (badge) badge.classList.add('d-none');
            if (headerCount) headerCount.textContent = '0 Baru';
            if (btnMarkAll) btnMarkAll.classList.add('d-none');
        } else {
            if (badge) {
                badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                badge.classList.remove('d-none');
            }
            if (headerCount) {
                headerCount.textContent = unreadCount + ' Baru';
            }
        }
    }

    // Tombol tandai semua sudah dibaca
    if (btnMarkAll) {
        btnMarkAll.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const allIds = [];
            items.forEach(function(item) {
                const id = item.getAttribute('data-notification-id');
                if (id) allIds.push(id);
                item.classList.add('is-read');
            });

            try {
                localStorage.setItem(storageKey, JSON.stringify(allIds));
            } catch (e) {}

            if (badge) {
                badge.classList.add('d-none');
            }
            if (headerCount) {
                headerCount.textContent = '0 Baru';
            }
            btnMarkAll.classList.add('d-none');
        });
    }

    // Ketika salah satu item notifikasi diklik, tandai item tersebut sebagai dibaca
    items.forEach(function(item) {
        item.addEventListener('click', function() {
            const id = item.getAttribute('data-notification-id');
            if (id && !readIds.includes(id)) {
                readIds.push(id);
                try {
                    localStorage.setItem(storageKey, JSON.stringify(readIds));
                } catch (e) {}
            }
        });
    });
});
</script>