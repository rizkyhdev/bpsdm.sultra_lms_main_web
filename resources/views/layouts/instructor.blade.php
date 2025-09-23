@extends('layouts.studentapp')

@section('sidebar')
  @include('layouts.partials.studentapp.sidebar_instructor')
@endsection

@section('content')
  @yield('title')
  @hasSection('breadcrumb')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-3">
        @yield('breadcrumb')
      </ol>
    </nav>
  @endif
  @include('partials._flash')
  @include('partials._errors')
  @yield('content')
@endsection


