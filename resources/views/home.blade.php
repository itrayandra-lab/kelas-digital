@extends('layouts.app')
@section('title', config('app.name') . ' — Platform Belajar Online Terpercaya')

@push('styles')
<style>
.wrap{max-width:1280px;margin:0 auto;padding:0 1.5rem}
@media(max-width:640px){.wrap{padding:0 1.25rem}}
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
.hs-lead{font-size:clamp(.92rem,1.2vw,1.02rem);line-height:1.5;color:var(--hs-text);margin:0;}
.hs-pill{display:inline-block;font-family:'DM Sans',sans-serif;font-size:.74rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
    color:var(--hs-navy);background:#dceaf9;border-radius:999px;padding:.4rem 1rem;margin-bottom:1rem;}

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
@include('home._corporate')
@if($events->isNotEmpty())
    @include('home._events')
@endif


@include('home._raya')
@include('home._experts')
@include('home._experience')
@include('home._beyond')
@include('home._insights')
@include('home._faq')
@include('home._closing')

@endsection

@push('scripts')
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
