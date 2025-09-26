<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand px-0 py-3 m-0 text-center" href="{{ route('admin.dashboard.index') }}" target="_blank">
            @php
                $globalSetting = app(\App\Models\Setting::class)->first();
            @endphp
            @if ($globalSetting && $globalSetting->logo)
                <img class="w-75 sidebar-logo" src="{{ asset('storage/' . $globalSetting->logo) }}"
                    alt="{{ $globalSetting->school_name ?? ($globalSetting->title ?? 'School Logo') }}"
                    style="max-height: 50px; object-fit: contain;">
            @else
                <img class="w-75 sidebar-logo" src="{{ asset('assets/img/logo_text.png') }}"
                    alt="{{ $globalSetting->school_name ?? ($globalSetting->title ?? 'School') }}">
            @endif
        </a>
    </div>
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse w-100">
        <ul class="navbar-nav">

            @foreach (config('sidebar') as $menu)
                @if (@isset($menu['name']))
                    <li class="nav-item mt-3">
                        <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">
                            {{ $menu['name'] }}</h6>
                    </li>
                @endif

                @foreach ($menu['items'] as $sidebarItem)
                    <li class="nav-item">
                        <a class="nav-link @if (Route::is($sidebarItem['route'])) active bg-gradient-dark text-white @else nav-link text-dark @endif "
                            href="{{ route($sidebarItem['route']) }}">
                            <i class="material-symbols-outlined opacity-5">{{ $sidebarItem['icon'] }}</i>
                            <span class="nav-link-text ms-1">{{ $sidebarItem['text'] }}</span>
                        </a>
                    </li>
                @endforeach
            @endforeach
        </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
        <div class="mx-3">
            <form method="POST" action="{{ route('logout') }}" class="w-100">
                @csrf
                <button class="btn btn-outline-primary w-100" type="submit">Logout</button>

            </form>
        </div>
    </div>
</aside>
