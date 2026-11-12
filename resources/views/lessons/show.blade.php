@extends('layouts.app')

@section('seo')
    <title>{{ $lesson->title . ' — ' . $course->title }} - Ray Academy</title>
    <meta name="robots" content="noindex">
@endsection

@section('content')

<style>
.learn-card { background:#fff; border:1px solid var(--border); border-radius:14px; }
.btn-next { display:inline-flex; align-items:center; gap:.5rem; padding:.75rem 1.4rem; background:var(--blue); color:#fff; font-weight:700; font-size:.9rem; border:none; border-radius:10px; cursor:pointer; text-decoration:none; }
.btn-next[disabled] { background:#cbd5e1; cursor:not-allowed; }
.lesson-text { line-height:1.8; color:var(--ink-2); font-size:.95rem; }
.lesson-text h1 { font-size:1.4rem; font-weight:700; margin:1.2rem 0 .6rem; }
.lesson-text h2, .lesson-text h3 { font-size:1.15rem; font-weight:700; margin:1rem 0 .5rem; }
.lesson-text ul { list-style:disc; padding-left:1.4rem; margin:.6rem 0; }
.lesson-text ol { list-style:decimal; padding-left:1.4rem; margin:.6rem 0; }
.lesson-text img { max-width:100%; border-radius:8px; margin:1rem 0; }
.lesson-text a { color:var(--blue); text-decoration:underline; }
.lesson-text blockquote { border-left:3px solid var(--blue); padding-left:1rem; color:var(--muted); margin:1rem 0; }
</style>

@php
    $done = $progress?->isCompleted($lesson) ?? false;
    $preview = ! $progress;
@endphp

<x-classroom :course="$course" :progress="$progress" :current="'lesson-'.$lesson->id">
    @if($lesson->isVideo())
        <div style="border-radius:12px;overflow:hidden;box-shadow:0 8px 32px rgba(10,22,40,.12);background:#000;">
            <div style="aspect-ratio:16/9;"><div id="player" style="width:100%;height:100%;"></div></div>
        </div>
    @endif

    <div class="learn-card" style="margin-top:{{ $lesson->isVideo() ? '1.25rem' : '0' }};padding:1.5rem;">
        <div style="font-size:.75rem;font-weight:700;color:var(--blue);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.35rem;">{{ $lesson->section?->title }}</div>
        <h1 style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;color:var(--ink);margin-bottom:1rem;">{{ $lesson->title }}</h1>

        @unless($lesson->isVideo())
            <div class="lesson-text">{!! $lesson->content !!}</div>
            <div id="text-end" style="height:1px;"></div>
        @endunless

        @if($preview)
            <div style="margin-top:1.5rem;padding:1rem;background:var(--blue-xl);border-radius:10px;font-size:.875rem;">
                Ini materi preview. <a href="{{ route('course.show', $course->slug) }}" style="color:var(--blue);font-weight:700;">Daftar kelas</a> untuk membuka semua materi.
            </div>
        @else
            <div style="margin-top:1.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                @if($done)
                    <a href="{{ route('course.learn', $course->slug) }}" class="btn-next">Lanjut <i class="fas fa-arrow-right"></i></a>
                    <span style="font-size:.8rem;color:#16a34a;font-weight:600;"><i class="fas fa-check-circle"></i> Materi ini sudah selesai</span>
                @else
                    <button id="btn-complete" class="btn-next" disabled>Tandai Selesai &amp; Lanjut <i class="fas fa-arrow-right"></i></button>
                    <span id="complete-hint" style="font-size:.8rem;color:var(--muted);">
                        {{ $lesson->isVideo() ? 'Tonton video sampai selesai untuk lanjut.' : 'Baca materi sampai bawah untuk lanjut.' }}
                    </span>
                @endif
            </div>
        @endif
    </div>
</x-classroom>

@if($lesson->isVideo())
<script>
// Video tracking via the YouTube IFrame API. The server is the source of truth: the player reports
// its position every few seconds, the server decides how much counts as watched (it rejects jumps
// faster than real time) and whether the lesson may be completed. A refresh or another device
// resumes from the last position the server accepted.
(function () {
    const tracking = {{ $watch ? 'true' : 'false' }};
    const canComplete = {{ ($progress && ! $done) ? 'true' : 'false' }};
    const resumeAt = {{ (int) ($watch?->position ?? 0) }};
    let maxWatched = {{ (int) ($watch?->watched_seconds ?? 0) }};
    let player, timer = null, lastPing = 0, unlocked = false;

    window.onYouTubeIframeAPIReady = function () {
        player = new YT.Player('player', {
            videoId: @json($lesson->youtube_video_id),
            // Resume a few seconds early so the learner gets context back.
            playerVars: { rel: 0, modestbranding: 1, disablekb: canComplete ? 1 : 0, start: Math.max(0, resumeAt - 3) },
            events: { onStateChange: onState },
        });
    };

    @if($watch?->videoWatchedEnough($lesson))
    window.addEventListener('DOMContentLoaded', unlock);
    @endif

    function ping(keepalive) {
        if (!tracking || !player || !player.getCurrentTime) return;
        lastPing = Date.now();
        fetch(@json(route('course.lesson.progress', [$course->slug, $lesson])), {
            method: 'POST',
            keepalive: !!keepalive,
            headers: { 'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ position: Math.floor(player.getCurrentTime()), duration: Math.floor(player.getDuration()) }),
        })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
            if (!data) return;
            maxWatched = Math.max(maxWatched, data.watched);
            if (canComplete && data.can_complete) unlock();
        })
        .catch(() => {});
    }

    function onState(e) {
        clearInterval(timer);
        if (e.data === YT.PlayerState.PLAYING) timer = setInterval(tick, 1000);
        if (e.data === YT.PlayerState.PAUSED || e.data === YT.PlayerState.ENDED) ping();
    }

    function tick() {
        const t = player.getCurrentTime(), d = player.getDuration();
        // Forward seeks past the furthest watched point snap back (the server would not count them anyway).
        if (canComplete && t > maxWatched + 3) { player.seekTo(maxWatched, true); return; }
        maxWatched = Math.max(maxWatched, t);
        const nearEnd = d > 0 && maxWatched / d >= 0.9;
        if (Date.now() - lastPing >= 10000 || (nearEnd && !unlocked && Date.now() - lastPing >= 2000)) ping();
    }

    window.addEventListener('pagehide', () => ping(true));
    document.addEventListener('visibilitychange', () => { if (document.visibilityState === 'hidden') ping(true); });

    function unlock() {
        if (unlocked) return;
        unlocked = true;
        window.lessonUnlock && window.lessonUnlock();
    }

    const s = document.createElement('script');
    s.src = 'https://www.youtube.com/iframe_api';
    document.head.appendChild(s);
})();
</script>
@endif

@if($progress && ! $done)
<script>
(function () {
    const btn = document.getElementById('btn-complete');
    const hint = document.getElementById('complete-hint');

    window.lessonUnlock = function () {
        btn.disabled = false;
        hint.textContent = 'Materi selesai, klik untuk lanjut.';
    };

    @unless($lesson->isVideo())
    // Text lessons unlock once the end of the content has scrolled into view; the server also checks reading time.
    new IntersectionObserver((entries, obs) => {
        if (entries[0].isIntersecting) { window.lessonUnlock(); obs.disconnect(); }
    }).observe(document.getElementById('text-end'));
    @endunless

    btn.addEventListener('click', function () {
        if (btn.disabled) return;
        btn.disabled = true;
        fetch(@json(route('course.lesson.complete', [$course->slug, $lesson])), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json' },
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (ok) { window.location.href = data.next; return; }
            btn.disabled = false;
            hint.textContent = data.message || 'Belum bisa lanjut, coba lagi.';
        })
        .catch(() => { btn.disabled = false; hint.textContent = 'Gagal menyimpan progress, periksa koneksi lalu coba lagi.'; });
    });
})();
</script>
@endif

@endsection

@push('scripts')
@include('partials.tour-assets')
<script>
KATour.page('lesson', [
  { element: '#player', popover: { title: 'Tonton videonya', description: 'Video harus ditonton minimal 90% dan tidak bisa dipercepat dengan menggeser ke bagian yang belum ditonton.', side: 'bottom' } },
  { element: '.lesson-text', popover: { title: 'Baca materinya', description: 'Scroll sampai bagian paling bawah untuk menyelesaikan materi tulisan.', side: 'top' } },
  { element: '#btn-complete', popover: { title: 'Tandai selesai', description: 'Tombol ini aktif setelah video/bacaan selesai. Klik untuk lanjut ke materi berikutnya.', side: 'top' } },
  { element: '.cr-side', popover: { title: 'Daftar materi & progress', description: 'Centang hijau = sudah selesai, gembok = belum terbuka. Persentase di atas menunjukkan progress kelasmu.', side: 'left' } }
]);
</script>
@endpush
