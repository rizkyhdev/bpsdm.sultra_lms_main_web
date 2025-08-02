@extends('app') <!-- Sesuaikan dengan layout kamu -->

@section('content')
<div class="container-fluid my-1">
  <!-- Section Header -->
  <div style="background-color: #21b3ca;" class="text-white p-3 rounded-top">
    <h5 class="mb-0 fw-bold">ENROLLED COURSES</h5>
  </div>
  <hr style="border-top: 4px solid #dee2e6;" class="my-0">

  <!-- Tabs -->
  <div style="background-color: #21b3ca;" class="p-2 rounded-bottom">
    <ul class="nav nav-tabs border-0 mt-2" id="coursesTab">
      <li class="nav-item">
        <a class="nav-link btn-outline-info text-white fw-semibold px-4 rounded me-4" href="{{ route('enrolled') }}" data-tab="enrolled">Enrolled Courses</a>
      </li>
      <li class="nav-item">
        <a class="nav-link active btn-outline-info text-dark fw-semibold px-4 rounded me-4" href="{{ route('active') }}" data-tab="active">Active Courses</a>
      </li>
      <li class="nav-item">
        <a class="nav-link btn-outline-info text-white fw-semibold px-4 rounded" href="{{ route('complete') }}" data-tab="complete">Complete Courses</a>
      </li>
    </ul>
  </div>

  <!-- Course Cards -->
  <div class="row mt-4">
    <!-- Contoh 1 Card -->
    <div class="col-md-3 mb-3">
      <a href="#" class="text-decoration-none">
        <div class="card shadow rounded-4 border-0 h-100">
          <div class="card-header text-white rounded-top-3" style="background-color: #159796; height: 120px;">
            <h6 class="mt-4 ms-3">Active Class: Leadership</h6>
          </div>
          <div class="card-body">
            <p class="mb-1 text-dark">Leadership & Management</p>
            <p class="text-muted mb-1" style="font-size: 0.9rem;">
              <i class="bi bi-clock me-1"></i>2 hours
              <span class="mx-2">•</span>
              <i class="bi bi-bar-chart-fill me-1"></i>Beginner
            </p>
            <p class="text-warning mb-1" style="font-size: 0.9rem;">
              ★★★★★ <span class="text-muted">(0)</span>
            </p>
          </div>
          <div class="card-footer bg-white border-top-0 d-flex justify-content-between align-items-center pt-2 pb-3">
            <div class="d-flex align-items-center gap-2">
              <div class="bg-warning rounded-circle d-flex justify-content-center align-items-center" style="width: 32px; height: 32px;">
                <i class="bi bi-person-fill text-white"></i>
              </div>
              <small class="fw-semibold text-warning">Username</small>
            </div>
            <i class="bi bi-bookmark text-secondary"></i>
          </div>
        </div>
      </a>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('#coursesTab .nav-link');

    tabs.forEach(tab => {
      if (tab.classList.contains('active')) {
        tab.classList.add('bg-white', 'text-dark');
      }

      tab.addEventListener('click', function (e) {
        e.preventDefault();

        tabs.forEach(link => {
          link.classList.remove('active', 'bg-white', 'text-dark');
          link.classList.add('text-white');
        });

        tab.classList.add('active', 'bg-white', 'text-dark');

        const target = tab.getAttribute('data-tab');
        console.log(target);
      });
    });
  });
</script>
@endsection
