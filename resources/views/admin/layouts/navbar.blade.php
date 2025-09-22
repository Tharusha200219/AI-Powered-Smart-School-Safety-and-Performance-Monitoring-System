<nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                @php
                    $breadcrumbs = getBreadcrumbs();
                @endphp

                @if (is_array($breadcrumbs))
                    @foreach ($breadcrumbs as $key => $breadcrumb)
                        @if ($breadcrumb != 'index')
                            @php
                                $breadcrumb = ucfirst($breadcrumb);
                            @endphp
                            @if (count($breadcrumbs) - ($breadcrumb != 'index' ? 2 : 1) == $key)
                                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">
                                    {{ $breadcrumb }}
                                </li>
                            @else
                                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark"
                                        href="javascript:;">{{ $breadcrumb }}</a></li>
                            @endif
                        @endif
                    @endforeach
                @endif
            </ol>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center d-none d-md-block">
                <div class="input-group input-group-outline">
                    <label class="form-label">Type here...</label>
                    <input type="text" class="form-control">
                </div>
            </div>
            <ul class="navbar-nav d-flex align-items-center justify-content-end">
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </li>
                <li class="nav-item px-3 d-flex align-items-center d-none d-md-block">
                    <a href="{{ route('admin.setup.settings.index') }}" class="nav-link text-body p-0">
                        <i class="material-symbols-rounded fixed-plugin-button-nav">settings</i>
                    </a>
                </li>
                <li class="nav-item dropdown pe-3 d-flex align-items-center d-none d-md-block">
                    <a href="javascript:;" class="nav-link text-body p-0 position-relative" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="material-symbols-rounded">notifications</i>
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill"
                            id="notification-badge" style="display: none;">
                            <span id="notification-count">0</span>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton"
                        style="max-width: 350px; max-height: 400px; overflow-y: auto;">
                        <li class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Notifications</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="mark-all-read">
                                Mark all as read
                            </button>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <div id="notifications-container">
                            <li class="text-center text-muted py-3">
                                <i class="material-symbols-rounded">notifications</i>
                                <p class="mb-0">Loading notifications...</p>
                            </li>
                        </div>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li class="text-center">
                            <a href="#" class="btn btn-sm btn-link" id="view-all-notifications">View All
                                Notifications</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item d-flex align-items-center d-none d-md-block">
                    <a href="../pages/sign-in.html" class="nav-link text-body font-weight-bold px-0">
                        <i class="material-symbols-rounded">account_circle</i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
