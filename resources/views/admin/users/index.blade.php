@extends('layouts.admin')

@section('title', __('Users'))

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}" class="hover:underline">{{ __('Dashboard') }}</a></li>
    <li class="text-gray-600 dark:text-gray-400" aria-current="page">{{ __('Users') }}</li>
@endsection

@section('header-actions')
    @can('create', App\Models\User::class)
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-500">+ <span class="hidden sm:inline">{{ __('Add') }}</span></a>
    @endcan
@endsection

@section('content')
    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
        <x-admin.card>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                <x-admin.input name="search" :label="__('Search (NIP/Name/Email)')" :value="request('search')" />
                <x-admin.select name="role" :label="__('Role')" :options="['all'=>__('All'),'admin'=>__('Admin'),'instructor'=>__('Instructor'),'student'=>__('Student')]" :value="request('role')" />
                 {{-- <x-admin.select name="is_validated" :label="__('Validation')" :options="[''=>__('All'),'1'=>__('Validated'),'0'=>__('Not yet')]" :value="request('is_validated')" /> --}}
                <x-admin.select name="per_page" :label="__('Per page')" :options="[10=>10,25=>25,50=>50,100=>100]" :value="request('per_page',10)" />
                <div class="sm:col-span-2 lg:col-span-1 flex items-end">
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-2 px-3 py-2 rounded-md border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">{{ __('Filter') }}</button>
                </div>
            </div>
        </x-admin.card>
    </form>

    <x-admin.card>
        <x-admin.table :headers="[
            ['key'=>'nip','label'=>__('NIP'), 'sortable'=>true],
            ['key'=>'name','label'=>__('Name'), 'sortable'=>true],
            ['key'=>'email','label'=>__('Email'), 'sortable'=>true],
            ['key'=>'role','label'=>__('Role')],
            ['key'=>'validated','label'=>__('Validated')],
            ['key'=>'actions','label'=>__('Actions')],
        ]">
            @forelse($users as $user)
                <tr>
                    <td class="px-4 py-2">{{ $user->nip }}</td>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2"><x-admin.badge color="indigo">{{ strtoupper($user->role) }}</x-admin.badge></td>
                    <td class="px-4 py-2">
                        @if($user->is_validated)
                            <x-admin.badge color="green">{{ __('Yes') }}</x-admin.badge>
                        @else
                            <x-admin.badge>{{ __('No') }}</x-admin.badge>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex justify-end gap-2">
                            @can('view', $user)
                                <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-2 py-1 text-sm rounded border hover:bg-gray-50 dark:hover:bg-gray-800">{{ __('View') }}</a>
                            @endcan
                            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-md bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                {{ __('Edit') }}
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1 text-sm rounded-md bg-red-600 text-white hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    {{ __('Delete') }}
                                </button>
                            </form>
                            @can('validateUser', $user)
                                @if(!$user->is_validated)
                                    <form action="{{ route('admin.users.validate', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button class="inline-flex items-center px-2 py-1 text-sm rounded border border-green-300 text-green-700 hover:bg-green-50">{{ __('Validate') }}</button>
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6"><x-admin.empty-state :title="__('No data')" :description="__('No users found for this filter.')"/></td>
                </tr>
            @endforelse
        </x-admin.table>

        <x-admin.pagination :collection="$users" />
    </x-admin.card>
@endsection



