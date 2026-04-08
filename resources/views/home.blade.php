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

/* ══════ 1. HERO - HI-TECH REDESIGN V2 ══════ */
.hero {
    background: var(--ink);
    position: relative;
    overflow: hidden;
    padding: 6rem 0 4rem;
    min-height: 85vh;
    display: flex;
    align-items: center;
}

/* Animated gradient background */
.hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse 80% 60% at 70% 0%, rgba(20,116,188,.35) 0%, transparent 60%),
        radial-gradient(ellipse 50% 50% at 10% 80%, rgba(16,185,129,.15) 0%, transparent 60%);
    pointer-events: none;
    animation: gradientShift 20s ease-in-out infinite;
}

@keyframes gradientShift {
    0%, 100% { opacity: 1; transform: translateX(0); }
    50% { opacity: 0.8; transform: translateX(20px); }
}

/* Animated dot grid */
.hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(255,255,255,.06) 1px, transparent 1px);
    background-size: 32px 32px;
    pointer-events: none;
    animation: dotMove 40s linear infinite;
}

@keyframes dotMove {
    0% { background-position: 0 0; }
    100% { background-position: 32px 32px; }
}

.hero-inner {
    position: relative;
    z-index: 1;
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1.5rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}

/* Left Content */
.hero-left {
    opacity: 0;
    transform: translateY(40px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.hero-left.in {
    opacity: 1;
    transform: translateY(0);
}

/* Badge with pulse animation */
.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.15);
    backdrop-filter: blur(8px);
    border-radius: 999px;
    padding: .4rem 1rem;
    font-size: .78rem;
    font-weight: 600;
    color: rgba(255,255,255,.9);
    margin-bottom: 1.75rem;
    animation: badgeFloat 3s ease-in-out infinite;
}

@keyframes badgeFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

.hero-badge-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #4ade80;
    animation: pulse-dot 2s ease-in-out infinite;
}

@keyframes pulse-dot {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.5); opacity: .6; }
}

.hero-title {
    font-family: 'Sora', sans-serif;
    font-size: clamp(2.4rem, 5vw, 3.8rem);
    font-weight: 800;
    color: #fff;
    line-height: 1.1;
    letter-spacing: -.03em;
    margin-bottom: 1.5rem;
}

.hero-title em {
    font-style: normal;
    color: #38bdf8;
}

/* Kelap-kelip effect untuk "Para Ahli" - TANPA BORDER */
.hero-title mark {
    background: transparent;
    color: var(--accent);
    position: relative;
    padding: 0;
    animation: blink-text 2s ease-in-out infinite;
    text-shadow: 0 0 20px rgba(16,185,129,.5);
}

@keyframes blink-text {
    0%, 100% { 
        opacity: 1;
        text-shadow: 0 0 20px rgba(16,185,129,.5);
    }
    50% { 
        opacity: 0.7;
        text-shadow: 0 0 30px rgba(16,185,129,.8);
    }
}

.hero-desc {
    font-size: 1.05rem;
    color: rgba(255,255,255,.65);
    line-height: 1.8;
    max-width: 460px;
    margin-bottom: 2.5rem;
}

/* Stats - HORIZONTAL DI MOBILE */
.hero-stats {
    display: flex;
    gap: 2rem;
    margin-bottom: 2.5rem;
    flex-wrap: wrap;
}

.hero-stat-num {
    font-family: 'Sora', sans-serif;
    font-size: 1.85rem;
    font-weight: 800;
    color: #fff;
    line-height: 1;
}

.hero-stat-num em {
    color: #38bdf8;
    font-style: normal;
}

.hero-stat-label {
    font-size: .72rem;
    color: rgba(255,255,255,.5);
    font-weight: 500;
    margin-top: .25rem;
}

.hero-stat-sep {
    width: 1px;
    background: rgba(255,255,255,.12);
    align-self: stretch;
}

/* Buttons */
.hero-btns {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-hero-primary {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    background: var(--blue);
    color: #fff;
    font-weight: 700;
    font-family: 'DM Sans', sans-serif;
    font-size: .9375rem;
    padding: .875rem 1.75rem;
    border-radius: 12px;
    text-decoration: none;
    border: 1.5px solid var(--blue);
    transition: all .22s;
    box-shadow: 0 6px 20px rgba(20,116,188,.4);
    position: relative;
    overflow: hidden;
}

.btn-hero-primary::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, transparent, rgba(255,255,255,.2), transparent);
    transform: translateX(-100%);
    transition: transform 0.6s;
}

