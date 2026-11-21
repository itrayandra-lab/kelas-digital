@extends('layouts.app')

@section('seo')
    <title>Event - Ray Academy</title>
    <meta name="description" content="Kelas dan workshop gratis dari Ray Academy, offline maupun online. Pilih sesi dan daftar dengan akunmu.">
@endsection

@include('partials.event-styles')

@push('styles')
<style>
.evx-hero { background:linear-gradient(135deg,#eaf4fd 0%,#f3f8ff 55%,#eefaf5 100%); border-bottom:1px solid var(--border); padding:2.5rem 0 2.25rem; }
.evx-crumb { font-size:.78rem; color:var(--muted); margin-bottom:.6rem; }
.evx-crumb a { color:var(--muted); text-decoration:none; }
.evx-crumb a:hover { color:var(--blue); }
.evx-hero h1 { font-family:'Sora',sans-serif; font-size:clamp(1.6rem,3.5vw,2.3rem); font-weight:700; letter-spacing:-.02em; margin:0 0 .5rem; }
.evx-hero h1 span { color:var(--blue); }
.evx-hero p { color:var(--ink-2); max-width:620px; margin:0; }
.evx-h2 { font-family:'Sora',sans-serif; font-size:1.25rem; font-weight:700; margin:0 0 1.1rem; }
</style>
@endpush

@section('content')
<section class="evx-hero">
    <div class="ev-wrap">
        <div class="evx-crumb"><a href="{{ route('home') }}">Beranda</a> › Event</div>
        <h1>Event <span>Ray Academy</span></h1>
        <p>Kelas praktik dan workshop gratis, offline maupun online. Pilih sesi yang cocok, lalu daftar dengan akun Ray Academy-mu.</p>
    </div>
</section>

<div class="ev-wrap" style="padding-block:2.5rem 4rem;">
    <h2 class="evx-h2">Event mendatang</h2>
    @if($upcoming->isEmpty())
        <div class="ev-empty">
            <i class="far fa-calendar" aria-hidden="true"></i>
            <p style="margin:0;">Belum ada event yang dijadwalkan. Cek lagi nanti, ya.</p>
        </div>
    @else
        <div class="ev-grid">
            @foreach($upcoming as $event)
                @include('events._card')
            @endforeach
        </div>
    @endif

    @if($past->isNotEmpty())
        <h2 class="evx-h2" style="margin-top:3rem;">Event sebelumnya</h2>
        <div class="ev-grid">
            @foreach($past as $event)
                @include('events._card')
            @endforeach
        </div>
    @endif
</div>
@endsection
