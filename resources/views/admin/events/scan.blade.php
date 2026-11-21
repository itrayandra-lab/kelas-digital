<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <title>Scan tiket · {{ $event->title }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700&family=DM+Sans:opsz,wght@9..40,400;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <style>
        * { box-sizing:border-box; }
        [hidden] { display:none !important; }
        body { margin:0; min-height:100vh; background:#0a1628; color:#fff; font-family:'DM Sans',system-ui,sans-serif; }
        .sc { max-width:560px; margin:0 auto; padding:calc(.9rem + env(safe-area-inset-top,0px)) 1rem calc(2rem + env(safe-area-inset-bottom,0px)); }
        .sc-top { display:flex; align-items:center; gap:.75rem; }
        .sc-back { display:inline-flex; align-items:center; justify-content:center; width:44px; height:44px; border-radius:12px; background:rgba(255,255,255,.08); color:#fff; text-decoration:none; flex-shrink:0; }
        .sc-title { flex:1; min-width:0; }
        .sc-title small { display:block; font-size:.72rem; letter-spacing:.08em; text-transform:uppercase; color:#93c5fd; }
        .sc-title b { display:block; font-family:'Sora',sans-serif; font-size:1rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .sc-count { text-align:right; flex-shrink:0; }
        .sc-count b { display:block; font-family:'Sora',sans-serif; font-size:1.5rem; line-height:1; font-variant-numeric:tabular-nums; }
        .sc-count span { font-size:.72rem; color:#94a3b8; }

        .sc-cam { position:relative; margin-top:1rem; aspect-ratio:1; border-radius:24px; overflow:hidden; background:#111c2e; }
        .sc-cam video { width:100%; height:100%; object-fit:cover; display:block; }
        .sc-frame { position:absolute; inset:14%; pointer-events:none; }
        .sc-frame i { position:absolute; width:42px; height:42px; border:5px solid #fff; border-radius:6px; }
        .sc-frame i:nth-child(1) { top:0; left:0; border-right:0; border-bottom:0; }
        .sc-frame i:nth-child(2) { top:0; right:0; border-left:0; border-bottom:0; }
        .sc-frame i:nth-child(3) { bottom:0; left:0; border-right:0; border-top:0; }
        .sc-frame i:nth-child(4) { bottom:0; right:0; border-left:0; border-top:0; }
        .sc-line { position:absolute; left:16%; right:16%; height:3px; top:50%; background:#38bdf8; box-shadow:0 0 14px #38bdf8; animation:sc-scan 2.2s ease-in-out infinite; }
        @keyframes sc-scan { 0%,100% { top:18%; } 50% { top:80%; } }
        .sc-start { position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:1rem; padding:1.5rem; text-align:center; }
        .sc-start p { margin:0; color:#cbd5e1; line-height:1.5; }
        .sc-btn { display:inline-flex; align-items:center; justify-content:center; gap:.6rem; min-height:58px; padding:.9rem 1.6rem; border:0; border-radius:16px; background:#10b981; color:#fff; font:inherit; font-size:1.1rem; font-weight:700; cursor:pointer; }
        .sc-btn:hover { background:#059669; }
        .sc-btn:focus-visible, .sc-back:focus-visible, .sc-manual button:focus-visible, .sc-manual input:focus-visible { outline:3px solid #38bdf8; outline-offset:2px; }
        .sc-hint { text-align:center; margin:.9rem 0 0; color:#cbd5e1; font-size:.95rem; }

        .sc-result { position:fixed; inset:0; z-index:20; display:flex; align-items:center; justify-content:center; padding:1.5rem; background:rgba(5,12,24,.82); }
        .sc-result[hidden] { display:none; }
        .sc-card { width:100%; max-width:420px; border-radius:26px; padding:2rem 1.5rem; text-align:center; color:#fff; box-shadow:0 24px 60px rgba(0,0,0,.45); animation:sc-pop .25s ease-out; }
        @keyframes sc-pop { from { transform:scale(.9); } to { transform:scale(1); } }
        .sc-card.ok { background:#059669; } .sc-card.already { background:#d97706; } .sc-card.invalid, .sc-card.wrong_event, .sc-card.error { background:#dc2626; }
        .sc-card .ic { width:88px; height:88px; margin:0 auto 1rem; border-radius:50%; background:rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font-size:2.6rem; }
        .sc-card h2 { font-family:'Sora',sans-serif; font-size:1.9rem; margin:0; }
        .sc-card .nm { font-size:1.35rem; font-weight:700; margin:.6rem 0 0; }
        .sc-card .ss { font-size:1rem; opacity:.92; margin:.25rem 0 0; }
        .sc-card .ms { font-size:.95rem; opacity:.92; margin:.7rem 0 0; }
        .sc-card .tap { font-size:.8rem; opacity:.75; margin-top:1.2rem; }

        .sc-manual { margin-top:1.4rem; background:rgba(255,255,255,.06); border-radius:18px; padding:1rem; }
        .sc-manual label { display:block; font-size:.88rem; color:#cbd5e1; margin-bottom:.5rem; }
        .sc-manual div { display:flex; gap:.5rem; }
        .sc-manual input { flex:1; min-width:0; min-height:52px; padding:.7rem .9rem; border-radius:12px; border:1px solid #334155; background:#0f1d33; color:#fff; font:inherit; font-size:1.15rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; }
        .sc-manual button { min-height:52px; padding:0 1.2rem; border:0; border-radius:12px; background:#1474bc; color:#fff; font:inherit; font-weight:700; cursor:pointer; }
        .sc-log { margin-top:1.4rem; }
        .sc-log h3 { font-size:.8rem; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; margin:0 0 .6rem; }
        .sc-log ul { list-style:none; margin:0; padding:0; display:grid; gap:.45rem; }
        .sc-log li { display:flex; align-items:center; gap:.7rem; background:rgba(255,255,255,.05); border-radius:12px; padding:.6rem .8rem; font-size:.9rem; }
        .sc-log li i { width:1.2rem; text-align:center; }
        .sc-log li span { flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .sc-log li em { font-style:normal; font-size:.78rem; color:#94a3b8; }
        @media (prefers-reduced-motion: reduce) { .sc-line, .sc-card { animation:none; } }
    </style>
</head>
<body>
<div class="sc">
    <div class="sc-top">
        <a href="{{ route('admin.events.registrations', $event) }}" class="sc-back" aria-label="Selesai, kembali ke daftar peserta"><i class="fas fa-arrow-left" aria-hidden="true"></i></a>
        <div class="sc-title"><small>Scan tiket</small><b>{{ $event->title }}</b></div>
        <div class="sc-count" aria-live="polite"><b id="count">{{ $checkedIn }}/{{ $total }}</b><span>sudah hadir</span></div>
    </div>

    <div class="sc-cam">
        <video id="video" playsinline muted></video>
        <div class="sc-frame" id="frame" hidden><i></i><i></i><i></i><i></i><div class="sc-line"></div></div>
        <div class="sc-start" id="start">
            <i class="fas fa-qrcode" style="font-size:3rem;color:#38bdf8;" aria-hidden="true"></i>
            <p>Arahkan kamera HP ke QR di tiket peserta.<br>Kehadiran langsung tercatat.</p>
            <button type="button" class="sc-btn" id="open"><i class="fas fa-camera" aria-hidden="true"></i> Buka kamera</button>
        </div>
    </div>
    <p class="sc-hint" id="hint">Tekan "Buka kamera", lalu izinkan akses kamera.</p>

    <form class="sc-manual" id="manual">
        <label for="code">QR tidak terbaca? Ketik kode tiketnya</label>
        <div>
            <input id="code" name="code" placeholder="RA-7K3P9Q" autocomplete="off" autocapitalize="characters" required>
            <button type="submit">Cek</button>
        </div>
    </form>

    <div class="sc-log" id="log" hidden>
        <h3>Barusan dipindai</h3>
        <ul id="logList"></ul>
    </div>
</div>

<div class="sc-result" id="result" hidden role="alertdialog" aria-live="assertive" aria-labelledby="resTitle">
    <div class="sc-card" id="resCard">
        <div class="ic"><i id="resIcon" class="fas fa-check" aria-hidden="true"></i></div>
        <h2 id="resTitle"></h2>
        <p class="nm" id="resName"></p>
        <p class="ss" id="resSession"></p>
        <p class="ms" id="resMsg"></p>
        <p class="tap">Ketuk untuk lanjut scan</p>
    </div>
</div>

<script src="{{ asset('vendor/jsqr/jsQR.js') }}"></script>
<script>
(function () {
    const URL_CHECKIN = @js(route('admin.events.scan.checkin', $event));
    const token = document.querySelector('meta[name=csrf-token]').content;
    const video = document.getElementById('video');
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d', { willReadFrequently: true });
    const $ = (id) => document.getElementById(id);
    const LOOK = {
        ok: ['fa-check', 'HADIR'],
        already: ['fa-clock-rotate-left', 'Sudah check-in'],
        wrong_event: ['fa-triangle-exclamation', 'Event lain'],
        invalid: ['fa-xmark', 'Tiket tidak valid'],
        error: ['fa-wifi', 'Gagal mengecek'],
    };
    let busy = false, lastCode = '', lastAt = 0, hideTimer;

    function beep(ok) {
        try {
            const a = new (window.AudioContext || window.webkitAudioContext)();
            [0, ok ? null : 0.22].forEach((delay) => {
                if (delay === null) return;
                const o = a.createOscillator(), g = a.createGain();
                o.frequency.value = ok ? 880 : 220; o.connect(g); g.connect(a.destination);
                g.gain.setValueAtTime(0.25, a.currentTime + delay); g.gain.exponentialRampToValueAtTime(0.001, a.currentTime + delay + 0.18);
                o.start(a.currentTime + delay); o.stop(a.currentTime + delay + 0.2);
            });
        } catch (e) { /* no audio */ }
        if (navigator.vibrate) navigator.vibrate(ok ? 120 : [80, 60, 80]);
    }

    function show(data) {
        const [icon, title] = LOOK[data.status] || LOOK.error;
        $('resCard').className = 'sc-card ' + data.status;
        $('resIcon').className = 'fas ' + icon;
        $('resTitle').textContent = title;
        $('resName').textContent = data.name || data.code || '';
        $('resSession').textContent = data.session || '';
        $('resMsg').textContent = data.message || '';
        $('result').hidden = false;
        beep(data.status === 'ok');
        clearTimeout(hideTimer);
        hideTimer = setTimeout(hide, data.status === 'ok' ? 2200 : 3500);

        if (data.total !== undefined) $('count').textContent = data.checked_in + '/' + data.total;
        const li = document.createElement('li');
        li.innerHTML = '<i class="fas ' + icon + '" style="color:' + ({ ok: '#34d399', already: '#fbbf24' }[data.status] || '#f87171') + '"></i><span></span><em></em>';
        li.querySelector('span').textContent = (data.name || data.code || 'QR tidak dikenal') + ' · ' + title;
        li.querySelector('em').textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        $('logList').prepend(li);
        while ($('logList').children.length > 5) $('logList').lastChild.remove();
        $('log').hidden = false;
    }

    function hide() { $('result').hidden = true; busy = false; }
    $('result').addEventListener('click', hide);

    async function check(code) {
        busy = true;
        try {
            const res = await fetch(URL_CHECKIN, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify({ code }),
            });
            if (res.status === 419) return show({ status: 'error', message: 'Sesi login habis. Muat ulang halaman ini.' });
            if (!res.ok) return show({ status: 'error', message: 'Server menolak (' + res.status + '). Coba lagi.' });
            show(await res.json());
        } catch (e) {
            show({ status: 'error', message: 'Tidak ada koneksi internet. Coba lagi.' });
        }
    }

    function tick() {
        if (video.readyState === video.HAVE_ENOUGH_DATA && !busy) {
            const scale = Math.min(1, 640 / video.videoWidth);
            canvas.width = video.videoWidth * scale; canvas.height = video.videoHeight * scale;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const found = jsQR(ctx.getImageData(0, 0, canvas.width, canvas.height).data, canvas.width, canvas.height, { inversionAttempts: 'dontInvert' });
            // The same ticket stays in front of the camera for a moment; read it once.
            if (found && found.data && !(found.data === lastCode && Date.now() - lastAt < 5000)) {
                lastCode = found.data; lastAt = Date.now();
                check(found.data);
            }
        }
        requestAnimationFrame(tick);
    }

    $('open').addEventListener('click', async () => {
        if (!window.isSecureContext || !navigator.mediaDevices) {
            $('hint').textContent = 'Kamera hanya bisa dibuka lewat alamat https. Pakai kolom kode di bawah.';
            return;
        }
        try {
            video.srcObject = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } }, audio: false });
            await video.play();
            $('start').hidden = true; $('frame').hidden = false;
            $('hint').textContent = 'Arahkan ke QR tiket. Tidak perlu menekan apa pun.';
            requestAnimationFrame(tick);
        } catch (e) {
            $('hint').textContent = e.name === 'NotAllowedError'
                ? 'Izin kamera ditolak. Buka pengaturan browser, izinkan kamera untuk situs ini, lalu muat ulang.'
                : 'Kamera tidak bisa dibuka (' + e.name + '). Pakai kolom kode di bawah.';
        }
    });

    $('manual').addEventListener('submit', (e) => {
        e.preventDefault();
        const v = $('code').value.trim().toUpperCase();
        if (!v) return;
        check(v.startsWith('RA-') ? v : 'RA-' + v.replace(/^-/, ''));
        $('code').value = '';
    });
})();
</script>
</body>
</html>