.btn-hero-primary:hover::before {
    transform: translateX(100%);
}

.btn-hero-primary:hover {
    background: #1a8ad6;
    border-color: #1a8ad6;
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(20,116,188,.5);
}

.btn-hero-ghost {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    background: rgba(255,255,255,.08);
    color: rgba(255,255,255,.9);
    font-weight: 600;
    font-family: 'DM Sans', sans-serif;
    font-size: .9375rem;
    padding: .875rem 1.75rem;
    border-radius: 12px;
    text-decoration: none;
    border: 1.5px solid rgba(255,255,255,.18);
    transition: all .22s;
}

.btn-hero-ghost:hover {
    background: rgba(255,255,255,.14);
    border-color: rgba(255,255,255,.3);
    transform: translateY(-2px);
}

/* Right Visual - DIGESER KE KANAN */
.hero-right {
    position: relative;
    opacity: 0;
    transform: translateX(60px); /* Lebih ke kanan dari 40px */
    transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
    padding-left: 2rem; /* Tambahan padding kiri */
}

.hero-right.in {
    opacity: 1;
    transform: translateX(0);
}

.hero-visual-container {
    position: relative;
    width: 100%;
    height: 500px;
    transform-style: preserve-3d;
    perspective: 1000px;
}

.hero-illustration {
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Floating elements */
.floating-element {
    position: absolute;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.15);
    backdrop-filter: blur(12px);
    border-radius: 18px;
    padding: 1.5rem;
    box-shadow: 0 24px 60px rgba(0,0,0,.3);
    transition: transform 0.3s ease;
}

.floating-element:hover {
    transform: translateY(-10px) scale(1.05);
    box-shadow: 0 30px 80px rgba(0,0,0,.4);
}

/* Video player element - GESER KE KANAN */
.element-video {
    width: 320px;
    height: 200px;
    top: 20%;
    left: 55%; /* Dari 50% jadi 55% */
    transform: translateX(-50%);
    animation: float-1 6s ease-in-out infinite;
}

@keyframes float-1 {
    0%, 100% { transform: translateX(-50%) translateY(0) rotateY(0deg); }
    50% { transform: translateX(-50%) translateY(-20px) rotateY(5deg); }
}

.video-header {
    display: flex;
    gap: 6px;
    margin-bottom: 12px;
}

.video-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgba(255,255,255,.3);
}

.video-screen {
    width: 100%;
    height: 120px;
    background: linear-gradient(135deg, #1474bc 0%, #0891b2 100%);
    border-radius: 8px;
    position: relative;
    overflow: hidden;
}

.video-play {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
}

.video-play:hover {
    transform: translate(-50%, -50%) scale(1.1);
    background: #fff;
}

.video-play::after {
    content: '';
    width: 0;
    height: 0;
    border-left: 12px solid var(--blue);
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    margin-left: 3px;
}

.video-progress {
    width: 100%;
    height: 4px;
    background: rgba(255,255,255,.2);
    border-radius: 2px;
    margin-top: 10px;
    overflow: hidden;
}

.video-progress-bar {
    width: 60%;
    height: 100%;
    background: #38bdf8;
    border-radius: 2px;
    animation: progress 3s ease-in-out infinite;
}

@keyframes progress {
    0%, 100% { width: 40%; }
    50% { width: 80%; }
}

/* Stats card - GESER KE KANAN */
.element-stats {
    width: 200px;
    top: 55%;
    right: 5%; /* Dari 10% jadi 5% */
    animation: float-2 7s ease-in-out infinite;
}

@keyframes float-2 {
    0%, 100% { transform: translateY(0) rotateZ(0deg); }
    50% { transform: translateY(-15px) rotateZ(-3deg); }
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.stat-item:last-child {
    margin-bottom: 0;
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.stat-icon svg {
    width: 20px;
    height: 20px;
    color: #fff;
}

.stat-info h4 {
    font-family: 'Sora', sans-serif;
    font-size: 1.2rem;
    font-weight: 800;
    color: #fff;
    line-height: 1;
    margin: 0;
}

.stat-info p {
    font-size: 0.7rem;
    color: rgba(255,255,255,.5);
    margin: 2px 0 0;
}

/* Certificate badge - GESER KE KANAN */
.element-cert {
    width: 180px;
    top: 10%;
    left: 10%; /* Dari 5% jadi 10% */
    animation: float-3 8s ease-in-out infinite;
}

@keyframes float-3 {
    0%, 100% { transform: translateY(0) rotateZ(0deg); }
    50% { transform: translateY(-10px) rotateZ(5deg); }
}

.cert-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}

.cert-icon svg {
    width: 28px;
    height: 28px;
    color: #fff;
}

.cert-text {
    text-align: center;
}

.cert-text h4 {
    font-family: 'Sora', sans-serif;
    font-size: 0.9rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 4px;
}

.cert-text p {
    font-size: 0.7rem;
    color: rgba(255,255,255,.6);
    margin: 0;
}

/* Active users - GESER KE KANAN */
.element-users {
    width: 160px;
    bottom: 15%;
    left: 12%; /* Dari 8% jadi 12% */
    animation: float-4 6.5s ease-in-out infinite;
}

@keyframes float-4 {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-12px); }
}

