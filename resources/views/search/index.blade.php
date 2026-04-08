@extends('layouts.app')

@section('title', 'Pencarian - Ray Academy')

@push('styles')
<style>
/* ══════ SEARCH PAGE - CLEAN & CONSISTENT ══════ */

/* Page Hero */
.search-hero {
    background: linear-gradient(135deg, var(--ink) 0%, #1a2942 100%);
    padding: 4rem 0 3rem;
    position: relative;
    overflow: hidden;
}

.search-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 60% 50% at 50% 0%, rgba(20,116,188,.15) 0%, transparent 60%);
    pointer-events: none;
}

.search-hero-inner {
    position: relative;
    z-index: 1;
    max-width: 800px;
    margin: 0 auto;
    padding: 0 1.5rem;
    text-align: center;
}

.search-hero h1 {
    font-family: 'Sora', sans-serif;
    font-size: clamp(1.8rem, 4vw, 2.6rem);
    font-weight: 800;
    color: #fff;
    margin-bottom: 0.75rem;
}

.search-hero p {
    font-size: 1rem;
    color: rgba(255,255,255,.65);
    margin-bottom: 2rem;
}

/* Search Form */
.search-form {
    display: flex;
    gap: 0.75rem;
    background: #fff;
    border-radius: 14px;
    padding: 0.5rem;
    box-shadow: 0 20px 60px rgba(0,0,0,.2);
}

.search-input {
    flex: 1;
    border: none;
    padding: 0.875rem 1.25rem;
    font-size: 0.9375rem;
    border-radius: 10px;
    outline: none;
    color: var(--ink);
}

.search-input::placeholder {
    color: var(--muted);
}

.search-btn {
    padding: 0.875rem 2rem;
    background: var(--blue);
    color: #fff;
    font-weight: 700;
    font-size: 0.9375rem;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.search-btn:hover {
    background: var(--blue-d);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(20,116,188,.3);
}

/* Filters */
.filters-section {
    background: #fff;
    padding: 6rem 0 4rem;
}

.filter-card {
    background: var(--surf);
    border: 1.5px solid var(--border);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.filter-title {
    font-family: 'Sora', sans-serif;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 1.5rem;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}

.filter-input-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-label {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--ink-2);
}

.filter-select,
.filter-date-input {
    padding: 0.75rem 1rem;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 0.875rem;
    color: var(--ink);
    background: #fff;
    transition: all 0.2s;
    outline: none;
}

.filter-select:focus,
.filter-date-input:focus {
    border-color: var(--blue);
    box-shadow: 0 0 0 3px rgba(20,116,188,.1);
}

/* Tags */
.tags-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.tag-checkbox {
    display: none;
}

.tag-label {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 999px;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--ink-2);
    cursor: pointer;
    transition: all 0.2s;
    user-select: none;
}

.tag-label:hover {
    border-color: var(--blue);
    background: var(--blue-xl);
    color: var(--blue);
}

.tag-checkbox:checked + .tag-label {
    background: var(--blue);
    border-color: var(--blue);
    color: #fff;
}

.tag-count {
    opacity: 0.7;
    font-size: 0.75rem;
}

/* Filter Actions */
.filter-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn-filter-apply {
    flex: 1;
    min-width: 180px;
    padding: 0.875rem 2rem;
    background: var(--blue);
    color: #fff;
    font-weight: 700;
    font-size: 0.9375rem;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-filter-apply:hover {
    background: var(--blue-d);
    transform: translateY(-2px);
}

.btn-filter-reset {
    flex: 1;
    min-width: 180px;
    padding: 0.875rem 2rem;
    background: var(--surf);
    color: var(--ink-2);
    font-weight: 700;
    font-size: 0.9375rem;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-filter-reset:hover {
    border-color: var(--blue);
    background: var(--blue-xl);
    color: var(--blue);
}

/* Active Filters */
.active-filters {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 2rem;
}

.active-filter-tag {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--blue-xl);
    color: var(--blue);
    font-size: 0.8125rem;
    font-weight: 600;
    border-radius: 999px;
}

.active-filter-remove {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    background: var(--blue);
    color: #fff;
    border-radius: 50%;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.active-filter-remove:hover {
    background: var(--blue-d);
    transform: scale(1.1);
}

/* Results */
.results-section {
    background: var(--surf);
    padding: 0 0 6rem;
}

.results-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.results-title {
    font-family: 'Sora', sans-serif;
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--ink);
}

.results-count {
    font-size: 0.875rem;
    color: var(--muted);
}

.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 1.25rem;
    margin-bottom: 3rem;
}

