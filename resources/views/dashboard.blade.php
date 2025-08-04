@extends('layout.app')

@section('content')
<div class="container-fluid my-1">
  <!-- Cards -->
  <div class="row">
    <div class="col-md-4">
      <div class="card text-white bg-info mb-3">
        <div class="card-body">
          <h5 class="card-title">ENROLLED COURSES</h5>
          <p class="card-text">0</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-white bg-warning mb-3">
        <div class="card-body">
          <h5 class="card-title">ACTIVE COURSES</h5>
          <p class="card-text">2</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-white bg-success mb-3">
        <div class="card-body">
          <h5 class="card-title">COMPLETE COURSES</h5>
          <p class="card-text">0</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