.users-avatars {
    display: flex;
    margin-bottom: 10px;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,.15);
    margin-right: -12px;
}

.user-avatar:nth-child(1) { background: linear-gradient(135deg, #ff6b6b, #ee5a6f); }
.user-avatar:nth-child(2) { background: linear-gradient(135deg, #4ecdc4, #44a08d); }
.user-avatar:nth-child(3) { background: linear-gradient(135deg, #a8edea, #fed6e3); }
.user-avatar:last-child {
    background: rgba(255,255,255,.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    font-weight: 700;
    color: #fff;
}

.users-text {
    text-align: left;
    padding-left: 4px;
}

.users-text h4 {
    font-family: 'Sora', sans-serif;
    font-size: 1.1rem;
    font-weight: 800;
    color: #fff;
    margin: 0 0 2px;
}

.users-text p {
    font-size: 0.7rem;
    color: rgba(255,255,255,.5);
    margin: 0;
}

/* Particles */
.particles {
    position: absolute;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
}

.particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: rgba(56,189,248,.4);
    border-radius: 50%;
}

.particle:nth-child(1) { top: 20%; left: 10%; animation: particle-float 8s ease-in-out infinite; }
.particle:nth-child(2) { top: 60%; left: 80%; animation: particle-float 10s ease-in-out infinite 2s; }
.particle:nth-child(3) { top: 80%; left: 20%; animation: particle-float 12s ease-in-out infinite 4s; }
.particle:nth-child(4) { top: 40%; left: 70%; animation: particle-float 9s ease-in-out infinite 1s; }
.particle:nth-child(5) { top: 10%; left: 90%; animation: particle-float 11s ease-in-out infinite 3s; }

@keyframes particle-float {
    0%, 100% {
        transform: translateY(0) translateX(0);
        opacity: 0;
    }
    10% { opacity: 1; }
    90% { opacity: 1; }
    50% {
        transform: translateY(-80px) translateX(40px);
    }
}

/* Responsive */
@media (max-width: 900px) {
    .hero {
        min-height: auto;
        padding: 4rem 0 3rem;
    }
    .hero-inner {
        grid-template-columns: 1fr;
        gap: 3rem;
    }
    .hero-right {
        display: none;
    }
}

/* MOBILE: Stats HORIZONTAL (tidak vertikal) */
@media (max-width: 640px) {
    .hero-btns {
        flex-direction: column;
        width: 100%;
    }
    .btn-hero-primary,
    .btn-hero-ghost {
        width: 100%;
        justify-content: center;
    }
    /* Stats tetap HORIZONTAL di mobile */
    .hero-stats {
        display: flex;
        flex-direction: row; /* HORIZONTAL */
        gap: 1.5rem;
        overflow-x: auto;
        padding-bottom: 0.5rem;
    }
    .hero-stats > div {
        flex-shrink: 0;
        min-width: 80px;
    }
    .hero-stat-sep {
        display: block; /* Tetap tampil sebagai separator */
    }
    .hero-stat-num {
        font-size: 1.5rem;
    }
    .hero-stat-label {
        font-size: 0.65rem;
    }
}
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

/* ══════ TRENDING COURSES & FAQ SECTIONS ══════ */

/* Trending Courses Section */
.trending-section {
    background: #fff;
    padding: 6rem 0;
}

.trending-header {
    text-align: center;
    margin-bottom: 3.5rem;
}

.trending-header h2 {
    font-family: 'Sora', sans-serif;
    font-size: clamp(2rem, 4vw, 2.8rem);
    font-weight: 800;
    color: var(--ink);
    margin-bottom: 0.75rem;
}

.trending-header p {
    font-size: 1.05rem;
    color: var(--muted);
    max-width: 600px;
    margin: 0 auto;
}

/* Trending Tabs */
.trending-tabs {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 3rem;
    flex-wrap: wrap;
}

.trending-tab {
    padding: 0.75rem 1.75rem;
    border-radius: 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 0.9375rem;
    font-weight: 600;
    border: 2px solid var(--border);
    background: #fff;
    color: var(--ink-2);
    cursor: pointer;
    transition: all 0.2s;
}

.trending-tab:hover {
    border-color: var(--blue);
    color: var(--blue);
}

.trending-tab.active {
    background: var(--blue);
    border-color: var(--blue);
    color: #fff;
    box-shadow: 0 6px 20px rgba(20,116,188,.25);
}

/* Trending Course Grid */
.trending-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
}

@media (max-width: 900px) {
    .trending-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {
    .trending-grid {
        grid-template-columns: 1fr;
    }
}

.trending-card {
    background: #fff;
    border: 2px solid var(--border);
    border-radius: 18px;
    overflow: hidden;
    transition: all 0.3s;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
}

.trending-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 60px rgba(10,22,40,.12);
    border-color: var(--blue);
}

.trending-card-image {
    position: relative;
    aspect-ratio: 16/9;
    background: linear-gradient(135deg, #dbeafe, #eff6ff);
    overflow: hidden;
}

.trending-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s;
}

.trending-card:hover .trending-card-image img {
    transform: scale(1.08);
}

.trending-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    padding: 0.4rem 0.9rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 700;
    background: rgba(255,255,255,0.95);
    color: var(--blue);
    backdrop-filter: blur(8px);
}

.trending-badge.hot {
    background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
    color: #fff;
}

.trending-card-body {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.trending-category {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--blue);
    margin-bottom: 0.6rem;
}

.trending-title {
    font-family: 'Sora', sans-serif;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.4;
    margin-bottom: 0.75rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.trending-instructor {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 1rem;
}

.trending-instructor-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--blue-xl);
    object-fit: cover;
}

.trending-instructor-name {
    font-size: 0.8125rem;
    color: var(--muted);
    font-weight: 500;
}

.trending-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}

.trending-rating {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--ink);
}

.trending-rating svg {
    width: 14px;
    height: 14px;
    color: #fbbf24;
    fill: #fbbf24;
}

.trending-students {
    font-size: 0.75rem;
    color: var(--muted);
}

/* FAQ Section */
.faq-section {
    background: var(--surf);
    padding: 6rem 0;
}

.faq-header {
    text-align: center;
    margin-bottom: 3.5rem;
}

.faq-header h2 {
    font-family: 'Sora', sans-serif;
    font-size: clamp(1.8rem, 4vw, 2.6rem);
    font-weight: 800;
    color: var(--ink);
    margin-bottom: 0.75rem;
}

.faq-header p {
    font-size: 1rem;
    color: var(--muted);
    max-width: 600px;
    margin: 0 auto;
}

.faq-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
    max-width: 900px;
    margin: 0 auto;
}

