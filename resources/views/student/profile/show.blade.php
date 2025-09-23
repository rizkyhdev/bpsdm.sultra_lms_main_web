{{--
    @phpdoc
    Variabel:
    - $user: object{name,email,avatar_url,bio,phone,timezone,locale}
--}}
@extends('layouts.studentapp')

@section('title', __('Profile'))

@section('content')
    @include('student._breadcrumbs', ['crumbs' => [
        ['label' => __('Dashboard'), 'route' => 'student.dashboard'],
        ['label' => __('Profile')],
    ]])

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <x-student.card class="lg:col-span-1" :title="__('Your Info')">
            <div class="flex flex-col items-center text-center">
                <img src="{{ $user->avatar_url ?? asset('image/user.png') }}" alt="{{ __('Avatar') }}" class="h-24 w-24 rounded-full object-cover">
                <h1 class="mt-3 text-lg font-bold">{{ $user->name }}</h1>
                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</p>
                <p class="mt-2 text-sm">{{ $user->bio }}</p>
                <a href="{{ route('student.profile.show') }}#edit" class="mt-3 inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">{{ __('Edit Profile') }}</a>
            </div>
        </x-student.card>

        <x-student.card class="lg:col-span-2" :title="__('Details')">
            <dl class="text-sm space-y-2">
                <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-300">{{ __('Phone') }}</dt><dd>{{ $user->phone ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-300">{{ __('Timezone') }}</dt><dd>{{ $user->timezone ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-600 dark:text-gray-300">{{ __('Locale') }}</dt><dd>{{ $user->locale ?? '-' }}</dd></div>
            </dl>
        </x-student.card>
    </div>
@endsection


