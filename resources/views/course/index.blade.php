@extends('layouts.app')

@section('seo')
    <title>{{ $activeCategory ? 'Kelas '.$activeCategory->name : 'Katalog Kelas' }} - Ray Academy</title>
@endsection

@push('styles')
<style>
/* Same look as the home and search pages: cream hero with grid lines, #0056D2 blue, bordered filter boxes. */
.ct-wrap { max-width:1280px; margin:0 auto; padding:0 1.5rem; }
.ct-hero { background:#F5F5F4; border-bottom:1px solid #E2E8F0; padding:2rem 0 0; position:relative; overflow:hidden; }
.ct-hero::before { content:''; position:absolute; inset:0; background-image:linear-gradient(rgba(0,86,210,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,86,210,.03) 1px,transparent 1px); background-size:48px 48px; pointer-events:none; }
.ct-hero-inner { position:relative; z-index:1; display:grid; grid-template-columns:1fr auto; gap:2rem; align-items:end; }
.ct-hero-left { padding-bottom:1.75rem; }
.ct-crumb { font-size:.78rem; color:#6B7280; margin-bottom:.6rem; }
.ct-crumb a { color:#6B7280; text-decoration:none; }
.ct-crumb a:hover { color:#0056D2; }
.ct-hero h1 { font-family:'Sora',sans-serif; font-size:clamp(1.4rem,3vw,2rem); font-weight:700; color:#0F1114; letter-spacing:-.02em; margin-bottom:.25rem; }
.ct-hero h1 span { color:#0056D2; }
.ct-hero p { font-size:.85rem; color:#6B7280; max-width:620px; }
.ct-hero-img { width:180px; height:120px; object-fit:contain; object-position:bottom; display:block; opacity:.9; }

.ct-bar { background:#fff; border-bottom:1px solid #E2E8F0; padding:.875rem 0; box-shadow:0 1px 8px rgba(0,0,0,.06); }
.ct-search { display:flex; align-items:center; gap:.75rem; }
.ct-search-wrap { flex:1; display:flex; align-items:center; background:#F8FAFF; border:1.5px solid #CBD5E1; border-radius:10px; overflow:hidden; transition:border-color .2s, box-shadow .2s; }
.ct-search-wrap:focus-within { border-color:#0056D2; box-shadow:0 0 0 3px rgba(0,86,210,.1); }
.ct-search-wrap i { color:#94A3B8; margin:0 .75rem; }
.ct-search-wrap input { flex:1; min-width:0; border:0; background:transparent; outline:0; padding:.75rem .5rem .75rem 0; font-size:.9rem; color:#0F1114; }
.ct-search button { background:#0056D2; color:#fff; border:0; padding:.75rem 1.5rem; border-radius:8px; font-weight:600; font-size:.875rem; cursor:pointer; white-space:nowrap; }
.ct-search button:hover { background:#0048B0; }

.ct-body { background:#fff; padding:1.5rem 0 4rem; }
.ct-layout { display:grid; grid-template-columns:240px 1fr; gap:2rem; align-items:start; }
.ct-side { position:sticky; top:88px; }
.ct-box { border:1.5px solid #E2E8F0; border-radius:12px; overflow:hidden; background:#fff; margin-bottom:1rem; }
.ct-box-head { padding:.875rem 1rem; border-bottom:1px solid #F1F5F9; font-family:'Sora',sans-serif; font-size:.82rem; font-weight:700; color:#0F1114; display:flex; justify-content:space-between; align-items:center; }
.ct-box-head a { font-size:.72rem; font-weight:600; color:#0056D2; text-decoration:none; }
.ct-box-body { padding:.6rem .75rem; display:flex; flex-direction:column; gap:.2rem; }
.ct-chip { display:flex; align-items:center; gap:.5rem; padding:.45rem .6rem; border-radius:8px; font-size:.8rem; font-weight:500; color:#374151; text-decoration:none; transition:background .15s; }
.ct-chip i { width:16px; text-align:center; font-size:.75rem; color:#94A3B8; }
.ct-chip:hover { background:#F1F5FF; color:#0056D2; }
.ct-chip.on { background:#EEF4FF; color:#0056D2; font-weight:600; }
.ct-chip.on i { color:#0056D2; }
.ct-chip em { margin-left:auto; font-style:normal; font-size:.7rem; color:#9CA3AF; }

.ct-head { display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap; margin-bottom:1rem; padding-bottom:.75rem; border-bottom:1px solid #F1F5F9; }
.ct-head h2 { font-family:'Sora',sans-serif; font-size:.95rem; font-weight:700; color:#0F1114; }
.ct-head-right { display:flex; align-items:center; gap:.5rem; }
.ct-sort { border:1.5px solid #E2E8F0; background:#fff; border-radius:8px; padding:.45rem .7rem; font-size:.8rem; color:#0F1114; }
.ct-filter-btn { display:none; border:1.5px solid #E2E8F0; background:#fff; border-radius:8px; padding:.45rem .8rem; font-size:.8rem; font-weight:600; color:#0F1114; }
.ct-tags { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1rem; }
.ct-tag { display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .75rem; background:#EEF4FF; color:#0056D2; font-size:.73rem; font-weight:600; border-radius:999px; border:1px solid rgba(0,86,210,.2); text-decoration:none; }
.ct-tag:hover { color:#c00; }
.ct-empty { text-align:center; padding:3.5rem 1.5rem; }
.ct-empty i { font-size:2.5rem; color:#CBD5E1; }
.ct-empty h3 { font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:700; color:#0F1114; margin:1rem 0 .4rem; }
.ct-empty p { font-size:.85rem; color:#6B7280; }
.ct-empty a { color:#0056D2; font-weight:600; text-decoration:none; }

@media (max-width: 900px) {
    .ct-layout { grid-template-columns:1fr; }
    .ct-side { position:static; display:none; }
    .ct-side.open { display:block; }
    .ct-filter-btn { display:inline-flex; gap:.4rem; align-items:center; }
}
@media (max-width: 640px) {
    .ct-hero-img { display:none; }
    .ct-search button { padding:.75rem 1rem; }
}
</style>
@endpush

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
