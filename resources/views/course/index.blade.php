@extends('layouts.app')

@section('title', 'Semua Kursus — Ray Academy')

@push('styles')
<style>
/* ══════ COURSES INDEX - CLEAN & CONSISTENT ══════ */

/* Page Hero */
.page-hero {
    background: linear-gradient(135deg, var(--ink) 0%, #1a2942 100%);
    padding: 4rem 0 3rem;
    position: relative;
    overflow: hidden;
}

.page-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 60% 50% at 50% 0%, rgba(20,116,188,.15) 0%, transparent 60%);
    pointer-events: none;
}

.page-hero-inner {
    position: relative;
    z-index: 1;
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1.5rem;
    text-align: center;
}

.page-hero-label {
    display: inline-block;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,.7);
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.2);
    padding: 0.35rem 0.85rem;
    border-radius: 999px;
    margin-bottom: 1rem;
}

.page-hero h1 {
    font-size: clamp(2rem, 5vw, 3rem);
    font-weight: 800;
    color: #fff;
    margin-bottom: 0.75rem;
}

.page-hero p {
    font-size: 1.05rem;
    color: rgba(255,255,255,.65);
    max-width: 600px;
    margin: 0 auto;
}

/* Filter Tabs - SAMA dengan home */
.f-wrap {
    background: #fff;
    border-bottom: 1px solid var(--border);
    padding: 0.75rem 0;
    position: sticky;
    top: 68px;
    z-index: 100;
}

.f-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1.5rem;
}

.f-tabs {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}

.f-tabs::-webkit-scrollbar {
    display: none;
}

.f-tab {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.83rem;
    font-weight: 600;
    cursor: pointer;
    padding: 0.5rem 1.1rem;
    border-radius: 9px;
    border: 1.5px solid var(--border);
    background: #fff;
    color: var(--ink-2);
    transition: all 0.18s;
    white-space: nowrap;
    flex-shrink: 0;
    text-decoration: none;
    display: inline-block;
}

.f-tab:hover {
    border-color: var(--blue);
    color: var(--blue);
}

.f-tab.active {
    background: var(--blue);
    color: #fff;
    border-color: var(--blue);
    box-shadow: 0 4px 14px rgba(20,116,188,.28);
}

/* Course Card - SAMA PERSIS dengan home */
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
    background: linear-gradient(135deg, #dbeafe, #eff6ff);
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
    object-fit: cover;
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

/* Grid */
.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    gap: 1.25rem;
}

/* Mobile Responsive */
@media (max-width: 900px) {
    .courses-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {
    .courses-grid {
        grid-template-columns: 1fr;
    }
    
    .page-hero {
        padding: 3rem 0 2rem;
    }
}

/* Breadcrumb */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    margin-bottom: 2rem;
}

.breadcrumb a {
    color: var(--muted);
    text-decoration: none;
    transition: color 0.18s;
}

.breadcrumb a:hover {
    color: var(--blue);
}

.breadcrumb span {
    color: var(--ink);
    font-weight: 600;
}

.breadcrumb svg {
    color: var(--muted);
    width: 14px;
    height: 14px;
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
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="page-hero">
    <div class="page-hero-inner">
        <span class="page-hero-label">Kursus Online</span>
        <h1>{{ $activeCategory ? $activeCategory->name : 'Semua Kursus' }}</h1>
        <p>{{ $activeCategory ? 'Kursus dalam kategori ' . $activeCategory->name : 'Temukan kursus yang tepat dan mulai perjalanan belajarmu hari ini.' }}</p>
    </div>
</section>

{{-- Filter Tabs --}}
@if(isset($courseCategories) && $courseCategories->count())
<div class="f-wrap">
    <div class="f-inner">
        <div class="f-tabs">
            <a href="{{ route('course.index') }}" class="f-tab {{ !$categorySlug ? 'active' : '' }}">Semua Kursus</a>
            @foreach($courseCategories as $cat)
                <a href="{{ route('course.index', ['category' => $cat->slug]) }}"
                   class="f-tab {{ $categorySlug === $cat->slug ? 'active' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Content --}}
<section style="background:var(--surf); padding:3.5rem 0 6rem;">
    <div style="max-width:1280px;margin:0 auto;padding:0 1.5rem;">

        <nav class="breadcrumb rv">
            <a href="{{ route('home') }}">Beranda</a>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>Kursus</span>
        </nav>

        @if($courses->count() > 0)

            <p class="rv" style="font-size:0.85rem;color:var(--muted);margin-bottom:1.75rem;">
                Menampilkan <strong style="color:var(--ink);">{{ $courses->total() }}</strong> kursus tersedia
            </p>

            <div class="courses-grid" id="courses-grid">
                @foreach($courses as $i => $course)
                <a href="{{ route('course.show', $course->slug) }}"
                   class="crs-card rv rv-d{{ min($i % 4 + 1, 4) }}"
                   data-cat="{{ $course->category->slug ?? '' }}">

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
                        
                        @if(($course->price ?? 0) > 0 || ($course->price ?? 0) == 0)
                        <span class="crs-badge {{ $course->course_type === 'free' ? 'badge-free' : (($course->price ?? 0) > 0 ? 'badge-paid' : 'badge-free') }}">
                            {{ $course->course_type === 'free' ? 'Kelas Gratis' : (($course->price ?? 0) > 0 ? 'Premium' : 'Gratis') }}
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
                            <span class="crs-price {{ $course->course_type === 'free' || ($course->price ?? 0) == 0 ? 'crs-price-free' : '' }}">
                                @if($course->course_type === 'free')
                                    Gratis!
                                @elseif(($course->price ?? 0) == 0)
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

            {{-- Pagination --}}
            @if($courses->hasPages())
            <div style="margin-top:3rem;" class="rv">
                {{ $courses->links() }}
            </div>
            @endif

        @else
            <div class="empty-state rv">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <h3>Belum ada kursus</h3>
                <p>Kursus akan segera tersedia. Pantau terus!</p>
            </div>
        @endif
    </div>
</section>

@endsection

@push('scripts')
<script>
// Scroll reveal
const obs = new IntersectionObserver(e => {
    e.forEach(x => {
        if (x.isIntersecting) {
            x.target.classList.add('in');
            obs.unobserve(x.target);
        }
    });
}, { threshold: 0.05, rootMargin: '0px 0px -40px 0px' });

document.querySelectorAll('.rv').forEach(el => obs.observe(el));
</script>
@endpush