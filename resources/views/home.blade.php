@extends('layouts.app')
@section('title', config('app.name') . ' — Platform Belajar Online Terpercaya')

@push('styles')
<style>
/* Scroll reveal */
.rv{opacity:0;transform:translateY(28px);transition:opacity .6s ease,transform .6s ease}
.rv.in{opacity:1;transform:translateY(0)}
@media (prefers-reduced-motion: reduce){.rv{opacity:1;transform:none;transition:none}}

.wrap{max-width:1280px;margin:0 auto;padding:0 1.5rem}
@media(max-width:640px){.wrap{padding:0 1rem}}
@media(max-width:640px){html,body{overflow-x:hidden!important;max-width:100vw!important;}}

/* ═══ Shared pieces of the homepage sections ═══ */
.hs{--hs-navy:#0b1f4d;--hs-blue:#1f6fd1;--hs-gold:#d9a441;--hs-text:#33415c;
    position:relative;overflow:hidden;padding:clamp(3.5rem,7vw,5.5rem) 0;}
.hs-bg{position:absolute;inset:0;z-index:0;pointer-events:none;}
.hs-bg img{width:100%;height:100%;object-fit:cover;display:block;}
.hs > .wrap{position:relative;z-index:1;}
.hs-bar{display:block;width:56px;height:6px;border-radius:999px;background:var(--hs-gold);margin-bottom:1.1rem;}
.hs-center{text-align:center;max-width:900px;margin:0 auto clamp(2rem,4vw,3rem);}
.hs-center .hs-bar{margin-inline:auto;}
.hs-eyebrow{font-family:'DM Sans',sans-serif;font-size:clamp(.72rem,1.1vw,.85rem);font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:var(--hs-blue);margin:0 0 .6rem;}
.hs-title{font-family:'Cinzel',Georgia,serif;font-weight:600;text-transform:uppercase;line-height:1.08;letter-spacing:.01em;color:var(--hs-navy);
    font-size:clamp(1.85rem,4.2vw,3.25rem);margin:0 0 1rem;text-wrap:balance;}
