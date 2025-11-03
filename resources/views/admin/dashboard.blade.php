@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

    @if(auth()->user()->hasRole('instructor'))
        <!-- Dashboard khusus untuk Instructor -->
        <div class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-1 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">My Courses</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalCourses }}</p>
                </div>
                <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center bg-primary-100 rounded-full">
                    <i class="fas fa-book-open text-primary-600 text-2xl"></i>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->hasRole('content-manager'))
        <!-- Dashboard khusus untuk Content Manager -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Articles</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalArticles }}</p>
                </div>
                <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center bg-primary-100 rounded-full">
                    <i class="fas fa-file-alt text-primary-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Scheduled Articles</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $scheduledArticles ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Published Today</p>
                    <p class="text-3xl font-bold text-green-600">{{ $publishedToday ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center bg-green-100 rounded-full">
                    <i class="fas fa-calendar-check text-green-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Ready to Publish</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $readyToPublish ?? 0 }}</p>
                </div>
                <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center bg-blue-100 rounded-full">
                    <i class="fas fa-rocket text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>
    @else
        <!-- Dashboard untuk Admin dan Super Admin -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Courses</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalCourses }}</p>
                </div>
                <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center bg-primary-100 rounded-full">
                    <i class="fas fa-book-open text-primary-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Users</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
                </div>
                <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center bg-primary-100 rounded-full">
                    <i class="fas fa-users text-primary-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Enrollments</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalEnrollments }}</p>
                </div>
                <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center bg-primary-100 rounded-full">
                    <i class="fas fa-clipboard-list text-primary-600 text-2xl"></i>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Payments</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $pendingPayments }}</p>
                </div>
                <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center bg-yellow-100 rounded-full">
                    <i class="fas fa-coins text-yellow-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Article Statistics Section for Admin -->
        @if($totalArticles !== null)
        <div class="mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Article Statistics</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Articles</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $totalArticles }}</p>
                    </div>
                    <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center bg-primary-100 rounded-full">
                        <i class="fas fa-file-alt text-primary-600 text-2xl"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Scheduled Articles</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $scheduledArticles ?? 0 }}</p>
                    </div>
                    <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center bg-yellow-100 rounded-full">
                        <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Published Today</p>
                        <p class="text-3xl font-bold text-green-600">{{ $publishedToday ?? 0 }}</p>
                    </div>
                    <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center bg-green-100 rounded-full">
                        <i class="fas fa-calendar-check text-green-600 text-2xl"></i>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Ready to Publish</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $readyToPublish ?? 0 }}</p>
                    </div>
                    <div class="flex-shrink-0 h-14 w-14 flex items-center justify-center bg-blue-100 rounded-full">
                        <i class="fas fa-rocket text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif

    <div class="mt-8 bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">
                @if(auth()->user()->hasRole('instructor'))
                    My Course Enrollments
                @elseif(auth()->user()->hasRole('content-manager'))
                    Recent Articles
                @else
                    Recent Enrollments
                @endif
            </h2>
        </div>
        <div class="overflow-x-auto">
            @if(auth()->user()->hasRole('content-manager'))
                <!-- Articles table for content manager -->
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Title</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Author</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Scheduled</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentArticles as $article)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $article->title }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($article->excerpt ?? '', 50) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $article->author ?? 'Unknown' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $article->published_at ? $article->published_at->format('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($article->isPublished())
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Published
                                        </span>
                                    @elseif($article->isScheduled())
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Scheduled
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    @if($article->isScheduled() && $article->scheduled_at)
                                        {{ $article->scheduled_at->format('d M Y H:i') }}
                                    @elseif($article->isPublished() && $article->published_at)
                                        {{ $article->published_at->format('d M Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No recent articles found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <!-- Enrollments table for instructor and admin -->
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">User</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Course</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentEnrollments as $enrollment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full"
                                                src="https://ui-avatars.com/api/?name={{ urlencode($enrollment->user->name) }}&background=E6B4B8&color=333333"
                                                alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $enrollment->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $enrollment->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $enrollment->course->title }}</div>
                                    <div class="text-sm text-gray-500">by {{ $enrollment->course->instructor }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $enrollment->enrolled_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($enrollment->payment_status === 'completed')
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    @else
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No recent enrollments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>

@endsection
