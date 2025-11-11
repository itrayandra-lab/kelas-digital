@extends('layouts.admin')

@section('title', 'Create Role')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Roles
    </a>
</div>

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf

        <div class="mb-6">
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                Role Name <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name') }}"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('name') border-red-500 @enderror"
                required
                placeholder="e.g., Content Moderator"
            >
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                Description
            </label>
            <textarea
                name="description"
                id="description"
                rows="3"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('description') border-red-500 @enderror"
                placeholder="Describe the purpose of this role..."
            >{{ old('description') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-gray-500">You can assign permissions after creating the role.</p>
        </div>

        <div class="flex items-center gap-3">
            <button
                type="submit"
                class="px-6 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300"
            >
                <i class="fas fa-save mr-2"></i>Create Role
            </button>
            <a
                href="{{ route('admin.roles.index') }}"
                class="px-6 py-2.5 bg-gray-100 text-gray-700 font-semibold text-sm rounded-lg hover:bg-gray-200 transition-colors duration-300"
            >
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection
