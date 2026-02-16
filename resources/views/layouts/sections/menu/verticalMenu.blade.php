@php
use Illuminate\Support\Facades\Route;
$configData = Helper::appClasses();
$currentPath = request()->path();
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu"
  @foreach ($configData['menuAttributes'] as $attribute => $value)
    {{ $attribute }}="{{ $value }}"
  @endforeach
>

  {{-- App Brand --}}
  @if (!isset($navbarFull))
    <div class="app-brand demo">
      <a href="{{ url('/') }}" class="app-brand-link">
        @php
  $settings = \App\Models\AdminSetting::first();
  $dynamicLogo = $settings && $settings->system_logo 
                 ? asset($settings->system_logo) 
                 : asset('assets/img/branding/logo.png');
@endphp
        <span class="app-brand-logo demo">
          {{-- 1. Full Logo: Shown when menu is open or hovered --}}
          <img src="{{ $dynamicLogo }}" class="logo-full" style="width: 150px;">
          
          {{-- 2. Small Logo: Shown ONLY when menu is collapsed and NOT hovered --}}
          <img src="{{ asset('assets/img/branding/ezitech.png') }}" class="logo-small" style="display: none; width: 35px;">
        </span>
      </a>

      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <style>
          /* --- LOGO LOGIC --- */
          /* Default: Show full, hide small */
          .logo-full { display: block; }
          .logo-small { display: none; }

          /* When collapsed AND not hovered: Hide full, show small */
          .layout-menu-collapsed:not(.layout-menu-hover) .logo-full {
            display: none !important;
          }
          .layout-menu-collapsed:not(.layout-menu-hover) .logo-small {
            display: block !important;
          }

          /* --- TOGGLE ICON (DOT) LOGIC --- */
          /* 1. Hide the dot when collapsed */
          .layout-menu-collapsed .menu-vertical .menu-toggle-icon {
            display: none !important;
          }

          /* 2. Show the dot again when hovering over the collapsed menu */
          .layout-menu-collapsed.layout-menu-hover .menu-vertical .menu-toggle-icon {
            display: block !important;
          }
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

      {{-- MENU ITEM --}}
      <li class="menu-item {{ $activeClass }}">

        {{-- MENU WITH SUBMENU --}}
        @isset($menu->submenu)
          <a href="javascript:void(0);" class="menu-link menu-toggle">

            {{-- TEXT + ICON (REDIRECT ONLY) --}}
            <span
              onclick="event.stopPropagation(); window.location='{{ url($menu->url) }}';"
              class="d-flex align-items-center flex-grow-1"
              style="cursor:pointer;">

              @isset($menu->icon)
                <i class="{{ $menu->icon }}"></i>
              @endisset

              <div>{{ __($menu->name ?? '') }}</div>
            </span>

            {{-- TOGGLE ARROW (ONLY FOR SUBMENU) --}}
            <i class="menu-toggle-icon"></i>

          </a>
        @else
          {{-- SIMPLE MENU (NO SUBMENU, NO ARROW) --}}
          <a href="{{ url($menu->url) }}" class="menu-link">

            @isset($menu->icon)
              <i class="{{ $menu->icon }}"></i>
            @endisset

            <div>{{ __($menu->name ?? '') }}</div>

          </a>
        @endisset

        {{-- SUBMENU --}}
        @isset($menu->submenu)
          <ul class="menu-sub">
            @foreach ($menu->submenu as $sub)

              @php
                $subActive = '';
                $subSlug = ltrim($sub->slug ?? '', '/');
                if ($subSlug && str_starts_with($currentPath, $subSlug)) {
                    $subActive = 'active';
                }
              @endphp

              <li class="menu-item {{ $subActive }}">
                <a href="{{ url($sub->url) }}" class="menu-link">
                  <div>{{ __($sub->name) }}</div>
                </a>
              </li>

            @endforeach
          </ul>
        @endisset

      </li>
    @endforeach

  </ul>
</aside>
