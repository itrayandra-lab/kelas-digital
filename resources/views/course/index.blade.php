@extends('layouts.app')

@section('seo')
    <title>{{ $activeCategory ? 'Kelas '.$activeCategory->name : 'Katalog Kelas' }} - Ray Academy</title>
@endsection

@push('styles')
<style>
.ct-wrap { max-width:1280px; margin:0 auto; padding:0 1.5rem; }
.ct-hero { background:linear-gradient(135deg,#0a1628 0%,#14304f 100%); color:#fff; padding:3rem 0 2.5rem; position:relative; overflow:hidden; }
.ct-hero::after { content:''; position:absolute; right:-120px; top:-120px; width:420px; height:420px; border-radius:50%; background:radial-gradient(circle,rgba(20,116,188,.35),transparent 70%); pointer-events:none; }
.ct-crumb { font-size:.82rem; color:rgba(255,255,255,.6); margin-bottom:.75rem; }
.ct-crumb a { color:rgba(255,255,255,.75); text-decoration:none; }
.ct-hero h1 { font-family:'Sora',sans-serif; font-size:clamp(1.7rem,4vw,2.6rem); font-weight:800; line-height:1.2; }
.ct-hero p { color:rgba(255,255,255,.7); margin-top:.5rem; max-width:620px; }
.ct-search { position:relative; z-index:1; display:flex; background:#fff; border-radius:12px; padding:.35rem; max-width:620px; margin-top:1.5rem; box-shadow:0 12px 32px rgba(0,0,0,.2); }
.ct-search i { color:#94a3b8; align-self:center; margin:0 .6rem 0 .75rem; }
.ct-search input { flex:1; border:0; outline:0; font-size:.95rem; color:var(--ink); min-width:0; background:transparent; }
.ct-search button { background:var(--blue); color:#fff; font-weight:700; border-radius:9px; padding:.65rem 1.25rem; font-size:.875rem; }
.ct-cats { position:relative; z-index:1; display:flex; gap:.6rem; overflow-x:auto; scrollbar-width:none; margin-top:1.75rem; padding-bottom:.25rem; }
.ct-cats::-webkit-scrollbar { display:none; }
.ct-catcard { flex-shrink:0; display:flex; align-items:center; gap:.6rem; padding:.6rem .9rem; border-radius:12px; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.14); color:#fff; text-decoration:none; font-size:.85rem; font-weight:600; transition:background .2s; }
.ct-catcard:hover { background:rgba(255,255,255,.16); }
.ct-catcard.on { background:#fff; color:var(--ink); }
.ct-catcard .ic { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; background:rgba(255,255,255,.14); font-size:.8rem; }
.ct-catcard.on .ic { background:var(--blue-xl); color:var(--blue); }
.ct-catcard small { display:block; font-size:.7rem; font-weight:500; opacity:.7; }

.ct-main { background:var(--surf); padding:2rem 0 5rem; }
.ct-layout { display:grid; grid-template-columns:250px 1fr; gap:1.75rem; align-items:start; }
.ct-side { background:#fff; border:1px solid var(--border); border-radius:14px; padding:1.25rem; position:sticky; top:88px; }
.ct-side h4 { font-size:.75rem; font-weight:800; text-transform:uppercase; letter-spacing:.06em; color:var(--ink); margin:1.1rem 0 .55rem; }
.ct-side h4:first-child { margin-top:0; }
.ct-opt { display:flex; align-items:center; gap:.55rem; padding:.35rem 0; font-size:.86rem; color:var(--ink-2,#334155); cursor:pointer; }
.ct-opt input { accent-color:var(--blue); width:15px; height:15px; }
.ct-opt span { flex:1; }
.ct-opt em { font-style:normal; font-size:.75rem; color:var(--muted); }
.ct-reset { display:block; margin-top:1.1rem; font-size:.82rem; font-weight:600; color:var(--blue); text-decoration:none; }

.ct-bar { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; margin-bottom:1.25rem; }
.ct-count { flex:1; font-size:.9rem; color:var(--muted); }
.ct-count strong { color:var(--ink); }
.ct-sort { border:1px solid var(--border); background:#fff; border-radius:9px; padding:.5rem .75rem; font-size:.85rem; color:var(--ink); }
.ct-filter-btn { display:none; border:1px solid var(--border); background:#fff; border-radius:9px; padding:.5rem .85rem; font-size:.85rem; font-weight:600; color:var(--ink); }
.ct-chips { display:flex; flex-wrap:wrap; gap:.4rem; margin:-.4rem 0 1.1rem; }
.ct-chip { display:inline-flex; align-items:center; gap:.4rem; background:var(--blue-xl); color:var(--blue); font-size:.78rem; font-weight:600; padding:.3rem .65rem; border-radius:999px; text-decoration:none; }
.ct-empty { text-align:center; background:#fff; border:2px dashed var(--border); border-radius:16px; padding:3.5rem 1.5rem; }
.ct-empty i { font-size:2.2rem; color:var(--blue); opacity:.4; }
.ct-empty h3 { font-family:'Sora',sans-serif; font-weight:700; color:var(--ink); margin:.75rem 0 .3rem; }
.ct-empty p { color:var(--muted); font-size:.9rem; }

@media (max-width: 900px) {
    .ct-layout { grid-template-columns:1fr; }
    .ct-side { position:static; display:none; }
    .ct-side.open { display:block; }
    .ct-filter-btn { display:inline-flex; gap:.4rem; align-items:center; }
}
</style>
@endpush

@section('content')
@php
    $levels = ['Beginner' => 'Pemula', 'Intermediate' => 'Menengah', 'Advanced' => 'Mahir'];
    $types = ['free' => 'Gratis', 'paid' => 'Berbayar'];
    $without = fn (string $key) => route('course.index', Arr::except($filters, $key));
@endphp

<section class="ct-hero">
    <div class="ct-wrap" style="position:relative;z-index:1;">
        <div class="ct-crumb"><a href="{{ route('home') }}">Beranda</a> › <a href="{{ route('course.index') }}">Kelas</a>@if($activeCategory) › {{ $activeCategory->name }}@endif</div>
        <h1>{{ $activeCategory ? 'Belajar '.$activeCategory->name : 'Mau belajar apa hari ini?' }}</h1>
        <p>{{ $activeCategory?->description && $activeCategory->description !== $activeCategory->name
            ? $activeCategory->description
            : 'Pilih bidang yang ingin kamu kuasai. Setiap kelas berisi video, bacaan, serta pre-test dan post-test di tiap bab.' }}</p>

        <form action="{{ route('course.index') }}" method="GET" class="ct-search" role="search">
            @foreach(Arr::except($filters, 'q') as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <i class="fas fa-search"></i>
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari kelas, topik, atau nama instruktur" aria-label="Cari kelas">
            <button type="submit">Cari</button>
        </form>

        @if($courseCategories->isNotEmpty())
        <nav class="ct-cats" aria-label="Kategori">
            <a href="{{ route('course.index', Arr::except($filters, 'category')) }}" class="ct-catcard {{ $categorySlug ? '' : 'on' }}">
                <span class="ic"><i class="fas fa-th-large"></i></span>
                <span>Semua<small>{{ $courseCategories->sum('courses_count') }} kelas</small></span>
            </a>
            @foreach($courseCategories as $cat)
                <a href="{{ route('course.index', ['category' => $cat->slug] + $filters) }}" class="ct-catcard {{ $categorySlug === $cat->slug ? 'on' : '' }}">
                    <span class="ic"><i class="fas {{ $cat->icon }}"></i></span>
                    <span>{{ $cat->name }}<small>{{ $cat->courses_count }} kelas</small></span>
                </a>
            @endforeach
        </nav>
        @endif
    </div>
</section>

<section class="ct-main" x-data="{ showFilters: false }">
    <div class="ct-wrap ct-layout">
        <form action="{{ route('course.index') }}" method="GET" class="ct-side" :class="showFilters && 'open'" id="ct-filters">
            @if($search !== '')<input type="hidden" name="q" value="{{ $search }}">@endif
            @if($sort)<input type="hidden" name="sort" value="{{ $sort }}">@endif

            <h4>Kategori</h4>
            <label class="ct-opt"><input type="radio" name="category" value="" @checked(! $categorySlug) onchange="this.form.submit()"><span>Semua kategori</span></label>
            @foreach($courseCategories as $cat)
                <label class="ct-opt"><input type="radio" name="category" value="{{ $cat->slug }}" @checked($categorySlug === $cat->slug) onchange="this.form.submit()"><span>{{ $cat->name }}</span><em>{{ $cat->courses_count }}</em></label>
            @endforeach

            <h4>Harga</h4>
            <label class="ct-opt"><input type="radio" name="type" value="" @checked(! isset($types[$type])) onchange="this.form.submit()"><span>Semua</span></label>
            @foreach($types as $value => $label)
                <label class="ct-opt"><input type="radio" name="type" value="{{ $value }}" @checked($type === $value) onchange="this.form.submit()"><span>{{ $label }}</span></label>
            @endforeach

            <h4>Level</h4>
            <label class="ct-opt"><input type="radio" name="level" value="" @checked(! isset($levels[$level])) onchange="this.form.submit()"><span>Semua level</span></label>
            @foreach($levels as $value => $label)
                <label class="ct-opt"><input type="radio" name="level" value="{{ $value }}" @checked($level === $value) onchange="this.form.submit()"><span>{{ $label }}</span></label>
            @endforeach

            <noscript><button type="submit" class="ct-sort" style="margin-top:1rem;">Terapkan</button></noscript>
            @if($filters)
                <a href="{{ route('course.index') }}" class="ct-reset"><i class="fas fa-undo"></i> Hapus semua filter</a>
            @endif
        </form>

        <div>
            <div class="ct-bar">
                <button type="button" class="ct-filter-btn" @click="showFilters = !showFilters"><i class="fas fa-sliders-h"></i> Filter</button>
                <p class="ct-count"><strong>{{ $courses->total() }}</strong> kelas ditemukan</p>
                <form action="{{ route('course.index') }}" method="GET">
                    @foreach(Arr::except($filters, 'sort') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="sort" class="ct-sort" onchange="this.form.submit()" aria-label="Urutkan">
                        <option value="">Terbaru</option>
                        <option value="popular" @selected($sort === 'popular')>Paling banyak peserta</option>
                        <option value="cheap" @selected($sort === 'cheap')>Harga terendah</option>
                    </select>
                </form>
            </div>

            @if($search !== '' || isset($types[$type]) || isset($levels[$level]))
            <div class="ct-chips">
                @if($search !== '')<a href="{{ $without('q') }}" class="ct-chip">"{{ $search }}" <i class="fas fa-times"></i></a>@endif
                @if(isset($types[$type]))<a href="{{ $without('type') }}" class="ct-chip">{{ $types[$type] }} <i class="fas fa-times"></i></a>@endif
                @if(isset($levels[$level]))<a href="{{ $without('level') }}" class="ct-chip">{{ $levels[$level] }} <i class="fas fa-times"></i></a>@endif
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
                    <p>Coba kata kunci lain atau hapus beberapa filter.</p>
                    <a href="{{ route('course.index') }}" class="ct-chip" style="margin-top:1rem;">Lihat semua kelas</a>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
