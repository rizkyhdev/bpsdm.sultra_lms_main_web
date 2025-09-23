@extends('layouts.studentapp')
@section('content')
<div class="container mt-4">

    <!-- Header Bar -->
    <div class="d-flex justify-content-between align-items-center p-3 bg-info text-white rounded-top">
        <div><strong>Pelatihan PBJ Level - 1</strong></div>
        <div>Your Progress : <strong>0 of 100 (0 %)</strong></div>
        <button class="btn btn-light btn-sm">Mark as Complete</button>
    </div>

    <!-- Card Content -->
    <div class="bg-success text-white text-center py-5" style="min-height: 200px;">
        <h1 class="fw-bold">Pelatihan<br>Sertifikasi PBJ</h1>
    </div>

    <!-- Navigation Buttons -->
    <div class="d-flex justify-content-between bg-warning p-2 rounded-bottom">
        <button class="btn btn-outline-dark btn-sm">&lt; Prev</button>
        <button class="btn btn-outline-dark btn-sm">Next &gt;</button>
    </div>

</div>
@endsection




