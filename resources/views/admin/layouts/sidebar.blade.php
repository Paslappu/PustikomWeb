<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('admin.dashboard') }}" class="text-nowrap">
                <div class="d-flex align-items-center">
                    <h4 class="mb-0 px-2 fw-bolder">ADMIN</h4>
                </div>
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar border-top overflow-hidden" data-simplebar="">
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Home</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link @if ($active == 'dashboard') active @endif"
                        href="{{ route('admin.dashboard') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-layout-dashboard"></i>
                        </span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Master Data</span>
                </li>
                @if (auth()->user()->role->slug == 'admin')
                    <li class="sidebar-item">
                        <a class="sidebar-link @if ($active == 'user') active @endif"
                            href="{{ route('admin.user') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-device-2"></i>
                            </span>
                            <span class="hide-menu">User</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link @if ($active == 'category') active @endif"
                            href="{{ route('admin.category') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-device-2"></i>
                            </span>
                            <span class="hide-menu">Category Dana</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link @if ($active == 'tipe') active @endif"
                            href="{{ route('admin.tipe') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-device-2"></i>
                            </span>
                            <span class="hide-menu">Tipe</span>
                        </a>
                    </li>
                @endif
                <li class="sidebar-item">
                    <a class="sidebar-link @if ($active == 'gedung') active @endif"
                        href="{{ route('admin.gedung') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-building"></i>
                        </span>
                        <span class="hide-menu">Gedung</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link @if ($active == 'device') active @endif"
                        href="{{ route('admin.device') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-smartphone"></i>
                        </span>
                        <span class="hide-menu">Device</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link @if ($active == 'device-report') active @endif"
                        href="{{ route('admin.device-report') }}" aria-expanded="false">
                        <span>
                            <i class="ti ti-clipboard-list"></i>
                        </span>
                        <span class="hide-menu">Device Report</span>
                    </a>
                </li>



            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
