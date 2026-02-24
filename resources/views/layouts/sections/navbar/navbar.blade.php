@php
  $containerNav = $configData['contentLayout'] === 'compact' ? 'container-xxl' : 'container-fluid';
  $navbarDetached = $navbarDetached ?? '';

  // English comments: Check current route/URL to decide which navbar to show
  // This is more reliable than checking guards if sessions are overlapping
  $isManagerRoute = request()->is('manager/*') || request()->is('manager');
  $isAdminRoute = request()->is('admin/*') || request()->is('admin') || request()->is('dashboard*');
@endphp

@if (isset($navbarDetached) && $navbarDetached == 'navbar-detached')
<nav
  class="layout-navbar {{ $containerNav }} {{ $navbarDetached }} navbar navbar-expand-xl align-items-center bg-navbar-theme"
  id="layout-navbar">
  
  {{-- English comments: Decision logic based on Route first, then Guard --}}
  @if($isManagerRoute)
      @include('layouts/sections/navbar/manager-navbar-partial')
  @else
      {{-- English comments: Default to admin/regular navbar for everything else --}}
      @include('layouts/sections/navbar/navbar-partial')
  @endif

</nav>
@else
<nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
  <div class="{{ $containerNav }}">
      @if($isManagerRoute)
          @include('layouts/sections/navbar/manager-navbar-partial')
      @else
          @include('layouts/sections/navbar/navbar-partial')
      @endif
  </div>
</nav>
@endif