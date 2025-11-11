@extends('layouts.admin')

@section('title', 'Edit Role')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Roles
    </a>
</div>

@if($isProtected)
    <div class="bg-yellow-50 border border-yellow-200 text-sm text-yellow-700 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        This is a protected system role. Some permissions cannot be modified.
    </div>
@endif

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg mb-6">
        {{ session('error') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow-sm">
    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Tabs -->
        <div class="border-b border-gray-200" x-data="{ activeTab: 'details' }">
            <nav class="flex -mb-px">
                <button
                    type="button"
                    @click="activeTab = 'details'"
                    :class="activeTab === 'details' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-6 py-3 border-b-2 font-semibold text-sm transition-colors"
                >
                    Details
                </button>
                <button
                    type="button"
                    @click="activeTab = 'permissions'"
                    :class="activeTab === 'permissions' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="px-6 py-3 border-b-2 font-semibold text-sm transition-colors"
                >
                    Permissions
                </button>
            </nav>

            <!-- Details Tab -->
            <div x-show="activeTab === 'details'" class="p-6">
                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Role Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $role->name) }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('name') border-red-500 @enderror"
                        required
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
                    >{{ old('description', $role->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Permissions Tab -->
            <div x-show="activeTab === 'permissions'" class="p-6">
                @include('admin.roles._permission-matrix')
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center gap-3">
            <button
                type="submit"
                class="px-6 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300"
            >
                <i class="fas fa-save mr-2"></i>Save Changes
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
