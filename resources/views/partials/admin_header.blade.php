<header id="topbar">

    <div class="topbar-breadcrumb">
        Admin &nbsp;/&nbsp; <span>@yield('page-title', 'Dashboard')</span>
    </div>

    <div class="topbar-right">


        <form action="{{ route('admin.logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span class="d-none d-sm-inline">Logout</span>
            </button>
        </form>
    </div>
</header>
