@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Get user based on guard
$userName = '';
$userRole = '';
$userImage = asset('assets/img/branding/ezitech.png');

if (Auth::guard('intern')->check()) {
    $user = Auth::guard('intern')->user();
    $userName = $user->name;
    $userRole = 'Intern';
    $userImage = \App\Helpers\Helpers::getProfileImage($user);
} elseif (Auth::guard('manager')->check()) {
    $user = Auth::guard('manager')->user();
    $userName = $user->name;
    $userRole = $user->loginas ?? 'Manager';
    if ($user->image) {
        $userImage = asset($user->image);
    }
} elseif (Auth::guard('admin')->check()) {
    $user = Auth::guard('admin')->user();
    $userName = $user->name;
    $userRole = 'Admin';
    if ($user->image) {
        $userImage = asset($user->image);
    }
} else {
    // Fallback
    $account = \App\Models\AdminAccount::first();
    $userName = $account->name ?? 'User';
    $userRole = 'Admin';
}
@endphp

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
@if (isset($navbarFull))
<div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4 ms-0">
  <a href="{{ url('/') }}" class="app-brand-link">
    <span class="app-brand-logo demo">@include('_partials.macros')</span>
    <span class="app-brand-text demo menu-text fw-bold">{{ config('variables.templateName') }}</span>
  </a>

  <!-- Display menu close icon only for horizontal-menu with navbar-full -->
  @if (isset($menuHorizontal))
  <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
    <i class="icon-base ti tabler-x icon-sm d-flex align-items-center justify-content-center"></i>
  </a>
  @endif
</div>
@endif

<!-- ! Not required for layout-without-menu -->
@if (!isset($navbarHideToggle))
<div
  class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
  <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
    <i class="icon-base ti tabler-menu-2 icon-md"></i>
  </a>
</div>
@endif

<div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">

  @if (!isset($menuHorizontal))
  <!-- Search -->
  <div class="navbar-nav align-items-center">
    <div class="nav-item navbar-search-wrapper px-md-0 px-2 mb-0">
      <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
        <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
      </a>
    </div>
  </div>
  <!-- /Search -->
  @endif

  <ul class="navbar-nav flex-row align-items-center ms-md-auto">
    @if (isset($menuHorizontal))
    <!-- Search -->
    <li class="nav-item navbar-search-wrapper btn btn-text-secondary btn-icon rounded-pill">
      <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
        <span class="d-inline-block text-body-secondary fw-normal" id="autocomplete"></span>
      </a>
    </li>
    <!-- /Search -->
    @endif

    <!-- Language -->
    {{-- <li class="nav-item dropdown-language dropdown">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
        href="javascript:void(0);" data-bs-toggle="dropdown">
        <i class="icon-base ti tabler-language icon-22px text-heading"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ url('lang/en') }}"
            data-language="en" data-text-direction="ltr">
            <span>English</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'fr' ? 'active' : '' }}" href="{{ url('lang/fr') }}"
            data-language="fr" data-text-direction="ltr">
            <span>French</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{ url('lang/ar') }}"
            data-language="ar" data-text-direction="rtl">
            <span>Arabic</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item {{ app()->getLocale() === 'de' ? 'active' : '' }}" href="{{ url('lang/de') }}"
            data-language="de" data-text-direction="ltr">
            <span>German</span>
          </a>
        </li>
      </ul>
    </li> --}}
    <!--/ Language -->

    @if ($configData['hasCustomizer'] == true)
    <!-- Style Switcher -->
    
    <!-- / Style Switcher-->
    @endif


    <!-- Notification -->
    {{-- <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
      ... (rest of notification code) ...
    </li> --}}
    <!--/ Notification -->
    
    <!-- User -->
    <!-- <li class="nav-item navbar-dropdown dropdown-user dropdown ms-2">
      <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online"> -->
         @php
  // Fetch the admin account and general settings
  // $account = \App\Models\AdminAccount::first();
  
  $settings = \App\Models\AdminSetting::first(); // Assuming you have a settings model

  // Logic: 
  // 1. Check if account has a profile image.
  // 2. If not, fallback to system logo.
  // 3. Final fallback to a static local asset.
  
  if ($account && $account->image) {
      $dynamicImage = (str_starts_with($account->image, 'data:image')
            ? $account->image
            : asset($account->image));
  
  } else {
      $dynamicImage = asset('assets/img/branding/ezitech.png');
  }
