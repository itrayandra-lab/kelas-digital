@extends('layouts.app')

@section('title', config('app.name') . ' — Platform Belajar Online Terpercaya')

@push('styles')
<style>
/* ══════════════════════════════════════
   HOME PAGE — Ray Academy Redesign
   Font: Sora (heading) + DM Sans (body)
   ══════════════════════════════════════ */

/* ── Scroll Reveal ── */
.rv { opacity: 0; transform: translateY(28px); transition: opacity .6s ease, transform .6s ease; }
.rv.in { opacity: 1; transform: translateY(0); }
.rv-d1 { transition-delay: .08s; }
.rv-d2 { transition-delay: .16s; }
.rv-d3 { transition-delay: .24s; }
.rv-d4 { transition-delay: .32s; }

/* ── Section label ── */
.section-tag {
    display: inline-flex; align-items: center; gap: .5rem;
    font-size: .72rem; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: var(--blue);
    background: var(--blue-xl); border: 1px solid rgba(20,116,188,.18);
    padding: .35rem .85rem; border-radius: 999px;
    margin-bottom: 1rem;
}
.section-tag-white {
    display: inline-flex; align-items: center; gap: .5rem;
    font-size: .72rem; font-weight: 700; letter-spacing: .1em;
    text-transform: uppercase; color: rgba(255,255,255,.8);
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
    padding: .35rem .85rem; border-radius: 999px;
    margin-bottom: 1rem;
}

/* ══════ 1. HERO ══════ */
.hero {
    background: var(--ink);
    position: relative; overflow: hidden;
    padding: 6rem 0 0;
}
.hero::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 80% 60% at 70% 0%, rgba(20,116,188,.35) 0%, transparent 60%),
        radial-gradient(ellipse 50% 50% at 10% 80%, rgba(16,185,129,.15) 0%, transparent 60%);
    pointer-events: none;
}
/* Dot grid */
.hero::after {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.06) 1px, transparent 1px);
    background-size: 32px 32px;
    pointer-events: none;
}
.hero-inner {
    position: relative; z-index: 1;
    max-width: 1280px; margin: 0 auto;
    padding: 0 1.5rem;
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 4rem; align-items: end;
}
@media (max-width: 900px) {
    .hero-inner { grid-template-columns: 1fr; gap: 3rem; padding-bottom: 3rem; }
    .hero-right { display: none; }
}

/* Badge */
.hero-badge {
    display: inline-flex; align-items: center; gap: .5rem;
    background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15);
    backdrop-filter: blur(8px); border-radius: 999px;
    padding: .4rem 1rem; font-size: .78rem; font-weight: 600; color: rgba(255,255,255,.9);
    margin-bottom: 1.75rem;
}
.hero-badge-dot { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; animation: pulse-dot 2s ease-in-out infinite; }
@keyframes pulse-dot { 0%,100%{transform:scale(1)} 50%{transform:scale(1.5);opacity:.6} }

