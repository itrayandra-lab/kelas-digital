@extends('layouts.admin')

@section('title', 'View Article')

@section('content')

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8">
        <div>
            <a href="{{ route('admin.articles.index') }}"
                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left w-5 h-5 mr-2 text-sm"></i>
                Back to Articles
            </a>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.articles.edit', $article->id) }}"
                class="inline-flex items-center justify-center px-6 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
                Edit Article
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if ($article->thumbnail && $article->thumbnail != 'default-article.jpg')
            <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}"
                class="w-full h-72 object-cover">
        @endif

        <div class="p-6 md:p-8">
            <div class="mb-4">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">{{ $article->title }}</h1>
                <p class="mt-2 text-sm text-gray-500">
                    by <span class="font-medium text-gray-700">{{ $article->author }}</span>
                    <span class="mx-2">&bull;</span>
                    Published on {{ $article->published_at->format('F d, Y') }}
                </p>
                <p class="mt-1 text-xs text-gray-500">Slug: <span class="font-mono">{{ $article->slug }}</span></p>
                <div class="mt-4 flex flex-wrap gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="font-bold">Categories:</span>
                        @if($article->categories->isNotEmpty())
                            <span>{{ $article->categories->pluck('name')->join(', ') }}</span>
                        @else
                            <span class="text-gray-500">N/A</span>
                        @endif
                    </div>
                    @if($article->post_type)
                        <div class="flex items-center gap-2">
                            <span class="font-bold">Post Type:</span>
                            <span>{{ $article->post_type }}</span>
                        </div>
                    @endif
                </div>
                <div class="mt-2">
                    <span class="font-bold">Tags:</span>
                    @if($article->tags->isNotEmpty())
                        <span>{{ $article->tags->pluck('name')->join(', ') }}</span>
                    @else
                        <span class="text-gray-500">N/A</span>
                    @endif
                </div>
            </div>

            @if($article->excerpt)
                <div class="my-6 p-4 bg-gray-100 rounded-lg">
                    <h3 class="font-bold text-gray-700 mb-2">Ringkasan Artikel</h3>
                    <p class="text-gray-700 italic">{{ $article->excerpt }}</p>
                </div>
            @endif

            <div class="rich-text-content">
                {!! $article->processed_content !!}
            </div>
        </div>
    </div>

@endsection
