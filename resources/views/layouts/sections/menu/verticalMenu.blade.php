@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Gate;

$configData = Helper::appClasses();
$currentPath = request()->path();
$firstSegment = Request::segment(1);

// 1. Menu file decide karein
if ($firstSegment == 'admin') {
    $menuPath = base_path('resources/menu/verticalMenu.json');
} else {
    $menuPath = base_path('resources/menu/managerMenu.json');
}

if (!file_exists($menuPath)) {
    $menuPath = base_path('resources/menu/verticalMenu.json');
}

// 2. JSON load karein
$menuJson = file_get_contents($menuPath);
$menuData = json_decode($menuJson);

// 3. --- ROLE BASED FILTERING LOGIC ---
if ($firstSegment == 'manager' && Auth::guard('manager')->check()) {
    $manager = Auth::guard('manager')->user();
    
    // Permission filter function
    $filteredMenu = array_filter($menuData->menu, function ($menu) use ($manager) {
        // Agar permission set nahi hai, toh menu dikha do
        if (!isset($menu->permission)) {
            return true;
        }
        // --- KEY CHANGE: Gate check for the menu item ---
        return Gate::forUser($manager)->allows('check-privilege', $menu->permission);
    });

    // Submenu filter logic
    foreach ($filteredMenu as $menu) {
        if (isset($menu->submenu)) {
            $menu->submenu = array_filter($menu->submenu, function ($sub) use ($manager) {
                if (!isset($sub->permission)) return true;
                // --- KEY CHANGE: Gate check for the submenu item ---
                return Gate::forUser($manager)->allows('check-privilege', $sub->permission);
            });
            // Re-index submenu array
            $menu->submenu = array_values($menu->submenu);
        }
    }

    $menuData->menu = array_values($filteredMenu);
}
// -------------------------------------

// Final data for loop
$menuDataFinal = [$menuData];
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu"
  @foreach ($configData['menuAttributes'] as $attribute => $value)
    {{ $attribute }}="{{ $value }}"
  @endforeach
>

  {{-- App Brand - Same for everyone --}}
  @if (!isset($navbarFull))
  <style>
  /* Display logic for full and small logo */
  .logo-full { display: block; }
  .logo-small { display: none; }

  /* When menu is collapsed, hide full logo and show small one */
  .layout-menu-collapsed:not(.layout-menu-hover) .logo-full { display: none !important; }
  .layout-menu-collapsed:not(.layout-menu-hover) .logo-small { display: block !important; }

  /* Hide the toggle button (i tags) when the menu is collapsed */
  .layout-menu-collapsed:not(.layout-menu-hover) .layout-menu-toggle i { 
    display: none !important; 
  }
</style>
    <div class="app-brand demo">
      <a href="{{ url('/') }}" class="app-brand-link">
        @php
          // Fetch dynamic logo from AdminSettings for both roles
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
        <i class="icon-base ti menu-toggle-icon d-none d-xl-block"></i>
        <i class="icon-base ti tabler-x d-block d-xl-none"></i>
      </a>
    </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    {{-- Loop mein $menuData[0] ki jagah $menuDataFinal[0] use karein --}}
    @foreach ($menuDataFinal[0]->menu as $menu)

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
            <span onclick="event.stopPropagation(); window.location='{{ url($menu->url ?? 'javascript:void(0);') }}';" class="d-flex align-items-center flex-grow-1" style="cursor:pointer;">
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