@extends('layouts.admin')

@section('title', 'Users')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('header-actions')
    @can('create', App\Models\User::class)
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Add
        </a>
    @endcan
@endsection

@section('content')
    {{-- Filter & Search --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-3">
                        <label for="search">Search (NIP / Name / Email)</label>
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               class="form-control"
                               placeholder="Type keyword">
                    </div>
                    <div class="form-group col-md-2">
                        <label for="role">Role</label>
                        <select id="role" name="role" class="form-control">
                            @php
                                $roles = ['all' => 'All', 'admin' => 'Admin', 'instructor' => 'Instructor', 'student' => 'Student'];
                            @endphp
                            @foreach($roles as $value => $label)
                                <option value="{{ $value }}" @if(request('role', 'all') == $value) selected @endif>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="per_page">Per Page</label>
                        <select id="per_page" name="per_page" class="form-control">
                            @foreach([10,25,50,100] as $size)
                                <option value="{{ $size }}" @if(request('per_page', 15)==$size) selected @endif>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="sort">Sort By</label>
                        <select id="sort" name="sort" class="form-control">
                            @php
                                $sortOptions = [
                                    'created_at' => 'Created at',
                                    'name' => 'Name',
                                    'email' => 'Email',
                                    'role' => 'Role',
                                    'enrollments_count' => 'Total enrollments',
                                    'completed_courses_count' => 'Completed courses',
                                ];
                            @endphp
                            @foreach($sortOptions as $value => $label)
                                <option value="{{ $value }}" @if(request('sort', 'created_at') == $value) selected @endif>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="direction">Order</label>
                        <select id="direction" name="direction" class="form-control">
                            <option value="desc" @if(request('direction', 'desc') === 'desc') selected @endif>Descending</option>
                            <option value="asc" @if(request('direction') === 'asc') selected @endif>Ascending</option>
                        </select>
                    </div>
                    <div class="form-group col-md-1">
                        <button type="submit" class="btn btn-outline-secondary btn-block mt-4">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Users table --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>NIP</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Validated</th>
                        <th class="text-right">Enrollments</th>
                        <th class="text-right">Completed Courses</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->nip }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="">{{ strtoupper($user->role) }}</span></td>
                        <td>
                            @if($user->is_validated)
                                <span class="">Yes</span>
                            @else
                                <span class="">No</span>
                            @endif
                        </td>
                        <td class="text-right">
                            {{ $user->enrollments_count ?? 0 }}
                        </td>
                        <td class="text-right">
                            {{ $user->completed_courses_count ?? 0 }}
                        </td>
                        <td class="text-right">
                            <div class="btn-group btn-group-sm">
                                @can('view', $user)
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline-secondary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @can('validateUser', $user)
                                    @if(!$user->is_validated)
                                        <form action="{{ route('admin.users.validate', $user->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success" title="Validate">
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
                        <td colspan="8" class="text-center text-muted py-4">No users found for this filter.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">
            @include('partials._pagination', ['collection' => $users])
        </div>
    </div>
@endsection

