@extends('student.studentapp')
@section('content')
<div class="container mt-4">
    <div class="training-header mb-4">
        <h2>Pelatihan Sertifikasi PBJ</h2>
    </div>

    <div class="row">
        <!-- kolom kiri -->
        <div class="col-md-8">
            <div class="card mb-3 border-0" style="box-shadow:0 4px 12px rgba(0,0,0,0.12);">
                <div class="card-body">
                    <h4 class="fw-bold">Pelatihan PBJ Level - 1</h4>
                    <div class="d-flex gap-3 small text-muted mb-2">        
                        <div style="font-size:12px; color:#d4a017; display:flex; gap:20px; align-items:center;">
                            <span style="display:flex; align-items:center; gap:5px;">
                                <i class="fa-regular fa-clock"></i> 2 jam
                            </span>
                            <span style="display:flex; align-items:center; gap:5px;">
                                <i class="fa-solid fa-signal"></i> Pemula
                            </span>
                            <span style="display:flex; align-items:center; gap:5px;">
                                <i class="fa-solid fa-user-group"></i> Enrolled
                            </span>
                        </div>
                    </div>
                    <div class="mb-3" style="font-size: 12px;">⭐⭐⭐⭐⭐ (1)</div>
                    <div class="d-flex align-items-center mb-3 mt-2 pb-2" 
                        style="border-bottom: 1px solid black;">
                    <div class="icon-circle bg-gold me-2">
                         <i class="fa-solid fa-user text-white"></i>
                    </div>
                         <span class="fw-semibold text-gold">Username</span>
                    </div>
                    <!-- Tabs -->
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#desc">Description</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#announcement">Announcement</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#review">Review</button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card mb-3 border-0" style="background-color: #88d3e1; min-height: 200px; box-shadow:0 4px 12px rgba(0,0,0,0.12);">
                <div class="tab-content p-3 border-top-1 rounded-bottom">
                        <div class="tab-pane fade show active" id="desc">Description</div>
                        <div class="tab-pane fade" id="announcement">Announcement</div>
                        <div class="tab-pane fade" id="review">Nice</div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-md-4">
            <div class="card mb-3 border-0 text-white" style="background-color: #88d3e1; box-shadow:0 4px 12px rgba(0,0,0,0.12);">
                <div class="card-body text-center">
                    
                  
                    <button class="btn btn-light w-100 fw-bold mb-3"><a href="{{ url('/sub_modul2') }}">Start Learning</a></button>
                    <div class="alert alert-warning text-dark fw-bold py-2" role="alert">
                ⚠️ Complete all lessons to mark this course as complete
                    </div>
                </div>
            </div>

            <div class="card mb-3 border-0" style="box-shadow:0 4px 12px rgba(0,0,0,0.12);">
                <div class="card-body">
                    <p class="mb-0" style="color: #21b3ca">You enrolled this course on <strong>July 2, 2025</strong></p>
                </div>
            </div>

            <!-- Accordion -->
            <div class="accordion shadow-sm" id="accordionExample" style="box-shadow:0 4px 12px rgba(0,0,0,0.12);">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#level1">
                            Pelatihan PBJ Level-1
                        </button>
                    </h2>
                    <div id="level1" class="accordion-collapse collapse show">
                        <div class="accordion-body">Konten Level 1</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#level2">
                            Pelatihan PBJ Level-2
                        </button>
                    </h2>
                    <div id="level2" class="accordion-collapse collapse">
                        <div class="accordion-body">Konten Level 2</div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#level3">
                            Pelatihan PBJ Level-3
                        </button>
                    </h2>
                    <div id="level3" class="accordion-collapse collapse">
                        <div class="accordion-body">Konten Level 3</div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection

