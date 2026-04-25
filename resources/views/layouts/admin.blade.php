@extends('layouts.studentapp')

@section('sidebar')
  @include('admin.layouts.partials.sidebar')
@endsection

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
          <h2 class="h3 mb-0 text-gray-800">@yield('title')</h2>
          @hasSection('breadcrumb')
            <nav aria-label="breadcrumb" class="mt-2">
              <ol class="breadcrumb mb-0 bg-transparent p-0 font-weight-normal" style="font-size: 0.875rem;">
                @yield('breadcrumb')
              </ol>
            </nav>
          @endif
      </div>
      <div>
          @yield('header-actions')
      </div>
  </div>
  @include('partials._flash')
  @include('partials._errors')
  @yield('admin_content')

@endsection


