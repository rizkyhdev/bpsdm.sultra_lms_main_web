<aside id="sidebar" style="background-color: #88d4e1;" class="text-black p-4">
  <div class="d-flex align-items-center gap-2 mb-4">
    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
      <i class="far fa-user text-black fs-3"></i>
    </div>
    <div>
      @auth
          <p class="mb-0 fw-bold">{{ auth()->user()->name }}</p>
          <small class="text-info">{{ auth()->user()->email }}</small>
      @else
          <p class="mb-0 fw-bold">Admin</p>
          <small class="text-white">admin@example.com</small>
      @endauth
    </div>
  </div>
  <nav>
    <h6 class="fw-bold text-uppercase text-white">Admin</h6>
    <ul class="nav flex-column mb-4">
      <li class="nav-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('admin.dashboard') }}">
          <i class="fas fa-home me-2"></i>Dashboard
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'admin.users') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('admin.users.index') }}">
          <i class="fas fa-users me-2"></i>Users
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'admin.courses') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('admin.courses.index') }}">
          <i class="fas fa-book me-2"></i>Pelatihan
        </a>
      </li>
      <!-- <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'admin.modules') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('admin.courses.index') }}">
          <i class="fas fa-layer-group me-2"></i>Modules
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'admin.sub_modules') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('admin.courses.index') }}">
          <i class="fas fa-sitemap me-2"></i>Sub-Modules
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'admin.contents') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('admin.courses.index') }}">
          <i class="far fa-file-alt me-2"></i>Contents
        </a>
      </li> -->
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'admin.quizzes') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('admin.quizzes.index-all') }}">
          <i class="fas fa-question-circle me-2"></i>Kuis
        </a>
      </li>
      <!-- <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'admin.questions') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('admin.enrollments.index') }}">
          <i class="fas fa-list-ol me-2"></i>Questions
        </a>
      </li> -->
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'admin.enrollments') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('admin.enrollments.index') }}">
          <i class="fas fa-user-check me-2"></i>Peserta Pelatihan
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'admin.certificates') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('admin.certificates.index') }}">
          <i class="fas fa-certificate me-2"></i>Sertifikat
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'admin.reports') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('admin.reports.dashboard') }}">
          <i class="fas fa-chart-bar me-2"></i>Laporan
        </a>
      </li>
    </ul>
    <h6 class="fw-bold text-uppercase text-white">Account</h6>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link text-black" href="#"
           onclick="event.preventDefault(); document.getElementById('logout-form-admin').submit();">
          <i class="fas fa-sign-out-alt me-2"></i>Keluar
        </a>
        <form id="logout-form-admin" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>
      </li>
    </ul>
  </nav>
</aside>

<!-- @php
    $nav = [
        ['label' => __('Dashboard'), 'route' => 'admin.dashboard', 'icon' => 'home', 'active' => request()->routeIs('admin.dashboard')],
        ['label' => __('Users'), 'route' => 'admin.users.index', 'icon' => 'users', 'active' => request()->routeIs('admin.users.*')],
        ['label' => __('Courses'), 'route' => 'admin.courses.index', 'icon' => 'book-open', 'active' => request()->routeIs('admin.courses.*')],
        ['label' => __('Reports'), 'route' => 'admin.reports.dashboard', 'icon' => 'chart', 'active' => request()->routeIs('admin.reports.*')],
        ['label' => __('Logs'), 'route' => 'admin.reports.dashboard', 'icon' => 'document', 'active' => false],
        ['label' => __('Notifications'), 'route' => 'admin.users.index', 'icon' => 'bell', 'active' => false],
        ['label' => __('Settings'), 'route' => 'admin.dashboard', 'icon' => 'cog', 'active' => false],
    ];
@endphp
<div class="h-[calc(100vh-4rem)] overflow-y-auto p-3">
    <ul class="space-y-1">
        @foreach($nav as $item)
            <li>
                <a href="{{ route($item['route']) }}" @class([
                    'flex items-center gap-3 px-3 py-2 rounded-md text-sm',
                    'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/50 dark:text-indigo-300' => $item['active'],
                    'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' => !$item['active'],
                ]) aria-current="{{ $item['active'] ? 'page' : 'false' }}">
                    <span class="inline-flex h-5 w-5 items-center justify-center">
                        {{-- Simple icon placeholders; replace with your icon component if any --}}
                        <span class="block h-1.5 w-1.5 rounded-full bg-current"></span>
                    </span>
                    <span>{{ $item['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</div> -->


