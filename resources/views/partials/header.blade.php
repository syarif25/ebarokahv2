<div class="header border-bottom">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left">
                    <div class="dashboard_bar">
                        @yield('page_title', 'Dashboard')
                    </div>
                </div>
                <ul class="navbar-nav header-right">
                    <li class="nav-item header-profile">
                        <div class="nav-link d-flex align-items-center" style="cursor: default;">
                            <img src="{{ asset('images/user.svg') }}" alt="User Profile" style="border-radius: 50%; object-fit: cover; width: 40px; height: 40px;"/>
                            <div class="header-info ms-3">
                                <span class="fs-16 font-w600 mb-0 d-block text-dark">{{ Auth::check() ? Auth::user()->username : 'Guest' }}</span>
                                <small class="fs-12 text-muted font-w400">{{ Auth::check() ? Auth::user()->jabatan : '-' }}</small>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>
