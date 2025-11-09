@extends('layouts.studentapp')

@section('title', __('Settings'))

@section('content')
<div class="container-fluid my-1">
    <!-- Section Header -->
    <div style="background-color: #21b3ca;" class="text-white p-3 rounded-top">
        <h5 class="mb-0 fw-bold">{{ __('Settings') }}</h5>
    </div>
    <hr style="border-top: 4px solid #dee2e6;" class="my-0">

    <!-- Settings Card -->
    <div class="card shadow-sm border-0 mb-4 mt-3" style="border-radius: 12px;">
        <!-- Strip Atas -->
        <div class="w-100" style="height: 18px; background-color: #00ACC1; border-radius: 12px 12px 0 0;"></div>

        <div class="card-body p-4">
            <h6 class="fw-bold mb-4">{{ __('Preferences') }}</h6>
            
            <form method="POST" action="{{ route('student.settings.index') }}">
                @csrf
                @method('PATCH')

                <div class="row g-3 mb-4">
                    <!-- Theme -->
                    <div class="col-md-6">
                        <label for="theme" class="form-label fw-semibold">{{ __('Theme') }}</label>
                        <select name="theme" id="theme" class="form-select" required>
                            <option value="light" {{ ($settings->theme ?? 'light') === 'light' ? 'selected' : '' }}>{{ __('Light') }}</option>
                            <option value="dark" {{ ($settings->theme ?? 'light') === 'dark' ? 'selected' : '' }}>{{ __('Dark') }}</option>
                            <option value="system" {{ ($settings->theme ?? 'light') === 'system' ? 'selected' : '' }}>{{ __('System') }}</option>
                        </select>
                        @error('theme')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Timezone -->
                    <div class="col-md-6">
                        <label for="timezone" class="form-label fw-semibold">{{ __('Timezone') }}</label>
                        <select name="timezone" id="timezone" class="form-select" required>
                            <option value="">{{ __('Select timezone') }}</option>
                            @foreach(timezone_identifiers_list() as $tz)
                                <option value="{{ $tz }}" {{ ($settings->timezone ?? config('app.timezone')) === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                            @endforeach
                        </select>
                        @error('timezone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Locale -->
                    <div class="col-md-6">
                        <label for="locale" class="form-label fw-semibold">{{ __('Locale') }}</label>
                        <select name="locale" id="locale" class="form-select" required>
                            <option value="en" {{ ($settings->locale ?? app()->getLocale()) === 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
                            <option value="id" {{ ($settings->locale ?? app()->getLocale()) === 'id' ? 'selected' : '' }}>{{ __('Bahasa Indonesia') }}</option>
                        </select>
                        @error('locale')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email Notifications -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold d-block">{{ __('Email Notifications') }}</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="email_notifications" value="1" id="email_notifications" 
                                   {{ old('email_notifications', (int)($settings->email_notifications ?? 0)) == 1 ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_notifications">
                                {{ __('Enable email notifications') }}
                            </label>
                        </div>
                        @error('email_notifications')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <button type="submit" class="btn btn-primary px-4" style="background-color: #21b3ca; border-color: #21b3ca;">
                        <i class="fas fa-save me-2"></i>{{ __('Save settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
