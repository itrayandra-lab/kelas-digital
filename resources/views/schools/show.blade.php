@extends('layouts.app')

@section('seo')
    <title>{{ $school['name'] }} - Ray Academy</title>
    <meta name="description" content="{{ Str::limit($school['description'], 155) }}">
@endsection

@include('schools._styles')

@section('content')
@php $schoolsBySlug = collect(config('homepage.families'))->keyBy('slug'); @endphp

<x-page-hero pill="RayAcademy School" keep-case :image="asset('img/home/'.$school['image'].'.webp')">
    <x-slot:crumbs><a href="{{ route('home') }}">Beranda</a> › <a href="{{ route('home') }}#learning-families">Bidang Belajar</a> › {{ $school['name'] }}</x-slot:crumbs>
    <x-slot:title>{{ $school['name'] }}</x-slot:title>
    {{ $school['description'] }}
    <x-slot:after>
        <div class="sc-actions">
            <a href="#kelas" class="hb hb-navy">Lihat Kelas <i class="fas fa-arrow-down" aria-hidden="true"></i></a>
            <a href="#tanya-raya" class="hb hb-outline">Tanya RAYA <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </x-slot:after>
</x-page-hero>

<section class="sc-sec" id="kelas">
    <div class="sc-wrap">
        <div class="sc-head">
            <div>
                <p class="sc-eyebrow">Programs &amp; classes</p>
                <h2 class="sc-title keep-case">Kelas di <span>{{ $school['name'] }}</span></h2>
            </div>
        </div>

        @forelse($programs as $program)
            <div class="sc-program">
                <h3><i class="fas {{ $program->icon ?: 'fa-book-open' }}" aria-hidden="true"></i> {{ $program->name }} <small>{{ $program->courses->count() }} kelas</small></h3>
                <div class="cc-grid">
                    @foreach($program->courses as $course)
                        @include('course._card')
                    @endforeach
                </div>
            </div>
        @empty
            <div class="sc-empty">
                <i class="fas fa-flask" aria-hidden="true"></i>
                <div>
                    <h3>Kelas {{ $school['name'] }} sedang disiapkan</h3>
                    <p>Sambil menunggu, ikuti event dan workshop kami atau tanyakan program yang cocok untuk Anda kepada RAYA.</p>
                </div>
                <div class="sc-actions">
                    <a href="{{ route('events.index') }}" class="hb hb-navy">Lihat Event</a>
                    <a href="#tanya-raya" class="hb hb-outline">Tanya RAYA</a>
                </div>
            </div>
        @endforelse
    </div>
</section>

@if($experts->isNotEmpty())
<section class="sc-sec alt" id="expert">
    <div class="sc-wrap">
        <div class="sc-head">
            <div>
                <p class="sc-eyebrow">Meet the experts</p>
                <h2 class="sc-title">Belajar bersama <span>para pakar</span></h2>
            </div>
            <a href="{{ route('experts.index') }}" class="sc-link">Semua expert RayAcademy <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="ex-grid">
            @foreach($experts as $expert)
                @include('schools._expert', ['schools' => $schoolsBySlug])
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="sc-sec">
    <div class="sc-wrap">
        <div class="sc-head">
            <div>
                <p class="sc-eyebrow">Explore more</p>
                <h2 class="sc-title">Bidang belajar <span>lainnya</span></h2>
            </div>
        </div>
        <div class="sc-families">
            @foreach($otherSchools as $family)
                @include('home._family-card')
            @endforeach
        </div>
    </div>
</section>
@endsection
