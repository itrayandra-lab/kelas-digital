@extends('layouts.app')

@section('seo')
    <title>Kursus Saya - Ray Academy</title>
    <meta name="robots" content="noindex">
@endsection

@push('styles')
<style>
.db-wrap { max-width:1200px; margin:0 auto; padding:0 1.25rem; }
.db-hero { background:linear-gradient(135deg,#0a1628 0%,#14304f 100%); color:#fff; padding:2.5rem 0 5.5rem; }
.db-hello { font-family:'Sora',sans-serif; font-size:clamp(1.5rem,3vw,2.1rem); font-weight:800; }
.db-stats { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-top:-3.5rem; }
.db-stat { background:#fff; border:1px solid var(--border); border-radius:14px; padding:1.1rem 1.25rem; display:flex; gap:.9rem; align-items:center; box-shadow:0 8px 24px rgba(10,22,40,.06); }
.db-stat i { width:42px; height:42px; border-radius:12px; display:flex; align-items:center; justify-content:center; background:var(--blue-xl); color:var(--blue); font-size:1.05rem; }
.db-stat strong { display:block; font-family:'Sora',sans-serif; font-size:1.4rem; color:var(--ink); line-height:1.1; }
.db-stat span { font-size:.8rem; color:var(--muted); }
.db-h2 { font-family:'Sora',sans-serif; font-size:1.25rem; font-weight:800; color:var(--ink); margin:2.25rem 0 1rem; }

.db-resume { display:grid; grid-template-columns:320px 1fr; background:#fff; border:1px solid var(--border); border-radius:16px; overflow:hidden; box-shadow:0 8px 24px rgba(10,22,40,.06); }
.db-resume .db-thumb { aspect-ratio:auto; min-height:190px; }
.db-resume-body { padding:1.5rem; display:flex; flex-direction:column; gap:.5rem; justify-content:center; }

.db-tabs { display:flex; gap:.5rem; margin-bottom:1rem; }
.db-tab { padding:.55rem 1rem; border-radius:999px; border:1px solid var(--border); background:#fff; font-weight:600; font-size:.875rem; color:var(--ink-2); cursor:pointer; }
.db-tab.on { background:var(--ink); color:#fff; border-color:var(--ink); }

.db-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1.25rem; }
.db-card { background:#fff; border:1px solid var(--border); border-radius:14px; overflow:hidden; display:flex; flex-direction:column; transition:box-shadow .2s; }
.db-card:hover { box-shadow:0 10px 28px rgba(10,22,40,.1); }
.db-thumb { position:relative; display:block; aspect-ratio:16/9; background:linear-gradient(135deg,#1474bc,#10b981); }
.db-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
.db-chip { position:absolute; top:.6rem; left:.6rem; font-size:.72rem; font-weight:700; padding:.25rem .6rem; border-radius:999px; background:#fff; color:var(--ink); }
.db-chip.done { background:#10b981; color:#fff; }
.db-body { padding:1rem 1.1rem 1.15rem; display:flex; flex-direction:column; flex:1; }
.db-cat { font-size:.72rem; font-weight:700; color:var(--blue); text-transform:uppercase; letter-spacing:.04em; }
.db-title { font-family:'Sora',sans-serif; font-size:1rem; font-weight:700; line-height:1.35; color:var(--ink); margin:.25rem 0 .5rem; }
.db-title a { color:inherit; text-decoration:none; }
.db-next { font-size:.8rem; color:var(--muted); margin-bottom:.85rem; display:flex; gap:.4rem; align-items:baseline; }
.db-next i { color:var(--blue); }
.db-prog-row { display:flex; justify-content:space-between; font-size:.75rem; color:var(--muted); margin:auto 0 .3rem; }
.db-prog-row strong { color:var(--ink); }
.db-prog { height:7px; background:var(--surf); border-radius:999px; overflow:hidden; }
.db-prog div { height:100%; background:var(--blue); border-radius:999px; }
.db-btn { margin-top:1rem; display:block; text-align:center; padding:.7rem; background:var(--blue); color:#fff; font-weight:700; font-size:.875rem; border-radius:10px; text-decoration:none; }
.db-btn:hover { background:var(--blue-d); }
.db-btn.ghost { background:#fff; color:var(--ink); border:1px solid var(--border); }
.db-empty { text-align:center; background:#fff; border:2px dashed var(--border); border-radius:16px; padding:3rem 1.5rem; }

@media (max-width: 1024px) { .db-grid { grid-template-columns:1fr 1fr; } }
@media (max-width: 720px) {
    .db-grid, .db-resume { grid-template-columns:1fr; }
    .db-stats { grid-template-columns:1fr 1fr 1fr; gap:.5rem; }
    .db-stat { flex-direction:column; text-align:center; padding:.9rem .5rem; gap:.4rem; }
    .db-stat i { display:none; }
}
</style>
@endpush

@section('content')
@php $resume = $ongoing->first(); @endphp

<section class="db-hero">
    <div class="db-wrap">
        <p style="color:rgba(255,255,255,.7);font-size:.9rem;">Selamat datang kembali,</p>
        <h1 class="db-hello">{{ $user->name }} 👋</h1>
    </div>
</section>

<div class="db-wrap" style="padding-bottom:4rem;" x-data="{ tab: 'ongoing' }">
    <div class="db-stats" data-tour="stats">
        <div class="db-stat"><i class="fas fa-book-open"></i><div><strong>{{ $ongoing->count() + $finished->count() }}</strong><span>Kelas diikuti</span></div></div>
        <div class="db-stat"><i class="fas fa-spinner"></i><div><strong>{{ $ongoing->count() }}</strong><span>Sedang dipelajari</span></div></div>
        <div class="db-stat"><i class="fas fa-trophy"></i><div><strong>{{ $finished->count() }}</strong><span>Selesai</span></div></div>
    </div>

    @if($resume)
        @php $p = $resume['progress']; @endphp
        <h2 class="db-h2">Lanjutkan belajar</h2>
        <div class="db-resume" data-tour="resume">
            <a href="{{ route('course.show', $resume['course']->slug) }}" class="db-thumb">
                @if($resume['course']->thumbnail_url)<img src="{{ $resume['course']->thumbnail_url }}" alt="{{ $resume['course']->title }}">@endif
            </a>
            <div class="db-resume-body">
                @if($resume['course']->category)<div class="db-cat">{{ $resume['course']->category->name }}</div>@endif
                <h3 class="db-title" style="font-size:1.25rem;margin:0;">{{ $resume['course']->title }}</h3>
                <div class="db-next" style="margin:0;"><i class="fas fa-forward"></i> Berikutnya: <strong style="color:var(--ink);">{{ $p->nextStepLabel() }}</strong></div>
                <div style="max-width:420px;">
                    <div class="db-prog-row" style="margin-top:.4rem;"><span>Progress</span><strong>{{ $p->percent() }}%</strong></div>
                    <div class="db-prog"><div style="width:{{ $p->percent() }}%"></div></div>
                </div>
                <a href="{{ route('course.learn', $resume['course']->slug) }}" class="db-btn" style="align-self:flex-start;padding:.75rem 1.5rem;">
                    <i class="fas fa-play mr-1"></i> {{ $p->percent() > 0 ? 'Lanjut Belajar' : 'Mulai Belajar' }}
                </a>
            </div>
        </div>
    @endif

    <h2 class="db-h2">Kursus saya</h2>
    @if($ongoing->isEmpty() && $finished->isEmpty())
        <div class="db-empty">
            <i class="fas fa-graduation-cap" style="font-size:2.2rem;color:var(--blue);opacity:.5;"></i>
            <h3 style="font-family:'Sora',sans-serif;font-weight:700;color:var(--ink);margin-top:.75rem;">Kamu belum mengikuti kelas apa pun</h3>
            <p style="color:var(--muted);font-size:.9rem;margin-top:.35rem;">Pilih kelas pertamamu dari katalog. Ada juga kelas gratis.</p>
            <a href="{{ route('course.index') }}" class="db-btn" style="display:inline-block;padding:.75rem 1.5rem;">Jelajahi Kelas</a>
        </div>
    @else
        <div class="db-tabs" data-tour="tabs">
            <button type="button" class="db-tab" :class="tab === 'ongoing' && 'on'" @click="tab = 'ongoing'">Sedang dipelajari ({{ $ongoing->count() }})</button>
            <button type="button" class="db-tab" :class="tab === 'finished' && 'on'" @click="tab = 'finished'">Selesai ({{ $finished->count() }})</button>
        </div>
        <div class="db-grid" x-show="tab === 'ongoing'">
            @forelse($ongoing as $item)
                @include('dashboard._course-card', $item)
            @empty
                <p style="color:var(--muted);font-size:.9rem;">Semua kelasmu sudah selesai. Keren!</p>
            @endforelse
        </div>
        <div class="db-grid" x-show="tab === 'finished'" x-cloak>
            @forelse($finished as $item)
                @include('dashboard._course-card', $item)
            @empty
                <p style="color:var(--muted);font-size:.9rem;">Belum ada kelas yang selesai. Ayo lanjutkan belajarmu!</p>
            @endforelse
        </div>
    @endif

    @if($recommended->isNotEmpty())
        <h2 class="db-h2">Rekomendasi untukmu</h2>
        <div class="db-grid">
            @foreach($recommended as $course)
                <a href="{{ route('course.show', $course->slug) }}" class="db-card" style="text-decoration:none;">
                    <div class="db-thumb">
                        @if($course->thumbnail_url)<img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" loading="lazy">@endif
                        @if($course->isFreeClass())<span class="db-chip" style="background:#10b981;color:#fff;">Gratis</span>@endif
                    </div>
                    <div class="db-body">
                        @if($course->category)<div class="db-cat">{{ $course->category->name }}</div>@endif
                        <h3 class="db-title">{{ $course->title }}</h3>
                        <div style="margin-top:auto;font-weight:800;color:var(--ink);">{{ $course->isFreeClass() ? 'Gratis' : 'Rp'.number_format($course->price, 0, ',', '.') }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>

@endsection

@push('scripts')
@include('partials.tour-assets')
<script>
KATour.page('student-dashboard', [
  { popover: { title: 'Selamat datang di Ray Academy!', description: 'Ini halaman Kursus Saya, tempat semua kelas yang kamu ikuti.' } },
  { element: '[data-tour="resume"]', popover: { title: 'Lanjutkan belajar', description: 'Kelas yang sedang kamu pelajari dan langkah berikutnya. Klik tombolnya untuk langsung lanjut.', side: 'bottom' } },
  { element: '[data-tour="tabs"]', popover: { title: 'Semua kelasmu', description: 'Lihat kelas yang sedang dipelajari atau yang sudah selesai.', side: 'bottom' } },
  { popover: { title: 'Cara belajar', description: 'Setiap kelas dimulai dengan pre-test, lalu materi video/bacaan berurutan, lalu post-test. Tombol Panduan di pojok kanan bawah bisa diklik kapan saja.' } }
]);
</script>
@endpush
