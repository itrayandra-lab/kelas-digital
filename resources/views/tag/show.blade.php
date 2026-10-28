@extends('layouts.app')

@section('seo')
    <title>{{ $tag->name }} - Beautyversity</title>
    <meta name="description" content="Browse articles tagged with {{ $tag->name }} on Beautyversity. Explore beauty education content on this topic.">
@endsection

@section('content')

    <!-- Hero Section -->
    <section class="py-16 md:py-24 bg-primary-500 text-center">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                {{ $tag->name }}
            </h1>
            <p class="text-lg text-white opacity-90 max-w-2xl mx-auto">
                Discover {{ $articles->total() }} article{{ $articles->total() !== 1 ? 's' : '' }} tagged with <strong>{{ $tag->name }}</strong>
            </p>
        </div>
    </section>

    <div class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-12">
                <nav class="flex items-center space-x-2 text-sm text-gray-600">
                    <a href="{{ route('home') }}" class="hover:text-gray-900 transition">Home</a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('tag.index') }}" class="hover:text-gray-900 transition">Topics</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-900 font-semibold">{{ $tag->name }}</span>
                </nav>
            </div>

            @if ($articles->isNotEmpty())
                <!-- Articles Grid -->
                <div id="articles-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                    @include('article.partials.articles', ['articles' => $articles->items()])
                </div>

                <!-- Pagination Info -->
                <div class="text-center text-gray-600 mb-12">
                    <p id="pagination-info" class="text-sm">
                        Showing {{ ($articles->currentPage() - 1) * $articles->perPage() + 1 }}-{{ min($articles->currentPage() * $articles->perPage(), $articles->total()) }} of {{ $articles->total() }}
                    </p>
                </div>

                <!-- Load More Button -->
                @if ($articles->hasMorePages())
                    <div class="flex justify-center">
                        <button id="load-more-btn"
                            class="px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition duration-200">
                            Load More Articles
                        </button>
                    </div>
                @endif
            @else
                <div class="text-center py-16">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-lg text-gray-600">No articles found with this tag.</p>
                    <a href="{{ route('tag.index') }}" class="mt-4 inline-block text-primary-600 hover:text-primary-700 font-semibold transition">
                        Browse all topics →
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if (!$articles->isEmpty() && $articles->hasMorePages())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let currentPage = {{ $articles->currentPage() }};
                const tagSlug = '{{ $tag->slug }}';
                const container = document.getElementById('articles-container');
                const loadMoreBtn = document.getElementById('load-more-btn');
                const paginationInfo = document.getElementById('pagination-info');

                loadMoreBtn?.addEventListener('click', async function() {
                    loadMoreBtn.disabled = true;
                    loadMoreBtn.textContent = 'Loading...';

                    try {
                        const response = await fetch(`/articles/tag/${tagSlug}?page=${currentPage + 1}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });

                        const data = await response.json();

                        if (data.articles && data.articles.length > 0) {
                            // Create article cards from JSON
                            const articlesHtml = data.articles.map(article => `
                                <div class="bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition">
                                    <a href="/article/${article.slug}">
                                        <img src="${article.thumbnail ? '/storage/' + article.thumbnail : 'https://via.placeholder.com/600x400.png'}"
                                            alt="${article.title}" class="w-full h-48 object-cover">
                                    </a>
                                    <div class="p-6">
                                        ${article.categories && article.categories.length > 0 ? `
                                            <a href="/articles/category/${article.categories[0].slug}"
                                                class="inline-block text-xs font-bold uppercase tracking-widest text-primary-600 mb-2 hover:underline">
                                                ${article.categories[0].name}
                                            </a>
                                        ` : ''}
                                        <p class="text-sm text-gray-500 mb-2">${new Date(article.published_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</p>
                                        <h2 class="text-xl font-bold text-gray-800 mb-3 line-clamp-2">
                                            <a href="/article/${article.slug}" class="hover:text-primary-600 transition">
                                                ${article.title}
                                            </a>
                                        </h2>
                                        ${article.excerpt ? `<p class="text-gray-600 text-sm mb-4 line-clamp-3">${article.excerpt}</p>` : ''}
                                        <div class="flex items-center justify-between">
                                            <a href="/article/${article.slug}" class="font-semibold text-primary-600 hover:underline text-sm">
                                                Baca Selengkapnya
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `).join('');

                            container.insertAdjacentHTML('beforeend', articlesHtml);
                            currentPage++;

                            // Update pagination info
                            const total = data.total;
                            const perPage = data.per_page;
                            const showing = Math.min(currentPage * perPage, total);
                            const from = (currentPage - 1) * perPage + 1;
                            paginationInfo.textContent = `Showing ${from}-${showing} of ${total}`;

                            // Hide button if no more pages
                            if (!data.has_more_pages) {
                                loadMoreBtn.style.display = 'none';
                            } else {
                                loadMoreBtn.disabled = false;
                                loadMoreBtn.textContent = 'Load More Articles';
                            }
                        }
                    } catch (error) {
                        console.error('Error loading more articles:', error);
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.textContent = 'Load More Articles';
                    }
                });
            });
        </script>
    @endif

@endsection
