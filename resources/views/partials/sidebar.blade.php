<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark"> <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <a href="/" class="brand-link">
            <img src="/logo.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow">
            <span class="brand-text fw-light">Notify</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2"> <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <li class="nav-header">Customers</li>

                <li class="nav-item">
                    <a href="{{ route('customers.index') }}" class="nav-link"> <i
                            class="nav-icon bi bi-people-fill"></i>
                        <p>Customers</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('segments.index') }}" class="nav-link"> <i class="nav-icon bi bi-people-fill"></i>
                        <p>Segments</p>
                    </a>
                </li>

                <li class="nav-header">Notifications</li>

                <li class="nav-item">
                    <a href="{{ route('notifications.index') }}" class="nav-link"> <i
                            class="nav-icon bi bi-envelope-heart-fill"></i>
                        <p>Notifications</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('notification-categories.index') }}" class="nav-link"> <i
                            class="nav-icon bi bi-collection-fill"></i>
                        <p>Notification Categories</p>
                    </a>
                </li>

                <li class="nav-header">Symlinks</li>

                <li class="nav-item">
                    <a href="{{ route('symlinks.index') }}" class="nav-link"> <i class="bi bi-link-45deg"></i>
                        <p>Symlinks</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
