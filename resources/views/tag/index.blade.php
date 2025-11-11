@extends('layouts.app')

@section('seo')
    <title>Browse Topics - Beautyversity</title>
    <meta name="description" content="Browse all topics and tags on Beautyversity. Explore beauty education articles organized by topic.">
@endsection

@section('content')

    <!-- Hero Section -->
    <section class="py-16 md:py-24 bg-primary-500">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl font-bold text-white leading-tight mb-4">
                    Browse by Topic
                </h1>
                <p class="text-lg text-white opacity-90">
                    Explore articles organized by topic to discover content that interests you
                </p>
            </div>
        </div>
    </section>

    <div class="py-16 md:py-24">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-12">
                <nav class="flex items-center space-x-2 text-sm text-gray-600">
                    <a href="{{ route('home') }}" class="hover:text-gray-900 transition">Home</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-900 font-semibold">Topics</span>
                </nav>
            </div>

            @if ($tags->isNotEmpty())
                <!-- Topic Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    @foreach ($tags as $tag)
                        <div class="bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                                            <a href="{{ route('tag.show', $tag->slug) }}" class="hover:text-primary-600 transition">
                                                {{ $tag->name }}
                                            </a>
                                        </h3>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <span class="text-sm text-gray-600">
                                        {{ $tag->articles_count }} {{ $tag->articles_count === 1 ? 'article' : 'articles' }}
                                    </span>
                                    <a href="{{ route('tag.show', $tag->slug) }}" class="inline-flex items-center text-sm font-semibold text-primary-600 hover:text-primary-700 transition">
                                        Browse
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <p class="text-lg text-gray-600">No topics available yet.</p>
                </div>
            @endif
        </div>
    </div>

@endsection
