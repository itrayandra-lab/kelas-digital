@extends('layouts.app')

@section('seo')
    <title>{{ $activeCategory ? 'Artikel '.$activeCategory->name : ($activeTag ? 'Artikel tentang '.$activeTag->name : 'Artikel') }} - Ray Academy</title>
@endsection

@include('partials.catalog-styles')

@push('styles')
<style>
.ct-tagcloud { padding:.75rem; display:flex; flex-wrap:wrap; gap:.4rem; }
.ct-tagcloud a { font-size:.74rem; color:#374151; border:1px solid #E2E8F0; border-radius:999px; padding:.25rem .65rem; text-decoration:none; transition:border-color .15s, color .15s; }
.ct-tagcloud a:hover, .ct-tagcloud a.on { border-color:#0056D2; color:#0056D2; }
.ct-tagcloud a.on { background:#EEF4FF; font-weight:600; }
</style>
@endpush

@section('content')
@php
    // The search and sort forms submit to the page they are on, so a category or tag stays applied.
    $here = url()->current();
    $filtered = $activeCategory || $activeTag || $search !== '' || $sort;
@endphp

<section class="ct-hero">
    <div class="ct-wrap ct-hero-inner">
        <div class="ct-hero-left">
            <div class="ct-crumb">
                <a href="{{ route('home') }}">Beranda</a> › <a href="{{ route('article.index') }}">Artikel</a>
                @if($activeCategory) › {{ $activeCategory->name }}@elseif($activeTag) › #{{ $activeTag->name }}@endif
            </div>
            @if($activeCategory)
                <h1>Artikel <span>{{ $activeCategory->name }}</span></h1>
                <p>{{ $activeCategory->description && $activeCategory->description !== $activeCategory->name ? $activeCategory->description : 'Bacaan seputar '.$activeCategory->name.' dari instruktur dan tim Ray Academy.' }}</p>
            @elseif($activeTag)
                <h1>Artikel tentang <span>{{ $activeTag->name }}</span></h1>
                <p>Semua artikel dengan topik {{ $activeTag->name }}.</p>
            @elseif($search !== '')
                <h1>Artikel untuk <span>"{{ $search }}"</span></h1>
                <p>Ditemukan {{ $articles->total() }} artikel</p>
            @else
                <h1>Baca, <span>pahami</span>, lalu praktikkan</h1>
                <p>Tips dan wawasan dari instruktur Ray Academy tentang marketing, bisnis, teknologi, dan pengembangan diri.</p>
            @endif
        </div>
        <img src="{{ asset('img/search/hero.png') }}" alt="" class="ct-hero-img" onerror="this.style.display='none'">
    </div>
</section>

<div class="ct-bar">
    <div class="ct-wrap">
        <form action="{{ $here }}" method="GET" class="ct-search" role="search">
            @if($sort)<input type="hidden" name="sort" value="{{ $sort }}">@endif
            <div class="ct-search-wrap">
                <i class="fas fa-search"></i>
                <input type="search" name="q" value="{{ $search }}" placeholder="Cari judul atau isi artikel..." aria-label="Cari artikel" autocomplete="off">
            </div>
            <button type="submit"><i class="fas fa-search"></i> Cari</button>
        </form>
    </div>
</div>

<section class="ct-body" x-data="{ showFilters: false }">
    <div class="ct-wrap ct-layout">
        <aside class="ct-side" :class="showFilters && 'open'">
            <div class="ct-box">
                <div class="ct-box-head">Kategori @if($filtered)<a href="{{ route('article.index') }}">Reset</a>@endif</div>
                <div class="ct-box-body">
                    <a href="{{ route('article.index', $query) }}" class="ct-chip {{ $activeCategory || $activeTag ? '' : 'on' }}"><i class="fas fa-th-large"></i> Semua artikel</a>
                    @foreach($categories as $cat)
                        <a href="{{ route('article.category', [$cat->slug] + $query) }}" class="ct-chip {{ $activeCategory?->is($cat) ? 'on' : '' }}">{{ $cat->name }} <em>{{ $cat->articles_count }}</em></a>
                    @endforeach
                </div>
            </div>
            @if($tags->isNotEmpty())
                <div class="ct-box">
                    <div class="ct-box-head">Topik populer</div>
                    <div class="ct-tagcloud">
                        @foreach($tags as $t)
                            <a href="{{ route('tag.show', [$t->slug] + $query) }}" class="{{ $activeTag?->is($t) ? 'on' : '' }}">#{{ $t->name }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </aside>

        <div>
            <div class="ct-head">
                <h2>{{ $articles->total() }} artikel</h2>
                <div class="ct-head-right">
                    <button type="button" class="ct-filter-btn" @click="showFilters = !showFilters"><i class="fas fa-sliders-h"></i> Filter</button>
                    <form action="{{ $here }}" method="GET">
                        @if($search !== '')<input type="hidden" name="q" value="{{ $search }}">@endif
                        <select name="sort" class="ct-sort" onchange="this.form.submit()" aria-label="Urutkan">
                            <option value="">Terbaru</option>
                            <option value="popular" @selected($sort === 'popular')>Paling banyak dibaca</option>
                        </select>
                    </form>
                </div>
            </div>

            @if($search !== '' || $activeTag)
                <div class="ct-tags">
                    @if($search !== '')<a href="{{ $here.($sort ? '?sort='.$sort : '') }}" class="ct-tag">"{{ $search }}" <i class="fas fa-times"></i></a>@endif
                    @if($activeTag)<a href="{{ route('article.index', $query) }}" class="ct-tag">#{{ $activeTag->name }} <i class="fas fa-times"></i></a>@endif
                </div>
            @endif

            @if($articles->isNotEmpty())
                <div class="ac-grid">
                    @foreach($articles as $article)
                        @include('article._card')
                    @endforeach
                </div>
                @if($articles->hasPages())
                    <div style="margin-top:2.5rem;">{{ $articles->links() }}</div>
                @endif
            @else
                <div class="ct-empty">
                    <i class="far fa-file-alt"></i>
                    <h3>Artikel yang kamu cari belum ada</h3>
                    <p>Coba kata kunci lain atau <a href="{{ route('article.index') }}">lihat semua artikel</a>.</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
