@extends('layouts.instructor')

@section('title', __('Instructor Profile'))

@section('breadcrumb')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ __('Profile') }}</li>
      </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    <h4 class="font-weight-bold mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-2">{{ $user->email }}</p>
                    <p class="small text-secondary">{{ $user->bio }}</p>
                    <a href="{{ route('instructor.profile.edit') }}" class="btn btn-primary mt-2">{{ __('Edit Profile') }}</a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">{{ __('Details') }}</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">{{ __('Role') }}</dt>
                        <dd class="col-sm-8 text-capitalize">{{ $user->role ?? '-' }}</dd>
                        
                        <dt class="col-sm-4 text-muted">{{ __('Phone') }}</dt>
                        <dd class="col-sm-8">{{ $user->phone ?? '-' }}</dd>
                        
                        <dt class="col-sm-4 text-muted">{{ __('Timezone') }}</dt>
                        <dd class="col-sm-8">{{ $user->timezone ?? '-' }}</dd>
                        
                        <dt class="col-sm-4 text-muted">{{ __('Locale') }}</dt>
                        <dd class="col-sm-8">{{ $user->locale ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
