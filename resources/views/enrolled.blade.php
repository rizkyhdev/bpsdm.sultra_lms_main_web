@extends('layout.app') <!-- Sesuaikan dengan layout kamu -->

@section('content')
<div class="container-fluid my-1">
  <!-- Section Header -->
  <div style="background-color: #21b3ca;" class="text-white p-3 rounded-top">
    <h5 class="mb-0 fw-bold">Daftar Pelatihan</h5>
  </div>
  <hr style="border-top: 4px solid #dee2e6;" class="my-0">

  <!-- Tabs -->
  <div div style="background-color: #21b3ca;" class="p-2 rounded-bottom">
      <ul class="nav nav-tabs border-0 mt-2" id="coursesTab">
          <li class="nav-item">
              <a class="nav-link active btn-outline-info text-dark fw-semibold px-4 rounded me-4" href="{{ route('enrolled') }}" data-tab="enrolled">Pelatihan yang diikuti</a>
          </li>
          <li class="nav-item">
              <a class="nav-link btn-outline-info text-white fw-semibold px-4 rounded me-4" href="{{ route('active') }}" data-tab="active">Pelatihan yang Aktif</a>
          </li>
          <li class="nav-item">
              <a class="nav-link btn-outline-info text-white fw-semibold px-4 rounded" href="{{ route('complete') }}" data-tab="complete">Pelatihan yang Selesai</a>
          </li>
      </ul>
  </div>
  <!-- Course Cards -->
   <div class="container-fluid my-4 px-1">
      <div class="row justify-content g-4">
        <!-- Card 1 -->
        <div class="col-auto">
          <a href="#" class="text-decoration-none">
            <div class="card shadow border-0 h-100 hover-card"
                style="width:230px; border-radius:22px; overflow:hidden; cursor:pointer;">
              <div class="text-white d-flex align-items-center" 
                  style="background-color:#336799; height:95px; 
                          border-top-left-radius:22px; border-top-right-radius:22px;">
                <h5 class="ms-3 mt-2 fw-bold" 
                    style="font-size:1.1rem; line-height:1.3;">Pelatihan Sertifikasi PBJ</h5>
              </div>
              <div class="card-body p-3">
                <p class="mb-1 fw-semibold text-dark" style="font-size:0.9rem;">
                  Pelatihan Sertifikasi PBJ
                </p>
                <p class="text-muted mb-1" style="font-size:0.8rem;">
                  <i class="bi bi-clock me-1"></i>2 Jam 
                  <span class="mx-2">•</span>
                  <i class="bi bi-bar-chart-fill me-1"></i>Pemula
                </p>
                <p class="text-warning mb-1" style="font-size:0.8rem;">
                  ★★★★★ <span class="text-muted">(0)</span>
                </p>
              </div>
              <div style="border-top:3px solid #ffc107; margin:0 1rem;"></div>
              <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center pt-2 pb-3"
                  style="border-bottom-left-radius:22px; border-bottom-right-radius:22px;">
                <div class="d-flex align-items-center gap-2">
                  <div class="bg-warning rounded-circle d-flex justify-content-center align-items-center" 
                      style="width:28px; height:28px;">
                    <i class="bi bi-person-fill text-white"></i>
                  </div>
                  <small class="fw-semibold text-warning">Fasilitator</small>
                </div>
                <i class="bi bi-bookmark text-secondary text-warning"></i>
              </div>
            </div>
          </a>
        </div>

        <!-- Card 2 -->
        <div class="col-auto">
          <a href="#" class="text-decoration-none">
            <div class="card shadow border-0 h-100 hover-card"
                style="width:230px; border-radius:22px; overflow:hidden; cursor:pointer;">
              <div class="text-white d-flex align-items-center" 
                  style="background-color:#fe670e; height:95px; 
                          border-top-left-radius:22px; border-top-right-radius:22px;">
                <h5 class="ms-3 mt-2 fw-bold" 
                    style="font-size:1.1rem; line-height:1.3;">Pelatihan Sertifikasi PBJ</h5>
              </div>
              <div class="card-body p-3">
                <p class="mb-1 fw-semibold text-dark" style="font-size:0.9rem;">
                  Pelatihan Sertifikasi PBJ
                </p>
                <p class="text-muted mb-1" style="font-size:0.8rem;">
                  <i class="bi bi-clock me-1"></i>3 Jam 
                  <span class="mx-2">•</span>
                  <i class="bi bi-bar-chart-fill me-1"></i>Menengah
                </p>
                <p class="text-warning mb-1" style="font-size:0.8rem;">
                  ★★★★☆ <span class="text-muted">(5)</span>
                </p>
              </div>
              <div style="border-top:3px solid #ffc107; margin:0 1rem;"></div>
              <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center pt-2 pb-3"
                  style="border-bottom-left-radius:22px; border-bottom-right-radius:22px;">
                <div class="d-flex align-items-center gap-2">
                  <div class="bg-warning rounded-circle d-flex justify-content-center align-items-center" 
                      style="width:28px; height:28px;">
                    <i class="bi bi-person-fill text-white"></i>
                  </div>
                  <small class="fw-semibold text-warning">Fasilitator</small>
                </div>
                <i class="bi bi-bookmark text-secondary text-warning"></i>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
</div>
@endsection
@section('scripts')
    <script src="{{ asset('./js/content.js') }}"></script>
@endsection
