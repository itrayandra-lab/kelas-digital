@extends('layouts.app')

@section('title', $category->name . ' - Ray Academy')

@section('content')
    <div class="bg-primary-500 py-16 md:py-24 text-center">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                {{ $category->name }}
            </h1>
            @if($category->description)
                <p class="text-lg text-white opacity-90 max-w-2xl mx-auto">
                    {{ $category->description }}
                </p>
            @else
                <p class="text-lg text-white opacity-90 max-w-2xl mx-auto">
                    Explore articles dalam kategori {{ $category->name }}
                </p>
            @endif
        </div>
    </div>

    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb -->
            <div class="mb-12">
                <nav class="flex items-center space-x-2 text-sm text-gray-600">
                    <a href="{{ route('home') }}" class="hover:text-gray-900 transition">Home</a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('article.index') }}" class="hover:text-gray-900 transition">Articles</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-900 font-semibold">{{ $category->name }}</span>
                </nav>
            </div>

            @if($articles->isEmpty())
                <div class="text-center py-16">
                    <p class="text-xl text-gray-600">Belum ada artikel dalam kategori ini.</p>
                    <p class="text-gray-500 mt-2">Kembali lagi nanti untuk melihat artikel terbaru.</p>
                    <a href="{{ route('article.index') }}"
                       class="inline-block mt-6 px-6 py-3 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition">
                        Lihat Semua Artikel
                    </a>
                </div>
            @else
                <div x-data="articleLoader('{{ $category->slug }}')" class="space-y-8">
                    <div id="articles-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @include('article.partials.articles', ['articles' => $articles])
                    </div>

                    @if($articles->hasMorePages())
                        <div class="mt-8 text-center">
                            <button @click="loadMore()"
                                    :disabled="loading"
                                    class="px-6 py-3 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!loading">Load More</span>
                                <span x-show="loading">Loading...</span>
                            </button>
                            <div x-show="loading" class="mt-4">
                                <svg class="animate-spin h-8 w-8 text-primary-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>

                <script>
                    function articleLoader(categorySlug = null) {
                        return {
                            loading: false,
                            page: {{ $articles->currentPage() + 1 }},
                            hasMore: {{ $articles->hasMorePages() ? 'true' : 'false' }},
                            categorySlug: categorySlug,

                            async loadMore() {
                                if (this.loading || !this.hasMore) return;

                                this.loading = true;

                                try {
                                    const params = { page: this.page };
                                    if (this.categorySlug) {
                                        params.category_slug = this.categorySlug;
                                    }

                                    const response = await axios.get('{{ route("article.load-more") }}', { params });
                                    const data = response.data;

                                    // Append new articles to the container
                                    document.getElementById('articles-container').insertAdjacentHTML('beforeend', data.articles_html);

                                    // Update state
                                    if (data.has_more) {
                                        this.page++;
                                    } else {
                                        this.hasMore = false;
                                    }

                                } catch (error) {
                                    console.error('Error loading more articles:', error);
                                } finally {
                                    this.loading = false;
                                }
                            }
                        }
                    }
                </script>
            @endif
        </div>
    </section>
@endsection
