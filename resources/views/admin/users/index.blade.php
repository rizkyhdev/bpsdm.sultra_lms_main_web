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
                                <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center justify-center w-8 h-8 text-sm rounded border border-gray-400 dark:border-gray-600 text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700" title="{{ __('View') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endcan
                            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center justify-center w-8 h-8 text-sm rounded-md bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 shadow-sm" title="{{ __('Edit') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-sm rounded-md bg-red-600 text-white hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 shadow-sm" title="{{ __('Delete') }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @can('validateUser', $user)
                                @if(!$user->is_validated)
                                    <form action="{{ route('admin.users.validate', $user) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 text-sm rounded border border-green-600 dark:border-green-500 text-white bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 shadow-sm" title="{{ __('Validate') }}">
                                            <i class="fas fa-check"></i>
                                        </button>
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