/* Course & Article Cards - SAMA dengan index pages */
.crs-card {
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
    transition: transform .3s ease, box-shadow .3s ease, border-color .25s;
}

.crs-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 48px rgba(10,22,40,.1);
    border-color: #93c5fd;
}

.crs-thumb {
    position: relative;
    overflow: hidden;
    aspect-ratio: 16/9;
    background: linear-gradient(135deg, #dbeafe, #eff6ff);
}

.crs-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .45s;
}

.crs-card:hover .crs-thumb img {
    transform: scale(1.07);
}

.crs-thumb-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.crs-badge {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.3rem 0.7rem;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.badge-free {
    background: #dcfce7;
    color: #15803d;
}

.badge-paid {
    background: #dbeafe;
    color: #1d4ed8;
}

.crs-body {
    padding: 1.1rem 1.25rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.crs-cat {
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--blue);
    margin-bottom: 0.4rem;
}

.crs-title {
    font-family: 'Sora', sans-serif;
    font-size: 0.925rem;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.crs-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: auto;
    padding-top: 0.85rem;
    border-top: 1px solid var(--border);
    margin-top: 0.85rem;
}

.crs-avatar {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: var(--blue-xl);
    flex-shrink: 0;
}

.crs-instructor {
    font-size: 0.72rem;
    font-weight: 500;
    color: var(--muted);
    flex: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.crs-price {
    font-family: 'Sora', sans-serif;
    font-size: 0.9rem;
    font-weight: 800;
    color: var(--ink);
    flex-shrink: 0;
}

.crs-price-free {
    color: var(--accent);
}

/* Article Cards - SAMA */
.art-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: transform .3s ease, box-shadow .3s ease, border-color .25s;
}

.art-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 48px rgba(10,22,40,.09);
    border-color: #93c5fd;
}

.art-card-thumb-wrap {
    position: relative;
    overflow: hidden;
    height: 200px;
    background: linear-gradient(135deg, #dbeafe, #eff6ff);
}

.art-card-thumb-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .45s ease;
}

.art-card:hover .art-card-thumb-img {
    transform: scale(1.06);
}

.art-card-thumb-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.art-card-badge {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
    z-index: 1;
    font-size: 0.63rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    background: #fff;
    color: var(--blue);
    padding: 0.3rem 0.7rem;
    border-radius: 6px;
}

.art-card-body {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.art-card-date {
    font-size: 0.72rem;
    color: var(--muted);
    margin-bottom: 0.4rem;
}

.art-card-title {
    font-family: 'Sora', sans-serif;
    font-size: 0.9375rem;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.18s;
}

.art-card:hover .art-card-title {
    color: var(--blue);
}

.art-card-excerpt {
    font-size: 0.8rem;
    color: var(--muted);
    line-height: 1.65;
    margin-top: 0.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    flex: 1;
}

.art-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 0.9rem;
    padding-top: 0.9rem;
    border-top: 1px solid var(--border);
}

.art-card-read {
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--blue);
}

.art-card-views {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.72rem;
    color: var(--muted);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-state svg {
    width: 64px;
    height: 64px;
    color: var(--muted);
    opacity: 0.3;
    margin: 0 auto 1.5rem;
}

.empty-state h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--muted);
    font-size: 0.95rem;
}

/* Mobile Responsive */
@media (max-width: 900px) {
    .results-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 640px) {
    .results-grid {
        grid-template-columns: 1fr;
    }
    
    .search-form {
        flex-direction: column;
    }
    
    .search-btn {
        width: 100%;
    }
    
    .filter-actions {
        flex-direction: column;
    }
    
    .btn-filter-apply,
    .btn-filter-reset {
        width: 100%;
    }
}

