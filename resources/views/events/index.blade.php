@extends('layouts.app')

@section('seo')
    <title>Event - Ray Academy</title>
    <meta name="description" content="Kelas dan workshop gratis dari Ray Academy, offline maupun online. Pilih sesi dan daftar dengan akunmu.">
@endsection

@include('partials.event-styles')

@push('styles')
<style>
.evx-body { background:#f6f9fe; padding-block:2.5rem 4rem; }
.evx-body .ev-wrap { max-width:1280px; }
.evx-h2 { font-family:'Sora',sans-serif; font-size:1.25rem; font-weight:700; color:#0b1f4d; margin:0 0 1.1rem; }
</style>
@endpush

@section('content')
<x-page-hero pill="Events & Workshops">
    <x-slot:crumbs><a href="{{ route('home') }}">Beranda</a> › Event</x-slot:crumbs>
    <x-slot:title>Join our <span>next event.</span></x-slot:title>
    Kelas praktik dan workshop, offline maupun online. Pilih sesi yang cocok, lalu daftar dengan akun Ray Academy-mu.
</x-page-hero>

<div class="evx-body"><div class="ev-wrap">
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
</div></div>
@endsection
