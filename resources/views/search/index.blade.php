@extends('layouts.app')

@section('title', 'Search Courses & Articles - Ray Academy')

@section('content')
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center mb-12">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Temukan Ilmu Kecantikan yang Anda Butuhkan</h1>
                <p class="text-gray-600">Cari kursus kecantikan atau artikel ilmiah berdasarkan kata kunci untuk mempercepat perjalanan kecantikan Anda.</p>
            </div>

            <form action="{{ route('search') }}" method="GET" class="max-w-3xl mx-auto mb-16">
                <label for="search" class="sr-only">Kata kunci</label>
                <div class="flex flex-col sm:flex-row gap-4 bg-white shadow-sm rounded-lg p-4 border border-gray-100">
                    <input
                        type="search"
                        id="search"
                        name="q"
                        value="{{ old('q', $keyword) }}"
                        placeholder="Cari kursus kecantikan atau artikel ilmiah..."
                        class="flex-1 border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-700"
                        autocomplete="off"
                    >
                    <button type="submit" class="bg-primary-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-primary-700 transition">
                        Cari
                    </button>
                </div>
            </form>

            @if ($keyword === '')
                <div class="max-w-2xl mx-auto text-center text-gray-500">
                    Masukkan kata kunci untuk mulai menelusuri kursus kecantikan dan artikel ilmiah.
                </div>
            @else
                <!-- Article Filters -->
                <div class="max-w-4xl mx-auto mb-12 bg-white rounded-lg border border-gray-100 p-6">
                    <form action="{{ route('search') }}" method="GET" id="filter-form">
                        <input type="hidden" name="q" value="{{ $keyword }}">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <!-- Category Filter -->
                            <div>
                                <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Kategori
                                </label>
                                <select name="category_id" id="category_id" class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }} ({{ $category->articles_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date From -->
                            <div>
                                <label for="date_from" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Dari Tanggal
                                </label>
                                <input type="date" name="date_from" id="date_from" value="{{ $selectedDateFrom }}"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>

                            <!-- Date To -->
                            <div>
                                <label for="date_to" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Sampai Tanggal
                                </label>
                                <input type="date" name="date_to" id="date_to" value="{{ $selectedDateTo }}"
                                    class="w-full border border-gray-200 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>

                        <!-- Tags Filter -->
                        @if ($tags->isNotEmpty())
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    Tags
                                </label>
                                <div class="space-y-2">
                                    @foreach ($tags as $tag)
                                        <label class="flex items-center">
                                            <input type="checkbox" name="tag_id[]" value="{{ $tag->id }}"
                                                {{ in_array($tag->id, is_array($selectedTagIds) ? $selectedTagIds : []) ? 'checked' : '' }}
                                                class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                                            <span class="ml-3 text-sm text-gray-700">
                                                {{ $tag->name }} <span class="text-gray-500">({{ $tag->articles_count }})</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Filter Actions -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit" class="flex-1 bg-primary-600 text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-primary-700 transition">
                                Terapkan Filter
                            </button>
                            @if (!empty($activeFilters))
                                <a href="{{ route('search', ['q' => $keyword]) }}" class="flex-1 text-center bg-gray-200 text-gray-700 font-semibold px-6 py-2.5 rounded-lg hover:bg-gray-300 transition">
                                    Hapus Filter
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <!-- Active Filters Display -->
                @if (!empty($activeFilters))
                    <div class="max-w-4xl mx-auto mb-8 flex flex-wrap gap-3">
                        @if (isset($activeFilters['category']))
                            <div class="inline-flex items-center gap-2 bg-primary-100 text-primary-700 px-4 py-2 rounded-full text-sm font-semibold">
                                Kategori: {{ $activeFilters['category']->name }}
                                <a href="{{ route('search', ['q' => $keyword, 'tag_id' => implode(',', is_array($selectedTagIds) ? $selectedTagIds : []), 'date_from' => $selectedDateFrom, 'date_to' => $selectedDateTo]) }}"
                                    class="hover:text-primary-900">✕</a>
                            </div>
                        @endif
                        @if (isset($activeFilters['tags']))
                            @foreach ($activeFilters['tags'] as $tag)
                                <div class="inline-flex items-center gap-2 bg-primary-100 text-primary-700 px-4 py-2 rounded-full text-sm font-semibold">
                                    {{ $tag->name }}
                                </div>
                            @endforeach
                        @endif
                        @if (isset($activeFilters['dates']))
                            <div class="inline-flex items-center gap-2 bg-primary-100 text-primary-700 px-4 py-2 rounded-full text-sm font-semibold">
                                {{ $activeFilters['dates']['from'] ?? '' }} - {{ $activeFilters['dates']['to'] ?? '' }}
                            </div>
                        @endif
                    </div>
                @endif

                <div class="space-y-16">
                    <div>
                        <div class="flex items-center justify-between mb-12">
                            <h2 class="text-2xl font-semibold text-gray-900">Kursus Kecantikan</h2>
                            <span class="text-sm text-gray-500">{{ $courses->count() }} hasil</span>
                        </div>

                        @if ($courses->isEmpty())
                            <p class="text-gray-500">Tidak ada kursus kecantikan yang cocok dengan pencarian "<strong>{{ $keyword }}</strong>".</p>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                                @foreach ($courses as $course)
                                    <div class="bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition">
                                        <a href="{{ route('course.show', $course->slug) }}">
                                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                                        </a>
                                        <div class="p-6 flex flex-col gap-3">
                                            <div class="text-xs font-semibold uppercase tracking-widest text-primary-600">
                                                {{ optional($course->category)->name ?? 'Tanpa Kategori' }}
                                            </div>
                                            <h3 class="text-lg font-semibold text-gray-900 leading-tight line-clamp-2">
                                                <a href="{{ route('course.show', $course->slug) }}" class="hover:text-primary-600 transition-colors">
                                                    {{ $course->title }}
                                                </a>
                                            </h3>
                                            @if ($course->instructor)
                                                <p class="text-sm text-gray-500">Instruktur: {{ $course->instructor }}</p>
                                            @endif
                                            <p class="text-sm text-gray-600 line-clamp-3">{{ \Illuminate\Support\Str::limit($course->description, 120) }}</p>
                                            <div class="flex items-center justify-between mt-auto pt-1">
                                                <span class="text-primary-600 font-bold">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                                                <a href="{{ route('course.show', $course->slug) }}" class="text-sm font-semibold text-primary-600 hover:underline">
                                                    Lihat Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-12">
                            <h2 class="text-2xl font-semibold text-gray-900">Artikel Ilmiah</h2>
                            <span class="text-sm text-gray-500" id="article-count">{{ $articles->total() }} hasil</span>
                        </div>

                        @if ($articles->isEmpty())
                            <p class="text-gray-500">Tidak ada artikel ilmiah yang cocok dengan pencarian "<strong>{{ $keyword }}</strong>".</p>
                        @else
                            <div id="articles-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @include('article.partials.articles', ['articles' => $articles->items()])
                            </div>

                            <!-- Load More Button -->
                            @if ($articles->hasMorePages())
                                <div class="flex justify-center mt-12">
                                    <button id="load-more-btn"
                                        class="px-8 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition">
                                        Load More Articles
                                    </button>
                                </div>
                            @endif

                            <!-- Pagination Info -->
                            <div class="text-center text-gray-600 mt-8">
                                <p id="pagination-info">
                                    Showing {{ ($articles->currentPage() - 1) * $articles->perPage() + 1 }}-{{ min($articles->currentPage() * $articles->perPage(), $articles->total()) }} of {{ $articles->total() }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>

    @if (!$articles->isEmpty() && $articles->hasMorePages())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let currentPage = {{ $articles->currentPage() }};
                const container = document.getElementById('articles-container');
                const loadMoreBtn = document.getElementById('load-more-btn');
                const paginationInfo = document.getElementById('pagination-info');
                const articleCount = document.getElementById('article-count');

                // Build current filter params
                const filterParams = new URLSearchParams({
                    q: '{{ $keyword }}',
                    category_id: '{{ $selectedCategoryId ?? '' }}',
                    date_from: '{{ $selectedDateFrom ?? '' }}',
                    date_to: '{{ $selectedDateTo ?? '' }}'
                });

                @if (!empty($selectedTagIds))
                    @foreach ($selectedTagIds as $tagId)
                        filterParams.append('tag_id[]', '{{ $tagId }}');
                    @endforeach
                @endif

                loadMoreBtn?.addEventListener('click', async function() {
                    loadMoreBtn.disabled = true;
                    loadMoreBtn.textContent = 'Loading...';

                    try {
                        const url = `/search?${filterParams.toString()}&page=${currentPage + 1}`;
                        const response = await fetch(url, {
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
