@extends('layouts.admin')

@section('title', __('Edit User'))

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:underline">{{ __('Dashboard') }}</a></li>
    <li><a href="{{ route('admin.users.index') }}" class="hover:underline">{{ __('Users') }}</a></li>
    <li class="text-gray-600 dark:text-gray-400" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
    @php /** @var App\Models\User $user */ @endphp
    <x-admin.card>
        <form action="{{ route('admin.users.update', $user) }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-admin.input name="nip" :label="__('NIP')" :value="old('nip', $user->nip)" />
                <x-admin.input name="nama" :label="__('Name')" :value="old('nama', $user->nama)" />
                <x-admin.input type="email" name="email" :label="__('Email')" :value="old('email', $user->email)" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <x-admin.input name="jabatan" :label="__('Position')" :value="old('jabatan', $user->jabatan)" />
                <x-admin.input name="unit_kerja" :label="__('Unit')" :value="old('unit_kerja', $user->unit_kerja)" />
                <x-admin.select name="role" :label="__('Role')" :options="['admin'=>__('Admin'),'instructor'=>__('Instructor'),'student'=>__('Student')]" :value="old('role', $user->role)" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <x-admin.select name="is_validated" :label="__('Validated?')" :options="['0'=>__('No'),'1'=>__('Yes')]" :value="old('is_validated', (string) $user->is_validated)" />
            </div>

            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">{{ __('Back') }}</a>
                <button type="submit" class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-500">{{ __('Save') }}</button>
            </div>
        </form>
    </x-admin.card>
@endsection


