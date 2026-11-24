@extends('layouts.app')

@section('seo')
    <title>Para Expert - Ray Academy</title>
    <meta name="description" content="Kenali akademisi, peneliti, dan praktisi yang mengajar di RayAcademy, dikelompokkan menurut bidang belajarnya.">
@endsection

@include('schools._styles')

@push('styles')
<style>
.exp-photo{max-width:1040px;margin:0 auto;}
.exp-photo img{display:block;width:100%;height:auto;border-radius:24px;box-shadow:0 18px 44px rgba(31,77,140,.16);}
.exp-nav{display:flex;flex-wrap:wrap;justify-content:center;gap:.5rem;margin-top:1.5rem;}
.exp-nav a{font-size:.88rem;font-weight:700;color:#0b2a63;background:#fff;border:1px solid #d6e3f5;border-radius:999px;padding:.5rem 1rem;text-decoration:none;}
.exp-nav a:hover{border-color:#1f6fd1;color:#1f6fd1;}
.exp-group{scroll-margin-top:90px;}
.exp-group + .exp-group{margin-top:2.75rem;}
</style>
@endpush

@section('content')
@php $schoolsBySlug = collect(config('homepage.families'))->keyBy('slug'); @endphp

<x-page-hero pill="Meet our experts">
    <x-slot:crumbs><a href="{{ route('home') }}">Beranda</a> › Expert</x-slot:crumbs>
    <x-slot:title>Learn from <span>the experts.</span></x-slot:title>
    {{ $total }} akademisi, peneliti, dan praktisi mengajar di RayAcademy, dari sains dan industri kosmetik, Artificial Intelligence (AI), branding dan marketing, hingga kewirausahaan dan kepemimpinan.
</x-page-hero>

<section class="sc-sec">
    <div class="sc-wrap">
        <figure class="exp-photo">
            <img src="{{ asset('img/home/experts-m.webp') }}" srcset="{{ asset('img/home/experts-m.webp') }} 900w, {{ asset('img/home/experts.webp') }} 1672w" sizes="(min-width:1040px) 1040px, 100vw" width="1672" height="605" alt="Para expert RayAcademy" decoding="async">
        </figure>
        <nav class="exp-nav" aria-label="Lompat ke bidang belajar">
            @foreach($groups as $group)
                <a href="#{{ $group['school']['slug'] }}">{{ $group['school']['name'] }}</a>
            @endforeach
        </nav>
    </div>
</section>

<section class="sc-sec alt">
    <div class="sc-wrap">
        @foreach($groups as $group)
            <div class="exp-group" id="{{ $group['school']['slug'] }}">
                <div class="sc-head">
                    <div>
                        <p class="sc-eyebrow">{{ $group['experts']->count() }} expert</p>
                        <h2 class="sc-title keep-case">{{ $group['school']['name'] }}</h2>
                    </div>
                    <a href="{{ route('school.show', $group['school']['slug']) }}" class="sc-link">Lihat kelasnya <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </div>
                <div class="ex-grid">
                    @foreach($group['experts'] as $expert)
                        @include('schools._expert', ['schools' => $schoolsBySlug])
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection
