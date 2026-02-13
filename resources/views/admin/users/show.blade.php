@extends('layouts.admin')

@section('title', 'View User')

@section('content')

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8">
    <div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left w-5 h-5 mr-2 text-sm"></i>
            Back to Users
        </a>
    </div>
    <div class="mt-4 sm:mt-0">
        <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center justify-center px-6 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
            Edit User
        </a>
    </div>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 text-center">
                <img class="h-24 w-24 rounded-full mx-auto mb-4" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=128&background=1474bc&color=ffffff" alt="User Avatar">
                <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                <div class="mt-4">
                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>
            <div class="border-t border-gray-200 px-6 py-4 text-sm text-gray-600">
                <p><strong>Member Since:</strong> {{ $user->created_at->format('d M Y') }}</p>
                @if($user->last_login)
                    <p class="mt-1"><strong>Last Login:</strong> {{ $user->last_login->format('d M Y H:i') }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Enrolled Courses ({{ $user->enrollments->count() }})</h3>
            </div>
            <div class="p-6">
                @if($user->enrollments->count() > 0)
                    <div class="space-y-4">
                        @foreach($user->enrollments as $enrollment)
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $enrollment->course->title }}</p>
                                    <p class="text-xs text-gray-500">Enrolled on {{ $enrollment->enrolled_at->format('d M Y') }}</p>
                                </div>
                                <div>
                                     <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                        {{ $enrollment->payment_status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($enrollment->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 text-sm">This user has not enrolled in any courses.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