.hs-title span{color:var(--hs-blue);}
.hs-lead{font-size:clamp(.92rem,1.2vw,1.02rem);line-height:1.7;color:var(--hs-text);margin:0;}
.hs-pill{display:inline-block;font-family:'DM Sans',sans-serif;font-size:.74rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
    color:var(--hs-navy);background:#dceaf9;border-radius:999px;padding:.4rem 1rem;margin-bottom:1rem;}

/* Pill buttons */
.hb{display:inline-flex;align-items:center;justify-content:center;gap:.6rem;min-height:46px;padding:.7rem 1.6rem;border-radius:999px;
    font-family:'Sora',sans-serif;font-size:.9rem;font-weight:600;text-decoration:none;text-align:center;
    transition:transform .2s ease,background-color .2s ease,color .2s ease,box-shadow .2s ease;border:1.5px solid transparent;cursor:pointer;}
.hb i{font-size:.8em;transition:transform .2s ease;}
.hb:hover{transform:translateY(-2px);}
.hb:hover i{transform:translateX(3px);}
.hb:focus-visible{outline:3px solid #7cb7ff;outline-offset:3px;}
.hb-navy{background:#0b2a63;color:#fff;box-shadow:0 8px 22px rgba(11,42,99,.25);}
.hb-navy:hover{background:#123a82;color:#fff;}
.hb-outline{background:rgba(255,255,255,.8);color:#0b2a63;border-color:#0b2a63;}
.hb-outline:hover{background:#0b2a63;color:#fff;}
.hb-white{background:#fff;color:#0b2a63;box-shadow:0 8px 22px rgba(0,0,0,.18);}
.hb-white:hover{background:#eef4ff;color:#0b2a63;}
.hb-ghost{background:transparent;color:#fff;border-color:rgba(255,255,255,.85);}
.hb-ghost:hover{background:rgba(255,255,255,.12);color:#fff;}

/* Partners strip */
.partners-section{background:#F8FAFF;padding:2.5rem 0;border-top:1px solid #EDF2F9;}
.partners-section h2{font-family:'Sora',sans-serif;font-size:clamp(1rem,2.2vw,1.15rem);font-weight:600;color:#6B7280;text-align:center;margin-bottom:1.25rem;}
.partners-logos{display:flex;align-items:center;justify-content:center;gap:1rem;flex-wrap:wrap;}
.partners-logos img{width:160px;height:88px;padding:.9rem 1.25rem;object-fit:contain;background:#fff;border:1px solid #E2E8F0;border-radius:12px;}

/* Upcoming events */
.ev-home{padding:3.5rem 0;background:linear-gradient(180deg,#f3f8ff 0%,#fff 100%);}
.ev-home-head{display:flex;justify-content:space-between;align-items:flex-end;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;}
.ev-home-head h2{font-family:'Sora',sans-serif;font-size:clamp(1.4rem,3vw,1.9rem);font-weight:700;letter-spacing:-.02em;margin:0 0 .3rem;}
.ev-home-head p{color:var(--muted);margin:0;}
.ev-home-all{display:inline-flex;align-items:center;gap:.4rem;color:#0056D2;font-weight:600;font-size:.85rem;text-decoration:none;white-space:nowrap;}
.ev-home-all:hover{text-decoration:underline;}
</style>
@endpush

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect(config('faq'))->map(fn ($f) => ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]])->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
</script>
@endpush

@include('partials.event-styles')

@section('content')

@include('home._hero')
@include('home._two-ways')
@include('home._families')
@include('home._next-class')

@if($events->isNotEmpty())
<section class="ev-home">
    <div class="wrap">
        <div class="ev-home-head rv">
            <div>
                <h2>Event mendatang</h2>
                <p>Kelas praktik dan workshop gratis, offline maupun online. Kursinya terbatas.</p>
            </div>
            <a href="{{ route('events.index') }}" class="ev-home-all">Lihat semua event <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="ev-grid">
            @foreach($events as $event)
                @include('events._card')
            @endforeach
        </div>
    </div>
</section>
@endif

@include('home._corporate')
@include('home._raya')
@include('home._experts')

<section class="partners-section">
    <div class="wrap rv">
        <h2>Bekerja sama dengan universitas dan lembaga pelatihan</h2>
        <div class="partners-logos">
            <img src="{{ asset('img/logo-unpad.png') }}" alt="Universitas Padjadjaran" loading="lazy" onerror="this.style.display='none'">
            <img src="{{ asset('img/logo-farmasi-unpad.jpg') }}" alt="Farmasi Unpad" loading="lazy" onerror="this.style.display='none'">
            <img src="{{ asset('img/logo-labcos.png') }}" alt="Labcos" loading="lazy" onerror="this.style.display='none'">
        </div>
    </div>
</section>

@include('home._experience')
@include('home._beyond')
@include('home._insights')
@include('home._faq')
@include('home._closing')

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting){e.target.classList.add('in');obs.unobserve(e.target);} });
    }, { threshold:0.06, rootMargin:'0px 0px -40px 0px' });
    document.querySelectorAll('.rv').forEach(el => obs.observe(el));
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
  { element: '.lf-grid', popover: { title: 'Pilih bidang yang kamu minati', description: 'Ada empat bidang belajar. Klik salah satunya untuk melihat kelas di bidang itu.', side: 'top' } },
  { element: '.nc-card', popover: { title: 'Buka kelas', description: 'Klik kartu kelas untuk melihat isi materi, harga, dan video cuplikannya.', side: 'right' } },
  { element: '.nav-avatar', popover: { title: 'Kelas yang kamu ikuti', description: 'Klik namamu di sini, lalu pilih <b>Dashboard Saya</b> untuk melihat kelas yang sudah kamu ikuti dan melanjutkan belajar.', side: 'bottom' } },
  { element: '.nav-mobile-btn', popover: { title: 'Menu', description: 'Semua menu (Kursus, Artikel, dan akunmu) ada di tombol ini.', side: 'bottom' } },
  { popover: { title: 'Cara belajarnya', description: '1. Pilih kelas, lalu daftar.<br>2. Kerjakan pre-test singkat.<br>3. Tonton video dan baca materi secara berurutan.<br>4. Kerjakan post-test. Kalau lulus, modul berikutnya terbuka.<br><br>Lupa caranya? Klik tombol <b>Panduan</b> di pojok kanan bawah kapan saja.' } }
]);
@endguest
</script>
@endpush