@endphp

<!-- <img src="{{ $dynamicImage }}" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;" /> -->
        <!-- </div>
      </a> -->
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item mt-0"
            href="{{ Route::has('profile.show') ? route('profile.show') : url('/admin/settings') }}">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0 me-2">
                <div class="avatar avatar-online">
                  <img src="{{ $userImage }}" alt class="rounded-circle" />
                </div>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-0">{{ $userName }}</h6>
                <small class="text-body-secondary">{{ $userRole }}</small>
              </div>
            </div>
          </a>
        </li>
        <li>
          <div class="dropdown-divider my-1 mx-n2"></div>
        </li>
        <li>
          <a class="dropdown-item"
            href="{{ Route::has('profile.show') ? route('profile.show') : url('/admin/settings') }}">
            <i class="icon-base ti tabler-user me-3 icon-md"></i><span class="align-middle">My Profile</span> </a>
        </li>
  @if (Auth::check() && class_exists(\Laravel\Jetstream\Jetstream::class) && \Laravel\Jetstream\Jetstream::hasApiFeatures())        <li>
          <a class="dropdown-item" href="{{ route('api-tokens.index') }}">
            <i class="icon-base ti tabler-settings me-3 icon-md"></i><span class="align-middle">API Tokens</span> </a>
        </li>
        @endif
      
  @if (Auth::user() && class_exists(\Laravel\Jetstream\Jetstream::class) && \Laravel\Jetstream\Jetstream::hasTeamFeatures())        <li>
          <div class="dropdown-divider my-1 mx-n2"></div>
        </li>
        <li>
          <h6 class="dropdown-header">Manage Team</h6>
        </li>
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        <li>
          <a class="dropdown-item"
            href="{{ Auth::user() ? route('teams.show', Auth::user()->currentTeam->id) : 'javascript:void(0)' }}">
            <i class="icon-base bx bx-cog icon-md me-3"></i><span>Team Settings</span>
          </a>
        </li>
        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
        <li>
          <a class="dropdown-item" href="{{ route('teams.create') }}">
            <i class="icon-base bx bx-user icon-md me-3"></i><span>Create New Team</span>
          </a>
        </li>
        @endcan
        @if (Auth::user()->allTeams()->count() > 1)
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        <li>
          <h6 class="dropdown-header">Switch Teams</h6>
        </li>
        <li>
          <div class="dropdown-divider my-1"></div>
        </li>
        @endif
        @if (Auth::user())
        @foreach (Auth::user()->allTeams() as $team)
     
        @endforeach
        @endif
        @endif
        <li>
          <div class="dropdown-divider my-1 mx-n2"></div>
        </li>
        @if (Auth::check())
        <li>
          <a class="dropdown-item" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="icon-base bx bx-power-off icon-md me-3"></i><span>Logout</span>
          </a>
        </li>
        <form method="POST" id="logout-form" action="{{ route('logout') }}">
          @csrf
        </form>
        @else
        <li>
          <div class="d-grid px-2 pt-2 pb-1">
            <a class="btn btn-sm btn-danger d-flex"
              href="{{route('logout')}}">
              <small class="align-middle">Logout</small>
              <i class="icon-base ti tabler-login ms-2 icon-14px"></i>
            </a>
          </div>
        </li>
        @endif
      </ul>
    </li>
    <!--/ User -->
  </ul>
</div>