.faq-item {
    background: #fff;
    border: 2px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.2s;
}

.faq-item:hover {
    border-color: #93c5fd;
}

.faq-item.active {
    border-color: var(--blue);
    box-shadow: 0 8px 24px rgba(20,116,188,.1);
}

.faq-question {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.25rem 1.5rem;
    cursor: pointer;
    user-select: none;
    gap: 1rem;
}

.faq-question h3 {
    font-family: 'DM Sans', sans-serif;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--ink);
    margin: 0;
    flex: 1;
}

.faq-icon {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: var(--surf);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.3s;
}

.faq-item.active .faq-icon {
    background: var(--blue);
    transform: rotate(180deg);
}

.faq-icon svg {
    width: 14px;
    height: 14px;
    color: var(--muted);
    transition: color 0.3s;
}

.faq-item.active .faq-icon svg {
    color: #fff;
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out, padding 0.3s ease-out;
}

.faq-item.active .faq-answer {
    max-height: 500px;
    padding: 0 1.5rem 1.25rem;
}

.faq-answer p {
    font-size: 0.9rem;
    color: var(--muted);
    line-height: 1.7;
    margin: 0;
}

/* Shared max-width wrapper */
.wrap { max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; }
</style>
@endpush
@section('content')
{{-- ══════════════════════════════════════════════
     1. HERO - HI-TECH REDESIGN V2
══════════════════════════════════════════════ --}}
<section class="hero" id="hero">
    <div class="hero-inner">
        {{-- LEFT CONTENT --}}
        <div class="hero-left" id="hero-left">
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
                </a>
            </div>
        </div>
        {{-- RIGHT VISUAL - 3D ANIMATED (GESER KE KANAN) --}}
        <div class="hero-right" id="hero-right">
            <div class="hero-visual-container">
                <div class="hero-illustration">
                    {{-- Particles --}}
                    <div class="particles">
                        <div class="particle"></div>
                        <div class="particle"></div>
                        <div class="particle"></div>
                        <div class="particle"></div>
                        <div class="particle"></div>
                    </div>

                    {{-- Video Player Element --}}
                    <div class="floating-element element-video">
                        <div class="video-header">
                            <div class="video-dot" style="background:#ff5f56;"></div>
                            <div class="video-dot" style="background:#ffbd2e;"></div>
                            <div class="video-dot" style="background:#27c93f;"></div>
                        </div>
                        <div class="video-screen">
                            <div class="video-play"></div>
                        </div>
                        <div class="video-progress">
                            <div class="video-progress-bar"></div>
                        </div>
                    </div>

                    {{-- Stats Card --}}
                    <div class="floating-element element-stats">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <h4>98%</h4>
                                <p>Tingkat Puas</p>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon" style="background:linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="stat-info">
                                <h4>24/7</h4>
                                <p>Akses Materi</p>
                            </div>
                        </div>
                    </div>

                    {{-- Certificate Badge --}}
                    <div class="floating-element element-cert">
                        <div class="cert-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <div class="cert-text">
                            <h4>Sertifikat Resmi</h4>
                            <p>Terverifikasi</p>
                        </div>
                    </div>

                    {{-- Active Users --}}
                    <div class="floating-element element-users">
                        <div class="users-avatars">
                            <div class="user-avatar"></div>
                            <div class="user-avatar"></div>
                            <div class="user-avatar"></div>
                            <div class="user-avatar">+5</div>
                        </div>
                        <div class="users-text">
                            <h4>{{ number_format($stats['total_students']) }}+</h4>
                            <p>Pelajar Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- ══════════════════════════════════════════════
     2. WHY US
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
     3. KURSUS POPULER (Coursera Style)
