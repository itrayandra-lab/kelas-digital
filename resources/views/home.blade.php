@extends('layouts.app')
@section('title', config('app.name') . ' — Platform Belajar Online Terpercaya')

@push('styles')
<style>
/* ═══════════════════════════════════════════
   RAY ACADEMY — HOME PAGE (Hi-Tech Redesign)
   Palette: #0056D2 blue · #0F1114 dark · #F5F5F4 cream
   ═══════════════════════════════════════════ */

/* Scroll Reveal */
.rv{opacity:0;transform:translateY(28px);transition:opacity .6s ease,transform .6s ease}
.rv.in{opacity:1;transform:translateY(0)}
.rv-d1{transition-delay:.08s}.rv-d2{transition-delay:.16s}.rv-d3{transition-delay:.24s}.rv-d4{transition-delay:.32s}

/* Wrapper */
.wrap{max-width:1280px;margin:0 auto;padding:0 1.5rem}
@media(max-width:640px){.wrap{padding:0 1rem}}

/* Shared section tag */
.sec-tag{
    display:inline-flex;align-items:center;gap:.4rem;
    font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
    color:#0056D2;background:#EEF4FF;border:1px solid rgba(0,86,210,.15);
    padding:.3rem .85rem;border-radius:999px;margin-bottom:.85rem;
}
.sec-tag-white{
    display:inline-flex;align-items:center;gap:.4rem;
    font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
    color:rgba(255,255,255,.85);background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);
    padding:.3rem .85rem;border-radius:999px;margin-bottom:.85rem;
}

/* Shared button (bottom call-to-action) */
.btn-primary-ra{
    display:inline-flex;align-items:center;gap:.5rem;
    background:#0056D2;color:#fff;font-family:'Sora',sans-serif;
    font-weight:600;font-size:.875rem;padding:.8rem 1.75rem;
    border-radius:8px;text-decoration:none;border:none;
    transition:all .22s;box-shadow:0 4px 16px rgba(0,86,210,.35);
}
.btn-primary-ra:hover{background:#0048B0;transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,86,210,.4);color:#fff;}

/* ═══ 2. PROMO CARDS ═══ */
.promo-section{background:#fff;padding:2rem 0;position:relative;z-index:2;}
.promo-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;}
.promo-card{
    border-radius:20px;overflow:hidden;display:flex;align-items:stretch;
    min-height:160px;text-decoration:none;
    transition:transform .25s,box-shadow .25s;position:relative;
}
.promo-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(0,0,0,.12);}
/* Glass look on the pastel cards: a soft light reflection and a thin white rim. */
.promo-card::before{content:'';position:absolute;inset:0;z-index:2;pointer-events:none;border-radius:inherit;
    background:radial-gradient(120% 90% at 0% 0%,rgba(255,255,255,.55),rgba(255,255,255,0) 55%);
    box-shadow:inset 0 0 0 1px rgba(255,255,255,.65);}
.promo-card-content{
    flex:1;padding:1.5rem;
    display:flex;flex-direction:column;justify-content:space-between;
    z-index:3;min-width:0;
}
.promo-logo-txt{display:inline-block;font-family:'Sora',sans-serif;font-size:.6rem;font-weight:800;letter-spacing:.1em;color:#0F1114;margin-bottom:.7rem;
    padding:.28rem .65rem;border-radius:999px;background:rgba(255,255,255,.5);border:1px solid rgba(255,255,255,.8);
    -webkit-backdrop-filter:blur(8px);backdrop-filter:blur(8px);}
