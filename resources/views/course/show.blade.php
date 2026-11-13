@extends('layouts.app')

@section('seo')
    {!! seo($course) !!}
@endsection

@push('styles')
<style>
.cd-hero { background:#F5F5F4; color:#0F1114; border-bottom:1px solid #E2E8F0; position:relative; }
.cd-hero::before { content:''; position:absolute; inset:0; background-image:linear-gradient(rgba(0,86,210,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,86,210,.03) 1px,transparent 1px); background-size:48px 48px; pointer-events:none; }
.cd-hero > .cd-wrap { position:relative; }
.cd-wrap { max-width:1200px; margin:0 auto; padding:0 1.25rem; }
.cd-grid { display:grid; grid-template-columns:minmax(0,1fr) 360px; gap:2.5rem; }
.cd-hero-inner { padding:2.5rem 0 2.75rem; }
.cd-crumb { font-size:.8rem; color:#6B7280; display:flex; gap:.4rem; flex-wrap:wrap; margin-bottom:1rem; }
.cd-crumb a { color:#6B7280; text-decoration:none; }
.cd-crumb a:hover { color:#0056D2; }
.cd-title { font-family:'Sora',sans-serif; font-size:clamp(1.6rem,3.2vw,2.4rem); font-weight:700; line-height:1.2; letter-spacing:-.02em; color:#0F1114; }
.cd-sub { color:#4B5563; font-size:1rem; margin-top:.75rem; max-width:680px; }
.cd-badges { display:flex; gap:.5rem; flex-wrap:wrap; margin-top:1rem; }
.cd-badge { font-size:.72rem; font-weight:700; padding:.3rem .75rem; border-radius:999px; background:#EEF4FF; color:#0056D2; border:1px solid rgba(0,86,210,.15); text-transform:uppercase; letter-spacing:.04em; }
.cd-badge.free { background:#dcfce7; color:#15803d; border-color:#bbf7d0; }
.cd-meta { display:flex; gap:1.25rem; flex-wrap:wrap; margin-top:1.25rem; font-size:.875rem; color:#374151; }
.cd-meta i { color:#0056D2; margin-right:.35rem; }

.cd-aside { grid-column:2; grid-row:1; margin-top:-17rem; }
.cd-card { position:sticky; top:88px; background:#fff; border:1px solid var(--border); border-radius:16px; box-shadow:0 12px 40px rgba(10,22,40,.15); overflow:hidden; }
.cd-thumb { position:relative; aspect-ratio:16/9; background:linear-gradient(135deg,#1474bc,#10b981); cursor:pointer; display:block; width:100%; border:0; padding:0; }
.cd-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
.cd-play { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.5rem; background:rgba(10,22,40,.35); color:#fff; font-weight:700; font-size:.85rem; }
.cd-play span:first-child { width:58px; height:58px; border-radius:50%; background:#fff; color:var(--ink); display:flex; align-items:center; justify-content:center; font-size:1.2rem; }
.cd-card-body { padding:1.4rem; }
.cd-price { font-family:'Sora',sans-serif; font-size:1.9rem; font-weight:700; color:var(--ink); }
.cta-btn { display:flex; width:100%; align-items:center; justify-content:center; gap:.5rem; padding:.9rem 1rem; margin-top:1rem; background:#0056D2; color:#fff; font-weight:700; font-size:.95rem; border:0; border-radius:8px; cursor:pointer; text-decoration:none; }
.cta-btn:hover { background:#0048B0; }
.cta-success { background:#10b981; } .cta-success:hover { background:#059669; }
.cta-warn { background:#f59e0b; } .cta-warn:hover { background:#d97706; }
.cta-outline { background:#fff; color:#0056D2; border:1.5px solid #0056D2; margin-top:.6rem; } .cta-outline:hover { background:#EEF4FF; }
.cd-badge.part { background:#fef3c7; color:#b45309; border-color:#fde68a; }
.cd-free { font-size:.68rem; font-weight:700; padding:.15rem .5rem; border-radius:999px; background:#dcfce7; color:#15803d; text-transform:uppercase; letter-spacing:.04em; }
/* Checkout panel: price, voucher, then Midtrans */
.co-modal { position:fixed; inset:0; z-index:60; background:rgba(10,22,40,.55); display:flex; align-items:center; justify-content:center; padding:1rem; }
.co-box { background:#fff; border-radius:16px; width:100%; max-width:420px; padding:1.5rem; box-shadow:0 24px 60px rgba(10,22,40,.3); }
.co-row { display:flex; justify-content:space-between; font-size:.9rem; color:var(--ink-2); padding:.35rem 0; }
.co-row.total { border-top:1px solid var(--border); margin-top:.4rem; padding-top:.75rem; font-weight:800; color:var(--ink); font-size:1.05rem; }
.co-voucher { display:flex; gap:.5rem; margin-top:1rem; }
.co-voucher input { flex:1; min-width:0; padding:.65rem .8rem; border:1.5px solid var(--border); border-radius:8px; font-size:.9rem; text-transform:uppercase; }
.co-voucher button { padding:.65rem 1rem; border:1.5px solid #0056D2; color:#0056D2; background:#fff; border-radius:8px; font-weight:700; cursor:pointer; }
.co-modal[hidden] { display:none; }
.cd-includes { margin-top:1.25rem; padding-top:1.1rem; border-top:1px solid var(--border); }
.cd-includes h4 { font-size:.85rem; font-weight:700; color:var(--ink); margin-bottom:.6rem; }
.cd-includes li { display:flex; gap:.6rem; font-size:.85rem; color:var(--ink-2); padding:.25rem 0; }
.cd-includes i { width:16px; color:#0056D2; margin-top:.2rem; }

.cd-main { grid-column:1; grid-row:1; padding:2rem 0 4rem; min-width:0; }
.cd-box { border:1px solid var(--border); border-radius:14px; padding:1.5rem; margin-bottom:2rem; background:#fff; }
.cd-h2 { font-family:'Sora',sans-serif; font-size:1.3rem; font-weight:700; color:var(--ink); margin-bottom:1rem; }
.cd-learn { display:grid; grid-template-columns:1fr 1fr; gap:.6rem 1.5rem; }
.cd-learn li { display:flex; gap:.6rem; font-size:.9rem; color:var(--ink-2); line-height:1.5; }
.cd-learn i { color:#10b981; margin-top:.25rem; }

.cd-steps { display:grid; grid-template-columns:repeat(4,1fr); gap:.75rem; }
.cd-step { text-align:center; padding:1rem .5rem; border-radius:12px; background:var(--surf); }
.cd-step-n { width:36px; height:36px; border-radius:50%; background:#0056D2; color:#fff; display:flex; align-items:center; justify-content:center; margin:0 auto .5rem; font-size:.9rem; }
.cd-step strong { display:block; font-size:.85rem; color:var(--ink); }
.cd-step span { font-size:.75rem; color:var(--muted); }

.cd-cur-head { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap:wrap; font-size:.85rem; color:var(--muted); margin-bottom:.75rem; }
.cd-cur-head button { color:#0056D2; font-weight:700; background:none; border:0; cursor:pointer; }
.cd-sec { border:1px solid var(--border); border-top:0; }
.cd-sec:first-of-type { border-top:1px solid var(--border); border-radius:12px 12px 0 0; }
.cd-sec:last-of-type { border-radius:0 0 12px 12px; }
.cd-sec-h { display:flex; align-items:center; gap:.75rem; padding:.95rem 1.1rem; background:var(--surf); cursor:pointer; user-select:none; }
.cd-sec-h strong { flex:1; font-size:.92rem; color:var(--ink); }
.cd-sec-h small { font-size:.78rem; color:var(--muted); white-space:nowrap; }
.cd-row { display:flex; align-items:center; gap:.75rem; padding:.7rem 1.1rem .7rem 2.6rem; font-size:.87rem; color:var(--ink-2); border-top:1px solid var(--border); }
.cd-row .ic { width:16px; color:var(--muted); text-align:center; }
.cd-row .t { flex:1; min-width:0; }
.cd-row a, .cd-row button.lnk { color:#0056D2; font-weight:600; text-decoration:underline; background:none; border:0; cursor:pointer; font-size:.8rem; }
.cd-row .dur { font-size:.78rem; color:var(--muted); white-space:nowrap; }
.cd-row .ok { color:#10b981; }

.cd-desc { font-size:.93rem; color:var(--ink-2); line-height:1.85; }
.cd-desc.clamp { max-height:13rem; overflow:hidden; -webkit-mask-image:linear-gradient(#000 60%,transparent); mask-image:linear-gradient(#000 60%,transparent); }
.cd-more { margin-top:.5rem; color:#0056D2; font-weight:700; background:none; border:0; cursor:pointer; font-size:.875rem; }

.cd-inst { display:flex; gap:1rem; align-items:center; }
.cd-avatar { width:64px; height:64px; border-radius:50%; background:#EEF4FF; color:#0056D2; font-family:'Sora',sans-serif; font-weight:800; font-size:1.5rem; display:flex; align-items:center; justify-content:center; flex-shrink:0; }

.cd-related { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
.cd-rel { border:1px solid var(--border); border-radius:12px; overflow:hidden; text-decoration:none; color:inherit; background:#fff; display:flex; flex-direction:column; }
.cd-rel:hover { box-shadow:0 8px 24px rgba(10,22,40,.1); }
.cd-rel-img { aspect-ratio:16/9; background:linear-gradient(135deg,#dbeafe,#eff6ff); }
.cd-rel-img img { width:100%; height:100%; object-fit:cover; }
.cd-rel-b { padding:.85rem; display:flex; flex-direction:column; gap:.35rem; flex:1; }
.cd-rel-b strong { font-size:.88rem; color:var(--ink); line-height:1.35; }
.cd-rel-b span { margin-top:auto; font-weight:800; color:var(--ink); font-size:.9rem; }

.cd-flash { border-radius:10px; padding:.8rem 1rem; font-size:.875rem; margin-bottom:1.25rem; border:1px solid; }
.cd-bar { display:none; }

@media (max-width: 1024px) {
    .cd-grid { grid-template-columns:1fr; gap:0; }
    .cd-aside, .cd-main { grid-column:1; grid-row:auto; }
    .cd-aside { margin-top:1.5rem; }
    .cd-card { position:static; }
    .cd-main { padding-bottom:6rem; }
    .cd-bar { display:flex; position:fixed; left:0; right:0; bottom:0; z-index:40; background:#fff; border-top:1px solid var(--border); padding:.7rem 1rem; gap:1rem; align-items:center; box-shadow:0 -6px 20px rgba(10,22,40,.08); }
    .cd-bar .cta-btn { margin-top:0; }
    .cd-related { grid-template-columns:1fr 1fr; }
}
@media (max-width: 640px) {
    .cd-learn, .cd-related { grid-template-columns:1fr; }
    .cd-steps { grid-template-columns:1fr 1fr; }
    .cd-row { padding-left:1.1rem; }
}
</style>
@endpush

@php
    $fmtMinutes = fn (int $m) => $m >= 60 ? intdiv($m, 60).' jam'.($m % 60 ? ' '.($m % 60).' menit' : '') : $m.' menit';
    $cleanLines = fn (?string $text) => collect(preg_split('/\r?\n/', (string) $text))
        ->map(fn ($line) => trim(preg_replace('/^[\s•\-\*✅✔️☑️]+/u', '', $line)))
        ->filter()->values();
    $learnPoints = $cleanLines($course->benefits)->merge($cleanLines($course->topics_preview));
    $videoCount = $lessons->filter->isVideo()->count();
    $textCount = $lessons->count() - $videoCount;
    $hasThumb = (bool) $course->thumbnail_url;
    $priceLabel = $course->isFreeClass() ? 'Gratis' : 'Rp'.number_format($course->price, 0, ',', '.');
    $partlyFree = $course->isPartlyFree();
    $freeSectionCount = $sections->where('is_free', true)->count();
@endphp

@section('content')

{{-- ═════ HERO ═════ --}}
<section class="cd-hero">
    <div class="cd-wrap">
        <div class="cd-grid">
            <div class="cd-hero-inner">
                <nav class="cd-crumb">
                    <a href="{{ route('course.index') }}">Kursus</a>
                    @if($course->category)
                        <span>›</span><a href="{{ route('course.index', ['category' => $course->category->slug]) }}">{{ $course->category->name }}</a>
                    @endif
                </nav>
                <h1 class="cd-title">{{ $course->title }}</h1>
                <p class="cd-sub">{{ Str::limit(strip_tags($course->description), 180) }}</p>
                <div class="cd-badges">
                    @if($course->isFreeClass())<span class="cd-badge free">Gratis</span>@endif
                    @if($partlyFree)<span class="cd-badge part">Sebagian Gratis</span>@endif
                    <span class="cd-badge">{{ $course->level }}</span>
                    @if($questionCount)<span class="cd-badge">Pre &amp; Post Test</span>@endif
                </div>
                <div class="cd-meta">
                    <span><i class="fas fa-chalkboard-teacher"></i>{{ $course->instructor }}</span>
                    @if($enrolledStudentsCount >= \App\Models\Course::MIN_STUDENTS_SHOWN)
                        <span><i class="fas fa-users"></i>{{ number_format($enrolledStudentsCount) }} peserta</span>
                    @else
                        <span><i class="fas fa-bolt"></i>Kelas baru</span>
                    @endif
                    <span><i class="fas fa-layer-group"></i>{{ $sections->count() }} bab · {{ $lessons->count() }} materi</span>
                    @if($totalMinutes)<span><i class="fas fa-clock"></i>{{ $fmtMinutes($totalMinutes) }}</span>@endif
                    @if($course->schedule_start)<span><i class="fas fa-calendar-alt"></i>Mulai {{ $course->schedule_start->translatedFormat('d M Y') }}</span>@endif
                </div>
            </div>
        </div>
    </div>
</section>

<div class="cd-wrap">
    <div class="cd-grid">

        {{-- ═════ STICKY CARD ═════ --}}
        <aside class="cd-aside">
            <div class="cd-card">
                <button type="button" class="cd-thumb" @if($course->trailer_video_id) onclick="openPreviewModal('{{ $course->trailer_video_id }}')" @endif aria-label="Putar trailer kelas">
                    @if($hasThumb)
                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}">
                    @endif
                    @if($course->trailer_video_id)
                        <div class="cd-play"><span><i class="fas fa-play"></i></span><span>Tonton trailer</span></div>
                    @endif
                </button>
                <div class="cd-card-body">
                    <div class="cd-price">{{ $priceLabel }}</div>
                    @if($partlyFree && ! $userHasAccess)
                        <p style="font-size:.82rem;color:#15803d;margin-top:.25rem;"><i class="fas fa-gift"></i> {{ $freeSectionCount }} bab bisa dipelajari gratis, lengkap dengan pre-test dan post-test.</p>
                    @endif

                    @if($progress && ! $progress->reviewer)
                        <div style="margin-top:.75rem;">
                            <div style="display:flex;justify-content:space-between;font-size:.8rem;color:var(--muted);margin-bottom:.3rem;">
                                <span>Progress belajarmu</span><strong style="color:#0056D2;">{{ $progress->percent() }}%</strong>
                            </div>
                            <div style="height:7px;background:var(--surf);border-radius:999px;overflow:hidden;">
                                <div style="height:100%;width:{{ $progress->percent() }}%;background:{{ $progress->isCourseFinished() ? '#10b981' : '#0056D2' }};"></div>
                            </div>
                        </div>
                    @endif

                    @include('course._cta')

                    @if($userEnrollment && $userEnrollment->payment_status === 'pending' && $userEnrollment->status === 'pending' && ! $course->isFreeClass())
                        <p style="font-size:.78rem;color:#b45309;margin-top:.5rem;text-align:center;">Pembayaranmu belum selesai.</p>
                    @endif

                    <div class="cd-includes">
                        <h4>Kelas ini mencakup</h4>
                        <ul>
                            @if($videoCount)<li><i class="fas fa-play-circle"></i>{{ $videoCount }} video pembelajaran</li>@endif
                            @if($textCount)<li><i class="fas fa-file-alt"></i>{{ $textCount }} materi bacaan</li>@endif
                            @if($totalMinutes)<li><i class="fas fa-clock"></i>Total {{ $fmtMinutes($totalMinutes) }}</li>@endif
                            @if($questionCount)<li><i class="fas fa-clipboard-check"></i>Pre-test &amp; post-test di setiap bab ({{ $questionCount }} soal)</li>@endif
                            @if($course->schedule_start)<li><i class="fas fa-video"></i>Sesi live {{ $course->meeting_platform ? 'via '.$course->meeting_platform : '' }}</li>@endif
                            <li><i class="fas fa-mobile-alt"></i>Akses dari HP dan laptop</li>
                            <li><i class="fas fa-infinity"></i>Belajar kapan saja, sesuai kecepatanmu</li>
                        </ul>
                    </div>
                </div>
            </div>
        </aside>

        {{-- ═════ MAIN ═════ --}}
        <main class="cd-main">

            @foreach (['checkout' => 'background:#fffbeb;color:#92400e;border-color:#fde68a', 'error' => 'background:#fef2f2;color:#b91c1c;border-color:#fecaca', 'message' => 'background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe', 'success' => 'background:#ecfdf5;color:#047857;border-color:#a7f3d0'] as $key => $style)
                @if(session($key))<div class="cd-flash" style="{{ $style }}">{{ session($key) }}</div>@endif
            @endforeach
            @if(request('payment') === 'success')
                <div class="cd-flash" style="background:#ecfdf5;color:#047857;border-color:#a7f3d0">Pembayaran diterima. Kalau kelas belum terbuka, tunggu sebentar lalu muat ulang halaman ini.</div>
            @endif

            {{-- Live session --}}
            @if($course->schedule_start || $course->meeting_link)
                <div class="cd-box" style="background:#EEF4FF;border-color:#bfdbfe;">
                    <h2 class="cd-h2" style="font-size:1.1rem;"><i class="fas fa-video" style="color:#0056D2;"></i> Sesi Live</h2>
                    <div style="display:flex;flex-direction:column;gap:.45rem;font-size:.9rem;color:var(--ink-2);">
                        @if($course->schedule_start)
                            <div><strong>Mulai:</strong> {{ $course->schedule_start->translatedFormat('l, d F Y') }} pukul {{ $course->schedule_start->format('H:i') }} WIB</div>
                        @endif
                        @if($course->schedule_end)
                            <div><strong>Selesai:</strong> {{ $course->schedule_end->translatedFormat('l, d F Y') }} pukul {{ $course->schedule_end->format('H:i') }} WIB</div>
                        @endif
                        @if($course->meeting_platform)<div><strong>Platform:</strong> {{ $course->meeting_platform }}</div>@endif
                        @if($course->meeting_link)
                            @if($userHasAccess)
                                <a href="{{ $course->meeting_link }}" target="_blank" rel="noopener" class="cta-btn cta-success" style="width:auto;align-self:flex-start;padding:.7rem 1.25rem;"><i class="fas fa-video"></i> Gabung Meeting</a>
                            @else
                                <div style="color:var(--muted);"><i class="fas fa-lock"></i> Link meeting muncul setelah kamu terdaftar.</div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif

            {{-- What you'll learn --}}
            @if($learnPoints->isNotEmpty())
                <div class="cd-box">
                    <h2 class="cd-h2">Yang akan kamu pelajari</h2>
                    <ul class="cd-learn">
                        @foreach($learnPoints as $point)
                            <li><i class="fas fa-check"></i><span>{{ $point }}</span></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Learning path --}}
            @if($lessons->isNotEmpty())
                @php
                    $steps = collect([
                        $questionCount ? ['fa-clipboard-list', 'Pre-test bab', 'Ukur pemahaman awal'] : null,
                        ['fa-book-open', 'Materi bab', 'Video & bacaan berurutan'],
                        $questionCount ? ['fa-clipboard-check', 'Post-test bab', 'Lulus minimal '.$course->post_test_pass_score.', lalu bab berikutnya terbuka'] : null,
                        ['fa-trophy', 'Selesai', 'Semua bab tuntas'],
                    ])->filter()->values();
                @endphp
                <div style="margin-bottom:2rem;">
                    <h2 class="cd-h2">Alur belajar</h2>
                    <div class="cd-steps" style="grid-template-columns:repeat({{ $steps->count() }},1fr);">
                        @foreach($steps as [$icon, $label, $hint])
                            <div class="cd-step">
                                <div class="cd-step-n"><i class="fas {{ $icon }}"></i></div>
                                <strong>{{ $loop->iteration }}. {{ $label }}</strong>
                                <span>{{ $hint }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Curriculum --}}
            <div style="margin-bottom:2rem;" x-data="{ all: false, open: { 0: true } }">
                <h2 class="cd-h2">Kurikulum</h2>
                @if($sections->isEmpty())
                    <div class="cd-box" style="text-align:center;color:var(--muted);border-style:dashed;">Materi kelas akan segera ditambahkan.</div>
                @else
                    <div class="cd-cur-head">
                        <span>{{ $sections->count() }} bab · {{ $lessons->count() }} materi{{ $totalMinutes ? ' · '.$fmtMinutes($totalMinutes) : '' }}</span>
                        <button type="button" @click="all = !all; open = {}; if (all) { @foreach($sections as $s) open[{{ $loop->index }}] = true; @endforeach }" x-text="all ? 'Tutup semua bab' : 'Buka semua bab'"></button>
                    </div>

                    @foreach($sections as $section)
                        @php $mins = (int) $section->lessons->sum(fn ($l) => (int) $l->duration); @endphp
                        <div class="cd-sec">
                            <div class="cd-sec-h" @click="open[{{ $loop->index }}] = !open[{{ $loop->index }}]">
                                <i class="fas fa-chevron-down" style="font-size:.75rem;color:var(--muted);transition:transform .2s;" :style="open[{{ $loop->index }}] ? '' : 'transform:rotate(-90deg)'"></i>
                                <strong>{{ $section->title }}</strong>
                                @if($section->is_free && $partlyFree)<span class="cd-free">Gratis</span>@endif
                                <small>{{ $section->lessons->count() }} materi{{ $mins ? ' · '.$fmtMinutes($mins) : '' }}</small>
                            </div>
                            <div x-show="open[{{ $loop->index }}]" x-collapse>
                                @if($section->pre_count)
                                    <div class="cd-row">
                                        <i class="fas {{ $progress?->preTestDone($section) ? 'fa-check-circle ok' : 'fa-clipboard-list' }} ic"></i>
                                        <span class="t"><strong>Pre-test</strong> · {{ $section->pre_count }} soal</span>
                                        @if($progress?->reviewer)<a href="{{ route('course.test', [$course->slug, $section, 'pre']) }}">Buka</a>@endif
                                    </div>
                                @endif
                                @foreach($section->lessons as $lesson)
                                    @php $done = $progress?->isCompleted($lesson); @endphp
                                    <div class="cd-row">
                                        <i class="fas {{ $done ? 'fa-check-circle ok' : ($lesson->isVideo() ? 'fa-play-circle' : ($lesson->isPdf() ? 'fa-file-pdf' : 'fa-file-alt')) }} ic"></i>
                                        <span class="t">{{ $lesson->title }}</span>
                                        {{-- Only the trailer is public; free chapters open after "Mulai Gratis". --}}
                                        @if($progress?->reviewer)
                                            <a href="{{ route('course.lesson', [$course->slug, $lesson]) }}">Buka</a>
                                        @elseif(! $userHasAccess && ! ($section->is_free && $partlyFree))
                                            <i class="fas fa-lock" style="font-size:.75rem;color:var(--muted);"></i>
                                        @endif
                                        @if($lesson->duration)<span class="dur">{{ $lesson->duration }} mnt</span>@endif
                                    </div>
                                @endforeach
                                @if($section->post_count)
                                    <div class="cd-row">
                                        <i class="fas {{ $progress?->postTestPassed($section) ? 'fa-check-circle ok' : 'fa-clipboard-check' }} ic"></i>
                                        <span class="t"><strong>Post-test</strong> · {{ $section->post_count }} soal · lulus minimal {{ $course->post_test_pass_score }}</span>
                                        @if($progress?->reviewer)<a href="{{ route('course.test', [$course->slug, $section, 'post']) }}">Buka</a>@endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Description --}}
            @if($course->description)
                <div style="margin-bottom:2rem;" x-data="{ more: false }">
                    <h2 class="cd-h2">Tentang kelas ini</h2>
                    <div class="cd-desc" :class="more ? '' : 'clamp'">{!! nl2br(e($course->description)) !!}</div>
                    @if(Str::length($course->description) > 600)
                        <button type="button" class="cd-more" @click="more = !more" x-text="more ? 'Tampilkan lebih sedikit' : 'Tampilkan selengkapnya'"></button>
                    @endif
                </div>
            @endif

            {{-- Instructor --}}
            <div style="margin-bottom:2rem;">
                <h2 class="cd-h2">Instruktur</h2>
                <div class="cd-inst">
                    <div class="cd-avatar">{{ strtoupper(substr($course->instructor, 0, 1)) }}</div>
                    <div>
                        <div style="font-family:'Sora',sans-serif;font-weight:700;color:var(--ink);font-size:1.05rem;">{{ $course->instructor }}</div>
                        <div style="font-size:.85rem;color:var(--muted);">Instruktur {{ $course->category?->name ?? 'Kelas Digital' }}</div>
                    </div>
                </div>
            </div>

            {{-- Related --}}
            @if($relatedCourses->isNotEmpty())
                <div>
                    <h2 class="cd-h2">Kelas lain yang mungkin kamu suka</h2>
                    <div class="cd-related">
                        @foreach($relatedCourses->take(3) as $related)
                            <a href="{{ route('course.show', $related->slug) }}" class="cd-rel">
                                <div class="cd-rel-img">
                                    @if($related->thumbnail_url)
                                        <img src="{{ $related->thumbnail_url }}" alt="{{ $related->title }}" loading="lazy">
                                    @endif
                                </div>
                                <div class="cd-rel-b">
                                    <strong>{{ $related->title }}</strong>
                                    <small style="color:var(--muted);">{{ $related->instructor }}</small>
                                    <span>{!! $related->isFreeClass() ? '<span style="color:#10b981;">Gratis</span>' : 'Rp'.number_format($related->price, 0, ',', '.') !!}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </main>
    </div>
</div>

{{-- Mobile bottom bar --}}
<div class="cd-bar">
    <div style="flex-shrink:0;">
        <div style="font-family:'Sora',sans-serif;font-weight:800;color:var(--ink);">{{ $priceLabel }}</div>
        @if($progress && ! $progress->reviewer)<div style="font-size:.72rem;color:var(--muted);">Progress {{ $progress->percent() }}%</div>@endif
    </div>
    <div style="flex:1;">@include('course._cta')</div>
</div>

@auth
@if(! $userHasAccess && ! $course->isFreeClass())
<div class="co-modal" id="co-modal" hidden onclick="if (event.target === this) closeCheckout()" role="dialog" aria-modal="true" aria-labelledby="co-title">
    <div class="co-box">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;">
            <div>
                <h3 id="co-title" style="font-family:'Sora',sans-serif;font-size:1.15rem;font-weight:700;color:var(--ink);">Beli kelas lengkap</h3>
                <p style="font-size:.85rem;color:var(--muted);margin-top:.2rem;">{{ $course->title }}</p>
            </div>
            <button type="button" onclick="closeCheckout()" aria-label="Tutup" style="background:none;border:0;font-size:1.2rem;color:var(--muted);cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        @if(session('checkout'))
            <p style="font-size:.85rem;color:#92400e;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:.6rem .8rem;margin-top:1rem;">{{ session('checkout') }}</p>
        @endif
        <div style="margin-top:1rem;">
            <div class="co-row"><span>Harga kelas</span><span>{{ $priceLabel }}</span></div>
            <div class="co-row" id="co-discount-row" hidden><span>Potongan voucher</span><span id="co-discount" style="color:#15803d;"></span></div>
            <div class="co-row total"><span>Total bayar</span><span id="co-total">{{ $priceLabel }}</span></div>
        </div>
        <div class="co-voucher">
            <input type="text" id="co-code" placeholder="Kode voucher (opsional)" maxlength="40" aria-label="Kode voucher">
            <button type="button" id="co-apply">Pakai</button>
        </div>
        <p id="co-msg" style="font-size:.8rem;margin-top:.4rem;" aria-live="polite"></p>
        <button type="button" class="cta-btn" id="co-pay" data-slug="{{ $course->slug }}">Bayar Sekarang</button>
        <p style="font-size:.75rem;color:var(--muted);text-align:center;margin-top:.6rem;">Pembayaran lewat Midtrans: VA, QRIS, e-wallet, atau kartu.</p>
    </div>
</div>
@endif
@endauth

{{-- Trailer / preview modal --}}
<div id="preview-modal" onclick="if (event.target === this) closePreviewModal()"
     style="display:none;position:fixed;inset:0;z-index:60;background:rgba(10,22,40,.85);align-items:center;justify-content:center;padding:1rem;">
    <div style="width:100%;max-width:900px;position:relative;">
        <button type="button" onclick="closePreviewModal()" aria-label="Tutup" style="position:absolute;top:-2.5rem;right:0;color:#fff;background:none;border:0;font-size:1.5rem;cursor:pointer;"><i class="fas fa-times"></i></button>
        <div id="preview-iframe-wrap" style="aspect-ratio:16/9;background:#000;border-radius:12px;overflow:hidden;"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openPreviewModal(videoId) {
    if (!videoId) return;
    const modal = document.getElementById('preview-modal');
    const wrap = document.getElementById('preview-iframe-wrap');
    wrap.innerHTML = '<iframe src="https://www.youtube.com/embed/' + videoId + '?autoplay=1" width="100%" height="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width:100%;height:100%;display:block;"></iframe>';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closePreviewModal() {
    const modal = document.getElementById('preview-modal');
    const wrap = document.getElementById('preview-iframe-wrap');
    wrap.innerHTML = '';
    modal.style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePreviewModal();
});

// Midtrans Snap
const snapClientKey = '{{ config('midtrans.client_key') }}';
const snapUrl = '{{ config('midtrans.snap_url') }}';
let snapScriptLoaded = false;

function loadSnapScript(callback) {
    if (window.snap) { callback(); return; }
    if (snapScriptLoaded) { setTimeout(() => loadSnapScript(callback), 100); return; }
    snapScriptLoaded = true;
    const script = document.createElement('script');
    script.src = snapUrl + '?client_key=' + snapClientKey;
    script.onload = callback;
    document.head.appendChild(script);
}

function setLoading(btn, loading) {
    if (!btn) return;
    if (loading) {
        btn._origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class=\"fas fa-spinner fa-spin mr-1\"></i> Memproses...';
    } else {
        btn.disabled = false;
        btn.innerHTML = btn._origHtml || 'Gabung Kelas Ini';
    }
}

function checkoutAndPay(slug, btn, voucher) {
    setLoading(btn, true);

    fetch('{{ url('/payment/checkout') }}/' + slug, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ voucher: voucher || '' }),
    })
    .then(r => {
        if (r.status === 401) {
            window.location.href = '{{ route('login') }}';
            return;
        }
        return r.json();
    })
    .then(data => {
        setLoading(btn, false);
        if (!data) return;
        if (data.error === 'already_enrolled') {
            window.location.reload();
            return;
        }
        if (data.error === 'free_course') {
            window.location.reload();
            return;
        }
        if (data.paid_by_voucher) {
            window.location.href = '{{ route('course.show', $course->slug) }}?payment=success';
            return;
        }
        if (data.error === 'voucher') {
            showVoucherMessage(data.message, false);
            return;
        }
        if (data.snap_token) {
            loadSnapScript(function () {
                window.snap.pay(data.snap_token, {
                    onSuccess: function (result) {
                        // Mark payment completed immediately via AJAX
                        fetch('{{ url('/payment/complete') }}/' + data.enrollment_id, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ order_id: result.order_id }),
                        })
                        .then(() => {
                            window.location.href = '{{ route('course.show', $course->slug) }}?payment=success';
                        })
                        .catch(() => {
                            window.location.href = '{{ route('course.show', $course->slug) }}?payment=success';
                        });
                    },
                    onPending: function () {
                        window.location.reload();
                    },
                    onError: function () {
                        window.location.reload();
                    },
                    onClose: function () {
                        window.location.reload();
                    }
                });
            });
        } else {
            alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
        }
    })
    .catch(e => {
        setLoading(btn, false);
        alert('Terjadi kesalahan: ' + e.message);
    });
}

// Pay buttons open the checkout panel first, so a voucher can be entered before Midtrans.
const coModal = document.getElementById('co-modal');
let coVoucher = '';

function openCheckout() { if (coModal) { coModal.hidden = false; document.getElementById('co-code').focus(); } }
function closeCheckout() { if (coModal) coModal.hidden = true; }
function showVoucherMessage(text, ok) {
    const msg = document.getElementById('co-msg');
    msg.textContent = text;
    msg.style.color = ok ? '#15803d' : '#b91c1c';
}

document.querySelectorAll('.btn-pay').forEach(btn => btn.addEventListener('click', openCheckout));

if (coModal) {
    document.getElementById('co-apply').addEventListener('click', function () {
        const code = document.getElementById('co-code').value.trim();
        if (!code) { showVoucherMessage('Masukkan kode voucher dulu.', false); return; }
        fetch({{ Js::from(route('payment.voucher', $course->slug)) }}, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ voucher: code }),
        })
        .then(r => r.json())
        .then(data => {
            coVoucher = data.ok ? code : '';
            showVoucherMessage(data.message, data.ok);
            document.getElementById('co-discount-row').hidden = !data.ok;
            document.getElementById('co-discount').textContent = data.ok ? '−' + data.discount_label : '';
            document.getElementById('co-total').textContent = data.total_label;
        })
        .catch(() => showVoucherMessage('Gagal mengecek voucher, coba lagi.', false));
    });
    document.getElementById('co-pay').addEventListener('click', function () {
        checkoutAndPay(this.dataset.slug, this, coVoucher);
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeCheckout(); });
    @if(session('checkout'))
    openCheckout();
    @endif
}

// Handle ?payment=success query param
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('payment') === 'success') {
    const alertBox = document.createElement('div');
    alertBox.style.cssText = 'position:fixed;top:1rem;right:1rem;z-index:9999;background:#059669;color:#fff;padding:1rem 1.5rem;border-radius:12px;font-weight:600;box-shadow:0 8px 32px rgba(5,150,105,.35);animation:fadeIn .3s ease;';
    alertBox.innerHTML = '<i class=\"fas fa-check-circle mr-2\"></i> Pembayaran berhasil! Selamat belajar.';
    document.body.appendChild(alertBox);
    setTimeout(() => { alertBox.style.opacity = '0'; alertBox.style.transition = 'opacity .3s'; setTimeout(() => alertBox.remove(), 400); }, 5000);
    // Clean URL
    window.history.replaceState({}, document.title, window.location.pathname);
}
</script>
@endpush

@push('scripts')
@include('partials.tour-assets')
<script>
@php $mustPay = ! $userHasAccess && $course->isPaidCourse(); @endphp
KATour.page({{ Js::from($mustPay ? 'course-detail-paid' : 'course-detail') }}, [
  { element: '.cd-card .cd-thumb', popover: { title: 'Tonton trailer', description: 'Klik untuk melihat cuplikan kelas sebelum memutuskan ikut.', side: 'left' } },
  { element: '.cd-card .cta-btn', popover: { title: {{ Js::from($userHasAccess ? 'Mulai belajar' : 'Daftar kelas') }}, description: {{ Js::from($userHasAccess ? 'Klik untuk lanjut dari langkah terakhirmu. Progress tersimpan otomatis.' : 'Kelas gratis langsung aktif setelah daftar. Kelas berbayar dibayar lewat Midtrans (VA, QRIS, e-wallet, kartu).') }}, side: 'left' } },
  @if($mustPay)
  { popover: { title: 'Cara membayar', description: 'Setelah tombol diklik, jendela pembayaran muncul. Pilih cara bayar (QRIS, e-wallet, transfer bank, atau kartu), lalu ikuti petunjuknya.<br><br>Begitu pembayaran diterima, kelas otomatis terbuka di <b>Kursus Saya</b>. Kalau bayar lewat transfer bank, kelas yang belum dibayar ada di <b>Kursus Saya → Menunggu pembayaran</b>.' } },
  @endif
  { element: '.cd-bar .cta-btn', popover: { title: {{ Js::from($userHasAccess ? 'Mulai belajar' : 'Daftar kelas') }}, description: 'Tombol ini selalu ada di bawah layar.', side: 'top' } },
  { element: '.cd-steps', popover: { title: 'Alur belajar', description: 'Urutannya tetap: pre-test dulu, lalu materi satu per satu, lalu post-test. Materi berikutnya terbuka setelah yang sebelumnya selesai.', side: 'top' } },
  { element: '.cd-cur-head', popover: { title: 'Kurikulum', description: {{ Js::from($partlyFree ? 'Lihat semua bab dan materi. Bab berlabel "Gratis" bisa dipelajari tanpa membayar, termasuk pre-test dan post-test-nya.' : 'Lihat semua bab dan materi yang akan kamu pelajari.') }}, side: 'top' } }
]);
</script>
@endpush
