@extends('layouts.app')

@section('title', config('app.name') . ' — Platform Belajar Online Terbaik')

@push('styles')
<style>
/* ─────────────────────────────────────────────
   GLOBAL — pakai Poppins yg sudah di-load app.blade
   ───────────────────────────────────────────── */
:root {
    --rb:   #1474bc;   /* Ray Blue — sama persis dgn footer */
    --rb-d: #0e5a9b;   /* darker */
    --rb-l: #1e8bd4;   /* lighter */
    --acc:  #F59E0B;
    --acc-h:#FBBF24;
    --txt:  #0f172a;
    --sub:  #64748b;
    --surf: #f8fafc;
    --bdr:  #e2e8f0;
}

/* ─── Hero ─── */
.hero-bg {
    background: linear-gradient(145deg, #092e5e 0%, #0e5a9b 45%, #1474bc 100%);
}
.hero-grid {
    background-image:
        linear-gradient(rgba(255,255,255,.055) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,.055) 1px, transparent 1px);
    background-size: 44px 44px;
}
.hero-glow-a { background: radial-gradient(circle, rgba(255,255,255,.13) 0%, transparent 65%); }
.hero-glow-b { background: radial-gradient(circle, rgba(245,158,11,.16) 0%, transparent 60%); }

.badge-pill {
    display: inline-flex; align-items: center; gap: .45rem;
    background: rgba(255,255,255,.14); backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,.22); border-radius: 999px;
    padding: .4rem 1rem; font-size: .8rem; font-weight: 600; color: #fff;
}
.badge-dot {
    width: 7px; height: 7px; border-radius: 50%; background: #4ade80;
    animation: bdot 2s ease-in-out infinite;
}
@keyframes bdot { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.4);opacity:.7} }

.stat-sep { width: 1px; background: rgba(255,255,255,.22); align-self: stretch; }

/* ─── Buttons ─── */
.btn-primary {
    display:inline-flex; align-items:center; gap:.5rem;
    background: var(--acc); color: #111; font-weight: 700;
    padding: .9rem 1.7rem; border-radius: 12px; font-size: .9375rem;
    text-decoration: none; transition: all .22s;
    box-shadow: 0 4px 16px rgba(245,158,11,.38);
}
.btn-primary:hover { background: var(--acc-h); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(245,158,11,.45); }

.btn-ghost {
    display:inline-flex; align-items:center; gap:.5rem;
    background: rgba(255,255,255,.13); border: 1px solid rgba(255,255,255,.28);
    backdrop-filter: blur(8px); color: #fff; font-weight: 600;
    padding: .9rem 1.7rem; border-radius: 12px; font-size: .9375rem;
    text-decoration: none; transition: all .22s;
}
.btn-ghost:hover { background: rgba(255,255,255,.22); transform: translateY(-2px); }

/* ─── Section Label ─── */
.sec-label {
    display: inline-flex; align-items: center; gap: .4rem;
    font-size: .72rem; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: var(--rb); margin-bottom: .75rem;
}
.sec-label::before {
    content:''; width:6px; height:6px; border-radius:50%;
    background: var(--rb); flex-shrink:0;
}