.promo-title{font-family:'Sora',sans-serif;font-size:.88rem;font-weight:600;color:#0F1114;line-height:1.4;flex:1;}
.promo-link-txt{
    align-self:flex-start;display:inline-flex;align-items:center;gap:.35rem;
    font-size:.75rem;font-weight:600;color:#0F1114;
    margin-top:.9rem;padding:.45rem .9rem;border-radius:999px;
    background:rgba(255,255,255,.6);border:1px solid rgba(255,255,255,.85);
    -webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);
    box-shadow:0 4px 14px rgba(15,17,20,.06);transition:background .2s;
}
.promo-link-txt svg{transition:transform .2s;}
.promo-card:hover .promo-link-txt{background:rgba(255,255,255,.92);}
.promo-card:hover .promo-link-txt svg{transform:translateX(3px);}
/* The photo's left edge fades into the card colour instead of a hard line. */
.promo-card-img{width:180px;flex-shrink:0;object-fit:cover;object-position:center;
    -webkit-mask-image:linear-gradient(to right,transparent,#000 22%);mask-image:linear-gradient(to right,transparent,#000 22%);}

/* ═══ 3. FREE COURSE BANNER — FIXED ═══ */
.free-banner{background:#fff;padding:3.5rem 0;border-top:1px solid #F1F5F9;}
.free-banner-inner{
    display:grid;grid-template-columns:1fr 1fr;gap:4rem;
    align-items:center;
}
.free-banner-text h2{
    font-family:'Sora',sans-serif;
    font-size:clamp(1.8rem,3.6vw,2.6rem);
    font-weight:700;color:#0F1114;
    line-height:1.2;letter-spacing:-.02em;margin-bottom:.9rem;text-wrap:balance;
}
.free-banner-text p{font-size:1rem;color:#6B7280;line-height:1.7;max-width:460px;margin-bottom:1.75rem;}
.btn-outline-blue{
    display:inline-flex;align-items:center;gap:.4rem;
    padding:.75rem 1.5rem;border:1.5px solid #0056D2;border-radius:8px;
    font-weight:600;font-size:.875rem;color:#0056D2;text-decoration:none;
    transition:all .2s;background:#fff;
}
.btn-outline-blue:hover{background:#0056D2;color:#fff;}

/* IMAGE FIXED — no crop */
.free-banner-img-wrap{
    position:relative;
    display:flex;align-items:center;justify-content:center;
    min-height:280px;
}
.free-banner-img-wrap img{
    width:100%;height:auto;
    max-height:360px;
    object-fit:contain;object-position:center;
    display:block;
    position:relative;z-index:1;
}
/* decorative background for image */
.free-banner-img-wrap::before{
    content:'';position:absolute;
    right:0;top:10%;bottom:10%;width:75%;
    background:linear-gradient(135deg,#EEF4FF 0%,#DBEAFE 100%);
    border-radius:20px;
}

/* ═══ 4. FEATURED COURSES ═══ */
.browse-section{background:#F8FAFF;padding:3.5rem 0;border-top:1px solid #F1F5F9;}
.browse-head h2{font-family:'Sora',sans-serif;font-size:clamp(1.4rem,3vw,2rem);font-weight:600;color:#0F1114;letter-spacing:-.02em;}
.browse-head p{font-size:.9rem;color:#6B7280;margin-top:.35rem;}
.browse-tabs{display:flex;gap:.5rem;overflow-x:auto;scrollbar-width:none;margin:1.25rem 0 1.75rem;padding-bottom:.25rem;}
.browse-tabs::-webkit-scrollbar{display:none;}
.browse-tab{flex-shrink:0;display:inline-flex;align-items:center;gap:.45rem;padding:.5rem 1rem;border-radius:999px;font-size:.8rem;font-weight:600;color:#0048B0;background:#fff;border:1px solid rgba(0,86,210,.18);transition:all .15s;}
.browse-tab i{font-size:.72rem;}
.browse-tab:hover{background:#F1F5FF;}
.browse-tab.on{background:#0056D2;border-color:#0056D2;color:#fff;}
.browse-panel-head{display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;margin-bottom:1rem;}
.browse-panel-head h3{font-family:'Sora',sans-serif;font-size:1.05rem;font-weight:600;color:#0F1114;}
.browse-panel-head p{font-size:.82rem;color:#6B7280;margin-top:.2rem;max-width:620px;}
.browse-all{display:inline-flex;align-items:center;gap:.4rem;color:#0056D2;font-weight:600;font-size:.85rem;text-decoration:none;white-space:nowrap;}
.browse-all:hover{text-decoration:underline;}

/* ═══ 5. SEARCH ═══ */
.search-section{background:#fff;padding:3rem 0;text-align:center;border-top:1px solid #F1F5F9;}
.search-section h2{font-family:'Sora',sans-serif;font-size:clamp(1.4rem,3vw,2rem);font-weight:600;color:#0F1114;margin-bottom:1.5rem;}
.search-box-ra{
    display:flex;align-items:center;background:#fff;
    border:1.5px solid #CBD5E1;border-radius:10px;overflow:hidden;
    max-width:680px;margin:0 auto;
    box-shadow:0 4px 16px rgba(0,0,0,.06);
    transition:border-color .2s,box-shadow .2s;
}
.search-box-ra:focus-within{border-color:#0056D2;box-shadow:0 4px 16px rgba(0,86,210,.15);}
.search-box-ra input{flex:1;border:none;outline:none;padding:.9rem 1rem;font-family:'DM Sans',sans-serif;font-size:.9rem;color:#0F1114;background:transparent;}
.search-box-ra input::placeholder{color:#94A3B8;}
.search-box-ra button{background:#0056D2;border:none;padding:.9rem 1.1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;}
.search-box-ra button:hover{background:#0048B0;}
.search-box-ra button svg{width:18px;height:18px;color:#fff;}
.chip-row{display:flex;gap:.5rem;flex-wrap:wrap;justify-content:center;margin-top:1rem;align-items:center;}
.chip-label{font-size:.72rem;font-weight:600;color:#374151;}
.search-chip{
    display:inline-flex;align-items:center;
    background:#F1F5FF;color:#0048B0;font-size:.73rem;font-weight:500;
    padding:.3rem .9rem;border-radius:999px;text-decoration:none;
    border:1px solid rgba(0,86,210,.12);transition:all .15s;
}
.search-chip:hover{background:#0056D2;color:#fff;border-color:#0056D2;}

/* ═══ 6. INSTRUCTORS (Belajar dari Ahli) ═══ */
.ins-section{
    padding:4rem 0;background:#0F1114;
    position:relative;overflow:hidden;
}
.ins-section::before{
    content:'';position:absolute;inset:0;
    background:radial-gradient(ellipse 60% 70% at 85% 50%,rgba(0,86,210,.18),transparent 65%);
    pointer-events:none;
}
.ins-header{text-align:center;margin-bottom:3rem;}
.ins-header h2{font-family:'Sora',sans-serif;font-size:clamp(1.8rem,3.5vw,2.5rem);font-weight:700;color:#fff;letter-spacing:-.02em;margin-top:.5rem;}
.ins-header p{color:rgba(255,255,255,.5);margin-top:.6rem;font-size:.9rem;max-width:400px;margin-left:auto;margin-right:auto;line-height:1.7;}

/* Staggered grid */
.ins-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:1rem;}

.ins-card{
    border-radius:18px;overflow:hidden;position:relative;
    transition:transform .3s,box-shadow .3s;cursor:default;
    display:flex;flex-direction:column;
}
.ins-card:hover{transform:translateY(-8px);box-shadow:0 24px 56px rgba(0,0,0,.4);}

.ins-card-inner{padding:1.4rem 1.4rem 0;position:relative;z-index:1;flex:1;}
.ins-card-logo{height:38px;object-fit:contain;margin-bottom:.8rem;display:block;}
.ins-card-name{font-family:'Sora',sans-serif;font-size:.88rem;font-weight:700;color:#fff;line-height:1.3;}
.ins-card-tag{font-size:.72rem;color:rgba(255,255,255,.5);margin-top:.2rem;}
.ins-card-btn{
    display:inline-block;margin-top:.9rem;padding:.45rem 1.1rem;
    background:rgba(255,255,255,.92);border-radius:999px;
    font-weight:700;font-size:.72rem;text-decoration:none;
    transition:opacity .2s,transform .15s;border:none;
}
.ins-card-btn:hover{opacity:.85;transform:translateX(3px);}
.ins-card-photo{display:block;height:185px;width:100%;object-fit:contain;object-position:bottom;margin-top:1rem;}

/* ═══ 7. ARTICLES ═══ */
.articles-section{background:#fff;padding:3.5rem 0;}
.art-head{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;}
.art-head h2{font-family:'Sora',sans-serif;font-size:clamp(1.4rem,3vw,1.75rem);font-weight:700;color:#0F1114;}
.art-head p{font-size:.9rem;color:#6B7280;margin-top:.25rem;}
.art-head a{font-size:.85rem;font-weight:600;color:#0056D2;text-decoration:none;}

/* ═══ 9. FAQ ═══ */
.faq-section{background:#fff;padding:3rem 0;border-top:1px solid #F1F5F9;}
.faq-section h2{font-family:'Sora',sans-serif;font-size:clamp(1.4rem,3vw,1.75rem);font-weight:700;color:#0F1114;margin-bottom:1.5rem;}
.faq-box{border:1.5px solid #E2E8F0;border-radius:12px;overflow:hidden;}
.faq-item{border-bottom:1px solid #F1F5F9;}
.faq-item:last-child{border-bottom:none;}
.faq-q{
    display:flex;align-items:center;justify-content:space-between;
    padding:1rem 1.25rem;cursor:pointer;user-select:none;gap:.75rem;
    transition:background .15s;
}
.faq-q:hover{background:#F8FAFF;}
.faq-q h3{font-family:'DM Sans',sans-serif;font-size:.86rem;font-weight:600;color:#0F1114;margin:0;flex:1;}
.faq-chevron{
    width:24px;height:24px;border-radius:50%;background:#F1F5F9;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
    transition:transform .3s,background .2s;
}
.faq-item.open .faq-chevron{transform:rotate(180deg);background:#0056D2;}
.faq-chevron svg{width:13px;height:13px;color:#6B7280;}
.faq-item.open .faq-chevron svg{color:#fff;}
.faq-ans{max-height:0;overflow:hidden;transition:max-height .3s ease;}
.faq-item.open .faq-ans{max-height:400px;}
.faq-ans p{padding:.25rem 1.25rem 1.1rem;font-size:.82rem;color:#6B7280;line-height:1.7;margin:0;}

/* ═══ 10. PARTNERS ═══ */
.partners-section{background:#F8FAFF;padding:3rem 0;border-top:1px solid #F1F5F9;}
.partners-section h2{font-family:'Sora',sans-serif;font-size:clamp(1rem,2.2vw,1.2rem);font-weight:600;color:#6B7280;text-align:center;margin-bottom:1.5rem;}
.partners-logos{display:flex;align-items:center;justify-content:center;gap:1rem;flex-wrap:wrap;}
.partners-logos img{width:160px;height:88px;padding:.9rem 1.25rem;object-fit:contain;background:#fff;border:1px solid #E2E8F0;border-radius:12px;}

/* ═══ 11. CTA BOTTOM ═══ */
.cta-section{background:#fff;padding:3.5rem 0;}
.cta-inner{display:flex;flex-direction:column;align-items:center;text-align:center;gap:1rem;padding:3rem 1.5rem;border-radius:20px;background:linear-gradient(135deg,#0056D2,#1E3A8A);position:relative;overflow:hidden;}
.cta-inner::before{content:'';position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.06) 1px,transparent 1px);background-size:48px 48px;pointer-events:none;}
.cta-inner > *{position:relative;}
.cta-inner h2{font-family:'Sora',sans-serif;font-size:clamp(1.5rem,3.5vw,2.25rem);font-weight:700;color:#fff;letter-spacing:-.02em;margin:0;}
.cta-inner p{font-size:.95rem;color:rgba(255,255,255,.8);max-width:480px;margin:0;line-height:1.7;}
.cta-inner .btn-primary-ra{background:#fff;color:#0056D2;box-shadow:none;}
.cta-inner .btn-primary-ra:hover{background:#EEF4FF;color:#0056D2;}
.cta-subtext{font-size:.78rem;color:rgba(255,255,255,.65)!important;}

/* ═══ RESPONSIVE ═══ */
@media(max-width:1100px){ .ins-grid{grid-template-columns:repeat(3,1fr);} }
@media(max-width:900px){
    .ins-grid{grid-template-columns:repeat(2,1fr);}
}
@media(max-width:768px){
    .free-banner-inner{grid-template-columns:1fr;}
    .free-banner-img-wrap{min-height:220px;}
    .free-banner-img-wrap::before{width:100%;top:0;bottom:0;right:0;}
    .promo-card-img{width:130px;}
}
@media(max-width:640px){
    html,body{overflow-x:hidden!important;max-width:100vw!important;}
    .promo-grid{grid-template-columns:1fr;}
    .promo-card-img{width:120px;}
    .ins-grid{grid-template-columns:1fr;}
}
</style>
@endpush

@include('partials.event-styles')
@push('styles')
<style>
.ev-home { padding:3.5rem 0; background:linear-gradient(180deg,#f3f8ff 0%,#fff 100%); }
.ev-home-head { display:flex; justify-content:space-between; align-items:flex-end; gap:1rem; flex-wrap:wrap; margin-bottom:1.5rem; }
.ev-home-head h2 { font-family:'Sora',sans-serif; font-size:clamp(1.4rem,3vw,1.9rem); font-weight:700; letter-spacing:-.02em; margin:0 0 .3rem; }
.ev-home-head p { color:var(--muted); margin:0; }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════
     1. HERO
═══════════════════════════════ --}}
@include('home._hero')

{{-- ═══════════════════════════════
     2. PROMO CARDS
═══════════════════════════════ --}}
<section class="promo-section" id="program">
    <div class="wrap">
        <div class="promo-grid">
            <a href="{{ route('course.index') }}" class="promo-card" style="background:#CCDDF6;">
                <div class="promo-card-content">
                    <div>
                        <div class="promo-logo-txt">RAY ACADEMY</div>
                        <div class="promo-title">Mulai dari kelas gratis, tanpa biaya apa pun</div>
                    </div>
                    <span class="promo-link-txt">Lihat kelas gratis <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>
                <img src="{{ asset('img/content-course1.jpg') }}" alt="" class="promo-card-img" onerror="this.style.display='none'">
            </a>
            <a href="{{ route('course.index', ['category' => 'business-strategy']) }}" class="promo-card" style="background:#CEFFCC;">
                <div class="promo-card-content">
                    <div>
                        <div class="promo-logo-txt">RAY ACADEMY</div>
                        <div class="promo-title">Memajukan bisnis Anda dan memberdayakan tim Anda</div>
                    </div>
                    <span class="promo-link-txt">Lihat kelas bisnis <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>
                <img src="{{ asset('img/content-course2.jpg') }}" alt="" class="promo-card-img" onerror="this.style.display='none'">
            </a>
            <a href="#instruktur" class="promo-card" style="background:#FFCDCD;">
                <div class="promo-card-content">
                    <div>
                        <div class="promo-logo-txt">RAY ACADEMY</div>
                        <div class="promo-title">Belajar langsung dari praktisi dan akademisi</div>
                    </div>
                    <span class="promo-link-txt">Kenali instruktur kami <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>
                <img src="{{ asset('img/content-course3.jpg') }}" alt="" class="promo-card-img" onerror="this.style.display='none'">
            </a>
            <a href="{{ route('course.index') }}" class="promo-card" style="background:#FFF9CC;">
                <div class="promo-card-content">
                    <div>
                        <div class="promo-logo-txt">RAY ACADEMY</div>
                        <div class="promo-title">Pre-test dan post-test untuk mengukur progresmu</div>
                    </div>
                    <span class="promo-link-txt">Lihat cara belajarnya <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>
                <img src="{{ asset('img/content-course4.jpg') }}" alt="" class="promo-card-img" onerror="this.style.display='none'">
            </a>
        </div>
    </div>
</section>

{{-- Upcoming events --}}
@if($events->isNotEmpty())
<section class="ev-home">
    <div class="wrap">
        <div class="ev-home-head rv">
            <div>
                <h2>Event mendatang</h2>
                <p>Kelas praktik dan workshop gratis, offline maupun online. Kursinya terbatas.</p>
            </div>
            <a href="{{ route('events.index') }}" class="browse-all">Lihat semua event <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="ev-grid">
            @foreach($events as $event)
                @include('events._card')
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════
     3. FREE COURSE BANNER — FIXED
═══════════════════════════════ --}}
<section class="free-banner">
    <div class="wrap">
        <div class="free-banner-inner rv">
            <div class="free-banner-text">
                <h2>Bingung harus mulai dari mana?</h2>
                <p>Mulai dari kelas gratis di Ray Academy. Pelajari materinya, kerjakan tesnya, lalu lanjutkan ke topik yang membuka peluang karier baru.</p>
                <a href="{{ route('course.index') }}" class="btn-outline-blue">Mulai Kursus Gratis</a>
            </div>
            <div class="free-banner-img-wrap">
                <img src="{{ asset('img/content-ray.png') }}" alt="Mulai Belajar Gratis"
                     onerror="this.style.opacity='.3'">
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     4. BROWSE BY CATEGORY
═══════════════════════════════ --}}
@php
    $tabs = collect([['key' => 'new', 'name' => $isFeaturedFallback ? 'Terbaru' : 'Pilihan', 'icon' => 'fa-star', 'courses' => $featuredCourses, 'count' => null, 'slug' => null, 'description' => null]])
        ->merge($courseCategories->map(fn ($cat) => ['key' => $cat->slug, 'name' => $cat->name, 'icon' => $cat->icon, 'courses' => $cat->courses, 'count' => $cat->courses_count, 'slug' => $cat->slug, 'description' => $cat->description !== $cat->name ? $cat->description : null]));
@endphp
@if($featuredCourses->isNotEmpty())
<section class="browse-section" x-data="{ tab: 'new' }">
    <div class="wrap">
        <div class="browse-head rv">
            <h2>Mau belajar apa?</h2>
            <p>Pilih bidang yang ingin kamu kuasai, lalu lihat kelas yang tersedia.</p>
        </div>
        <div class="browse-tabs" role="tablist">
            @foreach($tabs as $t)
                <button type="button" role="tab" class="browse-tab" :class="tab === '{{ $t['key'] }}' && 'on'" :aria-selected="tab === '{{ $t['key'] }}'" @click="tab = '{{ $t['key'] }}'">
                    <i class="fas {{ $t['icon'] }}"></i> {{ $t['name'] }}
                </button>
            @endforeach
        </div>
        @foreach($tabs as $t)
            <div class="browse-panel" role="tabpanel" x-show="tab === '{{ $t['key'] }}'" @unless($loop->first) x-cloak @endunless>
                <div class="browse-panel-head">
                    <div>
                        <h3>{{ $t['slug'] ? 'Belajar '.$t['name'] : ($isFeaturedFallback ? 'Kelas terbaru' : 'Kelas pilihan') }}</h3>
                        <p>{{ $t['description'] ?? ($t['slug'] ? $t['count'].' kelas di bidang '.$t['name'].', dari dasar sampai siap praktik.' : 'Kelas yang baru saja dibuka dan siap kamu ikuti.') }}</p>
                    </div>
                    <a href="{{ route('course.index', array_filter(['category' => $t['slug']])) }}" class="browse-all">
                        {{ $t['slug'] ? 'Lihat semua kelas '.$t['name'] : 'Lihat semua kelas' }} <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="cc-grid">
                    @foreach($t['courses'] as $course)
                        @include('course._card')
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif
{{-- ═══════════════════════════════
     5. SEARCH
═══════════════════════════════ --}}
<section class="search-section">
    <div class="wrap rv">
        <h2>Cari kelas yang kamu butuhkan</h2>
        <form action="{{ route('search') }}" method="GET">
            <div class="search-box-ra">
                <svg style="width:18px;height:18px;color:#94A3B8;margin-left:.85rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                <input type="search" name="q" placeholder="misalnya Digital Marketing" autocomplete="off">
                <button type="submit" aria-label="Cari">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                </button>
            </div>
        </form>
        <div class="chip-row">
            <span class="chip-label">Populer:</span>
            @if($courseCategories->isNotEmpty())
                @foreach($courseCategories->take(6) as $cat)
                <a href="{{ route('course.index') }}?category={{ $cat->slug }}" class="search-chip">{{ $cat->name }}</a>
                @endforeach
            @else
                <a href="{{ route('course.index') }}" class="search-chip">Bisnis</a>
                <a href="{{ route('course.index') }}" class="search-chip">Teknologi</a>
                <a href="{{ route('course.index') }}" class="search-chip">Kosmetik</a>
                <a href="{{ route('course.index') }}" class="search-chip">Kesehatan</a>
                <a href="{{ route('course.index') }}" class="search-chip">AI</a>
            @endif
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     6. INSTRUCTORS (Belajar dari Ahli)
═══════════════════════════════ --}}
<section class="ins-section" id="instruktur">
    <div class="wrap" style="position:relative;z-index:1;">
        <div class="ins-header rv">
            <span class="sec-tag-white">
                <i class="fas fa-chalkboard-teacher" style="font-size:.65rem;"></i>
                Pengajar dari Praktisi dan Ahli Industri
            </span>
            <h2>Belajar Langsung dari Para Ahli</h2>
            <p>Tingkatkan wawasan, pengalaman, dan kompetensi Anda bersama para profesional berpengalaman di bidangnya.</p>
        </div>

        <div class="ins-grid">
            <div class="ins-card rv rv-d1" style="background:linear-gradient(135deg,#ff5733,#c0392b);">
                <div class="ins-card-inner">
                    <img src="{{ asset('assets/logo-do better class.png') }}" alt="Ria R. Christiana SE, MBA." class="ins-card-logo" onerror="this.style.display='none'">
                    <h3 class="ins-card-name">Ria R. Christiana SE, MBA.</h3>
                    <p class="ins-card-tag">Business &amp; Branding</p>
                    <a href="{{ route('course.index') }}" class="ins-card-btn" style="color:#c0392b;">
                        Mulai Belajar <i class="fas fa-arrow-right" style="font-size:.6rem;"></i>
                    </a>
                </div>
                <img src="{{ asset('assets/ria.png') }}" alt="Ria R. Christiana SE, MBA." class="ins-card-photo" style="object-position:bottom ;" onerror="this.style.display='none'">
            </div>

            <div class="ins-card rv rv-d2" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);">
                <div class="ins-card-inner">
                    <img src="{{ asset('assets/logo-psikologi bisnis.png') }}" alt="Sukmayanti Ranadireksa, M.Psi." class="ins-card-logo" onerror="this.style.display='none'">
                    <h3 class="ins-card-name">Sukmayanti Ranadireksa, M.Psi.</h3>
                    <p class="ins-card-tag">Psikologi &amp; Komunikasi</p>
                    <a href="{{ route('course.index') }}" class="ins-card-btn" style="color:#7c3aed;">
                        Mulai Belajar <i class="fas fa-arrow-right" style="font-size:.6rem;"></i>
                    </a>
                </div>
                <img src="{{ asset('assets/s-sukmayanti.png') }}" alt="Sukmayanti Ranadireksa, M.Psi." class="ins-card-photo" onerror="this.style.display='none'">
            </div>

            <div class="ins-card rv rv-d3" style="background:linear-gradient(135deg,#db2777,#9d174d);">
                <div class="ins-card-inner">
                    <img src="{{ asset('assets/logo-ski.png') }}" alt="Apt. Cahya Khairani K., M.Farm" class="ins-card-logo" onerror="this.style.display='none'">
                    <h3 class="ins-card-name">Apt. Cahya Khairani K., M.Farm</h3>
                    <p class="ins-card-tag">Kosmetik &amp; Kecantikan</p>
                    <a href="{{ route('course.index') }}" class="ins-card-btn" style="color:#be185d;">
                        Mulai Belajar <i class="fas fa-arrow-right" style="font-size:.6rem;"></i>
                    </a>
                </div>
                <img src="{{ asset('assets/s-cahya.png') }}" alt="Apt. Cahya Khairani K., M.Farm" class="ins-card-photo" onerror="this.style.display='none'">
            </div>

            <div class="ins-card rv rv-d4" style="background:linear-gradient(135deg,#1d4ed8,#1e3a8a);">
                <div class="ins-card-inner">
                    <img src="{{ asset('assets/logo-amaizing.png') }}" alt="Wendra Wilendra M.MT." class="ins-card-logo" onerror="this.style.display='none'">
                    <h3 class="ins-card-name">Wendra Wilendra M.MT.</h3>
                    <p class="ins-card-tag">Teknologi &amp; AI</p>
                    <a href="{{ route('course.index') }}" class="ins-card-btn" style="color:#1d4ed8;">
                        Mulai Belajar <i class="fas fa-arrow-right" style="font-size:.6rem;"></i>
                    </a>
                </div>
                <img src="{{ asset('assets/s-wendra_new.png') }}" alt="Wendra Wilendra M.MT." class="ins-card-photo" onerror="this.style.display='none'">
            </div>

            <div class="ins-card rv rv-d4" style="background:linear-gradient(135deg,#0891b2,#0e7490);">
                <div class="ins-card-inner">
                    <img src="{{ asset('assets/logo-sobat-anak.png') }}" alt="dr. Frecillia Regina, Sp.A" class="ins-card-logo" onerror="this.style.display='none'">
                    <h3 class="ins-card-name">dr. Frecillia Regina, Sp.A</h3>
                    <p class="ins-card-tag">Kesehatan Anak</p>
                    <a href="{{ route('course.index') }}" class="ins-card-btn" style="color:#0891b2;">
                        Mulai Belajar <i class="fas fa-arrow-right" style="font-size:.6rem;"></i>
                    </a>
                </div>
                <img src="{{ asset('assets/s-fricil-1.png') }}" alt="dr. Frecillia Regina, Sp.A" class="ins-card-photo" onerror="this.style.display='none'">
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     7. ARTIKEL — FIXED kosong
═══════════════════════════════ --}}
<section class="articles-section">
    <div class="wrap">
        <div class="art-head">
            <div>
                <h2>Artikel terbaru</h2>
                <p>Tips dan wawasan dari instruktur Ray Academy.</p>
            </div>
            <a href="{{ route('article.index') }}">Lihat semua artikel <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="ac-grid">
            @foreach($latestArticles as $article)
                @include('article._card')
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     9. FAQ
═══════════════════════════════ --}}
<section class="faq-section" id="faq">
    <div class="wrap">
        <h2>Pertanyaan yang sering diajukan</h2>
        <div class="faq-box rv">
            @php
            $faqs=[
                ['q'=>'Bagaimana cara belajar di Ray Academy?','a'=>'Setiap kelas terbagi menjadi beberapa modul. Di setiap modul kamu mengerjakan pre-test, mempelajari materi video dan bacaan secara berurutan, lalu mengerjakan post-test. Modul berikutnya terbuka setelah post-test lulus.'],
                ['q'=>'Apakah ada kelas gratis?','a'=>'Ada. Kelas berlabel Gratis bisa langsung diikuti setelah mendaftar akun, tanpa pembayaran.'],
                ['q'=>'Bagaimana cara membayar kelas berbayar?','a'=>'Pembayaran diproses lewat Midtrans. Kamu bisa membayar dengan virtual account bank, QRIS, e-wallet, atau kartu. Kelas langsung terbuka setelah pembayaran berhasil.'],
                ['q'=>'Berapa lama akses kelas yang sudah saya ikuti?','a'=>'Tidak ada batas waktu. Kelas yang sudah kamu ikuti tetap bisa dibuka kapan saja dari halaman Kursus Saya.'],
                ['q'=>'Bisakah saya belajar dari smartphone?','a'=>'Bisa. Ray Academy bisa dibuka dari smartphone, tablet, maupun laptop. Progres belajarmu tersimpan, jadi kamu bisa lanjut di perangkat lain.'],
                ['q'=>'Kapan nilai tes saya keluar?','a'=>'Nilai langsung keluar setelah tes dikirim. Jawaban essay dinilai otomatis dengan mencocokkan maknanya ke kunci jawaban. Kalau sistem penilai sedang tidak tersedia, essay diperiksa oleh tim kami dan nilainya menyusul.'],
            ];
            @endphp
            @foreach($faqs as $faq)
            <div class="faq-item" onclick="toggleFAQ(this)">
                <div class="faq-q">
                    <h3>{{ $faq['q'] }}</h3>
                    <div class="faq-chevron"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
                </div>
                <div class="faq-ans"><p>{{ $faq['a'] }}</p></div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     10. PARTNERS
═══════════════════════════════ --}}
<section class="partners-section">
    <div class="wrap rv">
        <h2>Bekerja sama dengan universitas dan lembaga pelatihan</h2>
        <div class="partners-logos">
            <img src="{{ asset('img/logo-unpad.png') }}" alt="Universitas Padjadjaran" onerror="this.style.display='none'">
            <img src="{{ asset('img/logo-farmasi-unpad.jpg') }}" alt="Farmasi Unpad" onerror="this.style.display='none'">
            <img src="{{ asset('img/logo-labcos.png') }}" alt="Labcos" onerror="this.style.display='none'">
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     11. CTA BOTTOM
═══════════════════════════════ --}}
<section class="cta-section">
    <div class="wrap">
        <div class="cta-inner rv">
            <h2>Siap mulai belajar hari ini?</h2>
            <p>Daftar gratis, pilih kelas, dan mulai dari modul pertama. Progresmu tersimpan dan bisa dilanjutkan kapan saja.</p>
            @guest
            <a href="{{ route('register') }}" class="btn-primary-ra">Daftar Gratis Sekarang</a>
            <p class="cta-subtext">Gratis daftar &middot; Ada kelas gratis untuk mulai</p>
            @else
            <a href="{{ route('dashboard') }}" class="btn-primary-ra">Dashboard Saya</a>
            @endguest
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    /* Scroll reveal */
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting){e.target.classList.add('in');obs.unobserve(e.target);} });
    }, { threshold:0.06, rootMargin:'0px 0px -40px 0px' });
    document.querySelectorAll('.rv').forEach(el => obs.observe(el));

    /* FAQ */
    window.toggleFAQ = function(item) {
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item').forEach(f => f.classList.remove('open'));
        if(!isOpen) item.classList.add('open');
    };
});
</script>
@include('partials.tour-assets')
<script>
{{-- Plain-words tours, so visitors of any age know where to start. Guests get a short look at the menu;
     after logging in the full "how to start learning" tour opens once. Hidden elements are skipped, so both work on phones. --}}
@guest
KATour.page('home-guest', [
  { popover: { title: 'Selamat datang di Ray Academy 👋', description: 'Tempat belajar online langsung dari praktisi dan akademisi. Yuk, kenalan sebentar dengan menu di atas.' } },
  { element: '.nav-links > li:nth-child(2)', popover: { title: 'Kursus', description: 'Semua kelas ada di sini, dari bisnis sampai teknologi. Ada yang gratis dan ada yang berbayar.', side: 'bottom' } },
  { element: '.nav-links > li:nth-child(3)', popover: { title: 'Event', description: 'Kelas praktik dan workshop gratis, offline maupun online. Daftar, lalu tunjukkan tiketnya saat datang.', side: 'bottom' } },
  { element: '.nav-links > li:nth-child(4)', popover: { title: 'Artikel', description: 'Bacaan dan tips gratis dari instruktur kami. Bisa dibaca tanpa akun.', side: 'bottom' } },
  { element: '.btn-nav-cta', popover: { title: 'Daftar gratis', description: 'Belum punya akun? Daftar di sini cukup dengan nama, email, dan kata sandi. Gratis.', side: 'bottom' } },
  { element: '.btn-nav-login', popover: { title: 'Masuk', description: 'Sudah punya akun? Masuk di sini. Setelah masuk, kami tunjukkan cara mulai belajar.', side: 'bottom' } },
  { element: '.nav-mobile-btn', popover: { title: 'Menu', description: 'Menu Kursus, Artikel, Daftar, dan Masuk ada di tombol ini.', side: 'bottom' } }
]);
@else
KATour.page('home-member', [
  { popover: { title: {{ Js::from('Halo, '.e(Str::words(auth()->user()->name, 1, '')).' 👋') }}, description: 'Kamu sudah masuk. Ini cara mulai belajar di Ray Academy, hanya sekitar 1 menit.<br><br>Klik <b>Lanjut</b> untuk mulai, atau tanda <b>×</b> untuk menutup.' } },
  { element: '.nav-search', popover: { title: 'Cari kelas', description: 'Ketik topik yang ingin kamu pelajari di sini, misalnya <i>marketing</i>, lalu tekan Enter.', side: 'bottom' } },
  { element: '.browse-tabs', popover: { title: 'Pilih bidang yang kamu minati', description: 'Klik salah satu bidang. Kelas di bidang itu langsung muncul di bawahnya.', side: 'bottom' } },
  { element: '.browse-section .cc', popover: { title: 'Buka kelas', description: 'Klik kartu kelas untuk melihat isi materi, harga, dan video cuplikannya. Label <b>Kelas Gratis</b> artinya bisa diikuti tanpa bayar.', side: 'right' } },
  { element: '.nav-avatar', popover: { title: 'Kelas yang kamu ikuti', description: 'Klik namamu di sini, lalu pilih <b>Dashboard Saya</b> untuk melihat kelas yang sudah kamu ikuti dan melanjutkan belajar.', side: 'bottom' } },
  { element: '.nav-mobile-btn', popover: { title: 'Menu', description: 'Semua menu (Kursus, Artikel, dan akunmu) ada di tombol ini.', side: 'bottom' } },
  { popover: { title: 'Cara belajarnya', description: '1. Pilih kelas, lalu daftar.<br>2. Kerjakan pre-test singkat.<br>3. Tonton video dan baca materi secara berurutan.<br>4. Kerjakan post-test. Kalau lulus, modul berikutnya terbuka.<br><br>Lupa caranya? Klik tombol <b>Panduan</b> di pojok kanan bawah kapan saja.' } }
]);
@endguest
</script>
@endpush