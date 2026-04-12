@extends('layouts.admin')

@section('title', __('Edit User'))

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:underline">{{ __('Dashboard') }}</a></li>
    <li><a href="{{ route('admin.users.index') }}" class="hover:underline">{{ __('Users') }}</a></li>
    <li class="text-gray-600 dark:text-gray-400" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
    @php /** @var App\Models\User $user */ @endphp
    <div class="max-w-5xl mx-auto mt-6">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" novalidate class="space-y-6" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Sidebar: Profile & Status -->
                <div class="lg:col-span-1 space-y-6">
                    <x-admin.card>
                        <div class="flex flex-col items-center">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover shadow-sm mb-4">
                            <h3 class="text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            <span class="mt-3 px-3 py-1 rounded-full text-xs font-semibold uppercase {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : ($user->role === 'instructor' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $user->role }}
                            </span>
                        </div>
                    </x-admin.card>

                    <x-admin.card :title="__('Account Status')">
                        <div class="space-y-4">
                            <x-admin.select name="is_validated" :label="__('Validation Status')" :options="['0'=>__('Pending / Not Validated'), '1'=>__('Verified')]" :value="old('is_validated', (string) $user->is_validated)" />
                            <x-admin.select name="role" :label="__('Account Role')" :options="['admin'=>__('Admin'),'instructor'=>__('Instructor'),'student'=>__('Student')]" :value="old('role', $user->role)" />
                        </div>
                    </x-admin.card>
                </div>

                <!-- Right Main Area: Basic Information -->
                <div class="lg:col-span-2 space-y-6">
                    <x-admin.card :title="__('Basic Information')">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 py-2">
                            <div class="md:col-span-2">
                                <x-admin.input name="name" :label="__('Full Name')" :value="old('name', $user->name)" />
                            </div>
                            <div class="md:col-span-1">
                                <x-admin.input name="nip" :label="__('NIP (Optional)')" :value="old('nip', $user->nip)" />
                            </div>
                            <div class="md:col-span-1">
                                <x-admin.input type="email" name="email" :label="__('Email Address')" :value="old('email', $user->email)" />
                            </div>
                            <div class="md:col-span-1">
                                <x-admin.input name="jabatan" :label="__('Position (Jabatan)')" :value="old('jabatan', $user->jabatan)" />
                            </div>
                            <div class="md:col-span-1">
                                <x-admin.input name="unit_kerja" :label="__('Work Unit (Unit Kerja)')" :value="old('unit_kerja', $user->unit_kerja)" />
                            </div>
                        </div>
                    </x-admin.card>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none transition-colors">
                            <i class="fas fa-times"></i>
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            <i class="fas fa-save"></i>
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection


