@php
use App\Enums\UserType;
$userType = auth()->user()->usertype ?? null;
// usertype is cast to UserType enum, so compare with enum directly
$isStudent = $userType === UserType::STUDENT;
$isAdmin = in_array($userType, [UserType::ADMIN, UserType::USER]);

// Determine dashboard route based on user type
$dashboardRoute = $isStudent ? 'admin.student.dashboard.index' : 'admin.dashboard.index';

// Student portal section name for filtering
$studentPortalSection = 'Student Portal';
@endphp
<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand px-0 py-3 m-0 text-center" href="{{ route($dashboardRoute) }}">
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
            {{-- Unified permission-based sidebar for all user types --}}
            @foreach (config('sidebar') as $menu)
            @php
            $menuName = $menu['name'] ?? null;
            $isStudentPortal = ($menuName === $studentPortalSection);

            // For students: show only Student Portal section (skip admin-only sections)
            // For admins/staff: show all sections EXCEPT Student Portal
            if ($isStudent && !$isStudentPortal) continue;
            if (!$isStudent && $isStudentPortal) continue;
            @endphp

            @if ($menuName)
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">
                    {{ translateSidebarText($menuName) }}
                </h6>
            </li>
            @endif

            @foreach ($menu['items'] as $sidebarItem)
            @php
            // For students, filter items by permission
            // For admin/staff, show all items (permission enforced at controller level)
            $canView = $isStudent ? checkPermission($sidebarItem['route']) : true;

            // Build a wildcard for sub-page active matching
            // e.g. admin.student.homework.index → admin.student.homework.*
            $routeParts = explode('.', $sidebarItem['route']);
            array_pop($routeParts);
            $routeWildcard = implode('.', $routeParts) . '.*';
            $isActive = Route::is($sidebarItem['route']) || Route::is($routeWildcard);
            @endphp
            @if($canView)
            <li class="nav-item">
                <a class="nav-link {{ $isActive ? 'active bg-gradient-dark text-white' : 'nav-link text-dark' }}"
                    href="{{ route($sidebarItem['route']) }}">
                    <i class="material-symbols-outlined opacity-5">{{ $sidebarItem['icon'] }}</i>
                    <span class="nav-link-text ms-1">{{ translateSidebarText($sidebarItem['text']) }}</span>
                </a>
            </li>
            @endif
            @endforeach
            @endforeach

            @if($isStudent)
            {{-- Account section always visible for students --}}
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">{{ __('common.account') }}</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link @if (Route::is('admin.profile.*')) active bg-gradient-dark text-white @else text-dark @endif"
                    href="{{ route('admin.profile.index') }}">
                    <i class="material-symbols-outlined opacity-5">person</i>
                    <span class="nav-link-text ms-1">{{ __('common.my_profile') }}</span>
                </a>
            </li>
            @endif
        </ul>
    </div>
    <div class="sidenav-footer position-absolute w-100 bottom-0 ">
        <div class="mx-3">
            <form method="POST" action="{{ route('logout') }}" class="w-100">
                @csrf
                <button class="btn btn-outline-primary w-100" type="submit">{{ __('common.logout') }}</button>
            </form>
        </div>
    </div>
</aside>