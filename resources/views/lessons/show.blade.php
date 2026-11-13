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
.lesson-text h4 { font-size:1rem; font-weight:700; color:var(--ink); margin:1.1rem 0 .4rem; }
.lesson-text p { margin:.6rem 0; }
.lesson-text figure { margin:1.25rem 0; }
.lesson-text figure img { margin:0 auto; border:1px solid var(--border); }
/* PDF lessons: pages are drawn as images, so there is no viewer toolbar and no download button. */
.pdf-page { aspect-ratio:var(--ratio, .707); background:#f8fafc; border:1px solid var(--border); border-radius:8px; margin-bottom:.75rem; overflow:hidden; }
.pdf-page img { width:100%; display:block; -webkit-user-drag:none; user-select:none; }
.pdf-note { font-size:.85rem; color:var(--muted); text-align:center; padding:2rem 0; }
.lesson-text figcaption { font-size:.8rem; color:var(--muted); text-align:center; margin-top:.4rem; }
/* YouTube's own fullscreen is off (fs:0) so our overlay stays on top in fullscreen. */
.vid-wrap { position:relative; border-radius:12px; overflow:hidden; box-shadow:0 8px 32px rgba(10,22,40,.12); background:#000; }
.vid-ratio { aspect-ratio:16/9; }
/* Branded bottom band from the 1920x1080 frame design, over YouTube's Share / More videos row.
   Logo and URL are separate images so the band can grow to 56px on phones (enough to cover that row) without stretching. */
.vid-frame { position:absolute; left:0; right:0; bottom:0; height:max(13.7%, 56px); padding:0 9.9% 0 5%; pointer-events:none;
  display:flex; align-items:center; justify-content:space-between; background:#000; }
.vf-logo { height:40.5%; width:auto; }
.vf-url { height:27%; width:auto; position:relative; top:-6%; }
/* Blocks clicks on YouTube's links and buttons; a click only plays/pauses via the API. */
.vid-shield { position:absolute; inset:0; width:100%; border:0; padding:0; background:transparent; cursor:pointer; }
.vid-wrap:fullscreen { border-radius:0; display:flex; align-items:center; }
.vid-wrap:fullscreen .vid-ratio { width:100%; max-height:100%; }
.vid-fs { position:absolute; right:12px; bottom:64px; width:38px; height:38px; border:0; border-radius:8px; cursor:pointer;
  background:rgba(10,22,40,.55); color:#fff; font-size:15px; }
.vid-fs:hover { background:rgba(10,22,40,.8); }
</style>

@php
    $done = $progress->isCompleted($lesson);
@endphp

<x-classroom :course="$course" :progress="$progress" :current="'lesson-'.$lesson->id">
    @if($lesson->isVideo())
        <div class="vid-wrap" id="vid-wrap">
            <div class="vid-ratio"><div id="player" style="width:100%;height:100%;"></div></div>
            <div class="vid-frame" aria-hidden="true">
                <img class="vf-logo" src="{{ asset('img/lesson/video-frame-logo.webp') }}" alt="">
                <img class="vf-url" src="{{ asset('img/lesson/video-frame-url.webp') }}" alt="">
            </div>
            <button type="button" class="vid-shield" id="vid-shield" aria-label="Putar atau jeda video"></button>
            <button type="button" class="vid-fs" id="vid-fs" aria-label="Layar penuh"><i class="fas fa-expand"></i></button>
        </div>
    @endif

    <div class="learn-card" style="margin-top:{{ $lesson->isVideo() ? '1.25rem' : '0' }};padding:1.5rem;">
        <div style="font-size:.75rem;font-weight:700;color:var(--blue);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.35rem;">{{ $lesson->section?->title }}</div>
        <h1 style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;color:var(--ink);margin-bottom:1rem;">{{ $lesson->title }}</h1>

        @if($lesson->isPdf())
            <div class="pdf-pages" id="pdf-pages" data-src="{{ route('course.lesson.pdf', [$course->slug, $lesson]) }}" oncontextmenu="return false">
                <p class="pdf-note"><i class="fas fa-spinner fa-spin"></i> Memuat PDF...</p>
            </div>
            <div id="text-end" style="height:1px;"></div>
        @elseif(! $lesson->isVideo())
            <div class="lesson-text">{!! $lesson->content !!}</div>
            <div id="text-end" style="height:1px;"></div>
        @endif

        <div style="margin-top:1.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
            @if($progress->reviewer)
                <span style="font-size:.85rem;color:var(--muted);"><i class="fas fa-user-shield" style="color:var(--blue);"></i> Mode admin: semua materi terbuka dan video bisa digeser bebas. Kunjunganmu tidak dicatat sebagai progres peserta.</span>
            @elseif($done)
                <a href="{{ route('course.learn', $course->slug) }}" class="btn-next">Lanjut <i class="fas fa-arrow-right"></i></a>
                <span style="font-size:.8rem;color:#16a34a;font-weight:600;"><i class="fas fa-check-circle"></i> Materi ini sudah selesai</span>
            @else
                <button id="btn-complete" class="btn-next" disabled>Tandai Selesai &amp; Lanjut <i class="fas fa-arrow-right"></i></button>
                <span id="complete-hint" style="font-size:.8rem;color:var(--muted);">
                    {{ $lesson->isVideo() ? 'Tonton video sampai selesai untuk lanjut.' : 'Baca materi sampai bawah untuk lanjut.' }}
                </span>
            @endif
        </div>
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
    const canComplete = {{ (! $progress->reviewer && ! $done) ? 'true' : 'false' }};
    const resumeAt = {{ (int) ($watch?->position ?? 0) }};
    let maxWatched = {{ (int) ($watch?->watched_seconds ?? 0) }};
    let player, timer = null, lastPing = 0, unlocked = false;

    window.onYouTubeIframeAPIReady = function () {
        player = new YT.Player('player', {
            videoId: @json($lesson->youtube_video_id),
            // Resume a few seconds early so the learner gets context back.
            playerVars: { rel: 0, fs: 0, cc_load_policy: 0, iv_load_policy: 3, disablekb: canComplete ? 1 : 0, start: Math.max(0, resumeAt - 3) },
            events: { onStateChange: onState },
        });
    };

    @if($watch?->videoWatchedEnough())
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
        if (e.data === YT.PlayerState.PLAYING) {
            timer = setInterval(tick, 1000);
            // Learners can't reach YouTube's CC button behind the click shield, and cc_load_policy:0 doesn't stop
            // captions a viewer's YouTube settings turn on; unloading the module is the only thing that does.
            player.unloadModule && player.unloadModule('captions');
        }
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

    const wrap = document.getElementById('vid-wrap'), fsBtn = document.getElementById('vid-fs');
    document.getElementById('vid-shield').addEventListener('click', () => {
        if (!player || !player.getPlayerState) return;
        player.getPlayerState() === YT.PlayerState.PLAYING ? player.pauseVideo() : player.playVideo();
    });
    // iPhone Safari can't fullscreen a div; hide the button there.
    if (document.fullscreenEnabled) {
        fsBtn.addEventListener('click', () => document.fullscreenElement ? document.exitFullscreen() : wrap.requestFullscreen());
        document.addEventListener('fullscreenchange', () => {
            fsBtn.innerHTML = document.fullscreenElement ? '<i class="fas fa-compress"></i>' : '<i class="fas fa-expand"></i>';
        });
    } else {
        fsBtn.hidden = true;
    }

    const s = document.createElement('script');
    s.src = 'https://www.youtube.com/iframe_api';
    document.head.appendChild(s);
})();
</script>
@endif

@if($lesson->isPdf())
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
// Draws each page as an image when it scrolls near, so a long PDF doesn't hold every page in memory at once.
(function () {
    const box = document.getElementById('pdf-pages');
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    // Started during page parsing, PDF.js's first document load could stall forever; after "load" it is reliable.
    window.addEventListener('load', () => pdfjsLib.getDocument({ url: box.dataset.src, withCredentials: true, isEvalSupported: false }).promise.then(async function (pdf) {
        const first = await pdf.getPage(1).then(p => p.getViewport({ scale: 1 }));
        box.style.setProperty('--ratio', first.width / first.height);
        box.innerHTML = '';

        const draw = new IntersectionObserver(entries => entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            draw.unobserve(entry.target);
            renderPage(pdf, entry.target);
        }), { rootMargin: '800px 0px' });

        for (let n = 1; n <= pdf.numPages; n++) {
            const holder = document.createElement('div');
            holder.className = 'pdf-page';
            holder.dataset.page = n;
            box.appendChild(holder);
            draw.observe(holder);
        }
        document.dispatchEvent(new Event('pdf:ready'));
    }).catch(function () {
        box.innerHTML = '<p class="pdf-note">PDF gagal dimuat. Muat ulang halaman ini.</p>';
    }));

    async function renderPage(pdf, holder) {
        const page = await pdf.getPage(Number(holder.dataset.page));
        const scale = Math.min(2, window.devicePixelRatio || 1) * holder.clientWidth / page.getViewport({ scale: 1 }).width;
        const viewport = page.getViewport({ scale });
        const canvas = document.createElement('canvas');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
        canvas.toBlob(blob => {
            const img = new Image();
            img.src = URL.createObjectURL(blob);
            img.alt = 'Halaman ' + holder.dataset.page;
            img.draggable = false;
            holder.appendChild(img);
        }, 'image/jpeg', 0.85);
    }
})();
</script>
@endif

@if(! $progress->reviewer && ! $done)
<script>
(function () {
    const btn = document.getElementById('btn-complete');
    const hint = document.getElementById('complete-hint');

    window.lessonUnlock = function () {
        btn.disabled = false;
        hint.textContent = 'Materi selesai, klik untuk lanjut.';
    };

    @unless($lesson->isVideo())
    // Text and PDF lessons unlock once the end of the content has scrolled into view; the server also checks reading time.
    const watchEnd = () => new IntersectionObserver((entries, obs) => {
        if (entries[0].isIntersecting) { window.lessonUnlock(); obs.disconnect(); }
    }).observe(document.getElementById('text-end'));
    // A PDF's pages are added after it loads; before that the end marker would already be on screen.
    @if($lesson->isPdf()) document.addEventListener('pdf:ready', watchEnd, { once: true }); @else watchEnd(); @endif
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
