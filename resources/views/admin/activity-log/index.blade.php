@extends('layouts.admin')

@section('title', 'Activity Log')

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
        <i class="fas fa-arrow-left mr-2"></i>
        Back to Roles
    </a>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.activity-log.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="causer_id" class="block text-xs font-semibold text-gray-700 mb-1">User</label>
            <select
                name="causer_id"
                id="causer_id"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
                <option value="">All Users</option>
                @foreach($causers as $causer)
                    <option value="{{ $causer->id }}" {{ request('causer_id') == $causer->id ? 'selected' : '' }}>
                        {{ $causer->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="date_from" class="block text-xs font-semibold text-gray-700 mb-1">From Date</label>
            <input
                type="date"
                name="date_from"
                id="date_from"
                value="{{ request('date_from') }}"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
        </div>

        <div>
            <label for="date_to" class="block text-xs font-semibold text-gray-700 mb-1">To Date</label>
            <input
                type="date"
                name="date_to"
                id="date_to"
                value="{{ request('date_to') }}"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
        </div>

        <div>
            <label for="description" class="block text-xs font-semibold text-gray-700 mb-1">Action</label>
            <input
                type="text"
                name="description"
                id="description"
                value="{{ request('description') }}"
                placeholder="e.g., created, updated"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
            >
        </div>

        <div class="md:col-span-4 flex items-center gap-2">
            <button
                type="submit"
                class="px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 transition-colors"
            >
                <i class="fas fa-filter mr-1"></i>Apply Filters
            </button>
            <a
                href="{{ route('admin.activity-log.index') }}"
                class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors"
            >
                Clear
            </a>
        </div>
    </form>
</div>

<!-- Activity Timeline -->
<div class="space-y-4">
    @forelse($activities as $activity)
        <div class="bg-white rounded-lg shadow-sm p-5" x-data="{ expanded: false }">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    @if($activity->causer)
                        <img
                            src="https://ui-avatars.com/api/?name={{ urlencode($activity->causer->name) }}&background=1474bc&color=ffffff"
                            alt="{{ $activity->causer->name }}"
                            class="w-10 h-10 rounded-full"
                        >
                    @else
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between mb-1">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $activity->causer?->name ?? 'System' }}
                                <span class="font-normal text-gray-600">{{ $activity->description }}</span>
                            </p>
                            @if($activity->properties->has('role_name'))
                                <p class="text-xs text-gray-500 mt-1">
                                    Role: <span class="font-medium">{{ $activity->properties->get('role_name') }}</span>
                                </p>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 whitespace-nowrap ml-4">
                            {{ $activity->created_at->diffForHumans() }}
                        </div>
                    </div>

                    @if($activity->properties->isNotEmpty() && ($activity->properties->has('permissions_added') || $activity->properties->has('permissions_removed')))
                        <button
                            type="button"
                            @click="expanded = !expanded"
                            class="text-xs text-primary-600 hover:text-primary-900 font-medium mt-2"
                        >
                            <i :class="expanded ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="mr-1"></i>
                            <span x-text="expanded ? 'Hide Details' : 'Show Details'"></span>
                        </button>

                        <div x-show="expanded" x-collapse class="mt-3 p-3 bg-gray-50 rounded-lg">
                            @if($activity->properties->has('permissions_added') && count($activity->properties->get('permissions_added')) > 0)
                                <div class="mb-2">
                                    <p class="text-xs font-semibold text-green-700 mb-1">Permissions Added:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($activity->properties->get('permissions_added') as $permission)
                                            <span class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded">
                                                {{ $permission }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($activity->properties->has('permissions_removed') && count($activity->properties->get('permissions_removed')) > 0)
                                <div>
                                    <p class="text-xs font-semibold text-red-700 mb-1">Permissions Removed:</p>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($activity->properties->get('permissions_removed') as $permission)
                                            <span class="px-2 py-0.5 text-xs bg-red-100 text-red-800 rounded">
                                                {{ $permission }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500">
            <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
            <p>No activity found.</p>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($activities->hasPages())
    <div class="mt-6">
        {{ $activities->links() }}
    </div>
@endif

@endsection
