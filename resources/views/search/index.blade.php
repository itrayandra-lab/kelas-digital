@extends('layouts.app')
@section('title', $keyword ? 'Hasil: "' . $keyword . '" — Ray Academy' : 'Cari Kursus — Ray Academy')

@push('styles')
<style>
/* ══════════════════════════════════════════════════
   SEARCH PAGE — Ray Academy (Coursera-inspired)
   ══════════════════════════════════════════════════ */
.wrap { max-width:1280px; margin:0 auto; padding:0 1.5rem; }

/* ── HERO ── */
.sp-hero {
    background:#F5F5F4;
    border-bottom:1px solid #E2E8F0;
    padding:2rem 0 0;
    position:relative;
    overflow:hidden;
}
.sp-hero::before{
    content:'';position:absolute;inset:0;
    background-image:linear-gradient(rgba(0,86,210,.03)1px,transparent 1px),
                     linear-gradient(90deg,rgba(0,86,210,.03)1px,transparent 1px);
    background-size:48px 48px;pointer-events:none;
}
.sp-hero-inner{
    position:relative;z-index:1;
    display:grid;grid-template-columns:1fr auto;gap:2rem;align-items:end;
    max-width:1280px;margin:0 auto;padding:0 1.5rem;
}
.sp-hero-left { padding-bottom:1.5rem; }
.sp-hero-left h1{
    font-family:'Sora',sans-serif;
    font-size:clamp(1.4rem,3vw,2rem);
    font-weight:700;color:#0F1114;
    margin-bottom:.25rem;letter-spacing:-.02em;
}
.sp-hero-left p { font-size:.85rem;color:#6B7280; }
.sp-hero-img {
    width:180px;height:120px;object-fit:contain;
    object-position:bottom;display:block;
    opacity:.9;
}

/* search bar */
.sp-search-bar{
    background:#fff;border-bottom:1px solid #E2E8F0;
    padding:.875rem 0;position:sticky;top:0;z-index:50;
    box-shadow:0 1px 8px rgba(0,0,0,.06);
}
.sp-search-form{
    display:flex;align-items:center;gap:.75rem;
}
.sp-search-wrap{
    flex:1;display:flex;align-items:center;
    background:#F8FAFF;border:1.5px solid #CBD5E1;
    border-radius:10px;overflow:hidden;
    transition:border-color .2s,box-shadow .2s;
}
.sp-search-wrap:focus-within{border-color:#0056D2;box-shadow:0 0 0 3px rgba(0,86,210,.1);}
.sp-search-wrap svg{width:18px;height:18px;color:#94A3B8;margin:0 .75rem;flex-shrink:0;}
.sp-search-wrap input{
    flex:1;border:none;background:transparent;outline:none;
    padding:.75rem .5rem .75rem 0;
    font-family:'DM Sans',sans-serif;font-size:.9rem;color:#0F1114;
}
.sp-search-wrap input::placeholder{color:#94A3B8;}
.sp-search-btn{
    background:#0056D2;color:#fff;border:none;
    padding:.75rem 1.5rem;border-radius:8px;
    font-weight:600;font-size:.875rem;cursor:pointer;
    display:flex;align-items:center;gap:.35rem;
    transition:background .2s;white-space:nowrap;flex-shrink:0;
}
.sp-search-btn:hover{background:#0048B0;}

/* ── LAYOUT ── */
.sp-body { background:#fff;padding:1.5rem 0 4rem; }
.sp-layout {
    display:grid;
    grid-template-columns:240px 1fr;
    gap:2rem;
    align-items:start;
}

/* ── SIDEBAR ── */
.sp-sidebar { position:sticky;top:72px; }
.sp-sidebar-box{
    border:1.5px solid #E2E8F0;border-radius:12px;
    overflow:hidden;background:#fff;
    margin-bottom:1rem;
}
.sp-sidebar-head{
    padding:.875rem 1rem;border-bottom:1px solid #F1F5F9;
    font-family:'Sora',sans-serif;font-size:.82rem;
    font-weight:700;color:#0F1114;
    display:flex;justify-content:space-between;align-items:center;
}
.sp-sidebar-head a{font-size:.72rem;font-weight:600;color:#0056D2;text-decoration:none;}
.sp-sidebar-head a:hover{text-decoration:underline;}
.sp-sidebar-body{padding:.75rem 1rem;}

/* filter chip buttons */
.sp-filter-chips{display:flex;flex-direction:column;gap:.25rem;}
.sp-chip{
    display:flex;align-items:center;gap:.5rem;
    padding:.45rem .6rem;border-radius:8px;
    font-size:.8rem;font-weight:500;color:#374151;
    cursor:pointer;transition:background .15s;border:none;background:transparent;
    text-align:left;width:100%;
}
.sp-chip:hover{background:#F1F5FF;color:#0056D2;}
.sp-chip.active{background:#EEF4FF;color:#0056D2;font-weight:600;}
.sp-chip .sp-chip-count{margin-left:auto;font-size:.7rem;color:#9CA3AF;}
.sp-chip.active .sp-chip-count{color:#0056D2;opacity:.7;}

/* date inputs */
.sp-date-group{display:flex;flex-direction:column;gap:.5rem;margin-bottom:.6rem;}
.sp-date-label{font-size:.72rem;font-weight:600;color:#6B7280;}
.sp-date-input{
    padding:.55rem .75rem;border:1.5px solid #E2E8F0;
    border-radius:8px;font-size:.8rem;color:#0F1114;
    outline:none;transition:border-color .2s;
}
.sp-date-input:focus{border-color:#0056D2;}
.sp-filter-apply{
    width:100%;padding:.65rem;background:#0056D2;color:#fff;
    border:none;border-radius:8px;font-weight:600;font-size:.8rem;
    cursor:pointer;transition:background .2s;margin-top:.5rem;
}
.sp-filter-apply:hover{background:#0048B0;}
.sp-filter-reset{
    display:block;text-align:center;
    width:100%;padding:.55rem;background:#F8FAFF;color:#6B7280;
    border:1.5px solid #E2E8F0;border-radius:8px;font-weight:600;font-size:.78rem;
    cursor:pointer;transition:all .2s;margin-top:.4rem;text-decoration:none;
}
.sp-filter-reset:hover{border-color:#0056D2;color:#0056D2;background:#EEF4FF;}

/* tag pills */
.sp-tags{display:flex;flex-wrap:wrap;gap:.4rem;}
.sp-tag-cb{display:none;}
.sp-tag-lbl{
    display:inline-flex;align-items:center;gap:.3rem;
    padding:.28rem .75rem;background:#F8FAFF;
    border:1.5px solid #E2E8F0;border-radius:999px;
    font-size:.72rem;font-weight:500;color:#374151;
    cursor:pointer;transition:all .15s;
}
.sp-tag-lbl:hover{border-color:#0056D2;background:#EEF4FF;color:#0056D2;}
.sp-tag-cb:checked + .sp-tag-lbl{background:#0056D2;border-color:#0056D2;color:#fff;}

/* ── MAIN RESULTS ── */
.sp-main {}
.sp-results-header{
    display:flex;align-items:center;justify-content:space-between;
    margin-bottom:1rem;padding-bottom:.75rem;
    border-bottom:1px solid #F1F5F9;
}
.sp-results-title{font-family:'Sora',sans-serif;font-size:.95rem;font-weight:700;color:#0F1114;}
.sp-results-count{font-size:.8rem;color:#6B7280;}

/* active filter tags */
.sp-active-tags{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1rem;}
.sp-active-tag{
    display:inline-flex;align-items:center;gap:.4rem;
    padding:.3rem .75rem;background:#EEF4FF;color:#0056D2;
    font-size:.73rem;font-weight:600;border-radius:999px;
    border:1px solid rgba(0,86,210,.2);
}
.sp-active-tag a{color:#0056D2;text-decoration:none;font-size:.7rem;}
.sp-active-tag a:hover{color:#c00;}

/* section label */
.sp-section-label{
    display:flex;align-items:center;gap:.5rem;
    font-family:'Sora',sans-serif;font-size:.82rem;font-weight:700;
    color:#0F1114;margin-bottom:.75rem;margin-top:1.25rem;
    padding-bottom:.5rem;border-bottom:2px solid #EEF4FF;
}
.sp-section-label:first-child{margin-top:0;}
.sp-section-label svg{width:15px;height:15px;color:#0056D2;}

/* ── COURSE CARD (Coursera style) ── */
.sp-course-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem;}
.sp-crs-card{
    background:#fff;border:1px solid #E2E8F0;border-radius:12px;
    overflow:hidden;text-decoration:none;color:inherit;
    transition:box-shadow .2s,transform .2s;
    display:flex;flex-direction:column;
}
.sp-crs-card:hover{box-shadow:0 8px 24px rgba(0,0,0,.1);transform:translateY(-3px);}
.sp-crs-thumb{
    aspect-ratio:16/9;overflow:hidden;background:#DBEAFE;
    position:relative;
}
.sp-crs-thumb img{width:100%;height:100%;object-fit:cover;transition:transform .3s;}
.sp-crs-card:hover .sp-crs-thumb img{transform:scale(1.04);}
.sp-crs-thumb-ph{
    width:100%;height:100%;display:flex;align-items:center;justify-content:center;
    background:linear-gradient(135deg,#EEF4FF,#DBEAFE);
}
.sp-crs-thumb-ph svg{width:28px;height:28px;color:#0056D2;opacity:.35;}
.sp-crs-badge{
    position:absolute;top:.5rem;right:.5rem;
    font-size:.6rem;font-weight:700;padding:.2rem .55rem;border-radius:999px;
    border:1px solid;
}
.sp-crs-badge.free{background:#fff;color:#16a34a;border-color:#16a34a;}
.sp-crs-badge.paid{background:#fff;color:#0056D2;border-color:#0056D2;}
.sp-crs-body{padding:.75rem;flex:1;display:flex;flex-direction:column;}
.sp-crs-cat{font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#0056D2;margin-bottom:.2rem;}
.sp-crs-title{
    font-family:'Sora',sans-serif;font-size:.76rem;font-weight:600;color:#0F1114;
    line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
    flex:1;
}
.sp-crs-instructor{font-size:.65rem;color:#6B7280;margin-top:.3rem;}
.sp-crs-footer{
    display:flex;align-items:center;justify-content:space-between;
    margin-top:.5rem;padding-top:.5rem;border-top:1px solid #F1F5F9;
}
.sp-crs-price{font-size:.72rem;font-weight:700;color:#0056D2;}
.sp-crs-price.free{color:#16a34a;}
.sp-crs-rating{font-size:.65rem;color:#6B7280;display:flex;align-items:center;gap:.2rem;}
.sp-crs-rating svg{color:#FBBF24;fill:#FBBF24;width:11px;height:11px;}

/* ── ARTICLE CARD ── */
.sp-art-list{display:flex;flex-direction:column;gap:.75rem;margin-bottom:1.5rem;}
.sp-art-card{
    background:#fff;border:1px solid #E2E8F0;border-radius:10px;
    padding:.875rem;display:flex;gap:.875rem;align-items:flex-start;
    text-decoration:none;color:inherit;
    transition:box-shadow .15s,border-color .15s;
}
.sp-art-card:hover{box-shadow:0 4px 16px rgba(0,86,210,.08);border-color:#BFDBFE;}
.sp-art-thumb{
    width:80px;height:60px;border-radius:8px;overflow:hidden;
    background:linear-gradient(135deg,#EEF4FF,#DBEAFE);flex-shrink:0;
}
.sp-art-thumb img{width:100%;height:100%;object-fit:cover;}
.sp-art-thumb-ph{width:100%;height:100%;display:flex;align-items:center;justify-content:center;}
.sp-art-thumb-ph svg{width:22px;height:22px;color:#0056D2;opacity:.35;}
.sp-art-body{flex:1;min-width:0;}
.sp-art-meta{display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;flex-wrap:wrap;}
.sp-art-cat{font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#0056D2;}
.sp-art-date{font-size:.63rem;color:#9CA3AF;}
.sp-art-title{
    font-family:'Sora',sans-serif;font-size:.82rem;font-weight:600;color:#0F1114;
    line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
}
.sp-art-excerpt{font-size:.73rem;color:#6B7280;line-height:1.55;margin-top:.2rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.sp-art-read{font-size:.68rem;font-weight:600;color:#0056D2;margin-top:.35rem;}

/* ── CTA banner (Coursera join-for-free style) ── */
.sp-cta-banner{
    background:linear-gradient(135deg,#EEF4FF 0%,#DBEAFE 100%);
    border:1px solid #BFDBFE;border-radius:16px;
    padding:1.5rem;display:flex;align-items:center;gap:1.5rem;
    margin-top:1.5rem;
}
.sp-cta-img{width:100px;height:80px;object-fit:contain;flex-shrink:0;}
.sp-cta-text h3{font-family:'Sora',sans-serif;font-size:.95rem;font-weight:700;color:#0F1114;margin-bottom:.3rem;}
.sp-cta-text p{font-size:.78rem;color:#374151;line-height:1.5;margin-bottom:.75rem;}
.sp-cta-text a{
    display:inline-flex;align-items:center;gap:.35rem;
    background:#0056D2;color:#fff;font-weight:600;font-size:.8rem;
    padding:.55rem 1.25rem;border-radius:8px;text-decoration:none;
    transition:background .2s;
}
.sp-cta-text a:hover{background:#0048B0;}

/* ── PAGINATION ── */
.sp-pager{display:flex;align-items:center;justify-content:center;gap:.4rem;margin-top:2rem;}
.sp-pager a,.sp-pager span{
    display:inline-flex;align-items:center;justify-content:center;
    min-width:36px;height:36px;padding:0 .5rem;border-radius:8px;
    font-size:.8rem;font-weight:600;text-decoration:none;color:#374151;
    border:1.5px solid #E2E8F0;transition:all .15s;
}
.sp-pager a:hover{border-color:#0056D2;color:#0056D2;background:#EEF4FF;}
.sp-pager span.current{background:#0056D2;color:#fff;border-color:#0056D2;}
.sp-pager span.dots{border:none;color:#9CA3AF;}

/* ── EMPTY STATE ── */
.sp-empty{
    text-align:center;padding:4rem 2rem;
    background:#F8FAFF;border:1.5px dashed #E2E8F0;
    border-radius:16px;
}
.sp-empty svg{width:56px;height:56px;color:#CBD5E1;margin:0 auto 1rem;}
.sp-empty h3{font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:700;color:#0F1114;margin-bottom:.4rem;}
.sp-empty p{font-size:.85rem;color:#6B7280;}
.sp-empty a{color:#0056D2;font-weight:600;text-decoration:none;}
.sp-empty a:hover{text-decoration:underline;}

/* ── NO KEYWORD STATE ── */
.sp-landing{padding:3rem 0;}
.sp-landing-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem;}
.sp-landing-card{
    border:1.5px solid #E2E8F0;border-radius:14px;padding:1.25rem;
    background:#fff;display:flex;align-items:center;gap:.875rem;
    cursor:pointer;text-decoration:none;color:inherit;
    transition:border-color .2s,box-shadow .2s;
}
.sp-landing-card:hover{border-color:#0056D2;box-shadow:0 4px 16px rgba(0,86,210,.1);}
.sp-landing-card-icon{
    width:44px;height:44px;border-radius:10px;background:#EEF4FF;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.sp-landing-card-icon svg{width:22px;height:22px;color:#0056D2;}
.sp-landing-card h3{font-family:'Sora',sans-serif;font-size:.85rem;font-weight:700;color:#0F1114;margin-bottom:.15rem;}
.sp-landing-card p{font-size:.73rem;color:#6B7280;line-height:1.4;}

/* related searches */
.sp-related h3{font-family:'Sora',sans-serif;font-size:.85rem;font-weight:700;color:#6B7280;margin-bottom:.75rem;}
.sp-related-list{display:flex;flex-wrap:wrap;gap:.5rem;}
.sp-related-list a{
    font-size:.82rem;font-weight:500;color:#0056D2;text-decoration:none;
    padding:.3rem .75rem;background:#EEF4FF;border-radius:999px;
    transition:background .15s;
}
.sp-related-list a em{font-style:normal;color:#1E40AF;font-weight:700;}
.sp-related-list a:hover{background:#DBEAFE;}

/* ── RESPONSIVE ── */
@media(max-width:1024px){ .sp-course-grid{grid-template-columns:repeat(3,1fr);} }
@media(max-width:900px){
    .sp-layout{grid-template-columns:1fr;}
    .sp-sidebar{position:static;}
    .sp-course-grid{grid-template-columns:repeat(2,1fr);}
    .sp-landing-grid{grid-template-columns:1fr 1fr;}
}
@media(max-width:640px){
    .sp-course-grid{grid-template-columns:1fr;}
    .sp-landing-grid{grid-template-columns:1fr;}
    .sp-hero-img{display:none;}
    .sp-cta-banner{flex-direction:column;text-align:center;}
    .sp-cta-img{display:none;}
}
</style>
@endpush

@section('content')

{{-- ══ HERO ══ --}}
<section class="sp-hero">
    <div class="sp-hero-inner">
        <div class="sp-hero-left">
            @if($keyword)
                <h1>Hasil untuk <span style="color:#0056D2;">"{{ $keyword }}"</span></h1>
                <p>
                    @php $total = ($courses->count() ?? 0) + ($articles->total() ?? 0); @endphp
                    Ditemukan {{ number_format($total) }} hasil pencarian
                </p>
            @else
                <h1>Jelajahi Kursus & Artikel</h1>
                <p>Temukan kursus, artikel, dan materi pembelajaran terbaik untuk Anda</p>
            @endif
        </div>
        <img src="{{ asset('img/search/hero.png') }}"
             alt="Search" class="sp-hero-img"
             onerror="this.style.display='none'">
    </div>
</section>

{{-- ══ STICKY SEARCH BAR ══ --}}
<div class="sp-search-bar">
    <div class="wrap">
        <form action="{{ route('search') }}" method="GET" class="sp-search-form">
            <div class="sp-search-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                <input type="search" name="q" value="{{ $keyword }}"
                       placeholder="Cari kursus, artikel, instruktur..."
                       autocomplete="off">
            </div>
            @if($selectedCategoryId) <input type="hidden" name="category_id" value="{{ $selectedCategoryId }}"> @endif
            @if($selectedDateFrom)   <input type="hidden" name="date_from"    value="{{ $selectedDateFrom }}"> @endif
            @if($selectedDateTo)     <input type="hidden" name="date_to"      value="{{ $selectedDateTo }}"> @endif
            <button type="submit" class="sp-search-btn">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="15" height="15"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                Cari
            </button>
        </form>
    </div>
</div>

{{-- ══ BODY ══ --}}
<section class="sp-body">
    <div class="wrap">

        @if($keyword === '')
        {{-- ══════ NO KEYWORD: LANDING DENGAN DATA POPULER ══════ --}}
        <div class="sp-landing">

            {{-- 3 shortcut cards --}}
            <div class="sp-landing-grid" style="margin-top:1.5rem;">
                <a href="{{ route('course.index') }}" class="sp-landing-card">
                    <div class="sp-landing-card-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div><h3>Jelajahi Kursus</h3><p>Akses ratusan kursus dari instruktur terbaik</p></div>
                </a>
                <a href="{{ route('article.index') }}" class="sp-landing-card">
                    <div class="sp-landing-card-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div><h3>Baca Artikel</h3><p>Tips, wawasan, dan panduan dari para ahli</p></div>
                </a>
                <a href="{{ route('course.index') }}" class="sp-landing-card">
                    <div class="sp-landing-card-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                    </div>
                    <div><h3>Kursus Gratis</h3><p>Mulai belajar tanpa biaya sekarang</p></div>
                </a>
            </div>

            {{-- Pencarian populer --}}
            <div class="sp-related" style="margin-top:1.75rem;">
                <h3>Pencarian populer</h3>
                <div class="sp-related-list">
                    @foreach(['Kosmetik','Bisnis Digital','Skincare','Branding','AI & Teknologi','Kesehatan Anak','Psikologi','Marketing'] as $s)
                    <a href="{{ route('search', ['q' => $s]) }}">{{ $s }}</a>
                    @endforeach
                </div>
            </div>

            {{-- ── KURSUS TERBARU ── --}}
            @if(isset($popularCourses) && $popularCourses->count() > 0)
            <div style="margin-top:2.5rem;">
                <div class="sp-results-header">
                    <div class="sp-section-label" style="margin:0;border:none;padding:0;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        Kursus Terbaru
                    </div>
                    <a href="{{ route('course.index') }}" style="font-size:.78rem;color:#0056D2;text-decoration:none;font-weight:600;">Lihat semua →</a>
                </div>
                <div class="sp-course-grid" style="margin-top:.75rem;">
                    @foreach($popularCourses as $course)
                    <a href="{{ route('course.show', $course->slug) }}" class="sp-crs-card">
                        <div class="sp-crs-thumb">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" loading="lazy"
                                     onerror="this.parentElement.innerHTML='<div class=sp-crs-thumb-ph><svg fill=none stroke=currentColor viewBox=\'0 0 24 24\'><path stroke-linecap=round stroke-linejoin=round stroke-width=1.5 d=\'M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z\'/></svg></div>'">
                            @else
                                <div class="sp-crs-thumb-ph">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                            <span class="sp-crs-badge {{ ($course->price ?? 0) == 0 ? 'free' : 'paid' }}">
                                {{ ($course->price ?? 0) == 0 ? 'Gratis' : 'Premium' }}
                            </span>
                        </div>
                        <div class="sp-crs-body">
                            @if($course->category)<div class="sp-crs-cat">{{ $course->category->name }}</div>@endif
                            <div class="sp-crs-title">{{ $course->title }}</div>
                            <div class="sp-crs-instructor">{{ is_string($course->instructor ?? null) ? $course->instructor : ($course->instructor->name ?? 'Ray Academy') }}</div>
                            <div class="sp-crs-footer">
                                <span class="sp-crs-price {{ ($course->price ?? 0) == 0 ? 'free' : '' }}">
                                    {{ ($course->price ?? 0) == 0 ? 'Gratis' : 'Rp '.number_format($course->price,0,',','.') }}
                                </span>
                                <span class="sp-crs-rating">
                                    <svg viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    4.8
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── KATEGORI ── --}}
            @if(isset($popularCategories) && $popularCategories->count() > 0)
            <div style="margin-top:2.5rem;">
                <div class="sp-results-header">
                    <div class="sp-section-label" style="margin:0;border:none;padding:0;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Jelajahi Kategori
                    </div>
                    <a href="{{ route('article.index') }}" style="font-size:.78rem;color:#0056D2;text-decoration:none;font-weight:600;">Lihat semua →</a>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:.6rem;margin-top:.75rem;">
                    @foreach($popularCategories as $cat)
                    <a href="{{ route('search', ['q' => $cat->name]) }}"
                       style="display:inline-flex;align-items:center;gap:.4rem;
                              padding:.5rem 1.1rem;background:#EEF4FF;
                              border:1.5px solid rgba(0,86,210,.15);border-radius:999px;
                              font-size:.8rem;font-weight:600;color:#0056D2;
                              text-decoration:none;transition:all .15s;"
                       onmouseover="this.style.background='#0056D2';this.style.color='#fff'"
                       onmouseout="this.style.background='#EEF4FF';this.style.color='#0056D2'">
                        {{ $cat->name }}
                        <span style="font-size:.68rem;opacity:.65;">{{ $cat->articles_count }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── ARTIKEL TERBARU ── --}}
            @if(isset($popularArticles) && $popularArticles->count() > 0)
            <div style="margin-top:2.5rem;">
                <div class="sp-results-header">
                    <div class="sp-section-label" style="margin:0;border:none;padding:0;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Artikel Terbaru
                    </div>
                    <a href="{{ route('article.index') }}" style="font-size:.78rem;color:#0056D2;text-decoration:none;font-weight:600;">Lihat semua →</a>
                </div>
                <div class="sp-art-list" style="margin-top:.75rem;">
                    @foreach($popularArticles as $article)
                    <a href="{{ route('article.show', $article->slug) }}" class="sp-art-card">
                        <div class="sp-art-thumb">
                            @if($article->thumbnail ?? $article->cover_image ?? null)
                                <img src="{{ asset('storage/' . ($article->thumbnail ?? $article->cover_image)) }}"
                                     alt="{{ $article->title }}" loading="lazy"
                                     onerror="this.parentElement.innerHTML='<div class=sp-art-thumb-ph><svg fill=none stroke=currentColor viewBox=\'0 0 24 24\'><path stroke-linecap=round stroke-linejoin=round stroke-width=1.5 d=\'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\'/></svg></div>'">
                            @else
                                <div class="sp-art-thumb-ph">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="sp-art-body">
                            <div class="sp-art-meta">
                                @if($article->categories->isNotEmpty())
                                    <span class="sp-art-cat">{{ $article->categories->first()->name }}</span>
                                @endif
                                @if($article->published_at)
                                    <span class="sp-art-date">{{ $article->published_at->isoFormat('D MMM YYYY') }}</span>
                                @endif
                            </div>
                            <div class="sp-art-title">{{ $article->title }}</div>
                            @if($article->excerpt)
                                <div class="sp-art-excerpt">{{ $article->excerpt }}</div>
                            @elseif($article->content ?? null)
                                <div class="sp-art-excerpt">{{ Str::limit(strip_tags($article->content), 120) }}</div>
                            @endif
                            <div class="sp-art-read">Baca Selengkapnya →</div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        @else
        {{-- ══════ HAS KEYWORD: LAYOUT SIDEBAR + MAIN ══════ --}}
        <div class="sp-layout" style="margin-top:1.5rem;">

            {{-- ── SIDEBAR ── --}}
            <aside class="sp-sidebar">
                <form action="{{ route('search') }}" method="GET" id="sp-filter-form">
                    <input type="hidden" name="q" value="{{ $keyword }}">

                    {{-- Categories --}}
                    @if(isset($categories) && $categories->count() > 0)
                    <div class="sp-sidebar-box">
                        <div class="sp-sidebar-head">
                            Filter Kategori
                            @if($selectedCategoryId)
                                <a href="{{ route('search', ['q'=>$keyword]) }}">Hapus</a>
                            @endif
                        </div>
                        <div class="sp-sidebar-body">
                            <div class="sp-filter-chips">
                                <button type="submit" name="category_id" value=""
                                    class="sp-chip {{ !$selectedCategoryId ? 'active' : '' }}">
                                    Semua Kategori
                                    <span class="sp-chip-count"></span>
                                </button>
                                @foreach($categories as $cat)
                                <button type="submit" name="category_id" value="{{ $cat->id }}"
                                    class="sp-chip {{ $selectedCategoryId == $cat->id ? 'active' : '' }}">
                                    {{ $cat->name }}
                                    <span class="sp-chip-count">{{ $cat->articles_count ?? '' }}</span>
                                </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Tags --}}
                    @if(isset($tags) && $tags->count() > 0)
                    <div class="sp-sidebar-box">
                        <div class="sp-sidebar-head">Tag</div>
                        <div class="sp-sidebar-body">
                            <div class="sp-tags">
                                @foreach($tags as $tag)
                                <input type="checkbox" name="tag_id[]" value="{{ $tag->id }}"
                                       id="tag-{{ $tag->id }}" class="sp-tag-cb"
                                       {{ in_array($tag->id, (array)($selectedTagIds ?? [])) ? 'checked' : '' }}>
                                <label for="tag-{{ $tag->id }}" class="sp-tag-lbl">
                                    {{ $tag->name }}
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Date Range --}}
                    <div class="sp-sidebar-box">
                        <div class="sp-sidebar-head">Rentang Tanggal</div>
                        <div class="sp-sidebar-body">
                            <div class="sp-date-group">
                                <label class="sp-date-label">Dari</label>
                                <input type="date" name="date_from" class="sp-date-input"
                                       value="{{ $selectedDateFrom ?? '' }}">
                            </div>
                            <div class="sp-date-group">
                                <label class="sp-date-label">Sampai</label>
                                <input type="date" name="date_to" class="sp-date-input"
                                       value="{{ $selectedDateTo ?? '' }}">
                            </div>
                            <button type="submit" class="sp-filter-apply">Terapkan Filter</button>
                            @if($selectedDateFrom || $selectedDateTo || !empty($selectedTagIds ?? []))
                                <a href="{{ route('search', ['q'=>$keyword, 'category_id'=>$selectedCategoryId]) }}"
                                   class="sp-filter-reset">Hapus Filter Tanggal</a>
                            @endif
                        </div>
                    </div>

                </form>

                {{-- CTA banner --}}
                <div class="sp-cta-banner" style="margin-top:.5rem;">
                    <img src="{{ asset('img/search/content-3.png') }}" alt="" class="sp-cta-img"
                         onerror="this.style.display='none'">
                    <div class="sp-cta-text">
                        <h3>Belajar tanpa batas</h3>
                        <p>Akses semua kursus premium dengan berlangganan Ray Academy.</p>
                        <a href="{{ route('course.index') }}">
                            Mulai Sekarang
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </aside>

            {{-- ── MAIN ── --}}
            <main class="sp-main">

                {{-- Active filter tags --}}
                @if(!empty($activeFilters ?? []))
                <div class="sp-active-tags">
                    @if(isset($activeFilters['category']))
                        <div class="sp-active-tag">
                            Kategori: {{ $activeFilters['category']->name }}
                            <a href="{{ route('search', ['q'=>$keyword]) }}">✕</a>
                        </div>
                    @endif
                    @if(isset($activeFilters['tags']))
                        @foreach($activeFilters['tags'] as $tag)
                        <div class="sp-active-tag">{{ $tag->name }}</div>
                        @endforeach
                    @endif
                    @if(isset($activeFilters['dates']))
                        <div class="sp-active-tag">
                            {{ $activeFilters['dates']['from'] ?? '...' }} s/d {{ $activeFilters['dates']['to'] ?? '...' }}
                        </div>
                    @endif
                </div>
                @endif

                {{-- ── COURSES ── --}}
                @if(isset($courses) && $courses->count() > 0)
                <div class="sp-section-label">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    Kursus
                </div>
                <div class="sp-results-header">
                    <span class="sp-results-title">{{ $courses->count() }} kursus ditemukan</span>
                    <a href="{{ route('course.index') }}?q={{ urlencode($keyword) }}" style="font-size:.78rem;color:#0056D2;text-decoration:none;font-weight:600;">
                        Lihat semua →
                    </a>
                </div>
                <div class="sp-course-grid">
                    @foreach($courses as $course)
                    <a href="{{ route('course.show', $course->slug) }}" class="sp-crs-card">
                        <div class="sp-crs-thumb">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}"
                                     loading="lazy" onerror="this.parentElement.innerHTML='<div class=sp-crs-thumb-ph><svg fill=none stroke=currentColor viewBox=\'0 0 24 24\' style=\'width:28px;height:28px;color:#0056D2;opacity:.35\'><path stroke-linecap=round stroke-linejoin=round stroke-width=2 d=\'M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z\'/></svg></div>'">
                            @else
                                <div class="sp-crs-thumb-ph">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                            <span class="sp-crs-badge {{ ($course->price ?? 0) == 0 ? 'free' : 'paid' }}">
                                {{ ($course->price ?? 0) == 0 ? 'Gratis' : 'Premium' }}
                            </span>
                        </div>
                        <div class="sp-crs-body">
                            @if($course->category)<div class="sp-crs-cat">{{ $course->category->name }}</div>@endif
                            <div class="sp-crs-title">{{ $course->title }}</div>
                            <div class="sp-crs-instructor">
                                {{ is_string($course->instructor ?? null) ? $course->instructor : ($course->instructor->name ?? 'Ray Academy') }}
                            </div>
                            <div class="sp-crs-footer">
                                <span class="sp-crs-price {{ ($course->price ?? 0) == 0 ? 'free' : '' }}">
                                    {{ ($course->price ?? 0) == 0 ? 'Gratis' : 'Rp '.number_format($course->price,0,',','.') }}
                                </span>
                                <span class="sp-crs-rating">
                                    <svg viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    4.8
                                </span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @endif

                {{-- ── ARTICLES ── --}}
                @if(isset($articles) && $articles->count() > 0)
                <div class="sp-section-label" style="margin-top:{{ (isset($courses) && $courses->count() > 0) ? '2rem' : '0' }};">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Artikel
                </div>
                <div class="sp-results-header">
                    <span class="sp-results-title">{{ $articles->total() }} artikel ditemukan</span>
                    <span class="sp-results-count">Halaman {{ $articles->currentPage() }} dari {{ $articles->lastPage() }}</span>
                </div>
                <div class="sp-art-list">
                    @foreach($articles as $article)
                    <a href="{{ route('article.show', $article->slug) }}" class="sp-art-card">
                        <div class="sp-art-thumb">
                            @if($article->thumbnail ?? $article->cover_image ?? null)
                                <img src="{{ asset('storage/' . ($article->thumbnail ?? $article->cover_image)) }}"
                                     alt="{{ $article->title }}" loading="lazy"
                                     onerror="this.parentElement.innerHTML='<div class=sp-art-thumb-ph><svg fill=none stroke=currentColor viewBox=\'0 0 24 24\' style=\'width:22px;height:22px;color:#0056D2;opacity:.35\'><path stroke-linecap=round stroke-linejoin=round stroke-width=1.5 d=\'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\'/></svg></div>'">
                            @else
                                <div class="sp-art-thumb-ph">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                            @endif
                        </div>
                        <div class="sp-art-body">
                            <div class="sp-art-meta">
                                @if($article->categories->isNotEmpty())
                                    <span class="sp-art-cat">{{ $article->categories->first()->name }}</span>
                                @endif
                                @if($article->published_at)
                                    <span class="sp-art-date">{{ $article->published_at->isoFormat('D MMM YYYY') }}</span>
                                @endif
                            </div>
                            <div class="sp-art-title">{{ $article->title }}</div>
                            @if($article->excerpt)
                                <div class="sp-art-excerpt">{{ $article->excerpt }}</div>
                            @elseif($article->content ?? null)
                                <div class="sp-art-excerpt">{{ Str::limit(strip_tags($article->content), 120) }}</div>
                            @endif
                            <div class="sp-art-read">Baca Selengkapnya →</div>
                        </div>
                    </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($articles->hasPages())
                <div class="sp-pager">
                    {{-- Prev --}}
                    @if($articles->onFirstPage())
                        <span style="opacity:.3;">‹</span>
                    @else
                        <a href="{{ $articles->previousPageUrl() }}&q={{ urlencode($keyword) }}">‹</a>
                    @endif

                    @for($p = max(1,$articles->currentPage()-2); $p <= min($articles->lastPage(),$articles->currentPage()+2); $p++)
                        @if($p == $articles->currentPage())
                            <span class="current">{{ $p }}</span>
                        @else
                            <a href="{{ $articles->url($p) }}&q={{ urlencode($keyword) }}">{{ $p }}</a>
                        @endif
                    @endfor

                    {{-- Next --}}
                    @if($articles->hasMorePages())
                        <a href="{{ $articles->nextPageUrl() }}&q={{ urlencode($keyword) }}">›</a>
                    @else
                        <span style="opacity:.3;">›</span>
                    @endif
                </div>
                @endif
                @endif

                {{-- ── NO RESULTS ── --}}
                @if((!isset($courses) || $courses->count() == 0) && (!isset($articles) || $articles->count() == 0))
                <div class="sp-empty">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3>Tidak ada hasil untuk "{{ $keyword }}"</h3>
                    <p>Coba gunakan kata kunci lain atau hapus filter yang aktif.</p>
                    <div class="sp-related-list" style="justify-content:center;margin-top:1rem;">
                        @foreach(['Kosmetik','Bisnis','Skincare','Teknologi','Kesehatan'] as $s)
                        <a href="{{ route('search', ['q'=>$s]) }}" style="font-size:.78rem;">{{ $s }}</a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- ── PARTNER BANNER ── --}}
                <div class="sp-cta-banner" style="background:linear-gradient(135deg,#0F1114 0%,#1a2942 100%);border-color:#334155;">
                    <img src="{{ asset('img/search/content-2.png') }}" alt="" class="sp-cta-img"
                         onerror="this.style.display='none'">
                    <div class="sp-cta-text">
                        <h3 style="color:#fff;">Memajukan karier Anda bersama mitra kami</h3>
                        <p style="color:rgba(255,255,255,.6);">Berkembang dengan kursus dari instruktur berpengalaman bersama Ray Academy.</p>
                        <a href="{{ route('course.index') }}" style="background:#fff;color:#0056D2;">
                            Bergabung Sekarang
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

            </main>
        </div>
        @endif

    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Auto-submit when clicking category chip
    document.querySelectorAll('.sp-chip').forEach(btn => {
        btn.addEventListener('click', () => {
            // let form submit naturally
        });
    });

    // Auto-submit tag checkboxes after short delay
    document.querySelectorAll('.sp-tag-cb').forEach(cb => {
        cb.addEventListener('change', () => {
            setTimeout(() => document.getElementById('sp-filter-form')?.submit(), 200);
        });
    });
});
</script>
@endpush