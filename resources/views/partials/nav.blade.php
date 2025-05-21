<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav">
            <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="bi bi-list"></i> </a> </li>
            {{-- <li class="nav-item d-none d-md-block"> <a href="#" class="nav-link">Home</a> </li> --}}
        </ul>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i> </a> </li>
            <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"> <span class="d-none d-md-inline">{{ auth()->user()->name }}</span> </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end"> <!--begin::User Image-->
                    <li class="user-header text-bg-primary" style="min-height: 65px">
                        <p>
                            {{ auth()->user()->name }}
                        </p>
                    </li>
                    <li class="user-footer">
                        {{-- <a href="#" class="btn btn-default btn-flat">Profile</a> --}}
                        <a href="{{ route('logout') }}" class="btn btn-default btn-flat float-end">Sign out</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