.hero-title {
    font-family: 'Sora', sans-serif;
    font-size: clamp(2.4rem, 5vw, 3.8rem);
    font-weight: 800; color: #fff; line-height: 1.1;
    letter-spacing: -.03em; margin-bottom: 1.5rem;
}
.hero-title em { font-style: normal; color: #38bdf8; }
.hero-title mark {
    background: none; color: var(--accent);
    position: relative; padding: 0 .1em;
}

.hero-desc {
    font-size: 1.05rem; color: rgba(255,255,255,.65);
    line-height: 1.8; max-width: 460px; margin-bottom: 2.5rem;
}

/* Hero stats */
.hero-stats {
    display: flex; gap: 2rem; margin-bottom: 2.5rem;
    flex-wrap: wrap;
}
.hero-stat-num {
    font-family: 'Sora', sans-serif;
    font-size: 1.85rem; font-weight: 800; color: #fff; line-height: 1;
}
.hero-stat-num em { color: #38bdf8; font-style: normal; }
.hero-stat-label { font-size: .72rem; color: rgba(255,255,255,.5); font-weight: 500; margin-top: .25rem; }
.hero-stat-sep { width: 1px; background: rgba(255,255,255,.12); align-self: stretch; }

/* Hero buttons */
.hero-btns { display: flex; gap: 1rem; flex-wrap: wrap; }
.btn-hero-primary {
    display: inline-flex; align-items: center; gap: .5rem;
    background: var(--blue); color: #fff; font-weight: 700;
    font-family: 'DM Sans', sans-serif; font-size: .9375rem;
    padding: .875rem 1.75rem; border-radius: 12px; text-decoration: none;
    border: 1.5px solid var(--blue);
    transition: all .22s; box-shadow: 0 6px 20px rgba(20,116,188,.4);
}
.btn-hero-primary:hover { background: #1a8ad6; border-color: #1a8ad6; transform: translateY(-2px); box-shadow: 0 10px 30px rgba(20,116,188,.5); }
.btn-hero-ghost {
    display: inline-flex; align-items: center; gap: .5rem;
    background: rgba(255,255,255,.08); color: rgba(255,255,255,.9); font-weight: 600;
    font-family: 'DM Sans', sans-serif; font-size: .9375rem;
    padding: .875rem 1.75rem; border-radius: 12px; text-decoration: none;
    border: 1.5px solid rgba(255,255,255,.18);
    transition: all .22s;
}
.btn-hero-ghost:hover { background: rgba(255,255,255,.14); border-color: rgba(255,255,255,.3); transform: translateY(-2px); }

/* Hero right: floating cards */
.hero-right {
    position: relative; padding: 0 1rem 4rem 0;
    display: flex; flex-direction: column; gap: 1rem;
}
.hero-preview-card {
    background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);
    backdrop-filter: blur(12px);
    border-radius: 18px; overflow: hidden;
    box-shadow: 0 24px 60px rgba(0,0,0,.4);
    flex-shrink: 0;
}
.hero-preview-card img {
    width: 100%; display: block; border-radius: 14px 14px 0 0;
    height: 200px; object-fit: cover;
}
.hero-card-info {
    padding: 1rem 1.25rem;
}
.hero-card-info h4 { font-family: 'Sora', sans-serif; font-size: .9rem; font-weight: 700; color: #fff; }
.hero-card-info p { font-size: .75rem; color: rgba(255,255,255,.5); margin-top: .25rem; }
.hero-card-meta { display: flex; align-items: center; gap: .75rem; margin-top: .6rem; }
.hero-card-pill {
    font-size: .68rem; font-weight: 700; padding: .25rem .65rem;
    border-radius: 6px; background: rgba(16,185,129,.2); color: #34d399;
}
.hero-card-rating { display: flex; gap: 1px; }
.hero-card-rating svg { width: 11px; height: 11px; color: #fbbf24; fill: #fbbf24; }

/* Floating mini cards */
.hero-float-card {
    position: absolute; right: -1.5rem;
    background: #fff; border-radius: 14px;
    padding: .85rem 1.1rem;
    box-shadow: 0 16px 48px rgba(0,0,0,.25);
    display: flex; align-items: center; gap: .75rem;
    z-index: 2; white-space: nowrap;
    animation: float-y 4s ease-in-out infinite;
}
.hero-float-card:nth-child(2) { top: 15%; animation-delay: -.5s; }
.hero-float-card:nth-child(3) { bottom: 22%; animation-delay: -1.5s; }
@keyframes float-y { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
.hf-icon { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.hf-label { font-size: .72rem; color: var(--muted); }
.hf-num { font-family: 'Sora', sans-serif; font-weight: 800; font-size: .95rem; color: var(--ink); }

/* ══════ 2. TRUST BAR ══════ */
.trust-bar {
    background: #fff; border-bottom: 1px solid var(--border);
    padding: 1.75rem 0;
}
.trust-bar-inner {
    max-width: 1280px; margin: 0 auto; padding: 0 1.5rem;
    display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; justify-content: center;
}
.trust-bar-label { font-size: .75rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .08em; white-space: nowrap; flex-shrink: 0; }
.trust-bar-sep { width: 1px; height: 24px; background: var(--border); flex-shrink: 0; }
.trust-logos { display: flex; align-items: center; gap: 2.5rem; flex-wrap: wrap; justify-content: center; }
.trust-logos span { font-family: 'Sora', sans-serif; font-size: .95rem; font-weight: 700; color: var(--muted); opacity: .6; }

/* ══════ 3. COURSES ══════ */
.courses-section {
    background: var(--surf);
    padding: 6rem 0;
}
.section-header {
    display: flex; align-items: flex-end; justify-content: space-between; gap: 2rem;
    margin-bottom: 2.5rem;
    flex-wrap: wrap;
}
.section-header-left h2 {
    font-family: 'Sora', sans-serif;
    font-size: clamp(1.65rem, 3vw, 2.4rem);
    font-weight: 800; color: var(--ink);
    line-height: 1.2; letter-spacing: -.02em;
}
.section-header-left p { font-size: .95rem; color: var(--muted); margin-top: .5rem; line-height: 1.7; }
.btn-see-all {
    display: inline-flex; align-items: center; gap: .4rem;
    font-size: .875rem; font-weight: 700; color: var(--blue);
    text-decoration: none; padding: .6rem 1.25rem;
    border: 1.5px solid rgba(20,116,188,.25); border-radius: 10px;
    background: rgba(20,116,188,.05); transition: all .2s; flex-shrink: 0; white-space: nowrap;
}
.btn-see-all:hover { background: rgba(20,116,188,.1); border-color: var(--blue); transform: translateX(3px); }

/* Filter tabs */
.filter-bar {
    display: flex; gap: .5rem; flex-wrap: wrap;
    margin-bottom: 2rem;
}
.f-btn {
    font-family: 'DM Sans', sans-serif;
    font-size: .83rem; font-weight: 600; cursor: pointer;
    padding: .5rem 1.1rem; border-radius: 9px; border: 1.5px solid var(--border);
    background: #fff; color: var(--ink-2); transition: all .18s;
}
.f-btn:hover { border-color: var(--blue); color: var(--blue); }
.f-btn.active { background: var(--blue); color: #fff; border-color: var(--blue); box-shadow: 0 4px 14px rgba(20,116,188,.28); }

/* Course Grid */
.course-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 1.25rem; }

.crs-card {
    background: #fff; border: 1.5px solid var(--border); border-radius: 16px;
    overflow: hidden; display: flex; flex-direction: column;
    text-decoration: none; color: inherit;
    transition: transform .3s ease, box-shadow .3s ease, border-color .25s;
}
.crs-card:hover { transform: translateY(-5px); box-shadow: 0 20px 48px rgba(10,22,40,.1); border-color: #93c5fd; }

.crs-thumb {
    position: relative; overflow: hidden;
    aspect-ratio: 16/9; background: linear-gradient(135deg, #dbeafe, #eff6ff);
}
.crs-thumb img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .45s ease; }
.crs-card:hover .crs-thumb img { transform: scale(1.07); }
.crs-thumb-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
}
.crs-badge {
    position: absolute; top: .75rem; left: .75rem;
    font-size: .65rem; font-weight: 700; padding: .3rem .7rem;
    border-radius: 6px; text-transform: uppercase; letter-spacing: .06em;
}
.badge-free { background: #dcfce7; color: #15803d; }
.badge-paid { background: #dbeafe; color: #1d4ed8; }

.crs-body { padding: 1.1rem 1.25rem; flex: 1; display: flex; flex-direction: column; }
.crs-cat { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--blue); margin-bottom: .4rem; }
.crs-title { font-family: 'Sora', sans-serif; font-size: .925rem; font-weight: 700; color: var(--ink); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.crs-meta { display: flex; align-items: center; gap: .5rem; margin-top: auto; padding-top: .85rem; border-top: 1px solid var(--border); margin-top: .85rem; }
.crs-avatar { width: 26px; height: 26px; border-radius: 50%; object-fit: cover; background: var(--blue-xl); flex-shrink: 0; }
.crs-instructor { font-size: .72rem; font-weight: 500; color: var(--muted); flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.crs-price { font-family: 'Sora', sans-serif; font-size: .9rem; font-weight: 800; color: var(--ink); flex-shrink: 0; }
.crs-price-free { color: var(--accent); }
.crs-stars { display: flex; align-items: center; gap: 2px; margin-top: .45rem; }
.crs-stars svg { width: 12px; height: 12px; }
.crs-stars span { font-size: .72rem; font-weight: 600; color: var(--muted); margin-left: .25rem; }

/* ══════ 4. WHY US ══════ */
.why-section { background: #fff; padding: 6rem 0; }
.why-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: center; }
@media (max-width: 768px) { .why-grid { grid-template-columns: 1fr; gap: 3rem; } }

.why-stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.why-stat-card {
    background: var(--surf); border: 1.5px solid var(--border);
    border-radius: 16px; padding: 1.5rem;
    transition: border-color .2s, transform .2s;
}
.why-stat-card:hover { border-color: var(--blue); transform: translateY(-3px); }
.why-stat-card.featured {
    background: linear-gradient(135deg, var(--blue-d), var(--blue));
    border-color: transparent; grid-column: span 2;
    color: #fff; padding: 1.75rem;
}
.why-stat-num {
    font-family: 'Sora', sans-serif;
    font-size: 2.4rem; font-weight: 800; color: var(--ink); line-height: 1;
}
.why-stat-card.featured .why-stat-num { color: #fff; font-size: 2.8rem; }
.why-stat-label { font-size: .78rem; color: var(--muted); margin-top: .35rem; font-weight: 500; }
.why-stat-card.featured .why-stat-label { color: rgba(255,255,255,.65); }
.why-stat-icon {
    width: 40px; height: 40px; border-radius: 11px;
    background: var(--blue-xl); display: flex; align-items: center; justify-content: center;
    margin-bottom: .85rem;
}
.why-stat-card.featured .why-stat-icon { background: rgba(255,255,255,.15); }

.benefit-list { display: flex; flex-direction: column; gap: 1.5rem; margin-top: 2rem; }
.benefit-item { display: flex; gap: 1rem; align-items: flex-start; }
.benefit-icon-wrap {
    flex-shrink: 0; width: 44px; height: 44px; border-radius: 12px;
    background: var(--blue-xl); display: flex; align-items: center; justify-content: center;
    transition: background .2s;
}
.benefit-item:hover .benefit-icon-wrap { background: #bfdbfe; }
.benefit-icon-wrap svg { color: var(--blue); width: 20px; height: 20px; }
.benefit-title { font-family: 'Sora', sans-serif; font-weight: 700; font-size: .9375rem; color: var(--ink); }
.benefit-desc { font-size: .825rem; color: var(--muted); line-height: 1.65; margin-top: .25rem; }

/* ══════ 5. INSTRUCTORS ══════ */
.instructors-section {
    padding: 6rem 0;
    background: var(--ink);
    position: relative; overflow: hidden;
}
.instructors-section::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 60% 80% at 80% 50%, rgba(20,116,188,.2), transparent 65%);
    pointer-events: none;
}
.instructors-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.25rem; }

.ins-card {
    border-radius: 20px; overflow: hidden; position: relative;
    transition: transform .3s ease, box-shadow .3s ease;
    cursor: default;
}
.ins-card:hover { transform: translateY(-6px); box-shadow: 0 24px 60px rgba(0,0,0,.35); }
.ins-card-inner { padding: 1.5rem 1.5rem 0; position: relative; z-index: 1; }
.ins-card-logo { height: 42px; object-fit: contain; margin-bottom: .9rem; display: block; }
.ins-card-name { font-family: 'Sora', sans-serif; font-size: .95rem; font-weight: 700; color: #fff; line-height: 1.3; }
.ins-card-tag { font-size: .75rem; color: rgba(255,255,255,.55); margin-top: .2rem; }
.ins-card-btn {
    display: inline-block; margin-top: .9rem; padding: .5rem 1.25rem;
    background: rgba(255,255,255,.92); border-radius: 999px;
    font-weight: 700; font-size: .775rem; text-decoration: none;
    transition: opacity .2s; border: none;
}
.ins-card-btn:hover { opacity: .85; }
.ins-card-photo {
    display: block; height: 200px; width: 100%; object-fit: contain; object-position: bottom;
}

/* ══════ 6. ARTICLES ══════ */
.articles-section { background: var(--surf); padding: 6rem 0; }
.articles-grid { display: grid; grid-template-columns: 3fr 2fr; gap: 1.25rem; }
@media (max-width: 900px) { .articles-grid { grid-template-columns: 1fr; } }

.art-featured {
    position: relative; border-radius: 20px; overflow: hidden;
    min-height: 380px; display: flex; flex-direction: column; justify-content: flex-end;
    background: var(--ink); text-decoration: none; cursor: pointer;
}
.art-featured-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; transition: transform .5s; opacity: .75; }
.art-featured:hover .art-featured-img { transform: scale(1.04); opacity: .65; }
.art-featured-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(to top, rgba(0,0,0,.9) 0%, rgba(0,0,0,.2) 55%, transparent 100%);
}
.art-featured-body { position: relative; padding: 2rem; color: #fff; }
.art-featured-cat { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #fde047; margin-bottom: .5rem; }
.art-featured-title { font-family: 'Sora', sans-serif; font-size: 1.25rem; font-weight: 800; line-height: 1.3; max-width: 480px; }
.art-featured-excerpt { font-size: .825rem; color: rgba(255,255,255,.6); margin-top: .5rem; line-height: 1.65; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.art-featured-date { font-size: .7rem; color: rgba(255,255,255,.4); margin-top: .6rem; }

.art-sidebar { display: flex; flex-direction: column; gap: 1rem; }
.art-small {
    display: flex; gap: 1rem; align-items: center;
    background: #fff; border: 1.5px solid var(--border); border-radius: 14px;
    overflow: hidden; text-decoration: none; color: inherit;
    transition: all .22s; flex: 1;
}
.art-small:hover { box-shadow: 0 8px 28px rgba(10,22,40,.08); border-color: #93c5fd; transform: translateX(4px); }
.art-small-thumb {
    flex-shrink: 0; width: 100px; height: 80px; overflow: hidden;
    background: linear-gradient(135deg, #dbeafe, #eff6ff);
}
.art-small-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform .3s; }
.art-small:hover .art-small-thumb img { transform: scale(1.08); }
.art-small-body { flex: 1; padding: .75rem 1rem .75rem 0; min-width: 0; }
.art-small-cat { font-size: .63rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--blue); }
.art-small-title { font-family: 'Sora', sans-serif; font-size: .825rem; font-weight: 700; color: var(--ink); margin-top: .2rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.art-small-date { font-size: .67rem; color: var(--muted); margin-top: .3rem; }

/* ══════ 7. CTA ══════ */
.cta-section {
    background: linear-gradient(135deg, #0a1628 0%, #0d3b72 50%, #1474bc 100%);
    padding: 6rem 0; position: relative; overflow: hidden;
}
.cta-section::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.07) 1px, transparent 1px);
    background-size: 24px 24px;
    pointer-events: none;
}
.cta-section::after {
    content: '';
    position: absolute; top: -100px; right: -100px;
    width: 400px; height: 400px; border-radius: 50%;
    background: radial-gradient(circle, rgba(255,255,255,.07), transparent 65%);
    pointer-events: none;
}
.cta-inner { position: relative; z-index: 1; text-align: center; color: #fff; max-width: 620px; margin: 0 auto; padding: 0 1.5rem; }
.cta-inner h2 {
    font-family: 'Sora', sans-serif;
    font-size: clamp(2rem, 4vw, 3rem); font-weight: 800; line-height: 1.2; letter-spacing: -.03em;
    margin-bottom: 1.25rem;
}
.cta-inner p { font-size: 1.05rem; color: rgba(255,255,255,.65); line-height: 1.75; margin-bottom: 2.5rem; max-width: 460px; margin-left: auto; margin-right: auto; }
.cta-btns { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
.btn-cta-primary {
    display: inline-flex; align-items: center; gap: .5rem;
    background: #fff; color: var(--blue); font-weight: 800;
    font-family: 'DM Sans', sans-serif; font-size: .9375rem;
    padding: .9rem 2rem; border-radius: 12px; text-decoration: none;
    transition: all .22s; box-shadow: 0 6px 24px rgba(0,0,0,.2);
}
.btn-cta-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 36px rgba(0,0,0,.3); }
.btn-cta-ghost {
    display: inline-flex; align-items: center; gap: .5rem;
    background: rgba(255,255,255,.1); color: #fff; font-weight: 600;
    font-family: 'DM Sans', sans-serif; font-size: .9375rem;
    padding: .9rem 2rem; border-radius: 12px; text-decoration: none;
    border: 1.5px solid rgba(255,255,255,.22); transition: all .22s;
}
.btn-cta-ghost:hover { background: rgba(255,255,255,.18); transform: translateY(-2px); }

/* Shared max-width wrapper */
.wrap { max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; }
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════
     1. HERO
══════════════════════════════════════════════ --}}
<section class="hero">
    <div class="hero-inner">

        {{-- Left --}}
        <div class="hero-left rv" id="hero-left" style="padding-bottom:5rem;">
            <div class="hero-badge">
                <span class="hero-badge-dot"></span>
                Platform Belajar Online Terpercaya
            </div>

            <h1 class="hero-title">
                Kembangkan<br>
                <em>Skill-mu</em><br>
                <mark>Bersama Para Ahli</mark>
            </h1>

            <p class="hero-desc">
                Akses ratusan kursus berkualitas tinggi dari instruktur berpengalaman.
                Belajar kapan saja, di mana saja — dan raih karir impianmu.
            </p>

            <div class="hero-stats">
                <div>
                    <div class="hero-stat-num">{{ number_format($stats['total_courses']) }}<em>+</em></div>
                    <div class="hero-stat-label">Kursus Tersedia</div>
                </div>
                <div class="hero-stat-sep"></div>
                <div>
                    <div class="hero-stat-num">{{ number_format($stats['total_students']) }}<em>+</em></div>
                    <div class="hero-stat-label">Pelajar Aktif</div>
                </div>
                <div class="hero-stat-sep"></div>
                <div>
                    <div class="hero-stat-num">{{ number_format($stats['total_articles']) }}<em>+</em></div>
                    <div class="hero-stat-label">Artikel & Tips</div>
                </div>
            </div>

            <div class="hero-btns">
                <a href="{{ route('course.index') }}" class="btn-hero-primary">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Mulai Belajar
                </a>
                <a href="{{ route('article.index') }}" class="btn-hero-ghost">
                    Jelajahi Artikel
                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════════════
     2. TRUST BAR
══════════════════════════════════════════════ --}}
<div class="trust-bar">
    <div class="trust-bar-inner">
        <span class="trust-bar-label">Dipercaya alumni dari</span>
        <div class="trust-bar-sep"></div>
        <div class="trust-logos">
            <span>Microsoft</span>
            <span>Google</span>
            <span>Tokopedia</span>
            <span>Gojek</span>
            <span>Bukalapak</span>
            <span>Grab</span>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════
     3. COURSES
══════════════════════════════════════════════ --}}
<section class="courses-section" id="kursus">
    <div class="wrap">

        <div class="section-header rv">
            <div class="section-header-left">
                <span class="section-tag">Kursus Terpopuler</span>
                <h2>Mulai dari Yang Kamu<br>Minati</h2>
                <p>Pilihan kursus terbaik dari instruktur berpengalaman di bidangnya.</p>
            </div>
            <a href="{{ route('course.index') }}" class="btn-see-all">
                Lihat Semua
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        {{-- Category Tabs --}}
        @if(isset($courseCategories) && $courseCategories->count())
        <div class="filter-bar rv">
            <button class="f-btn active" onclick="filterCourses(this, 'all')">Semua</button>
            @foreach($courseCategories->take(6) as $cat)
                <button class="f-btn" onclick="filterCourses(this, '{{ $cat->slug }}')">{{ $cat->name }}</button>
            @endforeach
        </div>
        @endif

        <div class="course-grid" id="course-grid">
            @forelse($featuredCourses ?? [] as $course)
            <a href="{{ route('course.show', $course->slug) }}"
               class="crs-card rv rv-d{{ min($loop->index % 4 + 1, 4) }}"
               data-cat="{{ $course->category->slug ?? '' }}">

                <div class="crs-thumb">
                    @if($course->thumbnail)
                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                        <div class="crs-thumb-placeholder" style="display:none;">
                            <svg style="width:36px;height:36px;color:var(--blue);opacity:.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                    @else
                        <div class="crs-thumb-placeholder">
                            <svg style="width:36px;height:36px;color:var(--blue);opacity:.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    <span class="crs-badge {{ ($course->price ?? 0) > 0 ? 'badge-paid' : 'badge-free' }}">
                        {{ ($course->price ?? 0) > 0 ? 'Premium' : 'Gratis' }}
                    </span>
                </div>

                <div class="crs-body">
                    @if($course->category)
                        <span class="crs-cat">{{ $course->category->name }}</span>
                    @endif
                    <h3 class="crs-title">{{ $course->title }}</h3>

                    @if($course->rating ?? null)
                    <div class="crs-stars" style="margin-top:.4rem;">
                        @for($i = 1; $i <= 5; $i++)
                        <svg style="width:12px;height:12px;fill:{{ $i <= round($course->rating) ? '#fbbf24' : 'none' }};stroke:#fbbf24;stroke-width:1.5;" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        @endfor
                        <span>{{ number_format($course->rating, 1) }}</span>
                    </div>
                    @endif

                    <div class="crs-meta">
                        @if($course->instructor->avatar ?? null)
                            <img src="{{ $course->instructor->avatar }}" alt="{{ $course->instructor->name ?? '' }}" class="crs-avatar" onerror="this.style.background='var(--blue-xl)';this.src=''">
                        @else
                            <div class="crs-avatar" style="display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:700;color:var(--blue);">
                                {{ strtoupper(substr($course->instructor->name ?? 'I', 0, 1)) }}
                            </div>
                        @endif
                        <span class="crs-instructor">{{ $course->instructor->name ?? 'Instruktur' }}</span>
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
            @empty
            {{-- Skeleton placeholder --}}
            @for($i = 0; $i < 8; $i++)
            <div class="crs-card" style="pointer-events:none;">
                <div class="crs-thumb" style="background:var(--border);"></div>
                <div class="crs-body">
                    <div style="height:10px;background:var(--border);border-radius:4px;width:40%;margin-bottom:.5rem;"></div>
                    <div style="height:14px;background:var(--border);border-radius:4px;margin-bottom:.3rem;"></div>
                    <div style="height:14px;background:var(--border);border-radius:4px;width:70%;"></div>
                </div>
            </div>
            @endfor
            @endforelse
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════════════
     4. WHY US
══════════════════════════════════════════════ --}}
<section class="why-section" id="kenapa-kami">
    <div class="wrap">
        <div class="why-grid">

            {{-- Stats grid --}}
            <div class="why-stats-grid rv">
                <div class="why-stat-card featured">
                    <div class="why-stat-icon">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                    </div>
                    <div class="why-stat-num">{{ number_format($stats['total_students']) }}+</div>
                    <div class="why-stat-label">Pelajar aktif yang sudah bergabung bersama kami</div>
                </div>
                <div class="why-stat-card">
                    <div class="why-stat-icon" style="background:#fef3c7;">
                        <svg style="width:20px;height:20px;color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div class="why-stat-num" style="color:var(--ink);">{{ number_format($stats['total_courses']) }}+</div>
                    <div class="why-stat-label">Kursus berkualitas tersedia</div>
                </div>
                <div class="why-stat-card">
                    <div class="why-stat-icon" style="background:#f0fdf4;">
                        <svg style="width:20px;height:20px;color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="why-stat-num" style="color:var(--ink);">Lifetime</div>
                    <div class="why-stat-label">Akses materi seumur hidup</div>
                </div>
                <div class="why-stat-card">
                    <div class="why-stat-icon" style="background:#fdf4ff;">
                        <svg style="width:20px;height:20px;color:#9333ea;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <div class="why-stat-num" style="color:var(--ink);">{{ number_format($stats['total_articles']) }}+</div>
                    <div class="why-stat-label">Artikel & tips eksklusif</div>
                </div>
            </div>

            {{-- Benefits --}}
            <div class="rv rv-d2">
                <span class="section-tag">Kenapa Ray Academy?</span>
                <h2 style="font-family:'Sora',sans-serif;font-size:clamp(1.65rem,3vw,2.4rem);font-weight:800;color:var(--ink);line-height:1.2;letter-spacing:-.02em;margin-top:.5rem;margin-bottom:0;">
                    Pengalaman Belajar<br>yang Berbeda dari Biasanya
                </h2>

                <div class="benefit-list">
                    @foreach([
                        ['p'=>'M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 't'=>'Video HD Berkualitas Tinggi', 'd'=>'Setiap materi disajikan dalam video resolusi tinggi agar mudah dipahami dan diingat.'],
                        ['p'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 't'=>'Sertifikat Resmi Terverifikasi', 'd'=>'Dapatkan sertifikat yang diakui dan bisa langsung kamu cantumkan di portofolio.'],
                        ['p'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0', 't'=>'Komunitas Belajar Aktif', 'd'=>'Bergabung dengan ribuan pelajar dan berdiskusi langsung bersama instruktur.'],
                        ['p'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 't'=>'Belajar Kapan Saja & Di Mana Saja', 'd'=>'Akses semua materi kapan saja dan di mana saja tanpa batasan waktu.'],
                    ] as $b)
                    <div class="benefit-item">
                        <div class="benefit-icon-wrap">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $b['p'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <div class="benefit-title">{{ $b['t'] }}</div>
                            <div class="benefit-desc">{{ $b['d'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════════════
     5. INSTRUCTORS
══════════════════════════════════════════════ --}}
<section class="instructors-section" id="instruktur">
    <div class="wrap" style="position:relative;z-index:1;">

        <div style="text-align:center;margin-bottom:3.5rem;" class="rv">
            <span class="section-tag-white">Instruktur Kami</span>
            <h2 style="font-family:'Sora',sans-serif;font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:800;color:#fff;letter-spacing:-.02em;margin-top:.5rem;">Belajar dari Para Ahli</h2>
            <p style="color:rgba(255,255,255,.55);margin-top:.6rem;font-size:.95rem;max-width:420px;margin-left:auto;margin-right:auto;line-height:1.75;">
                Instruktur berpengalaman yang siap membimbing perjalanan pembelajaran Anda
            </p>
        </div>

        <div class="instructors-grid">
            @foreach([
                ['bg'=>'linear-gradient(135deg,#ff5733,#c0392b)','logo'=>'assets/logo-do better class.png','photo'=>'assets/s-ria.png','name'=>'Ria R. Christiana SE, MBA.','tag'=>'Business & Branding','link'=>'#!','c'=>'#c0392b'],
                ['bg'=>'linear-gradient(135deg,#7c3aed,#5b21b6)','logo'=>'assets/logo-psikologi bisnis.png','photo'=>'assets/s-sukmayanti.png','name'=>'Sukmayanti Ranadireksa, M.Psi.','tag'=>'Psikologi & Komunikasi','link'=>'#!','c'=>'#7c3aed'],
                ['bg'=>'linear-gradient(135deg,#db2777,#9d174d)','logo'=>'assets/logo-ski.png','photo'=>'assets/s-cahya.png','name'=>'Apt. Cahya Khairani K., M.Farm','tag'=>'Kosmetik & Kecantikan','link'=>'#!','c'=>'#be185d'],
                ['bg'=>'linear-gradient(135deg,#1d4ed8,#1e3a8a)','logo'=>'assets/logo-amaizing.png','photo'=>'assets/s-wendra.png','name'=>'Wendra Wilendra M.MT.','tag'=>'Teknologi & AI','link'=>'#!','c'=>'#1d4ed8'],
                ['bg'=>'linear-gradient(135deg,#0891b2,#0e7490)','logo'=>'assets/logo-sobat-anak.png','photo'=>'assets/s-fricil-1.png','name'=>'dr. Frecillia Regina, Sp.A','tag'=>'Kesehatan Anak','link'=>'https://rayacademy.id/sobat-anak/','c'=>'#0891b2'],
            ] as $idx => $ins)
            <div class="ins-card rv rv-d{{ min($idx+1,4) }}" style="background:{{ $ins['bg'] }};">
                <div class="ins-card-inner">
                    <img src="{{ asset($ins['logo']) }}" alt="{{ $ins['name'] }}" class="ins-card-logo"
                         onerror="this.style.display='none'">
                    <h3 class="ins-card-name">{{ $ins['name'] }}</h3>
                    <p class="ins-card-tag">{{ $ins['tag'] }}</p>
                    <a href="{{ $ins['link'] }}" class="ins-card-btn" style="color:{{ $ins['c'] }};">
                        Mulai Belajar →
                    </a>
                </div>
                <img src="{{ asset($ins['photo']) }}" alt="{{ $ins['name'] }}"
                     class="ins-card-photo"
                     onerror="this.style.display='none'">
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- ══════════════════════════════════════════════
     6. ARTICLES
══════════════════════════════════════════════ --}}
@if($latestArticles->isNotEmpty())
<section class="articles-section" id="artikel">
    <div class="wrap">

        <div class="section-header rv">
            <div class="section-header-left">
                <span class="section-tag">Tips & Wawasan</span>
                <h2>Artikel & Tips Terbaru</h2>
                <p>Tingkatkan pengetahuanmu dengan konten berkualitas dari para ahli.</p>
            </div>
            <a href="{{ route('article.index') }}" class="btn-see-all">
                Lihat Semua
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <div class="articles-grid">
            @php $featured = $latestArticles->first(); @endphp

            {{-- Featured article --}}
            <a href="{{ route('article.show', $featured->slug) }}" class="art-featured rv">
                @if($featured->thumbnail ?? $featured->cover_image ?? null)
                    <img src="{{ $featured->thumbnail ?? $featured->cover_image }}" alt="{{ $featured->title }}" class="art-featured-img">
                @else
                    <div style="position:absolute;inset:0;background:linear-gradient(135deg,#0a1628,#1474bc);"></div>
                @endif
                <div class="art-featured-overlay"></div>
                <div class="art-featured-body">
                    @if($featured->categories->first())
                        <div class="art-featured-cat">{{ $featured->categories->first()->name }}</div>
                    @endif
                    <h3 class="art-featured-title">{{ $featured->title }}</h3>
                    @if($featured->excerpt ?? $featured->description ?? null)
                        <p class="art-featured-excerpt">{{ $featured->excerpt ?? $featured->description }}</p>
                    @endif
                    <p class="art-featured-date">
                        @if($featured->published_at) {{ $featured->published_at->isoFormat('D MMM YYYY') }} @endif
                        @if($featured->views_count ?? null) · {{ number_format($featured->views_count) }} views @endif
                    </p>
                </div>
            </a>

            {{-- Sidebar articles --}}
            <div class="art-sidebar">
                @foreach($latestArticles->skip(1)->take(3) as $article)
                <a href="{{ route('article.show', $article->slug) }}" class="art-small rv rv-d{{ $loop->index + 1 }}">
                    <div class="art-small-thumb">
                        @if($article->thumbnail ?? $article->cover_image ?? null)
                            <img src="{{ $article->thumbnail ?? $article->cover_image }}" alt="{{ $article->title }}">
                        @else
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                                <svg style="width:20px;height:20px;color:var(--blue);opacity:.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="art-small-body">
                        @if($article->categories->first())
                            <span class="art-small-cat">{{ $article->categories->first()->name }}</span>
                        @endif
                        <h3 class="art-small-title">{{ $article->title }}</h3>
                        @if($article->published_at)
                            <p class="art-small-date">{{ $article->published_at->isoFormat('D MMM YYYY') }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif


{{-- ══════════════════════════════════════════════
     7. CTA
══════════════════════════════════════════════ --}}
<section class="cta-section">
    <div class="cta-inner rv">
        <span class="section-tag-white">Mulai Sekarang</span>
        <h2>Siap Memulai Perjalanan<br>Belajarmu?</h2>
        <p>
            Bergabunglah bersama ribuan pelajar yang sudah meningkatkan skill mereka
            bersama instruktur terbaik kami. Gratis untuk mulai!
        </p>
        <div class="cta-btns">
            <a href="{{ route('course.index') }}" class="btn-cta-primary">
                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Lihat Semua Kursus
            </a>
            @guest
            <a href="{{ route('register') }}" class="btn-cta-ghost">
                Daftar Gratis Sekarang
            </a>
            @endguest
        </div>
    </div>
</section>

@endsection

@push('scripts')
{{-- Splide slider --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide-core.min.css">
<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>

<style>
    /* Splide pagination dots */
    .splide__pagination__page { background: rgba(255,255,255,.35) !important; }
    .splide__pagination__page.is-active { background: #fff !important; transform: scale(1.4) !important; }
    .splide__pagination { bottom: -1.75rem !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ── Scroll reveal ── */
    const obs = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('in'); obs.unobserve(e.target); }
        });
    }, { threshold: 0.07, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.rv').forEach(el => obs.observe(el));

    /* Trigger hero immediately */
    setTimeout(() => {
        document.getElementById('hero-left')?.classList.add('in');
        setTimeout(() => document.getElementById('hero-right')?.classList.add('in'), 200);
    }, 60);

    /* ── Splide hero slider ── */
    const sliderEl = document.getElementById('hero-slider');
    if (sliderEl && typeof Splide !== 'undefined') {
        new Splide(sliderEl, {
            type: 'loop', perPage: 1, autoplay: true,
            interval: 4500, pauseOnHover: true,
            arrows: false, pagination: true, speed: 700, gap: 0,
        }).mount();
    }

    /* ── Course filter ── */
    window.filterCourses = function(btn, cat) {
        document.querySelectorAll('.f-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.crs-card').forEach(card => {
            const show = cat === 'all' || (card.dataset.cat === cat);
            card.style.display = show ? '' : 'none';
        });
    };
});
</script>
@endpush