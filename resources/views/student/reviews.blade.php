@extends('student.studentapp')
@section('content')
<div class="d-flex">
    <!-- Sidebar sudah ada di layout.app -->

    <!-- Content -->
    <div class="content-wrapper flex-grow-1 p-2" style="margin-left: 1px;">
        <div class="container-fluid my-1">
            <!-- Section Header -->
            <div style="background-color: #21b3ca;" class="text-white p-3 rounded-top">
                <h5 class="mb-0 fw-bold">ULASAN</h5>
            </div>
            <hr style="border-top: 4px solid #dee2e6;" class="my-0">

            <!-- Tabs -->
            <div style="background-color: #21b3ca;" class="p-2 rounded-bottom">
                <ul class="nav nav-tabs border-0 mt-4" id="coursesTab"></ul>
            </div>

            <!-- Card Review -->
            <div class="card shadow-sm border-0 mb-4 mt-3" style="border-radius: 12px;">
                <!-- Strip Atas -->
                <div class="w-100" 
                    style="height: 18px; background-color: #00ACC1; border-radius: 12px 12px 0 0;">
                </div>

                <div class="card-body d-flex">
                    <!-- Avatar -->
                <div class="me-4 text-center">
                    <!-- Kotak abu-abu -->
                    <div class="d-flex flex-column justify-content-center align-items-center rounded" 
                        style="width:110px; height:120px; background-color:#f0f0f0; border-radius:12px;">
                        
                        <!-- Lingkaran biru di dalam kotak -->
                        <div class="rounded-circle d-flex justify-content-center align-items-center"
                            style="width:70px; height:70px; background-color:#00ACC1;">
                            <i class="bi bi-person fs-1 text-white"></i>
                        </div>

                        <!-- Nama di dalam kotak -->
                        <p class="mt-2 text-info fw-semibold mb-0">Pengguna</p>
                    </div>
                </div>

                    <!-- Konten Review -->
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center">
                            <!-- Judul -->
                            <h6 class="fw-bold mb-1 text-uppercase text-decoration-underline me-3">
                                Jenis Pelatihan
                            </h6>
                            <!-- Rating -->
                            <div class="d-flex align-items-center">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <span class="fw-semibold text-dark ms-1">5.0 (1)</span>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <p class="text-muted mb-2" style="font-size: 0.95rem;">
                            "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor 
                            incididunt ut labore et dolore magna aliqua Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor 
                            incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud 
                            exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. "
                        </p>

                        <!-- Read More -->
                        <a href="#" class="fw-bold text-dark text-uppercase text-decoration-underline">READ MORE</a>
                    </div>
                </div>
            </div>

            <!-- Card Review -->
            <div class="card shadow-sm border-0 mb-4 mt-3" style="border-radius: 12px;">
                <!-- Strip Atas -->
                <div class="w-100" 
                    style="height: 18px; background-color: #00ACC1; border-radius: 12px 12px 0 0;">
                </div>

                <div class="card-body d-flex">
                    <!-- Avatar -->
                <div class="me-4 text-center">
                    <!-- Kotak abu-abu -->
                    <div class="d-flex flex-column justify-content-center align-items-center rounded" 
                        style="width:110px; height:120px; background-color:#f0f0f0; border-radius:12px;">
                        
                        <!-- Lingkaran biru di dalam kotak -->
                        <div class="rounded-circle d-flex justify-content-center align-items-center"
                            style="width:70px; height:70px; background-color:#00ACC1;">
                            <i class="bi bi-person fs-1 text-white"></i>
                        </div>

                        <!-- Nama di dalam kotak -->
                        <p class="mt-2 text-info fw-semibold mb-0">Pengguna</p>
                    </div>
                </div>

                    <!-- Konten Review -->
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center">
                            <!-- Judul -->
                            <h6 class="fw-bold mb-1 text-uppercase text-decoration-underline me-3">
                                Jenis Pelatihan
                            </h6>
                            <!-- Rating -->
                            <div class="d-flex align-items-center">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <span class="fw-semibold text-dark ms-1">5.0 (1)</span>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <p class="text-muted mb-2" style="font-size: 0.95rem;">
                            "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor 
                            incididunt ut labore et dolore magna aliqua Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor 
                            incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud 
                            exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. "
                        </p>

                        <!-- Read More -->
                        <a href="#" class="fw-bold text-dark text-uppercase text-decoration-underline">READ MORE</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection