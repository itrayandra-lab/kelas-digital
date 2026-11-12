@extends('layouts.app')

@section('seo')
    <title>Kursus Saya - Ray Academy</title>
    <meta name="robots" content="noindex">
@endsection

@push('styles')
<style>
.db-wrap { max-width:1200px; margin:0 auto; padding:0 1.25rem; }
.db-hero { background:#F5F5F4; color:#0F1114; border-bottom:1px solid #E2E8F0; padding:2.25rem 0 2.5rem; position:relative; overflow:hidden; }
.db-hero::after { content:''; position:absolute; inset:0; background-image:linear-gradient(rgba(0,86,210,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,86,210,.03) 1px,transparent 1px); background-size:48px 48px; pointer-events:none; }
.db-hero-grid { position:relative; z-index:1; display:grid; grid-template-columns:1fr auto; gap:2rem; align-items:center; }
.db-hello { font-family:'Sora',sans-serif; font-size:clamp(1.5rem,3vw,2rem); font-weight:700; letter-spacing:-.02em; }
.db-sub { color:#6B7280; margin-top:.35rem; font-size:.92rem; }
.db-sub strong { color:#0F1114; }
.db-stats { display:grid; grid-template-columns:repeat(4,minmax(110px,1fr)); gap:.75rem; }
.db-stat { background:#fff; border:1px solid #E2E8F0; border-radius:12px; padding:.9rem 1rem; }
.db-stat i { width:30px; height:30px; border-radius:8px; background:#EEF4FF; color:#0056D2; font-size:.8rem; display:inline-flex; align-items:center; justify-content:center; }
.db-stat strong { display:block; font-family:'Sora',sans-serif; font-size:1.45rem; line-height:1.1; margin-top:.45rem; color:#0F1114; }
.db-stat span { font-size:.75rem; color:#6B7280; }

.db-body { display:grid; grid-template-columns:1fr 340px; gap:1.75rem; align-items:start; padding:2rem 0 1rem; }
.db-h2 { font-family:'Sora',sans-serif; font-size:1.15rem; font-weight:700; color:var(--ink); margin:0 0 .9rem; }
.db-box { background:#fff; border:1px solid var(--border); border-radius:16px; }

.db-resume { display:grid; grid-template-columns:280px 1fr; overflow:hidden; box-shadow:0 8px 24px rgba(10,22,40,.06); margin-bottom:2rem; }
.db-resume .db-thumb { aspect-ratio:auto; min-height:100%; }
.db-resume-body { padding:1.4rem 1.5rem; display:flex; flex-direction:column; gap:.5rem; }
.db-babs { display:flex; flex-direction:column; gap:.4rem; margin-top:.5rem; }
.db-bab { display:flex; align-items:center; gap:.6rem; font-size:.84rem; color:var(--ink); }
.db-bab .dot { width:22px; height:22px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:.62rem; background:var(--surf); color:var(--muted); border:1px solid var(--border); }
.db-bab.done .dot { background:#10b981; color:#fff; border-color:#10b981; }
.db-bab.now .dot { background:#0056D2; color:#fff; border-color:#0056D2; }
.db-bab.lock { color:var(--muted); }
.db-bab .name { flex:1; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.db-bab em { font-style:normal; font-size:.74rem; color:var(--muted); }

.db-tabs { display:flex; gap:.5rem; margin-bottom:1rem; flex-wrap:wrap; }
.db-tab { padding:.5rem .95rem; border-radius:999px; border:1px solid var(--border); background:#fff; font-weight:600; font-size:.84rem; color:var(--ink-2,#334155); cursor:pointer; }
.db-tab.on { background:var(--ink); color:#fff; border-color:var(--ink); }
.db-tab.warn { border-color:#f59e0b; color:#b45309; }
.db-tab.warn.on { background:#f59e0b; color:#fff; }

.db-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:1.1rem; }
.db-card { background:#fff; border:1px solid var(--border); border-radius:14px; overflow:hidden; display:flex; flex-direction:column; transition:box-shadow .2s; }
.db-card:hover { box-shadow:0 10px 28px rgba(10,22,40,.1); }
.db-thumb { position:relative; display:block; aspect-ratio:16/9; background:linear-gradient(135deg,#1474bc,#10b981); }
.db-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
.db-chip { position:absolute; top:.6rem; left:.6rem; font-size:.72rem; font-weight:700; padding:.25rem .6rem; border-radius:999px; background:#fff; color:var(--ink); }
.db-chip.done { background:#10b981; color:#fff; }
.db-chip.warn { background:#f59e0b; color:#fff; }
.db-body-card { padding:1rem 1.1rem 1.15rem; display:flex; flex-direction:column; flex:1; }
.db-cat { font-size:.72rem; font-weight:700; color:#0056D2; text-transform:uppercase; letter-spacing:.04em; }
.db-title { font-family:'Sora',sans-serif; font-size:1rem; font-weight:700; line-height:1.35; color:var(--ink); margin:.25rem 0 .5rem; }
.db-title a { color:inherit; text-decoration:none; }
.db-next { font-size:.8rem; color:var(--muted); margin-bottom:.85rem; display:flex; gap:.4rem; align-items:baseline; }
.db-next i { color:#0056D2; }
.db-prog-row { display:flex; justify-content:space-between; font-size:.75rem; color:var(--muted); margin:auto 0 .3rem; }
.db-prog-row strong { color:var(--ink); }
.db-prog { height:7px; background:var(--surf); border-radius:999px; overflow:hidden; }
.db-prog div { height:100%; background:#0056D2; border-radius:999px; }
.db-btn { margin-top:1rem; display:block; text-align:center; padding:.7rem; background:#0056D2; color:#fff; font-weight:700; font-size:.875rem; border-radius:10px; text-decoration:none; }
.db-btn:hover { background:#0048B0; color:#fff; }
.db-btn.ghost { background:#fff; color:var(--ink); border:1px solid var(--border); }
.db-btn.warn { background:#f59e0b; }
.db-empty { text-align:center; background:#fff; border:2px dashed var(--border); border-radius:16px; padding:2.5rem 1.5rem; }
.db-note { color:var(--muted); font-size:.9rem; }

.db-side { display:flex; flex-direction:column; gap:1.25rem; }
.db-side .db-box { padding:1.2rem 1.25rem; }
.db-side h3 { font-family:'Sora',sans-serif; font-size:.98rem; font-weight:700; color:var(--ink); margin-bottom:.8rem; }
.db-test { display:flex; align-items:center; gap:.75rem; padding:.65rem 0; border-top:1px solid var(--border); }
.db-test:first-of-type { border-top:0; padding-top:0; }
.db-test-info { flex:1; min-width:0; }
.db-test-info b { display:block; font-size:.84rem; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.db-test-info small { display:block; font-size:.74rem; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.db-score { text-align:right; flex-shrink:0; }
.db-score strong { font-family:'Sora',sans-serif; font-size:1.15rem; color:var(--ink); }
.db-tag { display:inline-block; font-size:.66rem; font-weight:700; padding:.15rem .45rem; border-radius:6px; background:var(--surf); color:var(--muted); }
.db-tag.ok { background:#dcfce7; color:#15803d; }
.db-tag.no { background:#fee2e2; color:#b91c1c; }
.db-tag.wait { background:#fef3c7; color:#b45309; }
.db-steps { counter-reset:s; display:flex; flex-direction:column; gap:.6rem; }
.db-steps li { list-style:none; display:flex; gap:.6rem; font-size:.84rem; color:var(--ink-2,#334155); line-height:1.45; }
.db-steps li::before { counter-increment:s; content:counter(s); width:22px; height:22px; flex-shrink:0; border-radius:50%; background:#EEF4FF; color:#0056D2; font-size:.72rem; font-weight:800; display:flex; align-items:center; justify-content:center; }

@media (max-width: 1024px) {
    .db-hero-grid, .db-body { grid-template-columns:1fr; }
}
@media (max-width: 720px) {
    .db-stats { grid-template-columns:1fr 1fr; }
    .db-grid, .db-resume { grid-template-columns:1fr; }
    .db-resume .db-thumb { aspect-ratio:16/9; }
}
</style>
@endpush

@section('content')
@php
    $resume = $ongoing->first();
    $total = $ongoing->count() + $finished->count();
@endphp

<section class="db-hero">
    <div class="db-wrap db-hero-grid">
        <div>
            <p class="db-sub" style="margin:0 0 .2rem;">Selamat datang kembali,</p>
            <h1 class="db-hello">{{ $user->name }} 👋</h1>
            <p class="db-sub">
                @if($resume)
                    Kamu sedang belajar <strong>{{ $resume['course']->title }}</strong>{{ $resume['progress']->percent() > 0 ? ", sudah ".$resume['progress']->percent()."%." : ". Yuk, mulai sekarang!" }}
                @elseif($total)
                    Semua kelasmu sudah selesai. Saatnya pilih kelas baru.
                @else
                    Ayo mulai kelas pertamamu hari ini.
                @endif
            </p>
        </div>
        <div class="db-stats" data-tour="stats">
            <div class="db-stat"><i class="fas fa-book-open"></i><strong>{{ $total }}</strong><span>Kelas diikuti</span></div>
            <div class="db-stat"><i class="fas fa-trophy"></i><strong>{{ $finished->count() }}</strong><span>Kelas selesai</span></div>
            <div class="db-stat"><i class="fas fa-play-circle"></i><strong>{{ $stats['lessons'] }}</strong><span>Materi dipelajari</span></div>
            <div class="db-stat"><i class="fas fa-clipboard-check"></i><strong>{{ $stats['passed'] }}</strong><span>Bab lulus post-test</span></div>
        </div>
    </div>
</section>

<div class="db-wrap" style="padding-bottom:4rem;" x-data="{ tab: '{{ $ongoing->isNotEmpty() ? 'ongoing' : ($unpaid->isNotEmpty() ? 'unpaid' : ($finished->isNotEmpty() ? 'finished' : 'ongoing')) }}' }">
    <div class="db-body">
        <div>
            @if($resume)
                @php
                    $p = $resume['progress'];
                    $nextSectionId = $p->nextStep()['section']->id ?? null;
                @endphp
                <h2 class="db-h2">Lanjutkan belajar</h2>
                <div class="db-box db-resume" data-tour="resume">
                    <a href="{{ route('course.show', $resume['course']->slug) }}" class="db-thumb">
                        @if($resume['course']->thumbnail_url)<img src="{{ $resume['course']->thumbnail_url }}" alt="{{ $resume['course']->title }}">@endif
                    </a>
                    <div class="db-resume-body">
                        @if($resume['course']->category)<div class="db-cat">{{ $resume['course']->category->name }}</div>@endif
                        <h3 class="db-title" style="font-size:1.2rem;margin:0;">{{ $resume['course']->title }}</h3>
                        <div class="db-next" style="margin:0;"><i class="fas fa-forward"></i> <span>Berikutnya: <strong style="color:var(--ink);">{{ $p->nextStepLabel() }}</strong></span></div>
                        <div>
                            <div class="db-prog-row" style="margin-top:.3rem;"><span>Progress kelas</span><strong>{{ $p->percent() }}%</strong></div>
                            <div class="db-prog"><div style="width:{{ $p->percent() }}%"></div></div>
                        </div>
                        <div class="db-babs">
                            @foreach($p->sections as $section)
                                @php
                                    $steps = $p->steps->filter(fn ($step) => $step['section']->id === $section->id);
                                    $done = $steps->filter(fn ($step) => $p->isStepDone($step))->count();
                                    $state = $steps->isNotEmpty() && $done === $steps->count() ? 'done' : ($section->id === $nextSectionId ? 'now' : 'lock');
                                @endphp
                                @continue($steps->isEmpty())
                                <div class="db-bab {{ $state }}">
                                    <span class="dot"><i class="fas {{ ['done' => 'fa-check', 'now' => 'fa-play', 'lock' => 'fa-lock'][$state] }}"></i></span>
                                    <span class="name">{{ $section->title }}</span>
                                    <em>{{ $done }}/{{ $steps->count() }} langkah</em>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('course.learn', $resume['course']->slug) }}" class="db-btn" style="align-self:flex-start;padding:.75rem 1.5rem;">
                            <i class="fas fa-play mr-1"></i> {{ $p->percent() > 0 ? 'Lanjut Belajar' : 'Mulai Belajar' }}
                        </a>
                    </div>
                </div>
            @endif

            <h2 class="db-h2">Kursus saya</h2>
            @if(! $total && $unpaid->isEmpty())
                <div class="db-empty">
                    <i class="fas fa-graduation-cap" style="font-size:2.2rem;color:#0056D2;opacity:.5;"></i>
                    <h3 style="font-family:'Sora',sans-serif;font-weight:700;color:var(--ink);margin-top:.75rem;">Kamu belum mengikuti kelas apa pun</h3>
                    <p class="db-note" style="margin-top:.35rem;">Pilih kelas pertamamu dari katalog. Ada juga kelas gratis.</p>
                    <a href="{{ route('course.index') }}" class="db-btn" style="display:inline-block;padding:.75rem 1.5rem;">Jelajahi Kelas</a>
                </div>
            @else
                <div class="db-tabs" data-tour="tabs">
                    <button type="button" class="db-tab" :class="tab === 'ongoing' && 'on'" @click="tab = 'ongoing'">Sedang dipelajari ({{ $ongoing->count() }})</button>
                    <button type="button" class="db-tab" :class="tab === 'finished' && 'on'" @click="tab = 'finished'">Selesai ({{ $finished->count() }})</button>
                    @if($unpaid->isNotEmpty())
                        <button type="button" class="db-tab warn" :class="tab === 'unpaid' && 'on'" @click="tab = 'unpaid'"><i class="fas fa-credit-card"></i> Menunggu pembayaran ({{ $unpaid->count() }})</button>
                    @endif
                </div>
                <div class="db-grid" x-show="tab === 'ongoing'" x-cloak>
                    @forelse($ongoing as $item)
                        @include('dashboard._course-card', $item)
                    @empty
                        <p class="db-note">{{ $total ? 'Semua kelasmu sudah selesai. Keren!' : 'Belum ada kelas yang aktif. Selesaikan pembayaran untuk mulai belajar.' }}</p>
                    @endforelse
                </div>
                <div class="db-grid" x-show="tab === 'finished'" x-cloak>
                    @forelse($finished as $item)
                        @include('dashboard._course-card', $item)
                    @empty
                        <p class="db-note">Belum ada kelas yang selesai. Ayo lanjutkan belajarmu!</p>
                    @endforelse
                </div>
                <div class="db-grid" x-show="tab === 'unpaid'" x-cloak>
                    @foreach($unpaid as $course)
                        <div class="db-card">
                            <a href="{{ route('course.show', $course->slug) }}" class="db-thumb">
                                @if($course->thumbnail_url)<img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" loading="lazy">@endif
                                <span class="db-chip warn"><i class="fas fa-clock"></i> Belum dibayar</span>
                            </a>
                            <div class="db-body-card">
                                @if($course->category)<div class="db-cat">{{ $course->category->name }}</div>@endif
                                <h3 class="db-title"><a href="{{ route('course.show', $course->slug) }}">{{ $course->title }}</a></h3>
                                <div class="db-next"><i class="fas fa-tag"></i> Rp{{ number_format($course->price, 0, ',', '.') }}</div>
                                <a href="{{ route('course.show', $course->slug) }}" class="db-btn warn" style="margin-top:auto;">Selesaikan Pembayaran</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <aside class="db-side">
            <div class="db-box">
                <h3><i class="fas fa-chart-line" style="color:#0056D2;margin-right:.35rem;"></i> Nilai tes terakhir</h3>
                @forelse($recentTests as $attempt)
                    <div class="db-test">
                        <div class="db-test-info">
                            <b>{{ $attempt->kind === 'pre' ? 'Pre-test' : 'Post-test' }} · {{ $attempt->section?->title }}</b>
                            <small>{{ $attempt->course?->title }}</small>
                        </div>
                        <div class="db-score">
                            @if($attempt->isPending())
                                <span class="db-tag wait">Diverifikasi</span>
                            @else
                                <strong>{{ $attempt->score }}</strong><br>
                                @if($attempt->kind === 'post')
                                    <span class="db-tag {{ $attempt->passed ? 'ok' : 'no' }}">{{ $attempt->passed ? 'Lulus' : 'Belum lulus' }}</span>
                                @else
                                    <span class="db-tag">Nilai awal</span>
                                @endif
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="db-note" style="font-size:.84rem;">Nilai pre-test dan post-test akan muncul di sini setelah kamu mengerjakannya.</p>
                @endforelse
            </div>

            <div class="db-box">
                <h3><i class="fas fa-route" style="color:#0056D2;margin-right:.35rem;"></i> Cara belajar di sini</h3>
                <ol class="db-steps">
                    <li>Kerjakan pre-test bab untuk mengukur pemahaman awal.</li>
                    <li>Pelajari video dan bacaan secara berurutan.</li>
                    <li>Lulus post-test bab supaya bab berikutnya terbuka.</li>
                    <li>Selesaikan semua bab untuk menuntaskan kelas.</li>
                </ol>
            </div>
        </aside>
    </div>

    @if($recommended->isNotEmpty())
        <h2 class="db-h2" style="margin-top:1rem;">Rekomendasi untukmu</h2>
        <div class="cc-grid">
            @foreach($recommended as $course)
                @include('course._card')
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
  { element: '[data-tour="stats"]', popover: { title: 'Ringkasan belajar', description: 'Jumlah kelas, materi yang sudah dipelajari, dan bab yang sudah lulus post-test.', side: 'bottom' } },
  { element: '[data-tour="resume"]', popover: { title: 'Lanjutkan belajar', description: 'Kelas yang sedang kamu pelajari, langkah berikutnya, dan progres tiap bab. Klik tombolnya untuk langsung lanjut.', side: 'bottom' } },
  { element: '[data-tour="tabs"]', popover: { title: 'Semua kelasmu', description: 'Lihat kelas yang sedang dipelajari, yang sudah selesai, atau yang masih menunggu pembayaran.', side: 'bottom' } },
  { popover: { title: 'Cara belajar', description: 'Setiap bab dimulai dengan pre-test, lalu materi video/bacaan berurutan, lalu post-test. Tombol Panduan di pojok kanan bawah bisa diklik kapan saja.' } }
]);
</script>
@endpush
