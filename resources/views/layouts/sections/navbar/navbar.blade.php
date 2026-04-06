@php
  $containerNav = $configData['contentLayout'] === 'compact' ? 'container-xxl' : 'container-fluid';
  $navbarDetached = $navbarDetached ?? '';
  $isNotDetached = !(isset($navbarDetached) && $navbarDetached == 'navbar-detached');
  
  $isManagerRoute = request()->is('manager/*') || request()->is('manager');
  $isSupervisorRoute = request()->is('supervisor/*') || request()->is('supervisor');
@endphp

<nav
  class="layout-navbar {{ $containerNav }} {{ $navbarDetached }} navbar navbar-expand-xl align-items-center{{ !$isNotDetached ? ' bg-navbar-theme' : '' }}"
  id="layout-navbar">
  
  @if ($isNotDetached)
  <div class="{{ $containerNav }}">
  @endif

    {{-- Route-based navbar selection --}}
    @if($isManagerRoute)
        @include('layouts/sections/navbar/manager-navbar-partial')
    @elseif($isSupervisorRoute)
        @include('layouts/sections/navbar/manager-navbar-partial')
    @else
        @include('layouts/sections/navbar/navbar-partial')
    @endif

  @if ($isNotDetached)
  </div>
  @endif

</nav>