══════════════════════════════════════════════ --}}
<section style="background:#fff;padding:4rem 0 3rem;">
    <div class="wrap">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <h2 style="font-family:'Sora',sans-serif;font-size:clamp(1.8rem,3.5vw,2.2rem);font-weight:800;color:var(--ink);margin-bottom:.5rem;">
                    Kursus Paling Populer
                </h2>
                <p style="color:var(--muted);font-size:.95rem;">Dipilih oleh ribuan pelajar di seluruh Indonesia</p>
            </div>
            <a href="{{ route('course.index') }}" style="color:var(--blue);font-weight:700;text-decoration:none;display:flex;align-items:center;gap:.35rem;font-size:.9rem;transition:gap .2s;">
                Lihat Semua
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        {{-- Horizontal Scroll Cards (Coursera Style) --}}
        <div style="display:flex;gap:1.25rem;overflow-x:auto;padding-bottom:1rem;scroll-snap-type:x mandatory;">
            @forelse($featuredCourses->take(6) ?? [] as $course)
            <a href="{{ route('course.show', $course->slug) }}" 
               style="flex:0 0 280px;background:#fff;border:1.5px solid var(--border);border-radius:12px;overflow:hidden;text-decoration:none;color:inherit;transition:all .3s;scroll-snap-align:start;"
               onmouseover="this.style.borderColor='#93c5fd';this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(10,22,40,.12)'"
               onmouseout="this.style.borderColor='var(--border)';this.style.transform='translateY(0)';this.style.boxShadow='none'">
                
                {{-- Thumbnail --}}
                <div style="position:relative;aspect-ratio:16/9;background:linear-gradient(135deg,#dbeafe,#eff6ff);overflow:hidden;">
                    @if($course->thumbnail)
                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" 
                             style="width:100%;height:100%;object-fit:cover;"
                             onerror="this.style.display='none'">
                    @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                            <svg style="width:32px;height:32px;color:var(--blue);opacity:.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    <span style="position:absolute;top:.75rem;left:.75rem;font-size:.68rem;font-weight:700;padding:.3rem .7rem;border-radius:6px;text-transform:uppercase;letter-spacing:.06em;{{ ($course->price ?? 0) > 0 ? 'background:#dbeafe;color:#1d4ed8;' : 'background:#dcfce7;color:#15803d;' }}">
                        {{ ($course->price ?? 0) > 0 ? 'Premium' : 'Gratis' }}
                    </span>
                </div>

                {{-- Content --}}
                <div style="padding:1rem;">
                    @if($course->category)
                    <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--blue);margin-bottom:.4rem;">
                        {{ $course->category->name }}
                    </div>
                    @endif
                    
                    <h3 style="font-family:'Sora',sans-serif;font-size:.88rem;font-weight:700;color:var(--ink);line-height:1.4;margin-bottom:.6rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ $course->title }}
                    </h3>

                    {{-- Rating --}}
                    @if(isset($course->rating))
                    <div style="display:flex;align-items:center;gap:.35rem;margin-top:.5rem;">
                        <div style="display:flex;gap:2px;">
                            @for($i = 0; $i < 5; $i++)
                            <svg style="width:12px;height:12px;{{ $i < floor($course->rating) ? 'color:#fbbf24;fill:#fbbf24;' : 'color:#e5e7eb;fill:#e5e7eb;' }}" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span style="font-size:.72rem;font-weight:600;color:var(--muted);">{{ number_format($course->rating, 1) }}</span>
                        @if(isset($course->students_count))
                        <span style="font-size:.72rem;color:var(--muted);">({{ number_format($course->students_count) }})</span>
                        @endif
                    </div>
                    @endif
                </div>
            </a>
            @empty
            @for($i = 0; $i < 4; $i++)
            <div style="flex:0 0 280px;background:#fff;border:1.5px solid var(--border);border-radius:12px;overflow:hidden;">
                <div style="aspect-ratio:16/9;background:var(--border);"></div>
                <div style="padding:1rem;">
                    <div style="height:8px;background:var(--border);border-radius:4px;width:40%;margin-bottom:.5rem;"></div>
                    <div style="height:12px;background:var(--border);border-radius:4px;margin-bottom:.3rem;"></div>
                    <div style="height:12px;background:var(--border);border-radius:4px;width:70%;"></div>
                </div>
            </div>
            @endfor
            @endforelse
        </div>
    </div>
