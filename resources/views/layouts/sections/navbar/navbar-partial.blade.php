@php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$userName = '';
$userRole = '';
$defaultImage = asset('assets/img/branding/ezitech.png');
$userImage = $defaultImage;

if (Auth::guard('intern')->check()) {
    $user = Auth::guard('intern')->user();
    $userName = $user->name;
    $userRole = 'Intern';
    $userImage = $user->image ? asset($user->image) . '?v=' . time() : $defaultImage;
}
elseif (Auth::guard('manager')->check()) {
    $user = Auth::guard('manager')->user();
    $userName = $user->name;
    $userRole = $user->loginas ?? 'Manager';
    $userImage = $user->image ? asset($user->image) : $defaultImage;
} elseif (Auth::guard('admin')->check()) {
    $user = Auth::guard('admin')->user();
    $userName = $user->name;
    $userRole = 'Admin';
    $userImage = $user->image ? asset($user->image) : $defaultImage;
} else {
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
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill" id="nav-theme"
        href="javascript:void(0);" data-bs-toggle="dropdown">
        <i class="icon-base ti tabler-sun icon-22px theme-icon-active text-heading"></i>
        <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
      </a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
        <li>
          <button type="button" class="dropdown-item align-items-center active" data-bs-theme-value="light"
            aria-pressed="false">
            <span><i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i>Light</span>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark" aria-pressed="true">
            <span><i class="icon-base ti tabler-moon-stars icon-22px me-3" data-icon="moon-stars"></i>Dark</span>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system"
            aria-pressed="false">
            <span><i class="icon-base ti tabler-device-desktop-analytics icon-22px me-3"
                data-icon="device-desktop-analytics"></i>System</span>
          </button>
        </li>
      </ul>
    </li>
    <!-- / Style Switcher-->
    @endif

    <!-- Quick links  -->
    <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown">
      <a class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
        href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        <i class="icon-base ti tabler-layout-grid-add icon-22px text-heading"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-end p-0">
        <div class="dropdown-menu-header border-bottom">
          <div class="dropdown-header d-flex align-items-center py-3">
            <h6 class="mb-0 me-auto">Shortcuts</h6>
            {{-- <a href="javascript:void(0)"
              class="dropdown-shortcuts-add py-2 btn btn-text-secondary rounded-pill btn-icon" data-bs-toggle="tooltip"
              data-bs-placement="top" title="Add shortcuts"><i
                class="icon-base ti tabler-plus icon-20px text-heading"></i></a> --}}
          </div>
        </div>
        
        <div class="dropdown-shortcuts-list scrollable-container">
          <div class="row row-bordered overflow-visible g-0">
              <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-device-desktop-analytics icon-26px text-heading"></i>
              </span>
              <a href="{{ url('admin/dashboard') }}" class="stretched-link">Dashboard</a>
              <small>User Dashboard</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-settings icon-26px text-heading"></i>
              </span>
              <a href="{{ url('admin/settings') }}" class="stretched-link">Setting</a>
              <small>Account Settings</small>
            </div>
          </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-id icon-26px text-heading"></i>
              </span>
              <a href="{{ url('admin/intern-accounts') }}" class="stretched-link">Intern Accounts</a>
              <small>Tech & Status</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-file-invoice icon-26px text-heading"></i>
              </span>
              <a href="{{ url('admin/invoice') }}" class="stretched-link">Invoices</a>
              <small>Amount & Remaining</small>
            </div>
          </div>
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-user icon-26px text-heading"></i>
              </span>
              <a href="{{ url('admin/managers') }}" class="stretched-link">Role Managers</a>
              <small>Permission</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-users icon-26px text-heading"></i>
              </span>
              <a href="{{ url('admin/supervisors') }}" class="stretched-link">Role Supervisors</a>
              <small>Permission</small>
            </div>
          </div>
        
          <div class="row row-bordered overflow-visible g-0">
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
                <i class="icon-base ti tabler-cash-banknote icon-26px text-heading"></i>
              </span>
              <a href="{{ url('admin/withdraw') }}" class="stretched-link">Withdraw</a>
              <small>Amount & Status</small>
            </div>
            <div class="dropdown-shortcuts-item col">
              <span class="dropdown-shortcuts-icon rounded-circle mb-3">
               
                <i class="icon-base ti tabler-book icon-26px text-heading"></i>
              </span>
              <a href="{{ url('admin/knowledge-base') }}" class="stretched-link">Knowledge Base</a>
              <small>Useful Knowledge</small>
            </div>
          </div>
        </div>
      </div>
    </li>
    <!-- Quick links -->

    <!-- Notification -->
    {{-- <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
      ... (rest of notification code) ...
    </li> --}}
    <!--/ Notification -->
    
    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown ms-2">
      <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
          <img src="{{ $userImage }}" alt="Profile Picture" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('assets/img/branding/ezitech.png') }}';"/>
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item mt-0"
  href="{{ Auth::guard('intern')->check() ? route('intern.profile') : (Route::has('profile.show') ? route('profile.show') : url('/admin/settings')) }}">  
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0 me-2">
                <div class="avatar avatar-online">
                  <img src="{{ $userImage }}" alt class="rounded-circle" onerror="this.onerror=null; this.src='{{ asset('assets/img/branding/ezitech.png') }}';"/>
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
    href="{{ Auth::guard('intern')->check() ? route('intern.profile') : (Route::has('profile.show') ? route('profile.show') : url('/admin/settings')) }}">
    <i class="icon-base ti tabler-user me-3 icon-md"></i><span class="align-middle">My Profile</span>
  </a>
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
       @if (Auth::guard('intern')->check())
<li>
    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-navbar').submit();">
        <i class="icon-base ti tabler-logout me-3 icon-md"></i>
        <span>Logout</span>
    </a>
    <form id="logout-form-navbar" action="{{ route('intern.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</li>
@elseif (Auth::guard('manager')->check())
<li>
    <a class="dropdown-item" href="{{ route('manager.logout') }}">
        <i class="icon-base ti tabler-logout me-3 icon-md"></i>
        <span>Logout</span>
    </a>
</li>
@elseif (Auth::guard('admin')->check())
<li>
    <a class="dropdown-item" href="{{ route('logout') }}">
        <i class="icon-base ti tabler-logout me-3 icon-md"></i>
        <span>Logout</span>
    </a>
</li>
@else
<li>
    <a class="dropdown-item" href="{{ route('login') }}">
        <i class="icon-base ti tabler-login me-3 icon-md"></i>
        <span>Login</span>
    </a>
</li>
@endif
      </ul>
    </li>
    <!--/ User -->
  </ul>
</div>