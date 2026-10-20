<header class="top-navbar">

    <div class="navbar-left">

        <button class="sidebar-toggle" type="button" id="sidebar-toggle" aria-label="Buka atau tutup sidebar" aria-expanded="true">

            <i class="bi bi-list"></i>

        </button>

    </div>


    <div class="navbar-right">

        {{-- NOTIFICATION --}}
        <button class="notification-button">

            <i class="bi bi-bell"></i>

            <span>3</span>

        </button>


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



.notification-button {

    position: relative;

    width: 40px;

    height: 40px;

    background: white;

    border: 1px solid #e3e7ee;

    border-radius: 8px;

}


.notification-button span {

    position: absolute;

    top: -5px;

    right: -5px;

    width: 17px;

    height: 17px;

    background: #dc3545;

    color: white;

    border-radius: 50%;

    font-size: 9px;

    display: flex;

    align-items: center;

    justify-content: center;

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