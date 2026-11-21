@extends('layouts.app')

@php $session = $registration->session; @endphp

@section('seo')
    <title>Kamu terdaftar! {{ $event->title }} - Ray Academy</title>
    <meta name="robots" content="noindex">
@endsection

@include('events._page-styles')

@push('styles')
<style>
.ok-page { position:relative; overflow:hidden; background:linear-gradient(180deg,#ecfdf5 0%,#f3f8ff 45%,#fff 100%); }
.ok { position:relative; z-index:1; max-width:560px; margin:0 auto; padding:2.5rem 1.25rem 4rem; text-align:center; font-size:1.02rem; }
.ok-badge { width:104px; height:104px; margin:0 auto 1.1rem; border-radius:50%; background:#10b981; color:#fff; font-size:2.8rem; display:flex; align-items:center; justify-content:center;
            box-shadow:0 0 0 10px rgba(16,185,129,.15), 0 14px 30px rgba(16,185,129,.35); animation:ok-pop .6s cubic-bezier(.2,1.6,.4,1) both; }
@keyframes ok-pop { from { transform:scale(.3); opacity:0; } to { transform:scale(1); opacity:1; } }
.ok h1 { font-family:'Sora',sans-serif; font-size:clamp(1.7rem,6vw,2.2rem); font-weight:700; letter-spacing:-.02em; margin:0; }
.ok-lead { font-size:1.08rem; color:var(--ink-2); margin:.5rem 0 0; line-height:1.55; }
.ok-card { text-align:left; margin-top:1.6rem; background:#fff; border:1px solid var(--border); border-radius:20px; padding:1.1rem 1.2rem; box-shadow:0 12px 30px rgba(10,22,40,.08); display:flex; gap:1rem; align-items:center; }
.ok-thumb { width:72px; aspect-ratio:4/5; flex-shrink:0; border-radius:12px; overflow:hidden; background:linear-gradient(150deg,#0d5a96,#1474bc 60%,#10b981); }
.ok-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
.ok-card b { display:block; font-family:'Sora',sans-serif; font-size:1.05rem; }
.ok-card span { display:block; font-size:.9rem; color:var(--muted); margin-top:.2rem; line-height:1.45; }
.ok-card code { display:inline-block; margin-top:.35rem; font-family:'Sora',monospace; font-weight:700; letter-spacing:.08em; color:var(--blue-d); background:var(--blue-xl); padding:.15rem .5rem; border-radius:8px; }

.ok-steps { text-align:left; margin-top:1.8rem; }
.ok-steps h2 { font-family:'Sora',sans-serif; font-size:1.15rem; font-weight:700; margin:0 0 .8rem; }
.ok-steps ol { list-style:none; margin:0; padding:0; display:grid; gap:.75rem; }
.ok-steps li { display:flex; gap:.9rem; align-items:flex-start; background:#fff; border:1px solid var(--border); border-radius:16px; padding:.9rem 1rem; }
.ok-steps li > span { width:34px; height:34px; flex-shrink:0; border-radius:50%; background:var(--blue); color:#fff; font-weight:700; display:flex; align-items:center; justify-content:center; }
.ok-steps li b { display:block; }
.ok-steps li p { margin:.15rem 0 0; font-size:.92rem; color:var(--muted); line-height:1.5; }
.ok-actions { display:grid; gap:.65rem; margin-top:1.6rem; }
.ok-actions .evf-btn { margin:0; min-height:58px; font-size:1.05rem; }
.ok-mail { font-size:.88rem; color:var(--muted); margin-top:1.2rem; }

#confetti { position:absolute; inset:0; width:100%; height:100%; pointer-events:none; z-index:0; }
@media (prefers-reduced-motion: reduce) { .ok-badge { animation:none; } #confetti { display:none; } }
</style>
@endpush

@section('content')
<div class="ok-page">
    <canvas id="confetti" aria-hidden="true"></canvas>
    <div class="ok">
        <div class="ok-badge" aria-hidden="true"><i class="fas fa-check"></i></div>
        <h1>Hore, kamu terdaftar!</h1>
        <p class="ok-lead">Kursimu di <b>{{ $event->title }}</b> sudah aman. Sampai jumpa, {{ \Illuminate\Support\Str::of($registration->name)->explode(' ')->first() }}!</p>

        <div class="ok-card">
            <div class="ok-thumb">@if($event->cover_url)<img src="{{ $event->cover_url }}" alt="">@endif</div>
            <div style="min-width:0;">
                <b>{{ $event->title }}</b>
                <span>{{ $event->dateLabel() }}<br>{{ $session->label }}, pukul {{ $session->timeLabel() }} {{ $event->timezone }} · {{ $event->isOnline() ? 'Online' : $event->venue }}</span>
                <code>{{ $registration->code }}</code>
            </div>
        </div>

        <div class="ok-actions">
            <a href="{{ route('tickets.show', $registration) }}" class="evf-btn"><i class="fas fa-ticket" aria-hidden="true"></i> Lihat tiket saya</a>
            <a href="{{ route('tickets.download', $registration) }}" class="evf-btn ghost"><i class="fas fa-download" aria-hidden="true"></i> Unduh tiket (PDF)</a>
            <a href="{{ $registration->calendarUrl() }}" target="_blank" rel="noopener" class="evf-btn ghost"><i class="far fa-calendar-plus" aria-hidden="true"></i> Simpan ke Google Calendar</a>
        </div>

        <div class="ok-steps">
            <h2>Apa selanjutnya?</h2>
            <ol>
                <li><span>1</span><div><b>Simpan tiketmu</b><p>Unduh PDF-nya atau screenshot halaman tiket. Tiket juga sudah kami kirim ke {{ $registration->email }}.</p></div></li>
                @if($event->isOnline())
                    <li><span>2</span><div><b>Buka link acara tepat waktu</b><p>{{ $event->dateLabel() }}, pukul {{ $session->timeLabel() }} {{ $event->timezone }}. Tombol masuk acara ada di halaman tiket dan dashboard.</p></div></li>
                    <li><span>3</span><div><b>Siapkan koneksi internet</b><p>Pakai HP atau laptop dengan sinyal yang stabil.</p></div></li>
                @else
                    <li><span>2</span><div><b>Datang ke lokasi</b><p>{{ $event->dateLabel() }}, pukul {{ $session->timeLabel() }} {{ $event->timezone }} di {{ $event->venue }}.</p></div></li>
                    <li><span>3</span><div><b>Tunjukkan QR ke panitia</b><p>Panitia akan memindai QR di tiketmu sebagai tanda hadir.</p></div></li>
                @endif
            </ol>
        </div>

        <a href="{{ route('dashboard') }}" class="evf-btn ghost" style="margin-top:1.4rem;">Ke dashboard saya</a>
        <p class="ok-mail">Berhalangan hadir? Batalkan dari halaman tiket supaya kursinya bisa dipakai orang lain.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
// A short burst of confetti; skipped for people who prefer less motion.
(function () {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    const canvas = document.getElementById('confetti');
    const ctx = canvas.getContext('2d');
    const colors = ['#1474bc', '#10b981', '#f59e0b', '#6d4ed8', '#ef4444', '#38bdf8'];
    let w, h;
    const resize = () => { w = canvas.width = canvas.offsetWidth; h = canvas.height = canvas.offsetHeight; };
    resize();
    window.addEventListener('resize', resize);
    const pieces = Array.from({ length: 140 }, () => ({
        x: Math.random() * w, y: -20 - Math.random() * h * 0.6, r: 4 + Math.random() * 6,
        vx: -1.5 + Math.random() * 3, vy: 2 + Math.random() * 3.5, a: Math.random() * Math.PI, va: -0.2 + Math.random() * 0.4,
        c: colors[Math.floor(Math.random() * colors.length)],
    }));
    const end = performance.now() + 3200;
    (function frame(t) {
        ctx.clearRect(0, 0, w, h);
        pieces.forEach(p => {
            p.x += p.vx; p.y += p.vy; p.a += p.va;
            ctx.save(); ctx.translate(p.x, p.y); ctx.rotate(p.a);
            ctx.globalAlpha = t > end ? Math.max(0, 1 - (t - end) / 800) : 1;
            ctx.fillStyle = p.c; ctx.fillRect(-p.r / 2, -p.r / 4, p.r, p.r / 2);
            ctx.restore();
        });
        if (t < end + 800) requestAnimationFrame(frame); else ctx.clearRect(0, 0, w, h);
    })(performance.now());
})();
</script>
@endpush
