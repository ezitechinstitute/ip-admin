@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

$configData = Helper::appClasses();
$currentPath = request()->path();
$firstSegment = Request::segment(1);

// --- 1. IDENTIFY THE LOGGED IN USER (The fix you asked for) ---
// We check every guard to make sure $user is not null
$user = Auth::guard('admin')->user() 
     ?? Auth::guard('manager')->user() 
     ?? Auth::guard('intern')->user() 
     ?? Auth::user();

$userModules = $user->assigned_modules ?? [];
$userRole = strtolower($user->role ?? '');

// --- 2. DETECT ROLE FOR SHARED ROUTES ---
if (!in_array($firstSegment, ['admin', 'supervisor', 'manager', 'intern'])) {
    if ($userRole === 'admin') {
        $firstSegment = 'admin';
    } elseif ($userRole === 'intern') {
        $firstSegment = 'intern';
    } elseif (Auth::guard('manager')->check()) {
        $loginAs = trim(strtolower($user->loginas ?? ''));
        $firstSegment = ($loginAs === 'supervisor') ? 'supervisor' : 'manager';
    }
}

// --- 3. SELECT THE CORRECT JSON FILE ---
if ($firstSegment == 'admin') {
    $menuPath = base_path('resources/menu/verticalMenu.json');
} elseif ($firstSegment == 'supervisor') {
    $menuPath = base_path('resources/menu/supervisorMenu.json');
} elseif ($firstSegment == 'intern') {
    $menuPath = base_path('resources/menu/internMenu.json');
} else {
    $menuPath = base_path('resources/menu/managerMenu.json');
}

// Fallback if file doesn't exist
if (!file_exists($menuPath)) {
    $menuPath = base_path('resources/menu/verticalMenu.json');
}

// 4. LOAD THE DATA
$menuJson = file_get_contents($menuPath);
$menuData = json_decode($menuJson);
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu"
  @foreach ($configData['menuAttributes'] as $attribute => $value)
    {{ $attribute }}="{{ $value }}"
  @endforeach
>
  {{-- App Brand --}}
  @if (!isset($navbarFull))
    <style>
      .logo-full { display: block; }
      .logo-small { display: none; }
      .layout-menu-collapsed:not(.layout-menu-hover) .logo-full { display: none !important; }
      .layout-menu-collapsed:not(.layout-menu-hover) .logo-small { display: block !important; }
    </style>
    <div class="app-brand demo">
        @php
          $settings = \App\Models\AdminSetting::first();
          $dynamicLogo = $settings && $settings->system_logo ? asset($settings->system_logo) : asset('assets/img/branding/logo.png');
        @endphp
        <span class="app-brand-logo demo">
          <img src="{{ $dynamicLogo }}" class="logo-full" style="width: 150px;">
          <img src="{{ asset('assets/img/branding/ezitech.png') }}" class="logo-small" style="width: 35px;">
        </span>
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
        <i class="icon-base ti tabler-x d-block d-xl-none"></i>
      </a>
    </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <p class="text-white">Total Menu Items: {{ count($menuData->menu ?? []) }}</p>
    @foreach ($menuData->menu as $menu)
    @php
        if (!$user) continue;

        // 1. ALWAYS define these variables at the start of the loop
        $isHeader = isset($menu->menuHeader);
        $menuSlug = isset($menu->slug) && is_array($menu->slug) ? $menu->slug[0] : ($menu->slug ?? '');

        // 2. Define Access Logic
        if ($userRole === 'admin') {
            $hasAccess = true;
        } else {
            // Non-admins check headers or assigned modules
            $hasAccess = $isHeader || in_array($menuSlug, $userModules);
        }
    @endphp

    @if($hasAccess)
        @if($isHeader)
            <li class="menu-header small">
                <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
            </li>
        @else
            @php
                // Active class logic
                $activeClass = (Route::currentRouteName() == $menuSlug || Request::is(ltrim($menuSlug, '/') . '*')) ? 'active' : '';
                if(isset($menu->submenu)) {
                   foreach($menu->submenu as $sub) {
                       $subSlug = is_array($sub->slug) ? $sub->slug[0] : ($sub->slug ?? '');
                       if (Request::is(ltrim($subSlug, '/') . '*')) { $activeClass = 'active open'; break; }
                   }
                }
            @endphp

            <li class="menu-item {{ $activeClass }}">
                <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}" 
                   class="menu-link {{ isset($menu->submenu) ? 'menu-toggle' : '' }}">
                    @isset($menu->icon) <i class="{{ $menu->icon }}"></i> @endisset
                    <div>{{ __($menu->name ?? '') }}</div>
                </a>

                @if (isset($menu->submenu))
                    <ul class="menu-sub">
                        @foreach ($menu->submenu as $sub)
                            @php
                                $subSlug = is_array($sub->slug) ? $sub->slug[0] : ($sub->slug ?? '');
                                // Only show sub-items if they are in the allowed modules (or admin)
                                $hasSubAccess = ($userRole === 'admin') || in_array($subSlug, $userModules);
                            @endphp
                            @if($hasSubAccess)
                                <li class="menu-item {{ Request::is(ltrim($subSlug, '/') . '*') ? 'active' : '' }}">
                                    <a href="{{ url($sub->url) }}" class="menu-link">
                                        <div>{{ __($sub->name) }}</div>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </li>
        @endif
    @endif
@endforeach
  </ul>
</aside>