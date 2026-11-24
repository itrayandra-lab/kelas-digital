@extends('layouts.app')
@section('title', $keyword ? 'Hasil: "' . $keyword . '" — Ray Academy' : 'Cari Kursus — Ray Academy')

@push('styles')
<style>
/* ══════════════════════════════════════════════════
   SEARCH PAGE — Ray Academy (Coursera-inspired)
   ══════════════════════════════════════════════════ */
.wrap { max-width:1280px; margin:0 auto; padding:0 1.5rem; }

/* ── LAYOUT ── */
.sp-body { background:#f6f9fe;padding:1.5rem 0 4rem; }
.sp-layout {
    display:grid;
    grid-template-columns:240px 1fr;
    gap:2rem;
    align-items:start;
}

/* ── SIDEBAR ── */
.sp-sidebar { position:sticky;top:72px; }
.sp-sidebar-box{
    border:1px solid #e3ecf8;border-radius:18px;box-shadow:0 8px 24px rgba(31,77,140,.06);
    overflow:hidden;background:#fff;
    margin-bottom:1rem;
}
.sp-sidebar-head{
    padding:.875rem 1rem;border-bottom:1px solid #F1F5F9;
    font-family:'Sora',sans-serif;font-size:.82rem;
    font-weight:700;color:#0b1f4d;
    display:flex;justify-content:space-between;align-items:center;
}
.sp-sidebar-head a{font-size:.8rem;font-weight:600;color:#1f6fd1;text-decoration:none;}
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
.sp-chip:hover{background:#F1F5FF;color:#1f6fd1;}
.sp-chip.active{background:#EEF4FF;color:#1f6fd1;font-weight:600;}
.sp-chip .sp-chip-count{margin-left:auto;font-size:.8rem;color:#6B7280;}
.sp-chip.active .sp-chip-count{color:#1f6fd1;opacity:.7;}

/* date inputs */
.sp-date-group{display:flex;flex-direction:column;gap:.5rem;margin-bottom:.6rem;}
.sp-date-label{font-size:.8rem;font-weight:600;color:#6B7280;}
.sp-date-input{
    padding:.55rem .75rem;border:1.5px solid #E2E8F0;
    border-radius:8px;font-size:.8rem;color:#0b1f4d;
    outline:none;transition:border-color .2s;
}
.sp-date-input:focus{border-color:#1f6fd1;}
.sp-filter-apply{
    width:100%;padding:.65rem;background:#1f6fd1;color:#fff;
    border:none;border-radius:8px;font-weight:600;font-size:.8rem;
    cursor:pointer;transition:background .2s;margin-top:.5rem;
}
.sp-filter-apply:hover{background:#123a82;}
.sp-filter-reset{
    display:block;text-align:center;
    width:100%;padding:.55rem;background:#F8FAFF;color:#6B7280;
    border:1.5px solid #E2E8F0;border-radius:8px;font-weight:600;font-size:.78rem;
    cursor:pointer;transition:all .2s;margin-top:.4rem;text-decoration:none;
}
.sp-filter-reset:hover{border-color:#1f6fd1;color:#1f6fd1;background:#EEF4FF;}

/* tag pills */
.sp-tags{display:flex;flex-wrap:wrap;gap:.4rem;}
.sp-tag-cb{display:none;}
.sp-tag-lbl{
    display:inline-flex;align-items:center;gap:.3rem;
    padding:.28rem .75rem;background:#F8FAFF;
    border:1.5px solid #E2E8F0;border-radius:999px;
    font-size:.8rem;font-weight:500;color:#374151;
    cursor:pointer;transition:all .15s;
}
.sp-tag-lbl:hover{border-color:#1f6fd1;background:#EEF4FF;color:#1f6fd1;}
.sp-tag-cb:checked + .sp-tag-lbl{background:#1f6fd1;border-color:#1f6fd1;color:#fff;}

/* ── MAIN RESULTS ── */
.sp-main {}
.sp-results-header{
    display:flex;align-items:center;justify-content:space-between;
    margin-bottom:1rem;padding-bottom:.75rem;
    border-bottom:1px solid #F1F5F9;
}
.sp-results-title{font-family:'Sora',sans-serif;font-size:.95rem;font-weight:700;color:#0b1f4d;}
.sp-results-count{font-size:.8rem;color:#6B7280;}

/* active filter tags */
.sp-active-tags{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:1rem;}
.sp-active-tag{
    display:inline-flex;align-items:center;gap:.4rem;
    padding:.3rem .75rem;background:#EEF4FF;color:#1f6fd1;
    font-size:.8rem;font-weight:600;border-radius:999px;
    border:1px solid rgba(31,111,209,.2);
}
.sp-active-tag a{color:#1f6fd1;text-decoration:none;font-size:.8rem;}
.sp-active-tag a:hover{color:#c00;}

/* section label */
.sp-section-label{
    display:flex;align-items:center;gap:.5rem;
    font-family:'Sora',sans-serif;font-size:.82rem;font-weight:700;
    color:#0b1f4d;margin-bottom:.75rem;margin-top:1.25rem;
    padding-bottom:.5rem;border-bottom:2px solid #EEF4FF;
}
.sp-section-label:first-child{margin-top:0;}
.sp-section-label svg{width:15px;height:15px;color:#1f6fd1;}


/* ── ARTICLE CARD ── */
.sp-art-list{display:flex;flex-direction:column;gap:.75rem;margin-bottom:1.5rem;}
.sp-art-card{
    background:#fff;border:1px solid #e3ecf8;border-radius:16px;
    padding:.875rem;display:flex;gap:.875rem;align-items:flex-start;
    text-decoration:none;color:inherit;
    transition:box-shadow .15s,border-color .15s;
}
.sp-art-card:hover{box-shadow:0 4px 16px rgba(31,111,209,.08);border-color:#BFDBFE;}
.sp-art-thumb{
    width:80px;height:60px;border-radius:8px;overflow:hidden;
    background:linear-gradient(135deg,#EEF4FF,#DBEAFE);flex-shrink:0;
}
.sp-art-thumb img{width:100%;height:100%;object-fit:cover;}
.sp-art-thumb-ph{width:100%;height:100%;display:flex;align-items:center;justify-content:center;}
.sp-art-thumb-ph svg{width:22px;height:22px;color:#1f6fd1;opacity:.35;}
.sp-art-body{flex:1;min-width:0;}
.sp-art-meta{display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;flex-wrap:wrap;}
.sp-art-cat{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#1f6fd1;}
.sp-art-date{font-size:.75rem;color:#6B7280;}
.sp-art-title{
    font-family:'Sora',sans-serif;font-size:.82rem;font-weight:600;color:#0b1f4d;
    line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;
}
.sp-art-excerpt{font-size:.8rem;color:#6B7280;line-height:1.55;margin-top:.2rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.sp-art-read{font-size:.75rem;font-weight:600;color:#1f6fd1;margin-top:.35rem;}

/* ── CTA banner (Coursera join-for-free style) ── */
.sp-cta-banner{
    background:linear-gradient(135deg,#EEF4FF 0%,#DBEAFE 100%);
    border:1px solid #BFDBFE;border-radius:16px;
    padding:1.5rem;display:flex;align-items:center;gap:1.5rem;
    margin-top:1.5rem;
}
.sp-cta-img{width:100px;height:80px;object-fit:contain;flex-shrink:0;}
.sp-cta-text h3{font-family:'Sora',sans-serif;font-size:.95rem;font-weight:700;color:#0b1f4d;margin-bottom:.3rem;}
.sp-cta-text p{font-size:.78rem;color:#374151;line-height:1.5;margin-bottom:.75rem;}
.sp-cta-text a{
    display:inline-flex;align-items:center;gap:.35rem;
    background:#1f6fd1;color:#fff;font-weight:600;font-size:.8rem;
    padding:.55rem 1.25rem;border-radius:8px;text-decoration:none;
    transition:background .2s;
}
.sp-cta-text a:hover{background:#123a82;}

/* ── PAGINATION ── */
.sp-pager{display:flex;align-items:center;justify-content:center;gap:.4rem;margin-top:2rem;}
.sp-pager a,.sp-pager span{
    display:inline-flex;align-items:center;justify-content:center;
    min-width:36px;height:36px;padding:0 .5rem;border-radius:8px;
    font-size:.8rem;font-weight:600;text-decoration:none;color:#374151;
    border:1.5px solid #E2E8F0;transition:all .15s;
}
.sp-pager a:hover{border-color:#1f6fd1;color:#1f6fd1;background:#EEF4FF;}
.sp-pager span.current{background:#1f6fd1;color:#fff;border-color:#1f6fd1;}
.sp-pager span.dots{border:none;color:#6B7280;}

/* ── EMPTY STATE ── */
.sp-empty{
    text-align:center;padding:4rem 2rem;
    background:#F8FAFF;border:1.5px dashed #E2E8F0;
    border-radius:16px;
}
.sp-empty svg{width:56px;height:56px;color:#CBD5E1;margin:0 auto 1rem;}
.sp-empty h3{font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:700;color:#0b1f4d;margin-bottom:.4rem;}
.sp-empty p{font-size:.85rem;color:#6B7280;}
.sp-empty a{color:#1f6fd1;font-weight:600;text-decoration:none;}
.sp-empty a:hover{text-decoration:underline;}

/* ── NO KEYWORD STATE ── */
.sp-landing{padding:1.5rem 0 2rem;}
.sp-landing-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem;}
.sp-landing-card{
    border:1px solid #e3ecf8;border-radius:20px;padding:1.25rem;box-shadow:0 12px 30px rgba(31,77,140,.08);
    background:#fff;display:flex;align-items:center;gap:.875rem;
    cursor:pointer;text-decoration:none;color:inherit;
    transition:border-color .2s,box-shadow .2s;
}
.sp-landing-card:hover{border-color:#1f6fd1;box-shadow:0 4px 16px rgba(31,111,209,.1);}
.sp-landing-card-icon{
    width:44px;height:44px;border-radius:10px;background:#EEF4FF;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.sp-landing-card-icon svg{width:22px;height:22px;color:#1f6fd1;}
.sp-landing-card h3{font-family:'Sora',sans-serif;font-size:.85rem;font-weight:700;color:#0b1f4d;margin-bottom:.15rem;}
.sp-landing-card p{font-size:.8rem;color:#6B7280;line-height:1.4;}

/* related searches */
.sp-related h3{font-family:'Sora',sans-serif;font-size:.85rem;font-weight:700;color:#6B7280;margin-bottom:.75rem;}
.sp-related-list{display:flex;flex-wrap:wrap;gap:.5rem;}
.sp-related-list a{
    font-size:.82rem;font-weight:500;color:#1f6fd1;text-decoration:none;
    padding:.3rem .75rem;background:#EEF4FF;border-radius:999px;
    transition:background .15s;
}
.sp-related-list a em{font-style:normal;color:#1E40AF;font-weight:700;}
.sp-related-list a:hover{background:#DBEAFE;}

/* ── RESPONSIVE ── */
@media(max-width:900px){
    .sp-layout{grid-template-columns:1fr;}
    .sp-sidebar{position:static;}
    .sp-landing-grid{grid-template-columns:1fr 1fr;}
}
@media(max-width:640px){
    .sp-landing-grid{grid-template-columns:1fr;}
    .sp-cta-banner{flex-direction:column;text-align:center;}
    .sp-cta-img{display:none;}
}
</style>
@endpush

@section('content')

{{-- ══ HERO ══ --}}
<x-page-hero pill="Search">
    <x-slot:crumbs><a href="{{ route('home') }}">Beranda</a> › Cari</x-slot:crumbs>
    @if($keyword)
        @php $total = ($courses->count() ?? 0) + ($articles->total() ?? 0); @endphp
        <x-slot:title>Hasil untuk <span>"{{ $keyword }}"</span></x-slot:title>
        Ditemukan {{ number_format($total) }} hasil pencarian.
    @else
        <x-slot:title>Find what you <span>want to learn.</span></x-slot:title>
        Temukan kursus, artikel, dan materi pembelajaran yang sesuai dengan kebutuhan Anda.
    @endif
    <x-slot:after>
        <form action="{{ route('search') }}" method="GET" class="ph-search" role="search">
            <i class="fas fa-search" aria-hidden="true"></i>
            <input type="search" name="q" value="{{ $keyword }}" placeholder="Cari kursus, artikel, instruktur..." aria-label="Cari kursus dan artikel" autocomplete="off">
            @if($selectedCategoryId) <input type="hidden" name="category_id" value="{{ $selectedCategoryId }}"> @endif
            @if($selectedDateFrom)   <input type="hidden" name="date_from"    value="{{ $selectedDateFrom }}"> @endif
            @if($selectedDateTo)     <input type="hidden" name="date_to"      value="{{ $selectedDateTo }}"> @endif
            <button type="submit"><i class="fas fa-search" aria-hidden="true"></i><span class="ph-btn-text">Cari</span></button>
        </form>
    </x-slot:after>
</x-page-hero>

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
                    <a href="{{ route('course.index') }}" style="font-size:.78rem;color:#1f6fd1;text-decoration:none;font-weight:600;">Lihat semua →</a>
                </div>
                <div class="cc-grid" style="margin-top:.75rem;">
                    @foreach($popularCourses as $course)
                        @include('course._card')
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
                    <a href="{{ route('article.index') }}" style="font-size:.78rem;color:#1f6fd1;text-decoration:none;font-weight:600;">Lihat semua →</a>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:.6rem;margin-top:.75rem;">
                    @foreach($popularCategories as $cat)
                    <a href="{{ route('search', ['q' => $cat->name]) }}"
                       style="display:inline-flex;align-items:center;gap:.4rem;
                              padding:.5rem 1.1rem;background:#EEF4FF;
                              border:1.5px solid rgba(31,111,209,.15);border-radius:999px;
                              font-size:.8rem;font-weight:600;color:#1f6fd1;
                              text-decoration:none;transition:all .15s;"
                       onmouseover="this.style.background='#1f6fd1';this.style.color='#fff'"
                       onmouseout="this.style.background='#EEF4FF';this.style.color='#1f6fd1'">
                        {{ $cat->name }}
                        <span style="font-size:.75rem;opacity:.65;">{{ $cat->articles_count }}</span>
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
                    <a href="{{ route('article.index') }}" style="font-size:.78rem;color:#1f6fd1;text-decoration:none;font-weight:600;">Lihat semua →</a>
                </div>
                <div class="sp-art-list" style="margin-top:.75rem;">
                    @foreach($popularArticles as $article)
                    <a href="{{ route('article.show', $article->slug) }}" class="sp-art-card">
                        <div class="sp-art-thumb">
                            @if($article->thumbnail_url)
                                <img src="{{ $article->thumbnail_url }}"
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
                    <a href="{{ route('course.index') }}?q={{ urlencode($keyword) }}" style="font-size:.78rem;color:#1f6fd1;text-decoration:none;font-weight:600;">
                        Lihat semua →
                    </a>
                </div>
                <div class="cc-grid">
                    @foreach($courses as $course)
                        @include('course._card')
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
                            @if($article->thumbnail_url)
                                <img src="{{ $article->thumbnail_url }}"
                                     alt="{{ $article->title }}" loading="lazy"
                                     onerror="this.parentElement.innerHTML='<div class=sp-art-thumb-ph><svg fill=none stroke=currentColor viewBox=\'0 0 24 24\' style=\'width:22px;height:22px;color:#1f6fd1;opacity:.35\'><path stroke-linecap=round stroke-linejoin=round stroke-width=1.5 d=\'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z\'/></svg></div>'">
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
                <div class="sp-cta-banner" style="background:linear-gradient(135deg,#0b1f4d 0%,#1a2942 100%);border-color:#334155;">
                    <img src="{{ asset('img/search/content-2.png') }}" alt="" class="sp-cta-img"
                         onerror="this.style.display='none'">
                    <div class="sp-cta-text">
                        <h3 style="color:#fff;">Memajukan karier Anda bersama mitra kami</h3>
                        <p style="color:rgba(255,255,255,.6);">Berkembang dengan kursus dari instruktur berpengalaman bersama Ray Academy.</p>
                        <a href="{{ route('course.index') }}" style="background:#fff;color:#1f6fd1;">
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