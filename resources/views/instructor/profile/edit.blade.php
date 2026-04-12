@extends('layouts.instructor')

@section('title', __('Edit Instructor Profile'))

@section('breadcrumb')
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('instructor.dashboard') }}">Instructor</a></li>
        <li class="breadcrumb-item"><a href="{{ route('instructor.profile.show') }}">{{ __('Profile') }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
      </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">{{ __('Update Your Profile') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('instructor.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row mb-3">
                            <div class="col-md-6 form-group">
                                <label for="name" class="font-weight-bold">{{ __('Name') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="email" class="font-weight-bold">{{ __('Email') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 form-group">
                                <label for="phone" class="font-weight-bold">{{ __('Phone') }}</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="avatar" class="font-weight-bold">{{ __('Avatar (Optional)') }}</label>
                                <input type="file" class="form-control-file @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                                @error('avatar')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @if($user->avatar)
                                    <small class="form-text text-success mt-1"><i class="fas fa-check-circle"></i> Current avatar is set.</small>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 form-group">
                                <label for="timezone" class="font-weight-bold">{{ __('Timezone') }}</label>
                                <select class="form-control" id="timezone" name="timezone">
                                    <option value="">{{ __('Select timezone') }}</option>
                                    @foreach(timezone_identifiers_list() as $tz)
                                        <option value="{{ $tz }}" {{ old('timezone', $user->timezone) == $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="locale" class="font-weight-bold">{{ __('Locale') }}</label>
                                <select class="form-control" id="locale" name="locale">
                                    <option value="">{{ __('Select locale') }}</option>
                                    <option value="id" {{ old('locale', $user->locale ?? 'id') == 'id' ? 'selected' : '' }}>Bahasa Indonesia</option>
                                    <option value="en" {{ old('locale', $user->locale) == 'en' ? 'selected' : '' }}>English</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="bio" class="font-weight-bold">{{ __('Bio (Optional)') }}</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('instructor.profile.show') }}" class="btn btn-light border">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> {{ __('Save changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
