@php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

$configData = Helper::appClasses();
$currentPath = request()->path();

// English comments: Get the first segment of the URL (e.g., 'admin' or 'manager')
$firstSegment = Request::segment(1);

/** * English comments: 
 * 1. If the URL starts with 'admin', we FORCE the admin menu.
 * 2. If the URL starts with 'manager', we FORCE the manager menu.
 * 3. Fallback to Admin menu for any other case.
 */
if ($firstSegment == 'admin') {
    $menuPath = base_path('resources/menu/verticalMenu.json');
} elseif ($firstSegment == 'manager' || Auth::guard('manager')->check()) {
    $menuPath = base_path('resources/menu/managerMenu.json');
} else {
    // English comments: Default fallback to Admin Menu
    $menuPath = base_path('resources/menu/verticalMenu.json');
}

// English comments: Final safety check for file existence
if (!file_exists($menuPath)) {
    $menuPath = base_path('resources/menu/verticalMenu.json');
}

$menuJson = file_get_contents($menuPath);
$menuData = [json_decode($menuJson)];
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu"
  @foreach ($configData['menuAttributes'] as $attribute => $value)
    {{ $attribute }}="{{ $value }}"
  @endforeach
>

  {{-- App Brand - Same for everyone --}}
  @if (!isset($navbarFull))
  <style>
  /* English comments: Display logic for full and small logo */
  .logo-full { display: block; }
  .logo-small { display: none; }

  /* English comments: When menu is collapsed, hide full logo and show small one */
  .layout-menu-collapsed:not(.layout-menu-hover) .logo-full { display: none !important; }
  .layout-menu-collapsed:not(.layout-menu-hover) .logo-small { display: block !important; }

  /* English comments: Hide the toggle button (i tags) when the menu is collapsed */
  .layout-menu-collapsed:not(.layout-menu-hover) .layout-menu-toggle i { 
    display: none !important; 
  }
</style>
    <div class="app-brand demo">
      <a href="{{ url('/') }}" class="app-brand-link">
        @php
          // English comments: Fetch dynamic logo from AdminSettings for both roles
          $settings = \App\Models\AdminSetting::first();
          $dynamicLogo = $settings && $settings->system_logo 
                         ? asset($settings->system_logo) 
                         : asset('assets/img/branding/logo.png');
        @endphp
        <span class="app-brand-logo demo">
          {{-- 1. Full Logo --}}
          <img src="{{ $dynamicLogo }}" class="logo-full" style="width: 150px;">
          
          {{-- 2. Small Logo (Collapsed state) --}}
          <img src="{{ asset('assets/img/branding/ezitech.png') }}" class="logo-small" style="display: none; width: 35px;">
        </span>
      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <style>
          .logo-full { display: block; }
          .logo-small { display: none; }
          .layout-menu-collapsed:not(.layout-menu-hover) .logo-full { display: none !important; }
          .layout-menu-collapsed:not(.layout-menu-hover) .logo-small { display: block !important; }
        </style>
        <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
        <i class="icon-base ti tabler-x d-block d-xl-none"></i>
      </a>
    </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @foreach ($menuData[0]->menu as $menu)

      {{-- MENU HEADER --}}
      @if (isset($menu->menuHeader))
        <li class="menu-header small">
          <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
        </li>
        @continue
      @endif

      {{-- ACTIVE LOGIC --}}
      @php
        $activeClass = '';
        $menuSlug = ltrim($menu->slug ?? '', '/');
        if ($menuSlug && str_starts_with($currentPath, $menuSlug)) {
            $activeClass = 'active';
        }

        if (isset($menu->submenu)) {
            foreach ($menu->submenu as $sub) {
                $subSlug = ltrim($sub->slug ?? '', '/');
                if ($subSlug && str_starts_with($currentPath, $subSlug)) {
                    $activeClass = 'active open';
                    break;
                }
            }
        }
      @endphp

      <li class="menu-item {{ $activeClass }}">
        @isset($menu->submenu)
          <a href="javascript:void(0);" class="menu-link menu-toggle">
            <span onclick="event.stopPropagation(); window.location='{{ url($menu->url) }}';" class="d-flex align-items-center flex-grow-1" style="cursor:pointer;">
              @isset($menu->icon) <i class="{{ $menu->icon }}"></i> @endisset
              <div>{{ __($menu->name ?? '') }}</div>
            </span>
            <i class="menu-toggle-icon"></i>
          </a>
          <ul class="menu-sub">
            @foreach ($menu->submenu as $sub)
              <li class="menu-item {{ str_contains($currentPath, ltrim($sub->slug ?? '', '/')) ? 'active' : '' }}">
                <a href="{{ url($sub->url) }}" class="menu-link"><div>{{ __($sub->name) }}</div></a>
              </li>
            @endforeach
          </ul>
        @else
          <a href="{{ url($menu->url) }}" class="menu-link">
            @isset($menu->icon) <i class="{{ $menu->icon }}"></i> @endisset
            <div>{{ __($menu->name ?? '') }}</div>
          </a>
        @endisset
      </li>
    @endforeach
  </ul>
</aside>