/* Scroll Reveal */
.rv {
    opacity: 0;
    transform: translateY(28px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.rv.in {
    opacity: 1;
    transform: translateY(0);
}

.rv-d1 { transition-delay: 0.08s; }
.rv-d2 { transition-delay: 0.16s; }
.rv-d3 { transition-delay: 0.24s; }
.rv-d4 { transition-delay: 0.32s; }

.wrap {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1.5rem;
}
</style>
@endpush

@section('content')

{{-- Hero dengan Search --}}
<section class="search-hero">
    <div class="search-hero-inner">
        <h1>Cari Kursus & Artikel</h1>
        <p>Temukan kursus dan artikel yang tepat untuk tingkatkan skill Anda</p>

        <form action="{{ route('search') }}" method="GET" class="search-form">
            <input
                type="search"
                name="q"
                value="{{ old('q', $keyword) }}"
                placeholder="Cari kursus atau artikel..."
                class="search-input"
                autocomplete="off"
                required
            >
            <button type="submit" class="search-btn">
                <svg style="width:18px;height:18px;display:inline-block;margin-right:0.35rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Cari
            </button>
        </form>
    </div>
</section>

@if ($keyword === '')
    {{-- Empty State --}}
    <section class="filters-section">
        <div class="wrap">
            <div class="empty-state rv">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3>Mulai Pencarian Anda</h3>
                <p>Masukkan kata kunci untuk mencari kursus atau artikel</p>
            </div>
        </div>
    </section>
@else
    {{-- Filters --}}
    <section class="filters-section">
        <div class="wrap">
            <div class="filter-card rv">
                <h3 class="filter-title">🔍 Filter Pencarian</h3>

                <form action="{{ route('search') }}" method="GET" id="filter-form">
                    <input type="hidden" name="q" value="{{ $keyword }}">

                    <div class="filter-grid">
                        {{-- Category --}}
                        @if(isset($categories) && $categories->count() > 0)
                        <div class="filter-input-group">
                            <label for="category_id" class="filter-label">Kategori</label>
                            <select name="category_id" id="category_id" class="filter-select">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }} ({{ $category->articles_count ?? 0 }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        {{-- Date From --}}
                        <div class="filter-input-group">
                            <label for="date_from" class="filter-label">Dari Tanggal</label>
                            <input type="date" name="date_from" id="date_from" value="{{ $selectedDateFrom ?? '' }}" class="filter-date-input">
                        </div>

                        {{-- Date To --}}
                        <div class="filter-input-group">
                            <label for="date_to" class="filter-label">Sampai Tanggal</label>
                            <input type="date" name="date_to" id="date_to" value="{{ $selectedDateTo ?? '' }}" class="filter-date-input">
                        </div>
                    </div>

                    {{-- Tags --}}
                    @if (isset($tags) && $tags->isNotEmpty())
                        <div style="margin-bottom:1.5rem;">
                            <label class="filter-label" style="display:block;margin-bottom:0.75rem;">Tags</label>
                            <div class="tags-grid">
                                @foreach ($tags as $tag)
                                    <input type="checkbox" 
                                           name="tag_id[]" 
                                           value="{{ $tag->id }}"
                                           id="tag-{{ $tag->id }}"
                                           class="tag-checkbox"
                                           {{ in_array($tag->id, is_array($selectedTagIds ?? []) ? $selectedTagIds : []) ? 'checked' : '' }}>
                                    <label for="tag-{{ $tag->id }}" class="tag-label">
                                        {{ $tag->name }}
                                        <span class="tag-count">({{ $tag->articles_count ?? 0 }})</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter-apply">
                            <svg style="width:16px;height:16px;display:inline-block;margin-right:0.35rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Terapkan Filter
                        </button>
                        @if (!empty($activeFilters ?? []))
                            <a href="{{ route('search', ['q' => $keyword]) }}" class="btn-filter-reset">
                                <svg style="width:16px;height:16px;display:inline-block;margin-right:0.35rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Hapus Filter
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Active Filters --}}
            @if (!empty($activeFilters ?? []))
                <div class="active-filters rv rv-d1">
                    @if (isset($activeFilters['category']))
                        <div class="active-filter-tag">
                            Kategori: {{ $activeFilters['category']->name }}
                            <a href="{{ route('search', ['q' => $keyword]) }}" class="active-filter-remove">✕</a>
                        </div>
                    @endif
                    @if (isset($activeFilters['tags']))
                        @foreach ($activeFilters['tags'] as $tag)
                            <div class="active-filter-tag">
                                {{ $tag->name }}
                            </div>
                        @endforeach
                    @endif
                    @if (isset($activeFilters['dates']))
                        <div class="active-filter-tag">
                            {{ $activeFilters['dates']['from'] ?? '' }} - {{ $activeFilters['dates']['to'] ?? '' }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>

    {{-- Results --}}
    <section class="results-section">
        <div class="wrap">
            {{-- Courses --}}
            @if(isset($courses) && $courses->count() > 0)
            <div style="margin-bottom:4rem;">
                <div class="results-header rv">
                    <h2 class="results-title">📚 Kursus</h2>
                    <span class="results-count">{{ $courses->count() }} hasil</span>
                </div>

                <div class="results-grid">
                    @foreach ($courses as $i => $course)
                    <a href="{{ route('course.show', $course->slug) }}" class="crs-card rv rv-d{{ min($i % 4 + 1, 4) }}">
                        <div class="crs-thumb">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                                     alt="{{ $course->title }}"
                                     loading="lazy"
                                     onerror="this.style.display='none';this.parentElement.querySelector('.crs-thumb-placeholder').style.display='flex'">
                            @endif
                            <div class="crs-thumb-placeholder" style="display:{{ $course->thumbnail ? 'none' : 'flex' }};">
                                <svg style="width:36px;height:36px;color:var(--blue);opacity:.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            
                            @if(($course->price ?? 0) >= 0)
                            <span class="crs-badge {{ ($course->price ?? 0) > 0 ? 'badge-paid' : 'badge-free' }}">
                                {{ ($course->price ?? 0) > 0 ? 'Premium' : 'Gratis' }}
                            </span>
                            @endif
                        </div>

                        <div class="crs-body">
                            @if($course->category)
                                <span class="crs-cat">{{ $course->category->name }}</span>
                            @endif
                            <h3 class="crs-title">{{ $course->title }}</h3>

                            <div class="crs-meta">
                                <div class="crs-avatar"></div>
                                <span class="crs-instructor">
                                    {{ is_string($course->instructor ?? null) ? $course->instructor : ($course->instructor->name ?? 'Ray Academy') }}
                                </span>
                                <span class="crs-price {{ ($course->price ?? 0) == 0 ? 'crs-price-free' : '' }}">
                                    @if(($course->price ?? 0) == 0)
                                        Gratis
                                    @else
                                        Rp {{ number_format($course->price, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Articles --}}
            @if(isset($articles) && $articles->count() > 0)
            <div>
                <div class="results-header rv">
                    <h2 class="results-title">📝 Artikel</h2>
                    <span class="results-count">{{ $articles->count() }} hasil</span>
                </div>

                <div class="results-grid">
                    @foreach ($articles as $i => $article)
                    <a href="{{ route('article.show', $article->slug) }}" class="art-card rv rv-d{{ min($i % 4 + 1, 4) }}">
                        <div class="art-card-thumb-wrap">
                            @if($article->thumbnail ?? $article->cover_image ?? null)
                                <img src="{{ asset('storage/' . ($article->thumbnail ?? $article->cover_image)) }}"
                                     alt="{{ $article->title }}"
                                     class="art-card-thumb-img"
                                     loading="lazy"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                                <div class="art-card-thumb-placeholder" style="display:none;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:36px;height:36px;color:var(--blue);opacity:.25;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            @else
                                <div class="art-card-thumb-placeholder">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:36px;height:36px;color:var(--blue);opacity:.25;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                            @endif

                            @if($article->categories->isNotEmpty())
                                @php $cat = $article->categories->first(); @endphp
                                <span class="art-card-badge">{{ $cat->name }}</span>
                            @endif
                        </div>

                        <div class="art-card-body">
                            <p class="art-card-date">
                                @if($article->published_at) {{ $article->published_at->isoFormat('D MMM YYYY') }} @endif
                            </p>
                            <h2 class="art-card-title">{{ $article->title }}</h2>
                            @if($article->excerpt)
                                <p class="art-card-excerpt">{{ $article->excerpt }}</p>
                            @elseif($article->content ?? null)
                                <p class="art-card-excerpt">{{ Str::limit(strip_tags($article->content), 110) }}</p>
                            @endif
                            <div class="art-card-footer">
                                <span class="art-card-read">Baca Selengkapnya →</span>
                                @if($article->formatted_views ?? $article->views_count ?? null)
                                <span class="art-card-views">
                                    <svg viewBox="0 0 20 20" fill="currentColor" style="width:12px;height:12px;">
                                        <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/>
                                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $article->formatted_views ?? number_format($article->views_count ?? 0) }}
                                </span>
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- No Results --}}
            @if((!isset($courses) || $courses->count() == 0) && (!isset($articles) || $articles->count() == 0))
            <div class="empty-state rv">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3>Tidak Ada Hasil</h3>
                <p>Tidak ditemukan kursus atau artikel untuk "<strong>{{ $keyword }}</strong>"</p>
                <p style="margin-top:0.5rem;font-size:0.875rem;">Coba gunakan kata kunci lain atau ubah filter pencarian</p>
            </div>
            @endif
        </div>
    </section>
@endif

@endsection

@push('scripts')
<script>
// Scroll reveal
const obs = new IntersectionObserver(e => {
    e.forEach(x => {
        if(x.isIntersecting){ 
            x.target.classList.add('in'); 
            obs.unobserve(x.target); 
        }
    });
}, { threshold: 0.05, rootMargin:'0px 0px -40px 0px' });

document.querySelectorAll('.rv').forEach(el => obs.observe(el));
</script>
@endpush