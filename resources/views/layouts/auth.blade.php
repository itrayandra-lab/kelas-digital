<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Ray Academy</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .au { --blue:#1474bc; --blue-d:#0d5a96; --ink:#0a1628; --muted:#5b6b82; --border:#dbe3ee;
              min-height:100vh; display:grid; grid-template-columns:minmax(0,5fr) minmax(0,6fr); font-family:'DM Sans',system-ui,sans-serif; color:var(--ink); background:#fff; }
        .au-side { position:sticky; top:0; height:100vh; display:flex; flex-direction:column; justify-content:space-between; padding:2.75rem 3rem;
                   color:#fff; background:linear-gradient(150deg,#0d5a96 0%,#1474bc 55%,#1b8fd6 100%); overflow:hidden; }
        .au-side::after { content:''; position:absolute; right:-120px; bottom:-120px; width:420px; height:420px; border-radius:50%; background:rgba(255,255,255,.07); pointer-events:none; }
        .au-side::before { content:''; position:absolute; left:-80px; top:30%; width:220px; height:220px; border-radius:50%; background:rgba(255,255,255,.05); pointer-events:none; }
        .au-side > * { position:relative; z-index:1; }
        .au-side h1 { font-family:'Sora',sans-serif; font-size:clamp(1.8rem,2.6vw,2.4rem); font-weight:700; line-height:1.2; letter-spacing:-.02em; margin:0 0 .9rem; }
        .au-side p { font-size:1.02rem; line-height:1.6; color:rgba(255,255,255,.88); margin:0; max-width:30rem; }
        .au-points { list-style:none; margin:2rem 0 0; padding:0; display:flex; flex-direction:column; gap:1rem; }
        .au-points li { display:flex; gap:.85rem; align-items:flex-start; font-size:.95rem; line-height:1.5; color:rgba(255,255,255,.92); }
        .au-points i { width:36px; height:36px; flex-shrink:0; border-radius:12px; background:rgba(255,255,255,.15); display:flex; align-items:center; justify-content:center; font-size:.85rem; }
        .au-points b { display:block; color:#fff; }
        .au-foot { font-size:.82rem; color:rgba(255,255,255,.7); }

        .au-main { display:flex; flex-direction:column; padding:1.5rem 1.25rem 2.5rem; }
        .au-top { display:flex; justify-content:space-between; align-items:center; gap:1rem; max-width:30rem; width:100%; margin:0 auto; }
        .au-top img { height:34px; }
        .au-back { display:inline-flex; align-items:center; gap:.45rem; min-height:44px; font-size:.88rem; font-weight:600; color:var(--muted); text-decoration:none; }
        .au-back:hover { color:var(--blue); }
        .au-form { max-width:30rem; width:100%; margin:auto; padding-top:2rem; }
        .au-form h2 { font-family:'Sora',sans-serif; font-size:1.75rem; font-weight:700; letter-spacing:-.02em; margin:0; }
        .au-switch { margin:.45rem 0 0; color:var(--muted); font-size:.95rem; }
        .au-switch a, .au-link { color:var(--blue); font-weight:600; text-decoration:none; }
        .au-switch a:hover, .au-link:hover { text-decoration:underline; }

        .au-fields { margin-top:1.75rem; display:flex; flex-direction:column; gap:1.1rem; }
        .au-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
        .au-label { display:block; font-size:.88rem; font-weight:600; margin-bottom:.4rem; }
        .au-input { width:100%; min-height:48px; padding:.7rem .95rem; border:1px solid var(--border); border-radius:12px; font:inherit; font-size:.95rem; color:var(--ink); background:#fff; transition:border-color .15s, box-shadow .15s; }
        .au-input::placeholder { color:#94a3b8; }
        .au-input:focus { outline:none; border-color:var(--blue); box-shadow:0 0 0 3px rgba(20,116,188,.18); }
        .au-hint { margin:.35rem 0 0; font-size:.8rem; color:var(--muted); line-height:1.45; }
        .au-pass { position:relative; }
        .au-pass .au-input { padding-right:3rem; }
        .au-eye { position:absolute; right:.25rem; top:50%; transform:translateY(-50%); width:44px; height:44px; border:0; background:none; color:var(--muted); cursor:pointer; border-radius:10px; }
        .au-eye:hover { color:var(--ink); }
        .au-group { border:1px solid var(--border); border-radius:14px; padding:1rem; background:#f8fafc; display:flex; flex-direction:column; gap:.9rem; }
        .au-group legend { padding:0 .35rem; font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.05em; }
        .au-btn { width:100%; min-height:50px; margin-top:.4rem; border:0; border-radius:12px; background:var(--blue); color:#fff; font:inherit; font-size:1rem; font-weight:700; cursor:pointer; transition:background .15s; }
        .au-btn:hover { background:var(--blue-d); }
        .au-btn:disabled { opacity:.7; cursor:wait; }
        .au-btn:focus-visible, .au-eye:focus-visible, .au-back:focus-visible, .au-link:focus-visible, .au-switch a:focus-visible { outline:2px solid var(--blue); outline-offset:2px; }
        .au-alert { margin-top:1.25rem; background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; font-size:.88rem; padding:.8rem 1rem; border-radius:12px; }
        .au-legal { margin-top:1.25rem; font-size:.8rem; color:var(--muted); text-align:center; }

        @media (prefers-reduced-motion: reduce) { .au-input, .au-btn { transition:none; } }
        @media (max-width: 960px) {
            .au { grid-template-columns:minmax(0,1fr); }
            .au-side { display:none; }
        }
        @media (min-width: 961px) { .au-top .au-logo { visibility:hidden; } }
        @media (max-width: 480px) { .au-row { grid-template-columns:minmax(0,1fr); } }
    </style>
</head>

<body class="antialiased">
    <div class="au">
        <aside class="au-side">
            <a href="{{ route('home') }}"><img src="{{ asset('logo-white.png') }}" alt="Ray Academy" style="height:44px;"></a>
            <div>
                @yield('side')
                <ul class="au-points">
                    <li><i class="fas fa-layer-group" aria-hidden="true"></i><span><b>Belajar bertahap per modul</b>Video dan bacaan disusun berurutan, mudah diikuti dari awal.</span></li>
                    <li><i class="fas fa-clipboard-check" aria-hidden="true"></i><span><b>Pre-test dan post-test</b>Kamu bisa lihat sendiri seberapa jauh kemajuanmu.</span></li>
                    <li><i class="fas fa-gift" aria-hidden="true"></i><span><b>Ada kelas gratis</b>Coba dulu tanpa bayar sebelum memilih kelas lain.</span></li>
                </ul>
            </div>
            <p class="au-foot">&copy; {{ date('Y') }} Ray Academy</p>
        </aside>

        <main class="au-main">
            <div class="au-top">
                <a href="{{ route('home') }}" class="au-logo"><img src="{{ asset('logo.png') }}" alt="Ray Academy"></a>
                <a href="{{ route('home') }}" class="au-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Kembali ke beranda</a>
            </div>
            <div class="au-form">
                @yield('form')
            </div>
        </main>
    </div>
    @include('auth._ajax-form')
</body>

</html>
