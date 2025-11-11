@extends('layouts.admin')

@section('title', 'Manage Roles')

@section('content')

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
    <div class="flex-1">
    </div>
    <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
        <i class="fas fa-plus mr-2 -ml-1 text-base"></i>
        Add New Role
    </a>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg mb-6">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Role Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Description</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Users</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Last Modified</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($roles as $role)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                                @if(in_array($role->name, config('authorization.protected_roles', [])))
                                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded bg-yellow-100 text-yellow-800">
                                        Protected
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700">{{ $role->description ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $role->users_count }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-700">{{ $role->updated_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.roles.edit', $role) }}" class="text-primary-600 hover:text-primary-900 font-medium">
                                    Edit
                                </a>
                                @if(!in_array($role->name, config('authorization.protected_roles', [])))
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this role? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            No roles found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    <a href="{{ route('admin.activity-log.index') }}" class="inline-flex items-center text-sm text-primary-600 hover:text-primary-900 font-medium">
        <i class="fas fa-history mr-2"></i>
        View Activity Log
    </a>
</div>

@endsection
