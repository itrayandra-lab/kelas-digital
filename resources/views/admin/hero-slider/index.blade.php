@extends('layouts.admin')

@section('title', 'Manage Hero Slider')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Hero Slider Management</h1>
        <p class="mt-2 text-gray-600">Manage articles displayed in the homepage hero slider (max 5)</p>

        @if($daysSinceUpdate && $daysSinceUpdate > 30)
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                <p class="text-sm text-yellow-800">
                    ⚠️ <strong>Stale content warning:</strong> Hero slider last updated {{ $daysSinceUpdate }} days ago.
                    Consider refreshing with new content.
                </p>
            </div>
        @endif

        @if(session('success'))
            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                <p class="text-sm text-green-800">
                    ✓ {{ session('success') }}
                </p>
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Current Hero Slider Articles ({{ $heroArticles->count() }}/5)</h2>

            @if($heroArticles->isEmpty())
                <p class="text-gray-500 italic">No articles manually added. Slider will show 5 latest published articles.</p>
            @else
                <div class="space-y-4">
                    @foreach($heroArticles as $article)
                        <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-primary-600 text-white rounded-full flex items-center justify-center font-bold">
                                {{ $article->hero_slider_order }}
                            </div>

                            <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : 'https://via.placeholder.com/100' }}"
                                 alt="{{ $article->title }}"
                                 class="w-20 h-20 object-cover rounded">

                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $article->title }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ $article->categories->first()->name ?? 'Uncategorized' }} •
                                    {{ $article->published_at ? $article->published_at->format('d M Y') : 'Not published' }}
                                </p>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ route('admin.articles.edit', $article->id) }}"
                                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                                    Edit
                                </a>
                                <form action="{{ route('admin.hero-slider.remove', $article) }}" method="POST"
                                      onsubmit="return confirm('Remove this article from hero slider?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($heroArticles->count() < 5)
                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                    <p class="text-sm text-blue-800">
                        <strong>Note:</strong> You have {{ 5 - $heroArticles->count() }} empty slot(s).
                        To add articles to the hero slider, edit an article and check the "Include in Hero Slider" option.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