</section>
{{-- ══════════════════════════════════════════════
     3. KURSUS SEDANG TREN (Trending)
══════════════════════════════════════════════ --}}
<section style="background:var(--surf);padding:4rem 0;">
    <div class="wrap">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
            <div>
                <h2 style="font-family:'Sora',sans-serif;font-size:clamp(1.8rem,3.5vw,2.2rem);font-weight:800;color:var(--ink);margin-bottom:.5rem;">
                    🔥 Kursus Sedang Tren
                </h2>
                <p style="color:var(--muted);font-size:.95rem;">Kursus yang paling banyak dipelajari minggu ini</p>
            </div>
            <a href="{{ route('course.index') }}" style="color:var(--blue);font-weight:700;text-decoration:none;display:flex;align-items:center;gap:.35rem;font-size:.9rem;">
                Lihat Semua
                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        {{-- Grid Cards --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1.25rem;">
            @forelse($featuredCourses->take(3) ?? [] as $course)
            <a href="{{ route('course.show', $course->slug) }}" 
               style="background:#fff;border:1.5px solid var(--border);border-radius:14px;overflow:hidden;text-decoration:none;color:inherit;transition:all .3s;position:relative;"
               onmouseover="this.style.borderColor='#93c5fd';this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(10,22,40,.12)'"
               onmouseout="this.style.borderColor='var(--border)';this.style.transform='translateY(0)';this.style.boxShadow='none'">
                
                {{-- Trending Badge --}}
                <div style="position:absolute;top:.75rem;right:.75rem;z-index:2;background:rgba(251,191,36,.95);backdrop-filter:blur(8px);color:#fff;font-size:.68rem;font-weight:700;padding:.35rem .75rem;border-radius:999px;display:flex;align-items:center;gap:.3rem;">
                    🔥 Trending
                </div>

                {{-- Thumbnail --}}
                <div style="position:relative;aspect-ratio:16/9;background:linear-gradient(135deg,#dbeafe,#eff6ff);overflow:hidden;">
                    @if($course->thumbnail)
                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;">
                            <svg style="width:36px;height:36px;color:var(--blue);opacity:.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                </div>

                {{-- Content --}}
                <div style="padding:1.1rem 1.25rem;">
                    @if($course->category)
                    <div style="font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--blue);margin-bottom:.4rem;">
                        {{ $course->category->name }}
                    </div>
                    @endif
                    
                    <h3 style="font-family:'Sora',sans-serif;font-size:.925rem;font-weight:700;color:var(--ink);line-height:1.4;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ $course->title }}
                    </h3>
                </div>
            </a>
            @empty
            @for($i = 0; $i < 3; $i++)
            <div style="background:#fff;border:1.5px solid var(--border);border-radius:14px;overflow:hidden;">
                <div style="aspect-ratio:16/9;background:var(--border);"></div>
                <div style="padding:1.1rem 1.25rem;">
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
     4. JELAJAHI KATEGORI (Explore Categories)
══════════════════════════════════════════════ --}}
<section style="background:#fff;padding:4rem 0;">
    <div class="wrap">
        <div style="text-align:center;margin-bottom:3rem;">
            <h2 style="font-family:'Sora',sans-serif;font-size:clamp(1.8rem,3.5vw,2.2rem);font-weight:800;color:var(--ink);margin-bottom:.75rem;">
                Jelajahi Kategori
            </h2>
            <p style="color:var(--muted);font-size:.95rem;max-width:500px;margin:0 auto;">
                Temukan kursus sesuai minat dan tujuan karir Anda
            </p>
        </div>
        {{-- Category Grid --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;">
            @php
            $categories = [
                ['icon'=>'💼','name'=>'Bisnis','slug'=>'bisnis','color'=>'#ef4444'],
                ['icon'=>'📊','name'=>'Ilmu Data','slug'=>'data','color'=>'#3b82f6'],
                ['icon'=>'💻','name'=>'Ilmu Komputer','slug'=>'komputer','color'=>'#8b5cf6'],
                ['icon'=>'🖥️','name'=>'Teknologi Informasi','slug'=>'it','color'=>'#06b6d4'],
                ['icon'=>'🚀','name'=>'Pengembangan Pribadi','slug'=>'personal','color'=>'#10b981'],
                ['icon'=>'🏥','name'=>'Layanan Kesehatan','slug'=>'kesehatan','color'=>'#f59e0b'],
                ['icon'=>'🌐','name'=>'Belajar Bahasa','slug'=>'bahasa','color'=>'#ec4899'],
                ['icon'=>'🧮','name'=>'Matematika','slug'=>'matematika','color'=>'#6366f1'],
            ];
            @endphp
            @foreach($categories as $cat)
            <a href="{{ route('course.index') }}?category={{ $cat['slug'] }}" 
               style="background:var(--surf);border:1.5px solid var(--border);border-radius:12px;padding:1.5rem 1rem;text-align:center;text-decoration:none;color:inherit;transition:all .3s;display:flex;flex-direction:column;align-items:center;gap:.75rem;"
               onmouseover="this.style.borderColor='{{ $cat['color'] }}';this.style.transform='translateY(-4px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,.08)'"
               onmouseout="this.style.borderColor='var(--border)';this.style.transform='translateY(0)';this.style.boxShadow='none'">
                <div style="font-size:2.5rem;line-height:1;">{{ $cat['icon'] }}</div>
                <div style="font-family:'Sora',sans-serif;font-size:.85rem;font-weight:700;color:var(--ink);">
                    {{ $cat['name'] }}
                </div>
            </a>
            @endforeach
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
     4.6. FAQ (PERTANYAAN YANG SERING DIAJUKAN)
══════════════════════════════════════════════ --}}
<section class="faq-section">
    <div class="wrap">
        <div class="faq-header rv">
            <span class="section-tag">💬 FAQ</span>
            <h2>Pertanyaan yang Sering Diajukan</h2>
            <p>Temukan jawaban untuk pertanyaan umum seputar Ray Academy</p>
        </div>

        <div class="faq-grid rv">
            @php
            $faqs = [
                [
                    'q' => 'Apakah Ray Academy menyediakan sertifikat resmi?',
                    'a' => 'Ya! Setiap kursus yang Anda selesaikan di Ray Academy akan mendapatkan sertifikat resmi yang terverifikasi. Sertifikat ini dapat langsung Anda cantumkan di CV atau portofolio LinkedIn untuk meningkatkan kredibilitas profesional Anda.'
                ],
                [
                    'q' => 'Berapa lama akses kursus yang saya beli?',
                    'a' => 'Anda mendapatkan akses LIFETIME (selamanya) untuk semua kursus yang Anda beli. Tidak ada batasan waktu, sehingga Anda bisa belajar dengan tempo Anda sendiri dan mengulang materi kapanpun Anda mau.'
                ],
                [
                    'q' => 'Apakah ada kursus gratis di Ray Academy?',
                    'a' => 'Tentu! Kami menyediakan beberapa kursus gratis yang berkualitas untuk membantu Anda memulai perjalanan belajar. Anda bisa langsung mengakses kursus gratis tanpa perlu melakukan pembayaran.'
                ],
                [
                    'q' => 'Bagaimana cara mendapatkan bantuan jika saya kesulitan dalam belajar?',
                    'a' => 'Kami punya komunitas belajar yang aktif! Anda bisa bertanya langsung di forum diskusi setiap kursus, atau menghubungi instruktur melalui fitur Q&A. Tim support kami juga siap membantu Anda melalui email atau live chat.'
                ],
                [
                    'q' => 'Apakah materi kursus selalu diupdate?',
                    'a' => 'Ya, instruktur kami secara berkala mengupdate materi kursus untuk memastikan konten tetap relevan dengan perkembangan industri terkini. Semua update materi dapat Anda akses secara gratis.'
                ],
                [
                    'q' => 'Bisakah saya belajar dari smartphone?',
                    'a' => 'Absolutely! Platform Ray Academy 100% mobile-friendly. Anda bisa belajar dari smartphone, tablet, atau laptop. Semua video dan materi telah dioptimalkan untuk berbagai perangkat.'
                ],
                [
                    'q' => 'Apakah ada garansi uang kembali?',
                    'a' => 'Ya, kami memberikan garansi uang kembali 30 hari untuk kursus premium. Jika Anda tidak puas dengan kursus yang dibeli, Anda bisa mengajukan refund dalam 30 hari pertama setelah pembelian.'
                ],
            ];
            @endphp

            @foreach($faqs as $idx => $faq)
            <div class="faq-item" onclick="toggleFAQ(this)">
                <div class="faq-question">
                    <h3>{{ $faq['q'] }}</h3>
                    <div class="faq-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
                <div class="faq-answer">
                    <p>{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
        {{-- Masih Punya Pertanyaan? --}}
        <div style="text-align:center;margin-top:3.5rem;padding:2.5rem;background:#fff;border-radius:18px;border:2px dashed var(--border);" class="rv">
            <h3 style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:700;color:var(--ink);margin-bottom:0.75rem;">
                Masih Punya Pertanyaan Lain?
            </h3>
            <p style="color:var(--muted);margin-bottom:1.5rem;font-size:0.95rem;">
                Tim support kami siap membantu Anda 24/7
            </p>
            <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;">
                <a href="mailto:support@rayacademy.id" style="display:inline-flex;align-items:center;gap:.5rem;padding:0.75rem 1.5rem;background:var(--blue);color:#fff;font-weight:600;border-radius:10px;text-decoration:none;transition:all .2s;">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Email Kami
                </a>
                <a href="https://wa.me/6281234567890" target="_blank" style="display:inline-flex;align-items:center;gap:.5rem;padding:0.75rem 1.5rem;background:rgba(37,211,102,.1);color:#25d366;font-weight:600;border-radius:10px;text-decoration:none;border:1.5px solid rgba(37,211,102,.3);transition:all .2s;">
                    <svg style="width:18px;height:18px;" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    WhatsApp
                </a>
            </div>
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

    /* ── Trending filter ── */
    window.filterTrending = function(btn, type) {
        document.querySelectorAll('.trending-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelectorAll('.trending-card').forEach(card => {
            if (type === 'popular') {
                card.style.display = '';
            } else {
                const show = card.dataset.type === type;
                card.style.display = show ? '' : 'none';
            }
        });
    };

    /* ── FAQ Toggle ── */
    window.toggleFAQ = function(item) {
        const isActive = item.classList.contains('active');
        
        // Close all other FAQs
        document.querySelectorAll('.faq-item').forEach(faq => {
            if (faq !== item) {
                faq.classList.remove('active');
            }
        });

        // Toggle current FAQ
        if (isActive) {
            item.classList.remove('active');
        } else {
            item.classList.add('active');
        }
    };
});
</script>
@endpush