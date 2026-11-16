@extends('layouts.app')

@section('seo')
    <title>{{ $activeCategory ? 'Kelas '.$activeCategory->name : 'Katalog Kelas' }} - Ray Academy</title>
@endsection

@include('partials.catalog-styles')

@section('content')
@php
    $levels = ['Beginner' => 'Pemula', 'Intermediate' => 'Menengah', 'Advanced' => 'Mahir'];
    $types = ['free' => 'Gratis', 'partial' => 'Sebagian gratis', 'paid' => 'Berbayar'];
    // Link that keeps the other filters and sets (or clears, with null) one of them.
    $link = fn (string $key, ?string $value) => route('course.index', array_filter([$key => $value] + Arr::except($filters, $key)));
@endphp

<section class="ct-hero">
    <div class="ct-wrap ct-hero-inner">
        <div class="ct-hero-left">
            <div class="ct-crumb"><a href="{{ route('home') }}">Beranda</a> › <a href="{{ route('course.index') }}">Kelas</a>@if($activeCategory) › {{ $activeCategory->name }}@endif</div>
            @if($activeCategory)
                <h1>Belajar <span>{{ $activeCategory->name }}</span></h1>
                <p>{{ $activeCategory->description && $activeCategory->description !== $activeCategory->name ? $activeCategory->description : $activeCategory->courses_count.' kelas di bidang '.$activeCategory->name.', dari dasar sampai siap praktik.' }}</p>
            @elseif($search !== '')
                <h1>Kelas untuk <span>"{{ $search }}"</span></h1>
                <p>Ditemukan {{ $courses->total() }} kelas</p>
            @else
                <h1>Mau belajar <span>apa</span> hari ini?</h1>
                <p>Pilih bidang yang ingin kamu kuasai. Setiap kelas berisi video, bacaan, serta pre-test dan post-test di tiap modul.</p>
            @endif
        </div>
        <img src="{{ asset('img/search/hero.png') }}" alt="" class="ct-hero-img" onerror="this.style.display='none'">
    </div>
</section>

<div class="ct-bar">
    <div class="ct-wrap">
        <form action="{{ route('course.index') }}" method="GET" class="ct-search" role="search">
            @foreach(Arr::except($filters, 'q') as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <div class="ct-search-wrap">
                <i class="fas fa-search"></i>
                <input type="search" name="q" value="{{ $search }}" placeholder="Cari kelas, topik, atau nama instruktur..." aria-label="Cari kelas" autocomplete="off">
            </div>
            <button type="submit"><i class="fas fa-search"></i> Cari</button>
        </form>
    </div>
</div>

<section class="ct-body" x-data="{ showFilters: false }">
    <div class="ct-wrap ct-layout">
        <aside class="ct-side" :class="showFilters && 'open'">
            <div class="ct-box">
                <div class="ct-box-head">Kategori @if($filters)<a href="{{ route('course.index') }}">Reset</a>@endif</div>
                <div class="ct-box-body">
                    <a href="{{ $link('category', null) }}" class="ct-chip {{ $categorySlug ? '' : 'on' }}"><i class="fas fa-th-large"></i> Semua kategori <em>{{ $courseCategories->sum('courses_count') }}</em></a>
                    @foreach($courseCategories as $cat)
                        <a href="{{ $link('category', $cat->slug) }}" class="ct-chip {{ $categorySlug === $cat->slug ? 'on' : '' }}"><i class="fas {{ $cat->icon }}"></i> {{ $cat->name }} <em>{{ $cat->courses_count }}</em></a>
                    @endforeach
                </div>
            </div>
            <div class="ct-box">
                <div class="ct-box-head">Harga</div>
                <div class="ct-box-body">
                    <a href="{{ $link('type', null) }}" class="ct-chip {{ isset($types[$type]) ? '' : 'on' }}">Semua</a>
                    @foreach($types as $value => $label)
                        <a href="{{ $link('type', $value) }}" class="ct-chip {{ $type === $value ? 'on' : '' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
            <div class="ct-box">
                <div class="ct-box-head">Level</div>
                <div class="ct-box-body">
                    <a href="{{ $link('level', null) }}" class="ct-chip {{ isset($levels[$level]) ? '' : 'on' }}">Semua level</a>
                    @foreach($levels as $value => $label)
                        <a href="{{ $link('level', $value) }}" class="ct-chip {{ $level === $value ? 'on' : '' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </aside>

        <div>
            <div class="ct-head">
                <h2>{{ $courses->total() }} kelas ditemukan</h2>
                <div class="ct-head-right">
                    <button type="button" class="ct-filter-btn" @click="showFilters = !showFilters"><i class="fas fa-sliders-h"></i> Filter</button>
                    <form action="{{ route('course.index') }}" method="GET">
                        @foreach(Arr::except($filters, 'sort') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <select name="sort" class="ct-sort" onchange="this.form.submit()" aria-label="Urutkan">
                            <option value="">Terbaru</option>
                            <option value="popular" @selected($sort === 'popular')>Terpopuler</option>
                            <option value="cheap" @selected($sort === 'cheap')>Harga terendah</option>
                        </select>
                    </form>
                </div>
            </div>

            @if($search !== '' || isset($types[$type]) || isset($levels[$level]))
            <div class="ct-tags">
                @if($search !== '')<a href="{{ $link('q', null) }}" class="ct-tag">"{{ $search }}" <i class="fas fa-times"></i></a>@endif
                @if(isset($types[$type]))<a href="{{ $link('type', null) }}" class="ct-tag">{{ $types[$type] }} <i class="fas fa-times"></i></a>@endif
                @if(isset($levels[$level]))<a href="{{ $link('level', null) }}" class="ct-tag">{{ $levels[$level] }} <i class="fas fa-times"></i></a>@endif
            </div>
            @endif

            @if($courses->isNotEmpty())
                <div class="cc-grid">
                    @foreach($courses as $course)
                        @include('course._card')
                    @endforeach
                </div>
                @if($courses->hasPages())
                    <div style="margin-top:2.5rem;">{{ $courses->links() }}</div>
                @endif
            @else
                <div class="ct-empty">
                    <i class="fas fa-search"></i>
                    <h3>Kelas yang kamu cari belum ada</h3>
                    <p>Coba kata kunci lain atau <a href="{{ route('course.index') }}">lihat semua kelas</a>.</p>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
