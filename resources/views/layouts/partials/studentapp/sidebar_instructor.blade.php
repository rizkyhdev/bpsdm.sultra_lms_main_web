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
          <p class="mb-0 fw-bold">Instructor</p>
          <small class="text-white">instructor@example.com</small>
      @endauth
    </div>
  </div>
  <nav>
    <h6 class="fw-bold text-uppercase text-white">Instructor</h6>
    <ul class="nav flex-column mb-4">
      <li class="nav-item {{ Route::is('instructor.dashboard') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('instructor.dashboard') }}">
          <i class="fas fa-home me-2"></i>Dashboard
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'instructor.courses') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('instructor.courses.index') }}">
          <i class="fas fa-book-open me-2"></i>Courses
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'instructor.modules') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('instructor.courses.index') }}">
          <i class="fas fa-layer-group me-2"></i>Modules
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'instructor.submodules') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('instructor.courses.index') }}">
          <i class="fas fa-sitemap me-2"></i>Sub-Modules
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'instructor.contents') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('instructor.courses.index') }}">
          <i class="far fa-file-alt me-2"></i>Contents
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'instructor.quizzes') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('instructor.quizzes.index') }}">
          <i class="fas fa-question-circle me-2"></i>Quizzes
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'instructor.questions') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('instructor.questions.index') }}">
          <i class="fas fa-list-ol me-2"></i>Questions
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'instructor.enrollments') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('instructor.enrollments.index') }}">
          <i class="fas fa-user-check me-2"></i>Enrollments
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'instructor.progress') ? 'active' : '' }}">
        <a class="nav-link text-black" href="#" onclick="event.preventDefault();">
          <i class="fas fa-tasks me-2"></i>Progress
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'instructor.attempts') ? 'active' : '' }}">
        <a class="nav-link text-black" href="{{ route('instructor.quizzes.index') }}">
          <i class="fas fa-clipboard-check me-2"></i>Attempts
        </a>
      </li>
      <li class="nav-item {{ str_starts_with(Route::currentRouteName(), 'instructor.reports') ? 'active' : '' }}">
        <a class="nav-link text-black" href="#" onclick="event.preventDefault();">
          <i class="fas fa-chart-bar me-2"></i>Reports
        </a>
      </li>
    </ul>
    <h6 class="fw-bold text-uppercase text-white">Account</h6>
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link text-black" href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>
      </li>
    </ul>
  </nav>
</aside>


