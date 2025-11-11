@extends('layouts.app')

@section('title', 'All Courses - Beautyversity')

@section('content')
    {{-- Page Header --}}
    <section class="bg-gray-50 py-12 border-b border-gray-200">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">All Courses</h1>
            <p class="text-lg text-gray-600">Browse our collection of structured learning programs</p>
        </div>
    </section>

    {{-- Courses Grid --}}
    <section class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            @if($courses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
                    @foreach($courses as $course)
                        <a href="{{ route('course.show', $course->slug) }}"
                           class="bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition block">
                            <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://via.placeholder.com/400x300' }}"
                                 alt="{{ $course->title }}"
                                 class="w-full h-48 object-cover">
                            <div class="p-6">
                                {{-- Badge --}}
                                <span class="inline-block text-xs font-bold uppercase text-primary-600 mb-2">
                                    {{ $course->category?->name ?? 'COURSE' }}
                                </span>

                                {{-- Title --}}
                                <h3 class="text-lg font-bold text-gray-800 mt-2 mb-3 line-clamp-2 hover:text-primary-600 transition">
                                    {{ $course->title }}
                                </h3>

                                {{-- Instructor & Level --}}
                                <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                                    <span>{{ $course->instructor }}</span>
                                    <span>•</span>
                                    <span>{{ $course->level }}</span>
                                </div>

                                {{-- Price & Enrollment --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-xl font-bold text-primary-600">
                                        @if($course->price > 0)
                                            Rp {{ number_format($course->price, 0, ',', '.') }}
                                        @else
                                            Free
                                        @endif
                                    </span>
                                    @if($course->enrollments_count > 0)
                                        <span class="text-xs text-gray-500">
                                            {{ $course->enrollments_count }} enrolled
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12">
                    {{ $courses->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-600 text-lg">No courses available at the moment.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
