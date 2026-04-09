<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">

    @hasSection('seo')
        @yield('seo')
    @else
        {!! seo() !!}
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-rich-text::styles theme="richtextlaravel" />

    <style>
        :root {
            --blue:    #1474bc;
            --blue-d:  #0d5a96;
            --blue-xl: #e8f4fd;
            --ink:     #0a1628;
            --ink-2:   #2d3a4e;
            --muted:   #6b7a92;
            --border:  #e4eaf2;
            --surf:    #f7f9fc;
            --white:   #ffffff;
            --accent:  #10b981;   /* emerald */
            --acc-bg:  #ecfdf5;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body { 
            font-family: 'DM Sans', sans-serif; 
            color: var(--ink); 
            background: #fff; 
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            max-width: 100vw;
        }
        h1,h2,h3,h4,h5,h6 { font-family: 'Sora', sans-serif; }

        /* ── NAVBAR ── */
        .site-nav {
    position: sticky; top: 0; z-index: 1000;
    background: rgba(255,255,255,.88);
    backdrop-filter: blur(16px) saturate(180%);
    -webkit-backdrop-filter: blur(16px) saturate(180%);
    border-bottom: 1px solid var(--border);
    transition: box-shadow .3s;
    max-width: 100vw;
}
        .site-nav.scrolled { box-shadow: 0 4px 28px rgba(10,22,40,.08); }

        .nav-inner {
            max-width: 1280px; margin: 0 auto;
            padding: 0 1.5rem;
            display: flex; align-items: center; gap: 2rem;
            height: 68px;
        }
        .nav-logo img { height: 44px; display: block; }
        .nav-logo span {
            font-family: 'Sora', sans-serif;
            font-weight: 800; font-size: 1.25rem; color: var(--ink);
            letter-spacing: -.02em;
        }
        .nav-logo span em { color: var(--blue); font-style: normal; }

        .nav-links {
            display: flex; align-items: center; gap: .25rem;
            margin: 0 auto; list-style: none; padding: 0;
        }
        .nav-links a, .nav-links button {
            font-family: 'DM Sans', sans-serif;
            font-size: .9375rem; font-weight: 600; color: var(--ink-2);
            text-decoration: none; padding: .6rem 1rem; border-radius: 8px;
            border: none; background: transparent; cursor: pointer;
            transition: background .18s, color .18s;
            display: flex; align-items: center; gap: .35rem;
            white-space: nowrap;
        }
        .nav-links a:hover, .nav-links button:hover { background: var(--surf); color: var(--blue); }
        .nav-links a.active { color: var(--blue); font-weight: 700; }

        .nav-dropdown {
            position: relative;
        }
        .nav-dropdown-menu {
            position: absolute; top: calc(100% + .5rem); left: 0;
            background: #fff; border: 1px solid var(--border);
            border-radius: 14px; padding: .5rem;
            min-width: 200px;
            box-shadow: 0 16px 48px rgba(10,22,40,.12);
            z-index: 9999;
        }
        .nav-dropdown-menu a {
            display: block; padding: .6rem .9rem;
            font-size: .83rem; border-radius: 8px;
            color: var(--ink-2);
        }
        .nav-dropdown-menu a:hover { background: var(--surf); color: var(--blue); }

        .nav-right { display: flex; align-items: center; gap: .75rem; flex-shrink: 0; }

        .nav-search {
            display: flex; align-items: center;
            background: var(--surf); border: 1.5px solid var(--border);
            border-radius: 10px; padding: .45rem .85rem; gap: .45rem;
            transition: border-color .2s, box-shadow .2s;
            max-width: 180px;
        }
        .nav-search:focus-within { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(20,116,188,.12); }
        .nav-search input {
            border: none; background: transparent; outline: none;
            font-family: 'DM Sans', sans-serif;
            font-size: .83rem; color: var(--ink); 
            width: 100%;
            min-width: 0;
        }
        .nav-search input::placeholder { color: var(--muted); }
        .nav-search svg { color: var(--muted); flex-shrink: 0; }

        .btn-nav-login {
            font-family: 'DM Sans', sans-serif;
            font-size: .875rem; font-weight: 600; color: var(--ink-2);
            text-decoration: none; padding: .5rem 1.1rem;
            border: 1.5px solid var(--border); border-radius: 10px;
            transition: all .2s; background: transparent;
        }
        .btn-nav-login:hover { border-color: var(--blue); color: var(--blue); background: var(--blue-xl); }

        .btn-nav-cta {
            font-family: 'DM Sans', sans-serif;
            font-size: .875rem; font-weight: 700; color: #fff;
            text-decoration: none; padding: .55rem 1.3rem;
            background: var(--blue); border-radius: 10px;
            border: 1.5px solid var(--blue);
            transition: all .2s; display: flex; align-items: center; gap: .4rem;
        }
        .btn-nav-cta:hover { background: var(--blue-d); border-color: var(--blue-d); transform: translateY(-1px); box-shadow: 0 6px 18px rgba(20,116,188,.3); }
        
        @media (max-width: 640px) {
            .btn-nav-cta {
                padding: .5rem 1rem;
                font-size: .8125rem;
            }
        }

        /* Avatar */
        .nav-avatar {
            display: flex; align-items: center; gap: .65rem;
            text-decoration: none; color: var(--ink-2);
            padding: .4rem .75rem; border-radius: 10px;
            transition: background .18s;
        }
        .nav-avatar:hover { background: var(--surf); }
        .nav-avatar img { width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border); }
        .nav-avatar span { font-size: .83rem; font-weight: 600; }

        /* Mobile toggle */
        .nav-mobile-btn {
            display: none; align-items: center; justify-content: center;
            width: 40px; height: 40px; border-radius: 9px;
            border: 1.5px solid var(--border); background: transparent; cursor: pointer;
            color: var(--ink); transition: background .18s;
            margin-left: auto;
        }
        .nav-mobile-btn:hover { background: var(--surf); }

        /* Mobile menu */
        .nav-mobile-panel {
            display: none; position: fixed; inset: 0; z-index: 999;
            background: #fff; padding: 1.25rem 1.5rem 2rem;
            flex-direction: column; gap: .25rem;
            overflow-y: auto;
        }
        .nav-mobile-panel.open { display: flex; }
        .nav-mobile-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
        .nav-mobile-close { width: 40px; height: 40px; border-radius: 9px; border: 1.5px solid var(--border); background: transparent; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .nav-mobile-link {
            display: block; padding: .85rem 1rem; border-radius: 10px;
            font-size: 1rem; font-weight: 500; color: var(--ink-2);
            text-decoration: none; transition: background .18s;
        }
        .nav-mobile-link:hover { background: var(--surf); color: var(--blue); }
        .nav-mobile-divider { height: 1px; background: var(--border); margin: .5rem 0; }
        .nav-mobile-cta {
            display: block; padding: 1rem; text-align: center;
            background: var(--blue); color: #fff; font-weight: 700;
            border-radius: 12px; text-decoration: none; margin-top: auto; font-size: .9rem;
        }

        @media (max-width: 1024px) {
            .nav-links { display: none !important; }
            .nav-search { display: none !important; }
            .nav-mobile-btn { display: flex !important; }
        }
        
        @media (max-width: 640px) {
            .btn-nav-login,
            .btn-nav-cta { 
                display: none !important; 
            }
            .nav-logo img {
                height: 32px !important;
            }
            .nav-inner {
                height: 56px !important;
                padding: 0 0.75rem !important;
                gap: 0.5rem !important;
            }
            .nav-mobile-btn {
                width: 36px !important;
                height: 36px !important;
            }
        }

        /* ── FOOTER ── */
        .site-footer {
            background: var(--ink);
            color: rgba(255,255,255,.75);
            padding: 4rem 0 0;
        }
        .footer-inner {
            max-width: 1280px; margin: 0 auto; padding: 0 1.5rem;
        }
        
        @media (max-width: 640px) {
            .footer-inner {
                padding: 0 1rem;
            }
        }
        .footer-top {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.4fr;
            gap: 3rem;
            padding-bottom: 3rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        @media (max-width: 900px) {
            .footer-top { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 560px) {
            .footer-top { grid-template-columns: 1fr; gap: 2rem; }
        }
        .footer-brand p { font-size: .875rem; line-height: 1.75; margin-top: .85rem; max-width: 300px; }
        .footer-brand-logo { height: 40px; display: block; filter: brightness(0) invert(1); }
        .footer-brand-name { font-family: 'Sora', sans-serif; font-size: 1.2rem; font-weight: 800; color: #fff; }
        .footer-brand-name em { color: var(--accent); font-style: normal; }

        .footer-social { display: flex; gap: .6rem; margin-top: 1.5rem; flex-wrap: wrap; }
        .footer-social a {
            width: 36px; height: 36px; border-radius: 9px;
            background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12);
            display: flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,.7); font-size: .8rem;
            text-decoration: none; transition: all .2s;
        }
        .footer-social a:hover { background: var(--blue); border-color: var(--blue); color: #fff; transform: translateY(-2px); }

        .footer-col h4 { font-family: 'Sora', sans-serif; font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #fff; margin-bottom: 1.25rem; }
        .footer-col ul { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: .7rem; }
        .footer-col a { font-size: .875rem; color: rgba(255,255,255,.65); text-decoration: none; transition: color .2s; }
        .footer-col a:hover { color: #fff; }

        .footer-contact-item { display: flex; gap: .75rem; align-items: flex-start; }
        .footer-contact-item .icon { flex-shrink: 0; width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,.07); display: flex; align-items: center; justify-content: center; margin-top: 2px; }
        .footer-contact-item .icon i { font-size: .75rem; color: rgba(255,255,255,.6); }
        .footer-contact-item p { font-size: .8rem; color: rgba(255,255,255,.42); margin: 0; }
        .footer-contact-item span { font-size: .875rem; color: rgba(255,255,255,.8); display: block; }
        .footer-contact-list { display: flex; flex-direction: column; gap: .9rem; }

        .footer-bottom {
            padding: 1.5rem 0;
            display: flex; align-items: center; justify-content: center;
            font-size: .8rem; color: rgba(255,255,255,.35);
        }
    </style>

    @stack('styles')
</head>
<body x-data="{ mobileMenuOpen: false }">

{{-- ═══════════════ NAVBAR ═══════════════ --}}
<nav class="site-nav" id="site-nav">
    <div class="nav-inner">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="nav-logo" style="display:flex;align-items:center;gap:.6rem;text-decoration:none;flex-shrink:0;">
            <img src="{{ asset('logo.png') }}" alt="Ray Academy" onerror="this.style.display='none'">
            <span style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.15rem;color:var(--ink);letter-spacing:-.02em;"><em style="color:var(--blue);"></em></span>
        </a>

        {{-- Desktop Links --}}
        {{-- Desktop Links --}}
        <ul class="nav-links">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a></li>
            <li><a href="{{ route('course.index') }}" class="{{ request()->routeIs('course.*') ? 'active' : '' }}">Kursus</a></li>
            
            @if(isset($articleCategories) && $articleCategories->count() > 0)
            <li class="nav-dropdown" x-data="{ open: false }" style="position: relative;">
                <button @click="open = !open" @click.outside="open = false" :class="open ? 'active' : ''" style="border:none; background:transparent; font-family:'DM Sans', sans-serif;">
                    Artikel
                    <svg style="width:13px;height:13px;transition:transform .2s;" :style="open ? 'transform:rotate(180deg)' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                </button>
                
                <div class="nav-dropdown-menu" x-show="open" style="display: none; position: absolute; top: calc(100% + 0.5rem); left: 0;"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    <a href="{{ route('article.index') }}" style="font-weight:600;color:var(--blue);">Semua Artikel</a>
                    @foreach($articleCategories->take(8) as $cat)
                        <a href="{{ route('article.category', $cat->slug) }}">{{ $cat->name }}</a>
                    @endforeach
                </div>
            </li>
            @else
            <li><a href="{{ route('article.index') }}" class="{{ request()->routeIs('article.*') ? 'active' : '' }}">Artikel</a></li>
            @endif
            
            <li><a href="{{ url('/about.index') }}" class="{{ request()->is('about') ? 'active' : '' }}">Tentang</a></li>
            <li><a href="{{ url('/contact.index') }}" class="{{ request()->is('contact') ? 'active' : '' }}">Kontak</a></li>
        </ul>

        {{-- Search + CTA --}}
        <div class="nav-right">
            <form action="{{ route('search') }}" method="GET" class="nav-search">
                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                <input type="search" name="q" placeholder="Cari kursus..." value="{{ request('q') }}">
            </form>

            @auth
                <div class="nav-dropdown" x-data="{ openProfile: false }" style="position: relative;">
                    <button @click="openProfile = !openProfile" @click.outside="openProfile = false" class="nav-avatar" style="border:none; background:transparent; cursor:pointer; font-family:'DM Sans', sans-serif;">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}">
                        @else
                            <div style="width:34px;height:34px;border-radius:50%;background:var(--blue);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:.85rem; flex-shrink: 0;">
    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
</div>
                        @endif
                        <span>{{ explode(' ', auth()->user()->name)[0] }}</span>
                        <svg style="width:13px;height:13px;transition:transform .2s;" :style="openProfile ? 'transform:rotate(180deg)' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div class="nav-dropdown-menu" x-show="openProfile" style="display: none; right: 0; left: auto; min-width: 180px;"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                        
                        <a href="{{ route('dashboard') }}" style="display:flex; align-items:center; gap:8px;">
                            <i class="fas fa-chart-line"></i> Dashboard Saya
                        </a>
                        
                        <hr style="border:none; border-top:1px solid var(--border); margin: .5rem 0;">
                        
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" style="width:100%; text-align:left; background:transparent; border:none; padding:.6rem .9rem; font-size:.83rem; border-radius:8px; color:#ef4444; cursor:pointer; display:flex; align-items:center; gap:8px; font-family:'DM Sans', sans-serif; font-weight: 600;" onmouseover="this.style.backgroundColor='var(--surf)';" onmouseout="this.style.backgroundColor='transparent';">
                                <i class="fas fa-sign-out-alt"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-nav-login">Masuk</a>
                <a href="{{ route('register') }}" class="btn-nav-cta">
                    Daftar Gratis
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            @endauth
        </div>

        {{-- Mobile button --}}
        <button class="nav-mobile-btn" onclick="document.getElementById('mobileNav').classList.toggle('open')" aria-label="Menu">
            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>
</nav>

{{-- Mobile Nav Panel --}}
<div class="nav-mobile-panel" id="mobileNav">
    <div class="nav-mobile-top">
        <span style="font-family:'Sora',sans-serif;font-weight:800;font-size:1.1rem;color:var(--ink);">Ray<em style="color:var(--blue);font-style:normal;">Academy</em></span>
        <button class="nav-mobile-close" onclick="document.getElementById('mobileNav').classList.remove('open')" aria-label="Tutup">
            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <form action="{{ route('search') }}" method="GET" style="display:flex;align-items:center;background:var(--surf);border:1.5px solid var(--border);border-radius:10px;padding:.6rem 1rem;gap:.5rem;margin-bottom:.75rem;">
        <svg style="width:15px;height:15px;color:var(--muted);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
        <input type="search" name="q" placeholder="Cari kursus..." style="border:none;background:transparent;outline:none;font-family:'DM Sans',sans-serif;font-size:.9rem;width:100%;color:var(--ink);">
    </form>

    <a href="{{ route('home') }}" class="nav-mobile-link">Beranda</a>
    <a href="{{ route('course.index') }}" class="nav-mobile-link">Kursus</a>
    <a href="{{ route('article.index') }}" class="nav-mobile-link">Artikel</a>
    <a href="{{ url('/about.index') }}" class="nav-mobile-link">Tentang</a>
    <a href="{{ url('/contact.index') }}" class="nav-mobile-link">Kontak</a>

    @if(isset($articleCategories) && $articleCategories->count() > 0)
    <div class="nav-mobile-divider"></div>
    <p style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--muted);padding:.25rem 1rem .1rem;">Kategori Artikel</p>
    @foreach($articleCategories->take(5) as $cat)
        <a href="{{ route('article.category', $cat->slug) }}" class="nav-mobile-link" style="font-size:.875rem;padding:.6rem 1rem;">{{ $cat->name }}</a>
    @endforeach
    @endif

    <div class="nav-mobile-divider" style="margin-top:auto;"></div>
    @auth
        <a href="{{ route('dashboard') }}" class="nav-mobile-cta">Dashboard Saya</a>
    @else
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:.75rem;">
            <a href="{{ route('login') }}" style="display:block;padding:.9rem;text-align:center;border:1.5px solid var(--border);border-radius:12px;font-weight:600;font-size:.9rem;color:var(--ink-2);text-decoration:none;">Masuk</a>
            <a href="{{ route('register') }}" class="nav-mobile-cta" style="margin-top:0;">Daftar Gratis</a>
        </div>
    @endauth
</div>

{{-- ═══════════════ CONTENT ═══════════════ --}}
<main>
    @yield('content')
</main>

{{-- ═══════════════ FOOTER ═══════════════ --}}
<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-top">

            {{-- Brand --}}
            <div class="footer-brand">
                <img src="{{ asset('logo.png') }}" alt="Ray Academy" class="footer-brand-logo" onerror="this.style.display='none';document.getElementById('footer-brand-text').style.display='block'">
                <span id="footer-brand-text" style="display:none;" class="footer-brand-name">Ray<em>Academy</em></span>
                <p>Platform belajar online terpercaya dengan ratusan kursus dari instruktur berpengalaman. Belajar kapan saja, di mana saja.</p>
                @if(!empty($settings['social_facebook']) || !empty($settings['social_instagram']) || !empty($settings['social_youtube']) || !empty($settings['social_tiktok']) || !empty($settings['social_whatsapp']) || !empty($settings['social_linkedin']) || !empty($settings['social_twitter']))
                <div class="footer-social">
                    @if(!empty($settings['social_facebook']))
                        <a href="{{ $settings['social_facebook'] }}" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    @endif
                    @if(!empty($settings['social_instagram']))
                        <a href="{{ $settings['social_instagram'] }}" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    @endif
                    @if(!empty($settings['social_youtube']))
                        <a href="{{ $settings['social_youtube'] }}" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    @endif
                    @if(!empty($settings['social_tiktok']))
                        <a href="{{ $settings['social_tiktok'] }}" target="_blank" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                    @endif
                    @if(!empty($settings['social_whatsapp']))
                        <a href="{{ $settings['social_whatsapp'] }}" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    @endif
                    @if(!empty($settings['social_linkedin']))
                        <a href="{{ $settings['social_linkedin'] }}" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    @endif
                    @if(!empty($settings['social_twitter']))
                        <a href="{{ $settings['social_twitter'] }}" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    @endif
                </div>
                @endif
            </div>

            {{-- Navigasi --}}
            <div class="footer-col">
                <h4>Platform</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Beranda</a></li>
                    <li><a href="{{ route('course.index') }}">Semua Kursus</a></li>
                    <li><a href="{{ route('article.index') }}">Artikel & Tips</a></li>
                    @guest
                    <li><a href="{{ route('register') }}">Daftar Gratis</a></li>
                    <li><a href="{{ route('login') }}">Masuk</a></li>
                    @endguest
                    @auth
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Kategori Artikel --}}
            <div class="footer-col">
                <h4>Kategori</h4>
                <ul>
                    @if(isset($articleCategories))
                        @foreach($articleCategories->take(6) as $category)
                            <li><a href="{{ route('article.category', $category->slug) }}">{{ $category->name }}</a></li>
                        @endforeach
                    @endif
                </ul>
            </div>

            {{-- Kontak --}}
            <div class="footer-col">
                <h4>Kontak Kami</h4>
                <div class="footer-contact-list">
                    @if(!empty($settings['contact_email']))
                    <div class="footer-contact-item">
                        <div class="icon"><i class="fas fa-envelope"></i></div>
                        <div><p>Email</p><span>{{ $settings['contact_email'] }}</span></div>
                    </div>
                    @endif
                    @if(!empty($settings['contact_phone']))
                    <div class="footer-contact-item">
                        <div class="icon"><i class="fas fa-phone"></i></div>
                        <div><p>Telepon</p><span>{{ $settings['contact_phone'] }}</span></div>
                    </div>
                    @endif
                    <div class="footer-contact-item">
                        <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div><p>Alamat</p><span>{{ $settings['contact_address'] ?? 'Bandung, Jawa Barat, Indonesia' }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>Copyright &copy; {{ date('Y') }} Ray Academy. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<script>
    // Navbar scroll effect
    const nav = document.getElementById('site-nav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 20);
    }, { passive: true });

    // Close mobile nav on link click
    document.querySelectorAll('.nav-mobile-link, .nav-mobile-cta').forEach(el => {
        el.addEventListener('click', () => {
            document.getElementById('mobileNav').classList.remove('open');
        });
    });
</script>


@stack('scripts')
</body>
</html>