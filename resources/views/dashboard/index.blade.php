@extends('layouts.app')

@section('seo')
    <title>Kursus Saya - Ray Academy</title>
    <meta name="robots" content="noindex">
@endsection

@push('styles')
<style>
.db-wrap { max-width:1200px; margin:0 auto; padding:0 1.25rem; }
.db-h2 { font-family:'Sora',sans-serif; font-size:1.15rem; font-weight:700; color:var(--ink); margin:0 0 1rem; }
.db-box { background:#fff; border:1px solid var(--border); border-radius:18px; }
.db-note { color:var(--muted); font-size:.9rem; }

/* Greeting */
.db-hero { background:linear-gradient(135deg,#eaf4fd 0%,#f3f8ff 55%,#eefaf5 100%); border-bottom:1px solid var(--border); padding:2.25rem 0; }
.db-hero-grid { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:2rem; align-items:center; }
.db-me { display:flex; align-items:center; gap:1.1rem; }
.db-avatar { width:64px; height:64px; flex-shrink:0; border-radius:50%; background:var(--blue); color:#fff; font-family:'Sora',sans-serif; font-weight:700; font-size:1.4rem; display:flex; align-items:center; justify-content:center; box-shadow:0 0 0 4px #fff; }
.db-greet { color:var(--muted); font-size:.92rem; margin:0; }
.db-hello { font-family:'Sora',sans-serif; font-size:clamp(1.45rem,3vw,1.9rem); font-weight:700; letter-spacing:-.02em; color:var(--ink); margin:.1rem 0 .3rem; }
.db-sub { color:#4b5b73; font-size:.93rem; margin:0; }
.db-sub strong { color:var(--ink); }
.db-stats { display:grid; grid-template-columns:repeat(4,minmax(120px,1fr)); gap:.75rem; }
.db-stat { background:#fff; border-radius:16px; padding:1rem; box-shadow:0 1px 2px rgba(10,22,40,.05); display:flex; flex-direction:column; gap:.15rem; }
.db-stat i { width:36px; height:36px; border-radius:12px; display:inline-flex; align-items:center; justify-content:center; font-size:.9rem; margin-bottom:.45rem; }
.db-stat strong, .db-score, .db-prog-line strong, .db-mods-meta strong { font-variant-numeric:tabular-nums; }
.db-stat strong { font-family:'Sora',sans-serif; font-size:1.5rem; line-height:1.1; color:var(--ink); }
.db-stat span { font-size:.78rem; color:var(--muted); line-height:1.3; }
.db-stat.c1 i { background:#e3f0fb; color:#1474bc; }
.db-stat.c2 i { background:#dff6ec; color:#0f8a5f; }
.db-stat.c3 i { background:#fff1db; color:#b45f06; }
.db-stat.c4 i { background:#efeafe; color:#6d4ed8; }

/* Origin prompt */
.db-origin { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:1rem; padding:1.1rem 1.25rem; margin:1.75rem 0 0; background:#fffbeb; border-color:#fcd34d; }
.db-origin .db-h2 { margin-bottom:.2rem; font-size:1.02rem; }
.db-origin-fields { display:flex; flex-wrap:wrap; gap:.5rem; flex:1 1 22rem; justify-content:flex-end; }
.db-origin-fields select, .db-origin-fields input { padding:.65rem .85rem; border:1px solid var(--border); border-radius:12px; font:inherit; font-size:.9rem; background:#fff; min-width:0; flex:1 1 10rem; min-height:44px; }
.db-origin-error { flex-basis:100%; margin:0; color:#dc2626; font-size:.85rem; }

/* Buttons */
.db-btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; min-height:44px; padding:.65rem 1.3rem; background:var(--blue); color:#fff; font-weight:700; font-size:.9rem; border:0; border-radius:12px; text-decoration:none; cursor:pointer; transition:background .2s; white-space:nowrap; }
.db-btn:hover { background:var(--blue-d); color:#fff; }
.db-btn.ghost { background:#fff; color:var(--ink); border:1px solid var(--border); }
.db-btn.ghost:hover { background:var(--surf); color:var(--ink); }
.db-btn.warn { background:#d97706; }
.db-btn.warn:hover { background:#b45309; }
.db-btn:focus-visible, .db-tab:focus-visible, .db-row a:focus-visible, .db-origin-fields :focus-visible { outline:2px solid var(--blue); outline-offset:2px; }

.db-body { display:grid; grid-template-columns:minmax(0,1fr) 340px; gap:2rem; align-items:start; padding:2rem 0 1rem; }

/* Continue learning */
.db-resume { display:grid; grid-template-columns:300px minmax(0,1fr); overflow:hidden; box-shadow:0 10px 30px rgba(10,22,40,.07); margin-bottom:2.25rem; }
.db-resume-thumb { position:relative; display:block; background:linear-gradient(135deg,#1474bc,#10b981); min-height:100%; }
.db-resume-thumb img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
.db-resume-body { padding:1.4rem 1.5rem; display:flex; flex-direction:column; gap:.75rem; }
.db-cat { font-size:.72rem; font-weight:700; color:var(--blue); text-transform:uppercase; letter-spacing:.05em; }
.db-resume-title { font-family:'Sora',sans-serif; font-size:1.25rem; font-weight:700; line-height:1.3; color:var(--ink); margin:.15rem 0 0; }
.db-nextbox { display:flex; gap:.75rem; align-items:center; background:var(--blue-xl); border-radius:12px; padding:.7rem .9rem; }
.db-nextbox i { width:32px; height:32px; flex-shrink:0; border-radius:50%; background:#fff; color:var(--blue); display:flex; align-items:center; justify-content:center; font-size:.75rem; }
.db-nextbox small { display:block; font-size:.72rem; color:var(--muted); }
.db-nextbox b { display:block; font-size:.88rem; color:var(--ink); line-height:1.35; }
.db-mods { display:flex; gap:4px; }
.db-mods span { flex:1; height:8px; border-radius:999px; background:#e4eaf2; }
.db-mods span.done { background:#10b981; }
.db-mods span.now { background:var(--blue); }
.db-mods-meta { display:flex; justify-content:space-between; gap:1rem; font-size:.78rem; color:var(--muted); margin-top:.4rem; }
.db-mods-meta strong { color:var(--ink); }

/* My courses */
.db-tabs { display:flex; gap:1.5rem; border-bottom:1px solid var(--border); margin-bottom:1rem; overflow-x:auto; }
.db-tab { background:none; border:0; border-bottom:3px solid transparent; padding:.6rem 0; font:inherit; font-weight:600; font-size:.9rem; color:var(--muted); cursor:pointer; white-space:nowrap; min-height:44px; }
.db-tab:hover { color:var(--ink); }
.db-tab.on { color:var(--ink); border-color:var(--blue); }
.db-tab.warn.on { border-color:#d97706; }
.db-tab b { display:inline-block; min-width:1.5rem; padding:0 .4rem; margin-left:.3rem; border-radius:999px; background:var(--surf); font-size:.75rem; color:var(--muted); }
.db-list { display:flex; flex-direction:column; gap:.85rem; }
.db-row { display:grid; grid-template-columns:168px minmax(0,1fr) auto; gap:1.1rem; align-items:center; padding:.85rem; background:#fff; border:1px solid var(--border); border-radius:16px; transition:box-shadow .2s, border-color .2s; }
.db-row:hover { box-shadow:0 8px 24px rgba(10,22,40,.08); border-color:#d3dde9; }
.db-row-thumb { position:relative; display:block; aspect-ratio:16/9; border-radius:12px; overflow:hidden; background:linear-gradient(135deg,#1474bc,#10b981); }
.db-row-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
.db-row-info { min-width:0; }
.db-row-title { font-family:'Sora',sans-serif; font-size:.98rem; font-weight:700; line-height:1.35; margin:.15rem 0 .3rem; }
.db-row-title a { color:var(--ink); text-decoration:none; }
.db-row-title a:hover { color:var(--blue); }
.db-row-next { font-size:.8rem; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:.55rem; }
.db-prog-line { display:flex; align-items:center; gap:.6rem; font-size:.78rem; color:var(--muted); }
.db-prog { flex:1; max-width:260px; height:7px; background:#e4eaf2; border-radius:999px; overflow:hidden; }
.db-prog div { height:100%; background:var(--blue); border-radius:999px; }
.db-prog.done div { background:#10b981; }
.db-prog-line strong { color:var(--ink); }
.db-badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:700; padding:.2rem .55rem; border-radius:999px; }
.db-badge.done { background:#dcfce7; color:#15803d; }
.db-badge.warn { background:#fef3c7; color:#92400e; }
.db-empty { text-align:center; background:#fff; border:2px dashed var(--border); border-radius:18px; padding:2.75rem 1.5rem; }
.db-empty-icon { width:64px; height:64px; margin:0 auto .9rem; border-radius:50%; background:var(--blue-xl); color:var(--blue); display:flex; align-items:center; justify-content:center; font-size:1.5rem; }

/* Sidebar */
.db-side { display:flex; flex-direction:column; gap:1.25rem; }
.db-side .db-box { padding:1.25rem; }
.db-side h3 { font-family:'Sora',sans-serif; font-size:1rem; font-weight:700; color:var(--ink); margin:0 0 .9rem; display:flex; align-items:center; gap:.5rem; }
.db-side h3 i { color:var(--blue); }
.db-test { display:flex; align-items:center; gap:.8rem; padding:.7rem 0; border-top:1px solid var(--border); }
.db-test:first-of-type { border-top:0; padding-top:0; }
.db-score { width:46px; height:46px; flex-shrink:0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-family:'Sora',sans-serif; font-weight:700; font-size:.95rem; border:3px solid #cfe3f5; color:var(--blue); }
.db-score.ok { border-color:#86efac; color:#15803d; }
.db-score.no { border-color:#fca5a5; color:#b91c1c; }
.db-score.wait { border-color:#fcd34d; color:#92400e; font-size:.8rem; }
.db-test-info { flex:1; min-width:0; }
.db-test-info b { display:block; font-size:.85rem; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.db-test-info small { display:block; font-size:.75rem; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.db-test-info em { font-style:normal; font-size:.72rem; font-weight:700; }
.db-test-info em.ok { color:#15803d; }
.db-test-info em.no { color:#b91c1c; }
.db-test-info em.wait { color:#92400e; }
.db-test-info em.pre { color:var(--blue); }
.db-steps { margin:0; padding:0; display:flex; flex-direction:column; gap:.9rem; }
.db-steps li { list-style:none; display:flex; gap:.75rem; align-items:flex-start; font-size:.85rem; color:#4b5b73; line-height:1.45; }
.db-steps li i { width:34px; height:34px; flex-shrink:0; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:.85rem; }
.db-steps li b { display:block; color:var(--ink); font-size:.88rem; }

@media (prefers-reduced-motion: reduce) { .db-row, .db-btn { transition:none; } }
@media (max-width: 1024px) {
    .db-hero-grid, .db-body { grid-template-columns:minmax(0,1fr); }
}
@media (max-width: 720px) {
    .db-stats { grid-template-columns:1fr 1fr; }
    .db-resume { grid-template-columns:minmax(0,1fr); }
    .db-resume-thumb { aspect-ratio:16/9; }
    .db-row { grid-template-columns:110px minmax(0,1fr); }
    .db-row > .db-btn { grid-column:1 / -1; }
    .db-avatar { width:52px; height:52px; font-size:1.15rem; }
}
</style>
@endpush

@section('content')
@php
    $resume = $ongoing->first();
    $total = $ongoing->count() + $finished->count();
    $hour = now()->hour;
    $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
    $initials = collect(preg_split('/\s+/', trim($user->name)))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
@endphp

<section class="db-hero">
    <div class="db-wrap db-hero-grid">
        <div class="db-me">
            <div class="db-avatar" aria-hidden="true">{{ $initials }}</div>
            <div>
                <p class="db-greet">{{ $greeting }},</p>
                <h1 class="db-hello">{{ $user->name }}</h1>
                <p class="db-sub">
                    @if($resume)
                        Kamu sedang belajar <strong>{{ $resume['course']->title }}</strong>{{ $resume['progress']->percent() > 0 ? ', sudah '.$resume['progress']->percent().'%. Ayo lanjutkan!' : '. Yuk, mulai hari ini!' }}
                    @elseif($total)
                        Semua kelasmu sudah selesai. Saatnya pilih kelas baru.
                    @else
                        Ayo mulai kelas pertamamu hari ini.
                    @endif
                </p>
            </div>
        </div>
        <div class="db-stats" data-tour="stats">
            <div class="db-stat c1"><i class="fas fa-book-open" aria-hidden="true"></i><strong>{{ $total }}</strong><span>Kelas diikuti</span></div>
            <div class="db-stat c2"><i class="fas fa-play" aria-hidden="true"></i><strong>{{ $stats['lessons'] }}</strong><span>Materi dipelajari</span></div>
            <div class="db-stat c3"><i class="fas fa-clipboard-check" aria-hidden="true"></i><strong>{{ $stats['passed'] }}</strong><span>Modul lulus</span></div>
            <div class="db-stat c4"><i class="fas fa-trophy" aria-hidden="true"></i><strong>{{ $finished->count() }}</strong><span>Kelas selesai</span></div>
        </div>
    </div>
</section>

<div class="db-wrap" style="padding-bottom:4rem;" x-data="{ tab: '{{ $ongoing->isNotEmpty() ? 'ongoing' : ($unpaid->isNotEmpty() ? 'unpaid' : ($finished->isNotEmpty() ? 'finished' : 'ongoing')) }}' }">
    @unless($user->origin_type)
        {{-- Accounts made before sign-up asked this; the box goes away once answered. --}}
        <form method="POST" action="{{ route('profile.origin') }}" class="db-box db-origin" x-data="{ type: @js(old('origin_type', '')) }">
            @csrf @method('PUT')
            <div>
                <h2 class="db-h2">Kamu belajar dari mana?</h2>
                <p class="db-sub" style="font-size:.86rem;">Isi sekali saja, supaya kami tahu asal kampus atau kantor peserta.</p>
            </div>
            <div class="db-origin-fields">
                <label class="sr-only" for="db-origin-type">Kamu saat ini</label>
                <select id="db-origin-type" name="origin_type" x-model="type" required>
                    <option value="" disabled>Kamu saat ini…</option>
                    @foreach(\App\Models\User::ORIGINS as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <label class="sr-only" for="db-institution">Nama universitas atau kantor</label>
                <input id="db-institution" name="institution" value="{{ old('institution') }}" maxlength="150" list="db-institution-list"
                    autocomplete="organization" placeholder="Nama universitas atau kantor" x-show="type !== 'umum'" :required="type !== 'umum'">
                <datalist id="db-institution-list">
                    @foreach(\App\Models\User::knownInstitutions() as $name)<option value="{{ $name }}">@endforeach
                </datalist>
                <button type="submit" class="db-btn">Simpan</button>
            </div>
            @if($errors->has('origin_type') || $errors->has('institution'))
                <p class="db-origin-error" role="alert">{{ $errors->first('origin_type') ?: $errors->first('institution') }}</p>
            @endif
        </form>
    @endunless

    <div class="db-body">
        <div>
            @if($resume)
                @php
                    $p = $resume['progress'];
                    $nextSectionId = $p->nextStep()['section']->id ?? null;
                    $modules = $p->sections->map(function ($section) use ($p, $nextSectionId) {
                        $steps = $p->steps->filter(fn ($step) => $step['section']->id === $section->id);
                        $done = $steps->filter(fn ($step) => $p->isStepDone($step))->count();

                        return ['title' => $section->title, 'state' => $steps->isNotEmpty() && $done === $steps->count() ? 'done' : ($section->id === $nextSectionId ? 'now' : 'lock')];
                    });
                    $current = $modules->search(fn ($m) => $m['state'] === 'now');
                @endphp
                <h2 class="db-h2">Lanjutkan belajar</h2>
                <div class="db-box db-resume" data-tour="resume">
                    <a href="{{ route('course.show', $resume['course']->slug) }}" class="db-resume-thumb" tabindex="-1" aria-hidden="true">
                        @if($resume['course']->thumbnail_url)<img src="{{ $resume['course']->thumbnail_url }}" alt="">@endif
                    </a>
                    <div class="db-resume-body">
                        <div>
                            @if($resume['course']->category)<div class="db-cat">{{ $resume['course']->category->name }}</div>@endif
                            <h3 class="db-resume-title">{{ $resume['course']->title }}</h3>
                        </div>
                        <div class="db-nextbox">
                            <i class="fas fa-play" aria-hidden="true"></i>
                            <div><small>Langkah berikutnya</small><b>{{ $p->nextStepLabel() }}</b></div>
                        </div>
                        <div>
                            <div class="db-mods" role="img" aria-label="{{ $modules->where('state', 'done')->count() }} dari {{ $modules->count() }} modul selesai">
                                @foreach($modules as $m)<span class="{{ $m['state'] }}" title="{{ $m['title'] }}"></span>@endforeach
                            </div>
                            <div class="db-mods-meta">
                                <span>{{ $current !== false ? 'Sedang di modul '.($current + 1).' dari '.$modules->count() : $modules->count().' modul' }}</span>
                                <strong>{{ $p->percent() }}% selesai</strong>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('course.learn', $resume['course']->slug) }}" class="db-btn">
                                <i class="fas fa-play" aria-hidden="true"></i> {{ $p->percent() > 0 ? 'Lanjut Belajar' : 'Mulai Belajar' }}
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <h2 class="db-h2">Kursus saya</h2>
            @if(! $total && $unpaid->isEmpty())
                <div class="db-empty">
                    <div class="db-empty-icon"><i class="fas fa-graduation-cap" aria-hidden="true"></i></div>
                    <h3 style="font-family:'Sora',sans-serif;font-weight:700;color:var(--ink);margin:0;">Kamu belum mengikuti kelas apa pun</h3>
                    <p class="db-note" style="margin:.4rem 0 1.1rem;">Pilih kelas pertamamu dari katalog. Ada juga kelas gratis.</p>
                    <a href="{{ route('course.index') }}" class="db-btn">Jelajahi Kelas</a>
                </div>
            @else
                <div class="db-tabs" data-tour="tabs">
                    <button type="button" class="db-tab" :class="tab === 'ongoing' && 'on'" :aria-pressed="tab === 'ongoing'" @click="tab = 'ongoing'">Sedang dipelajari<b>{{ $ongoing->count() }}</b></button>
                    <button type="button" class="db-tab" :class="tab === 'finished' && 'on'" :aria-pressed="tab === 'finished'" @click="tab = 'finished'">Selesai<b>{{ $finished->count() }}</b></button>
                    @if($unpaid->isNotEmpty())
                        <button type="button" class="db-tab warn" :class="tab === 'unpaid' && 'on'" :aria-pressed="tab === 'unpaid'" @click="tab = 'unpaid'">Menunggu pembayaran<b>{{ $unpaid->count() }}</b></button>
                    @endif
                </div>
                <div class="db-list" x-show="tab === 'ongoing'" x-cloak>
                    @forelse($ongoing as $item)
                        @include('dashboard._course-card', $item)
                    @empty
                        <p class="db-note">{{ $total ? 'Semua kelasmu sudah selesai. Keren!' : 'Belum ada kelas yang aktif. Selesaikan pembayaran untuk mulai belajar.' }}</p>
                    @endforelse
                </div>
                <div class="db-list" x-show="tab === 'finished'" x-cloak>
                    @forelse($finished as $item)
                        @include('dashboard._course-card', $item)
                    @empty
                        <p class="db-note">Belum ada kelas yang selesai. Ayo lanjutkan belajarmu!</p>
                    @endforelse
                </div>
                <div class="db-list" x-show="tab === 'unpaid'" x-cloak>
                    @foreach($unpaid as $course)
                        <div class="db-row">
                            <a href="{{ route('course.show', $course->slug) }}" class="db-row-thumb" tabindex="-1" aria-hidden="true">
                                @if($course->thumbnail_url)<img src="{{ $course->thumbnail_url }}" alt="" loading="lazy">@endif
                            </a>
                            <div class="db-row-info">
                                <span class="db-badge warn"><i class="fas fa-clock" aria-hidden="true"></i> Belum dibayar</span>
                                <h3 class="db-row-title"><a href="{{ route('course.show', $course->slug) }}">{{ $course->title }}</a></h3>
                                <div class="db-row-next">Rp{{ number_format($course->price, 0, ',', '.') }}</div>
                            </div>
                            <a href="{{ route('course.show', $course->slug) }}" class="db-btn warn">Selesaikan Pembayaran</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <aside class="db-side">
            <div class="db-box">
                <h3><i class="fas fa-chart-line" aria-hidden="true"></i> Nilai tes terakhir</h3>
                @forelse($recentTests as $attempt)
                    @php
                        $state = $attempt->isPending() ? 'wait' : ($attempt->kind === 'pre' ? 'pre' : ($attempt->passed ? 'ok' : 'no'));
                        $label = ['wait' => 'Sedang diverifikasi', 'pre' => 'Nilai awal', 'ok' => 'Lulus', 'no' => 'Belum lulus'][$state];
                    @endphp
                    <div class="db-test">
                        <div class="db-score {{ $state }}">{{ $state === 'wait' ? '…' : $attempt->score }}</div>
                        <div class="db-test-info">
                            <b>{{ $attempt->kind === 'pre' ? 'Pre-test' : 'Post-test' }} · {{ $attempt->section?->title }}</b>
                            <small>{{ $attempt->course?->title }}</small>
                            <em class="{{ $state }}">{{ $label }}</em>
                        </div>
                    </div>
                @empty
                    <p class="db-note" style="font-size:.85rem;margin:0;">Nilai pre-test dan post-test akan muncul di sini setelah kamu mengerjakannya.</p>
                @endforelse
            </div>

            <div class="db-box">
                <h3><i class="fas fa-route" aria-hidden="true"></i> Cara belajar di sini</h3>
                <ol class="db-steps">
                    <li><i class="fas fa-clipboard-list" style="background:#e3f0fb;color:#1474bc;" aria-hidden="true"></i><span><b>Pre-test</b>Ukur pemahaman awalmu di tiap modul.</span></li>
                    <li><i class="fas fa-play" style="background:#dff6ec;color:#0f8a5f;" aria-hidden="true"></i><span><b>Pelajari materi</b>Tonton video dan baca materi secara berurutan.</span></li>
                    <li><i class="fas fa-clipboard-check" style="background:#fff1db;color:#b45f06;" aria-hidden="true"></i><span><b>Post-test</b>Lulus post-test supaya modul berikutnya terbuka.</span></li>
                    <li><i class="fas fa-trophy" style="background:#efeafe;color:#6d4ed8;" aria-hidden="true"></i><span><b>Selesaikan kelas</b>Tuntaskan semua modul sampai akhir.</span></li>
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
  { element: '[data-tour="stats"]', popover: { title: 'Ringkasan belajar', description: 'Jumlah kelas, materi yang sudah dipelajari, modul yang sudah lulus post-test, dan kelas yang selesai.', side: 'bottom' } },
  { element: '[data-tour="resume"]', popover: { title: 'Lanjutkan belajar', description: 'Kelas yang sedang kamu pelajari dan langkah berikutnya. Garis berwarna menunjukkan modul yang sudah selesai. Klik tombolnya untuk langsung lanjut.', side: 'bottom' } },
  { element: '[data-tour="tabs"]', popover: { title: 'Semua kelasmu', description: 'Lihat kelas yang sedang dipelajari, yang sudah selesai, atau yang masih menunggu pembayaran.', side: 'bottom' } },
  { popover: { title: 'Cara belajar', description: 'Setiap modul dimulai dengan pre-test, lalu materi video/bacaan berurutan, lalu post-test. Tombol Panduan di pojok kanan bawah bisa diklik kapan saja.' } }
]);
</script>
@endpush