/* ─── Splide ─── */
.splide__pagination__page { background: rgba(255,255,255,.4) !important; }
.splide__pagination__page.is-active { background: #fff !important; transform: scale(1.3) !important; }

/* ─── Course Card ─── */
.c-card {
    background: #fff; border: 1px solid var(--bdr); border-radius: 16px;
    overflow: hidden; display: flex; flex-direction: column;
    text-decoration: none; color: inherit;
    transition: transform .3s ease, box-shadow .3s ease, border-color .28s;
}
.c-card:hover { transform: translateY(-5px); box-shadow: 0 18px 38px rgba(20,116,188,.13); border-color: #bfdbfe; }

.c-thumb {
    position: relative; overflow: hidden; aspect-ratio: 16/9;
    background: linear-gradient(135deg, #dbeafe, #eff6ff);
}
.c-thumb img { width:100%; height:100%; object-fit:cover; transition: transform .45s ease; display:block; }
.c-card:hover .c-thumb img { transform: scale(1.06); }
.c-thumb img.broken { display: none !important; }

.c-placeholder {
    display: none;
    width:100%; height:100%; flex-direction:column;
    align-items:center; justify-content:center; gap:10px;
    background: linear-gradient(135deg, #dbeafe, #eff6ff);
    padding: 1.5rem; text-align: center; position:absolute; inset:0;
}
.c-placeholder-icon {
    width: 50px; height: 50px; border-radius: 13px;
    background: rgba(20,116,188,.1);
    display:flex; align-items:center; justify-content:center;
}

/* ─── Filter Tabs ─── */
.f-tab {
    flex-shrink:0; padding:.5rem 1.15rem; border-radius: 9px;
    font-size:.875rem; font-weight:600; cursor:pointer;
    border: none; outline: none;
    background:#f1f5f9; color:#475569; transition: all .18s;
    font-family: inherit;
}
.f-tab:hover  { background:#e2e8f0; }
.f-tab.active { background: var(--rb); color: #fff; box-shadow: 0 4px 12px rgba(20,116,188,.3); }
.scrollbar-hide::-webkit-scrollbar { display:none; }
.scrollbar-hide { -ms-overflow-style:none; scrollbar-width:none; }

/* ─── Why us stat card ─── */
.stat-card-hero {
    background: linear-gradient(135deg, var(--rb-d), var(--rb));
    border-radius: 16px; padding: 1.5rem; color:#fff;
    grid-column: span 2; position:relative; overflow:hidden;
}
.stat-card-hero::after {
    content:''; position:absolute; top:-40px; right:-40px;
    width:130px; height:130px; border-radius:50%; background:rgba(255,255,255,.08);
}

/* ─── Benefit ─── */
.benefit-icon {
    flex-shrink:0; width:48px; height:48px; border-radius:13px;
    background:#eff6ff; display:flex; align-items:center; justify-content:center;
    transition: background .2s;
}
.benefit-row:hover .benefit-icon { background:#dbeafe; }

/* ─── Instructor card ─── */
.ins-card {
    position:relative; border-radius:20px; overflow:hidden;
    box-shadow: 0 8px 28px rgba(0,0,0,.18);
    transition: transform .3s ease, box-shadow .3s ease;
}
.ins-card:hover { transform: translateY(-7px); box-shadow: 0 22px 50px rgba(0,0,0,.25); }

/* ─── Article small ─── */
.art-small {
    display:flex; gap:1rem; align-items:center;
    background:#fff; border:1px solid #f1f5f9; border-radius:14px;
    overflow:hidden; text-decoration:none; color:inherit; transition: all .22s;
}
.art-small:hover { box-shadow: 0 6px 20px rgba(20,116,188,.1); border-color:#bfdbfe; }

/* ─── CTA ─── */
.cta-bg { background: linear-gradient(145deg, #092e5e 0%, #0e5a9b 45%, #1474bc 100%); }
.cta-dots {
    background-image: radial-gradient(rgba(255,255,255,.18) 1px, transparent 1px);
    background-size: 28px 28px;
}

/* ─── Scroll reveal ─── */
.reveal { opacity:0; transform:translateY(26px); transition: opacity .55s ease, transform .55s ease; }
.reveal.visible { opacity:1; transform:translateY(0); }
.rd1{transition-delay:.08s} .rd2{transition-delay:.16s} .rd3{transition-delay:.24s} .rd4{transition-delay:.32s}
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════════
     1. HERO
════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden hero-bg" style="min-height:90vh;display:flex;align-items:center;">

    <div class="absolute inset-0 hero-grid pointer-events-none"></div>
    <div class="absolute pointer-events-none hero-glow-a" style="top:-8rem;right:-8rem;width:500px;height:500px;"></div>
    <div class="absolute pointer-events-none hero-glow-b" style="bottom:0;left:-5rem;width:320px;height:320px;"></div>

    <div class="relative w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">
        <div class="grid lg:grid-cols-2 gap-14 items-center">

            {{-- ── Left ── --}}
            <div class="text-white" style="opacity:0;transform:translateY(20px);transition:all .6s ease;" id="hero-left">

                <span class="badge-pill" style="margin-bottom:1.75rem;">
                    <span class="badge-dot"></span>
                    Platform Belajar Online Terpercaya
                </span>

                <h1 class="font-bold" style="font-size:clamp(2.2rem,4.5vw,3.4rem);line-height:1.15;margin-bottom:1.5rem;">
                    Kembangkan<br>
                    <span style="color:#fde047;">Skill-mu</span><br>
                    Bersama Para Ahli
                </h1>

                <p style="font-size:1.0625rem;color:rgba(255,255,255,.78);max-width:440px;line-height:1.75;margin-bottom:2rem;">
                    Akses ratusan kursus berkualitas tinggi dari instruktur berpengalaman.
                    Belajar kapan saja, di mana saja, dan raih karir impianmu.
                </p>

                <div class="flex flex-wrap items-center" style="gap:1.5rem;margin-bottom:2.25rem;">
                    <div>
                        <div style="font-size:2rem;font-weight:800;color:#fde047;">{{ number_format($stats['total_courses']) }}+</div>
                        <div style="font-size:.72rem;color:rgba(255,255,255,.6);font-weight:500;margin-top:2px;">Kursus Tersedia</div>
                    </div>
                    <div class="stat-sep self-stretch hidden sm:block"></div>
                    <div>
                        <div style="font-size:2rem;font-weight:800;color:#fde047;">{{ number_format($stats['total_students']) }}+</div>
                        <div style="font-size:.72rem;color:rgba(255,255,255,.6);font-weight:500;margin-top:2px;">Pelajar Aktif</div>
                    </div>
                    <div class="stat-sep self-stretch hidden sm:block"></div>
                    <div>
                        <div style="font-size:2rem;font-weight:800;color:#fde047;">{{ number_format($stats['total_articles']) }}+</div>
                        <div style="font-size:.72rem;color:rgba(255,255,255,.6);font-weight:500;margin-top:2px;">Artikel & Tips</div>
                    </div>
                </div>

                <div style="display:flex;flex-wrap:wrap;gap:1rem;">
                    <a href="{{ route('course.index') }}" class="btn-primary">
                        <svg style="width:18px;height:18px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Mulai Belajar Sekarang
                    </a>
                    <a href="{{ route('article.index') }}" class="btn-ghost">
                        Jelajahi Artikel
                        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- ── Right: Slider ── --}}
            <div class="hidden lg:block" style="opacity:0;transform:translateY(20px);transition:all .6s .2s ease;" id="hero-right">
                @if($heroArticles->isNotEmpty())
                <div id="hero-article-slider" class="splide" aria-label="Artikel Pilihan">
                    <div class="splide__track" style="border-radius:22px;overflow:hidden;box-shadow:0 30px 60px rgba(0,0,0,.35);">
                        <ul class="splide__list">
                            @foreach($heroArticles as $article)
                            <li class="splide__slide">
                                <a href="{{ route('article.show', $article->slug) }}" class="block relative group">
                                    @if($article->thumbnail ?? $article->cover_image ?? null)
                                        <img src="{{ $article->thumbnail ?? $article->cover_image }}"
                                             alt="{{ $article->title }}"
                                             style="width:100%;height:340px;object-fit:cover;display:block;">
                                    @else
                                        <div style="width:100%;height:340px;background:linear-gradient(135deg,#092e5e,#0e5a9b);display:flex;align-items:center;justify-content:center;">
                                            <svg style="width:56px;height:56px;opacity:.25;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.85),rgba(0,0,0,.2) 55%,transparent 100%);">
                                        <div style="position:absolute;bottom:0;left:0;right:0;padding:1.75rem;color:#fff;">
                                            @if($article->categories->first())
                                                <span style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:#fde047;display:block;margin-bottom:.45rem;">
                                                    {{ $article->categories->first()->name }}
                                                </span>
                                            @endif
                                            <h3 style="font-size:1.1rem;font-weight:700;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                                {{ $article->title }}
                                            </h3>
                                            @if($article->published_at)
                                                <p style="font-size:.72rem;color:rgba(255,255,255,.5);margin-top:.5rem;">
                                                    {{ $article->published_at->isoFormat('D MMM Y') }}
                                                    @if($article->views_count ?? null) · {{ number_format($article->views_count) }} views @endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @else
                <div style="border-radius:22px;height:340px;background:linear-gradient(135deg,#092e5e,#0e5a9b);display:flex;align-items:center;justify-content:center;box-shadow:0 30px 60px rgba(0,0,0,.3);">
                    <p style="color:rgba(255,255,255,.3);font-size:.875rem;">Artikel segera hadir</p>
                </div>
                @endif
            </div>

        </div>
    </div>

    <div class="hidden lg:flex" style="position:absolute;bottom:1.75rem;left:50%;transform:translateX(-50%);flex-direction:column;align-items:center;gap:.35rem;color:rgba(255,255,255,.35);animation:bounce 2s infinite;">
        <span style="font-size:.6rem;font-weight:700;letter-spacing:.18em;">SCROLL</span>
        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════
     2. FEATURED COURSES
════════════════════════════════════════════════════ --}}
<section class="py-24 lg:py-32" style="background:#fff;" id="kursus">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5 mb-12 reveal">
            <div>
                <span class="sec-label">Kursus Pilihan</span>
                <h2 style="font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;color:var(--txt);line-height:1.2;margin-top:.25rem;">
                    Mulai Belajar dari<br>Kursus Terbaik Kami
                </h2>
            </div>
            <a href="{{ route('course.index') }}"
               style="display:inline-flex;align-items:center;gap:.4rem;color:var(--rb);font-weight:600;font-size:.875rem;text-decoration:none;flex-shrink:0;transition:color .2s;white-space:nowrap;"
               onmouseover="this.style.color='var(--rb-d)'" onmouseout="this.style.color='var(--rb)'">
                Lihat Semua Kursus
                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if($courseCategories->isNotEmpty())
        <div class="mb-10 reveal">
            <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                <button class="f-tab active" onclick="setTab(this,'all')">Semua Kursus</button>
                @foreach($courseCategories->take(6) as $cat)
                <button class="f-tab" onclick="setTab(this,'{{ $cat->slug }}')">{{ $cat->name }}</button>
                @endforeach
            </div>
        </div>
        @endif

        @if($featuredCourses->isNotEmpty())
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach($featuredCourses as $i => $course)
            <a href="{{ route('course.show', $course->slug) }}" class="c-card reveal rd{{ min($i+1,4) }}">
                <div class="c-thumb">
                    @if($course->thumbnail)
                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}"
                             onerror="this.classList.add('broken');this.nextElementSibling.style.display='flex';">
                        <div class="c-placeholder">
                            <div class="c-placeholder-icon">
                                <svg style="width:24px;height:24px;color:var(--rb);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p style="font-size:.68rem;color:var(--rb);font-weight:600;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;text-align:center;">{{ $course->title }}</p>
                        </div>
                    @else
                        <div class="c-placeholder" style="display:flex;">
                            <div class="c-placeholder-icon">
                                <svg style="width:24px;height:24px;color:var(--rb);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p style="font-size:.68rem;color:var(--rb);font-weight:600;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;text-align:center;">{{ $course->title }}</p>
                        </div>
                    @endif
                    @if($course->level)
                    <div style="position:absolute;top:10px;left:10px;">
                        <span style="padding:.22rem .55rem;border-radius:6px;font-size:.65rem;font-weight:700;background:rgba(255,255,255,.92);color:var(--rb-d);">{{ ucfirst($course->level) }}</span>
                    </div>
                    @endif
                    @if($course->category)
                    <div style="position:absolute;top:10px;right:10px;">
                        <span style="padding:.22rem .55rem;border-radius:6px;font-size:.65rem;font-weight:700;background:var(--rb);color:#fff;">{{ $course->category->name }}</span>
                    </div>
                    @endif
                </div>
                <div style="padding:1.1rem 1.15rem;display:flex;flex-direction:column;flex:1;">
                    <h3 style="font-weight:700;color:var(--txt);line-height:1.35;font-size:.875rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $course->title }}</h3>
                    @if($course->instructor)
                    <p style="font-size:.72rem;color:var(--sub);margin-top:.45rem;display:flex;align-items:center;gap:.3rem;">
                        <svg style="width:11px;height:11px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>{{ $course->instructor }}
                    </p>
                    @endif
                    <div style="display:flex;gap:2px;margin-top:.55rem;">
                        @for($s=0;$s<5;$s++)
                        <svg style="width:12px;height:12px;color:#f59e0b;" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        @endfor
                    </div>
                    <div style="margin-top:auto;padding-top:.85rem;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:.7rem;color:var(--sub);display:flex;align-items:center;gap:.3rem;">
                            <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                            </svg>{{ number_format($course->enrollments_count) }} siswa
                        </span>
                        @if($course->price > 0)
                            <span style="font-weight:800;color:var(--rb);font-size:.9rem;">Rp {{ number_format($course->price,0,',','.') }}</span>
                        @else
                            <span style="font-weight:800;color:#16a34a;font-size:.9rem;">Gratis</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        <div style="text-align:center;margin-top:3.5rem;" class="reveal">
            <a href="{{ route('course.index') }}"
               style="display:inline-flex;align-items:center;gap:.5rem;background:var(--rb);color:#fff;font-weight:700;padding:.95rem 2.2rem;border-radius:12px;text-decoration:none;font-size:.9375rem;transition:all .22s;box-shadow:0 4px 16px rgba(20,116,188,.3);"
               onmouseover="this.style.background='var(--rb-d)';this.style.transform='translateY(-2px)'"
               onmouseout="this.style.background='var(--rb)';this.style.transform='translateY(0)'">
                Eksplorasi Semua Kursus
                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        @else
        <div style="text-align:center;padding:6rem 0;color:var(--sub);"><p style="font-weight:500;">Belum ada kursus tersedia</p></div>
        @endif
    </div>
</section>


{{-- ═══════════════════════════════════════════════════
     3. WHY CHOOSE US
════════════════════════════════════════════════════ --}}
<section class="py-24 lg:py-32" style="background:var(--surf);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 xl:gap-24 items-center">

            <div class="relative reveal">
                <div style="position:absolute;inset:1rem;border-radius:24px;background:linear-gradient(135deg,#dbeafe,#eff6ff);transform:rotate(2deg) scale(.96);"></div>
                <div style="position:relative;background:#fff;border-radius:24px;padding:2rem;box-shadow:0 24px 50px rgba(20,116,188,.1);border:1px solid #e2e8f0;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div class="stat-card-hero">
                            <div style="font-size:2.2rem;font-weight:800;">{{ number_format($stats['total_students']) }}+</div>
                            <div style="color:rgba(255,255,255,.72);font-size:.8rem;font-weight:500;margin-top:.2rem;">Pelajar Aktif di Seluruh Indonesia</div>
                            <div style="position:absolute;top:1rem;right:1rem;width:34px;height:34px;border-radius:50%;background:rgba(255,255,255,.12);display:flex;align-items:center;justify-content:center;">
                                <svg style="width:17px;height:17px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                                </svg>
                            </div>
                        </div>
                        <div style="background:#fefce8;border:1px solid #fef08a;border-radius:16px;padding:1.15rem;">
                            <div style="width:36px;height:36px;border-radius:10px;background:#f59e0b;display:flex;align-items:center;justify-content:center;margin-bottom:.65rem;">
                                <svg style="width:17px;height:17px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                            </div>
                            <div style="font-size:1.5rem;font-weight:800;color:var(--txt);">{{ number_format($stats['total_courses']) }}+</div>
                            <div style="font-size:.68rem;color:var(--sub);font-weight:500;margin-top:2px;">Kursus Tersedia</div>
                        </div>
                        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:16px;padding:1.15rem;">
                            <div style="width:36px;height:36px;border-radius:10px;background:#22c55e;display:flex;align-items:center;justify-content:center;margin-bottom:.65rem;">
                                <svg style="width:17px;height:17px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div style="font-size:1.3rem;font-weight:800;color:var(--txt);line-height:1.2;">Seumur<br>Hidup</div>
                            <div style="font-size:.68rem;color:var(--sub);font-weight:500;margin-top:4px;">Akses Materi</div>
                        </div>
                        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:16px;padding:1.15rem;">
                            <div style="width:36px;height:36px;border-radius:10px;background:var(--rb);display:flex;align-items:center;justify-content:center;margin-bottom:.65rem;">
                                <svg style="width:17px;height:17px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div style="font-size:1.5rem;font-weight:800;color:var(--txt);">{{ number_format($stats['total_articles']) }}+</div>
                            <div style="font-size:.68rem;color:var(--sub);font-weight:500;margin-top:2px;">Artikel & Tips</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="reveal rd2">
                <span class="sec-label">Kenapa Kami?</span>
                <h2 style="font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;color:var(--txt);line-height:1.25;margin-bottom:2.5rem;margin-top:.25rem;">
                    Pengalaman Belajar<br>yang Berbeda dari Biasanya
                </h2>
                <div style="display:flex;flex-direction:column;gap:1.75rem;">
                    @foreach([
                        ['p'=>'M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z','t'=>'Video Berkualitas HD','d'=>'Setiap materi disajikan dalam video resolusi tinggi agar mudah dipahami dan diingat.'],
                        ['p'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','t'=>'Sertifikat Resmi','d'=>'Dapatkan sertifikat yang diakui dan bisa langsung kamu cantumkan di portofolio.'],
                        ['p'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0','t'=>'Komunitas Belajar Aktif','d'=>'Bergabung dengan ribuan pelajar dan berdiskusi langsung bersama instruktur.'],
                        ['p'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','t'=>'Belajar Kapan Saja','d'=>'Akses semua materi kapan saja dan di mana saja tanpa batasan waktu.'],
                    ] as $b)
                    <div class="benefit-row" style="display:flex;gap:1.1rem;cursor:default;">
                        <div class="benefit-icon">
                            <svg style="width:20px;height:20px;color:var(--rb);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $b['p'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-weight:700;color:var(--txt);font-size:.9375rem;">{{ $b['t'] }}</h3>
                            <p style="font-size:.825rem;color:var(--sub);margin-top:.3rem;line-height:1.65;">{{ $b['d'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════
     4. INSTRUCTORS
════════════════════════════════════════════════════ --}}
<section class="py-24 lg:py-32" style="background:linear-gradient(145deg,#092e5e 0%,#0e5a9b 45%,#1474bc 100%);" id="instruktur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div style="text-align:center;margin-bottom:3.5rem;" class="reveal">
            <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:#93c5fd;display:block;margin-bottom:.6rem;">Instruktur Kami</span>
            <h2 style="font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:800;color:#fff;">Belajar dari Para Ahli</h2>
            <p style="color:rgba(255,255,255,.6);margin-top:.6rem;font-size:.9375rem;max-width:460px;margin-left:auto;margin-right:auto;">
                Instruktur berpengalaman yang siap membimbing perjalanan pembelajaran Anda
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['bg'=>'linear-gradient(135deg,#ff5733,#e84118)','logo'=>'assets/logo-do better class.png','photo'=>'assets/s-ria.png','name'=>'Ria R. Christiana SE, MBA.','tag'=>'Business & Branding','link'=>'#!','c'=>'#c2410c'],
                ['bg'=>'linear-gradient(135deg,#a29bfe,#6c5ce7)','logo'=>'assets/logo-psikologi bisnis.png','photo'=>'assets/s-sukmayanti.png','name'=>'Sukmayanti Ranadireksa, M.Psi.','tag'=>'Psikologi & Komunikasi','link'=>'#!','c'=>'#7c3aed'],
                ['bg'=>'linear-gradient(135deg,#fd79a8,#e84393)','logo'=>'assets/logo-ski.png','photo'=>'assets/s-cahya.png','name'=>'Apt. Cahya Khairani K., M.Farm','tag'=>'Kosmetik & Kecantikan','link'=>'#!','c'=>'#be185d'],
                ['bg'=>'linear-gradient(135deg,#2451aa,#1a3a7d)','logo'=>'assets/logo-amaizing.png','photo'=>'assets/s-wendra.png','name'=>'Wendra Wilendra M.MT.','tag'=>'Teknologi & AI','link'=>'#!','c'=>'#1d4ed8'],
                ['bg'=>'linear-gradient(135deg,#ee5b8d,#d63864)','logo'=>'assets/logo-sobat-anak.png','photo'=>'assets/s-fricil-1.png','name'=>'dr. Frecillia Regina, Sp.A','tag'=>'Kesehatan Anak','link'=>'https://rayacademy.id/sobat-anak/','c'=>'#be185d'],
            ] as $idx => $ins)
            <div class="ins-card reveal rd{{ min($idx+1,4) }}" style="background:{{ $ins['bg'] }};">
                <div style="position:relative;padding:1.6rem 1.75rem;z-index:1;">
                    <img src="{{ asset($ins['logo']) }}" alt="{{ $ins['name'] }}" style="height:48px;margin-bottom:1rem;object-fit:contain;">
                    <h3 style="color:#fff;font-weight:700;font-size:1rem;line-height:1.3;">{{ $ins['name'] }}</h3>
                    <p style="color:rgba(255,255,255,.6);font-size:.775rem;margin-top:.2rem;">{{ $ins['tag'] }}</p>
                    <a href="{{ $ins['link'] }}"
                       style="display:inline-block;margin-top:.9rem;padding:.5rem 1.25rem;background:#fff;border-radius:999px;font-weight:700;font-size:.775rem;text-decoration:none;color:{{ $ins['c'] }};transition:opacity .2s;"
                       onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
                        Mulai Belajar →
                    </a>
                </div>
                <div style="position:relative;height:230px;z-index:1;">
                    <img src="{{ asset($ins['photo']) }}" alt="{{ $ins['name'] }}"
                         style="position:absolute;bottom:0;right:0;height:100%;object-fit:contain;filter:drop-shadow(0 6px 20px rgba(0,0,0,.22));">
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════════
     5. ARTICLES
════════════════════════════════════════════════════ --}}
@if($latestArticles->isNotEmpty())
<section class="py-24 lg:py-32" style="background:#fff;" id="artikel">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-5 mb-12 reveal">
            <div>
                <span class="sec-label">Tips & Wawasan</span>
                <h2 style="font-size:clamp(1.6rem,3vw,2.4rem);font-weight:800;color:var(--txt);line-height:1.2;margin-top:.25rem;">
                    Artikel & Tips Terbaru
                </h2>
            </div>
            <a href="{{ route('article.index') }}"
               style="display:inline-flex;align-items:center;gap:.4rem;color:var(--rb);font-weight:600;font-size:.875rem;text-decoration:none;flex-shrink:0;transition:color .2s;white-space:nowrap;"
               onmouseover="this.style.color='var(--rb-d)'" onmouseout="this.style.color='var(--rb)'">
                Lihat Semua Artikel
                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="grid lg:grid-cols-5 gap-6">
            @php $featured = $latestArticles->first(); @endphp
            <a href="{{ route('article.show', $featured->slug) }}"
               class="reveal lg:col-span-3 group"
               style="position:relative;background:#111;border-radius:22px;overflow:hidden;min-height:370px;display:flex;flex-direction:column;justify-content:flex-end;text-decoration:none;">
                @if($featured->thumbnail ?? $featured->cover_image ?? null)
                    <img src="{{ $featured->thumbnail ?? $featured->cover_image }}"
                         alt="{{ $featured->title }}"
                         style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.75;transition:transform .5s,opacity .3s;"
                         onmouseover="this.style.transform='scale(1.04)';this.style.opacity='.62'"
                         onmouseout="this.style.transform='scale(1)';this.style.opacity='.75'">
                @else
                    <div style="position:absolute;inset:0;background:linear-gradient(135deg,#092e5e,#1474bc);"></div>
                @endif
                <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.88),rgba(0,0,0,.2) 55%,transparent 100%);"></div>
                <div style="position:relative;padding:1.75rem 2rem;color:#fff;">
                    @if($featured->categories->first())
                        <span style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:#fde047;display:block;margin-bottom:.5rem;">{{ $featured->categories->first()->name }}</span>
                    @endif
                    <h3 style="font-size:1.3rem;font-weight:800;line-height:1.3;max-width:500px;">{{ $featured->title }}</h3>
                    @if($featured->excerpt ?? $featured->description ?? null)
                        <p style="font-size:.825rem;color:rgba(255,255,255,.62);margin-top:.5rem;line-height:1.65;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;max-width:460px;">{{ $featured->excerpt ?? $featured->description }}</p>
                    @endif
                    <p style="font-size:.7rem;color:rgba(255,255,255,.42);margin-top:.7rem;">
                        @if($featured->published_at) {{ $featured->published_at->isoFormat('D MMM Y') }} @endif
                        @if($featured->views_count ?? null) · {{ number_format($featured->views_count) }} views @endif
                    </p>
                </div>
            </a>

            <div class="lg:col-span-2 flex flex-col gap-4">
                @foreach($latestArticles->skip(1)->take(3) as $article)
                <a href="{{ route('article.show', $article->slug) }}" class="art-small reveal">
                    <div style="flex-shrink:0;width:108px;height:88px;overflow:hidden;background:linear-gradient(135deg,#dbeafe,#eff6ff);">
                        @if($article->thumbnail ?? $article->cover_image ?? null)
                            <img src="{{ $article->thumbnail ?? $article->cover_image }}" alt="{{ $article->title }}" style="width:100%;height:100%;object-fit:cover;transition:transform .3s;" onmouseover="this.style.transform='scale(1.07)'" onmouseout="this.style.transform='scale(1)'">
                        @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                <svg style="width:22px;height:22px;color:var(--rb);opacity:.35;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div style="flex:1;padding:.85rem 1rem;min-width:0;">
                        @if($article->categories->first())
                            <span style="font-size:.63rem;font-weight:700;color:var(--rb);text-transform:uppercase;letter-spacing:.08em;">{{ $article->categories->first()->name }}</span>
                        @endif
                        <h3 style="font-size:.8rem;font-weight:700;color:var(--txt);margin-top:.2rem;line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $article->title }}</h3>
                        @if($article->published_at)
                            <p style="font-size:.68rem;color:var(--sub);margin-top:.35rem;">{{ $article->published_at->isoFormat('D MMM Y') }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════════
     6. CTA
════════════════════════════════════════════════════ --}}
<section class="cta-bg py-24 lg:py-28" style="position:relative;overflow:hidden;">
    <div class="cta-dots absolute inset-0 pointer-events-none"></div>
    <div style="position:absolute;top:-80px;right:-80px;width:360px;height:360px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,.08),transparent 65%);pointer-events:none;"></div>
    <div style="position:relative;max-width:680px;margin:0 auto;padding:0 1.5rem;text-align:center;color:#fff;" class="reveal">
        <span style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:#93c5fd;display:block;margin-bottom:1rem;">Mulai Sekarang</span>
        <h2 style="font-size:clamp(2rem,4vw,3rem);font-weight:800;line-height:1.2;margin-bottom:1.25rem;">
            Siap Memulai Perjalanan<br>Belajarmu?
        </h2>
        <p style="font-size:1.0625rem;color:rgba(255,255,255,.68);line-height:1.75;margin-bottom:3rem;max-width:480px;margin-left:auto;margin-right:auto;">
            Bergabunglah bersama ribuan pelajar yang sudah meningkatkan skill mereka bersama instruktur terbaik kami.
        </p>
        <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:center;">
            <a href="{{ route('course.index') }}" class="btn-primary">
                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Lihat Semua Kursus
            </a>
            @guest
            <a href="{{ route('register') }}" class="btn-ghost">Daftar Sekarang — Gratis</a>
            @endguest
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    /* Hero: reveal immediately, no waiting for scroll */
    const hl = document.getElementById('hero-left');
    const hr = document.getElementById('hero-right');
    if (hl) setTimeout(() => { hl.style.opacity='1'; hl.style.transform='translateY(0)'; }, 80);
    if (hr) setTimeout(() => { hr.style.opacity='1'; hr.style.transform='translateY(0)'; }, 240);

    /* Splide slider */
    const slider = document.querySelector('#hero-article-slider');
    if (slider && typeof Splide !== 'undefined') {
        new Splide(slider, {
            type:'loop', perPage:1, autoplay:true,
            interval:4800, pauseOnHover:true,
            arrows:false, pagination:true, gap:0, speed:700,
        }).mount();
    }

    /* Filter tabs */
    window.setTab = function(el) {
        document.querySelectorAll('.f-tab').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
    };

    /* Scroll reveal */
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -50px 0px' });
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
});
</script>
@endpush