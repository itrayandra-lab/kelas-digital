@extends('layouts.admin')

@section('title', 'View Course')

@section('content')

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8">
        <div>
            <a href="{{ route('admin.courses.index') }}"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left w-5 h-5 mr-2 text-sm"></i>
                Back to Courses
            </a>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="{{ route('admin.curriculum.show', $course) }}"
                class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-50 transition-colors duration-300">
                <i class="fas fa-list-ol mr-2"></i> Kurikulum &amp; Tes
            </a>
            <a href="{{ route('admin.courses.edit', $course->id) }}"
                class="inline-flex items-center justify-center px-6 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
                Edit Course
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm">
        <div class="p-6 md:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1">
                    @if($course->thumbnail_url)
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}"
                            class="w-full object-cover rounded-lg shadow-md mb-6">
                    @else
                        <div
                            class="bg-gray-100 border-2 border-dashed rounded-lg w-full aspect-video flex items-center justify-center mb-6">
                            <span class="text-gray-500 text-sm">No Thumbnail</span>
                        </div>
                    @endif

                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Video Details</h3>
                    <dl class="space-y-3">
                        <div class="text-sm">
                            <dt class="font-medium text-gray-500">Trailer Video ID</dt>
                            <dd class="text-gray-900 mt-1">{{ $course->trailer_video_id }}</dd>
                        </div>
                        <div class="text-sm">
                            <dt class="font-medium text-gray-500">Full Video IDs</dt>
                            <dd class="text-gray-900 mt-1 break-words">
                                @php
                                    $videoIds = $course->full_video_ids ?? [];
                                @endphp
                                {{ count($videoIds) > 0 ? implode(', ', $videoIds) : 'N/A' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="lg:col-span-2">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $course->title }}</h1>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 mb-6 text-sm">
                        <div>
                            <dt class="font-medium text-gray-500">Instructor</dt>
                            <dd class="text-gray-900 mt-1">{{ $course->instructor }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Price</dt>
                            <dd class="text-gray-900 mt-1">Rp {{ number_format($course->price, 0, ',', '.') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Category</dt>
                            <dd class="text-gray-900 mt-1">{{ optional($course->category)->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-500">Level</dt>
                            <dd class="mt-1">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-primary-100 text-primary-800">
                                    {{ $course->level }}
                                </span>
                            </dd>
                        </div>
                    </dl>

                    <h3 class="text-lg font-bold text-gray-800 mb-2 border-b pb-2">Description</h3>
                    <div class="prose prose-sm max-w-none text-gray-700">
                        <p>{{ $course->description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
