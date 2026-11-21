@extends('layouts.app')

@php
    $event = $registration->event;
    $session = $registration->session;
    $past = $event->isPast();
@endphp

@section('seo')
    <title>Tiket {{ $registration->code }} - Ray Academy</title>
    <meta name="robots" content="noindex">
@endsection

@include('events._page-styles')

@push('styles')
<style>
.tk { max-width:520px; margin:0 auto; padding:1.5rem 1.25rem 4rem; font-size:1.02rem; }
.tk-back { display:inline-flex; align-items:center; gap:.45rem; min-height:44px; font-size:.92rem; font-weight:600; color:var(--muted); text-decoration:none; }
.tk-back:hover { color:var(--blue); }
.tk h1 { font-family:'Sora',sans-serif; font-size:1.5rem; font-weight:700; margin:.4rem 0 .2rem; }
.tk-lead { color:var(--ink-2); margin:0 0 1.2rem; line-height:1.55; }

.tk-ticket { background:#fff; border-radius:22px; overflow:hidden; box-shadow:0 18px 44px rgba(10,22,40,.13); border:1px solid var(--border); }
.tk-head { background:linear-gradient(135deg,#0d5a96,#1474bc 65%,#1b8fd6); color:#fff; padding:1.2rem 1.3rem; }
.tk-head small { font-size:.72rem; letter-spacing:.1em; text-transform:uppercase; opacity:.85; }
.tk-head b { display:block; font-family:'Sora',sans-serif; font-size:1.3rem; line-height:1.25; margin-top:.15rem; }
.tk-head span { display:block; margin-top:.3rem; font-size:.92rem; opacity:.92; }
.tk-qr { position:relative; padding:1.4rem 1.3rem 1.1rem; text-align:center; border-bottom:2px dashed #dbe4ef; }
.tk-qr::before, .tk-qr::after { content:''; position:absolute; bottom:-13px; width:24px; height:24px; border-radius:50%; background:#fff; border:1px solid var(--border); }
.tk-qr::before { left:-13px; } .tk-qr::after { right:-13px; }
.tk-qr svg { width:220px; height:220px; max-width:100%; display:block; margin:0 auto; }
.tk-code { font-family:'Sora',monospace; font-size:1.6rem; font-weight:700; letter-spacing:.12em; margin-top:.6rem; font-variant-numeric:tabular-nums; }
.tk-qr p { margin:.3rem 0 0; font-size:.88rem; color:var(--muted); }
.tk-rows { padding:1.1rem 1.3rem 1.3rem; display:grid; gap:.8rem; }
.tk-row { display:flex; gap:.85rem; align-items:flex-start; }
.tk-row i { width:36px; height:36px; flex-shrink:0; border-radius:11px; background:var(--blue-xl); color:var(--blue); display:flex; align-items:center; justify-content:center; font-size:.9rem; }
.tk-row small { display:block; font-size:.78rem; color:var(--muted); }
.tk-row b { font-size:1rem; }
.tk-row p { margin:.1rem 0 0; font-size:.86rem; color:var(--muted); }
.tk-status { display:inline-flex; align-items:center; gap:.4rem; font-size:.85rem; font-weight:700; padding:.35rem .8rem; border-radius:999px; margin-top:.8rem; }
.tk-status.in { background:#dcfce7; color:#15803d; }
.tk-status.wait { background:#fff7ed; color:#c2410c; }

.tk-actions { display:grid; gap:.65rem; margin-top:1.4rem; }
.tk-actions .evf-btn { margin:0; min-height:56px; font-size:1.02rem; }
.tk-cancel { display:block; margin:1.6rem auto 0; background:none; border:0; color:#b91c1c; font:inherit; font-size:.9rem; font-weight:600; cursor:pointer; min-height:44px; padding:.5rem 1rem; }
.tk-cancel:hover { text-decoration:underline; }

/* Verification views */
.tk-verify { text-align:center; padding:1.6rem 1.3rem; }
.tk-verify .big { width:84px; height:84px; margin:0 auto .9rem; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:2.2rem; }
.tk-verify .big.ok { background:#dcfce7; color:#15803d; }
.tk-verify .big.done { background:#dbeafe; color:#1d4ed8; }
.tk-verify h2 { font-family:'Sora',sans-serif; font-size:1.4rem; margin:0; }
.tk-verify p { color:var(--ink-2); margin:.4rem 0 0; }
.tk-staff { background:#0a1628; color:#fff; border-radius:18px; padding:1.1rem 1.2rem; margin-bottom:1.2rem; }
.tk-staff small { font-size:.72rem; letter-spacing:.08em; text-transform:uppercase; color:#93c5fd; }
.tk-staff b { display:block; font-size:1.15rem; margin:.15rem 0 .1rem; }
.tk-staff p { margin:0; font-size:.88rem; color:#cbd5e1; }
.tk-staff form { margin-top:.9rem; }
.tk-staff .evf-btn { margin:0; min-height:60px; font-size:1.1rem; background:#10b981; }
.tk-staff .evf-btn:hover { background:#059669; }
.tk-staff .evf-btn.undo { background:transparent; border:1px solid #475569; color:#e2e8f0; min-height:48px; font-size:.95rem; }
.tk-flash { border-radius:14px; padding:.9rem 1rem; font-weight:700; margin-bottom:1rem; display:flex; gap:.6rem; align-items:center; }
.tk-flash.in { background:#dcfce7; color:#166534; } .tk-flash.out { background:#f1f5f9; color:#334155; }
@media print { .tk-actions, .tk-cancel, .tk-back, header, footer, nav { display:none !important; } .tk-ticket { box-shadow:none; } }
</style>
@endpush

@section('content')
<div class="tk">
    @if($isStaff)
        {{-- Event staff scanning tickets at the door --}}
        @if(session('checkin'))
            <div class="tk-flash {{ session('checkin') === 'hadir' ? 'in' : 'out' }}" role="status">
                <i class="fas {{ session('checkin') === 'hadir' ? 'fa-circle-check' : 'fa-rotate-left' }}" aria-hidden="true"></i>
                {{ session('checkin') === 'hadir' ? $registration->name.' sudah ditandai hadir.' : 'Tanda hadir dibatalkan.' }}
            </div>
        @endif
        <div class="tk-staff" data-tour="checkin">
            <small>Mode panitia · {{ $event->title }}</small>
            <b>{{ $registration->name }}</b>
            <p>{{ $session->label }} · {{ $session->timeLabel() }} {{ $event->timezone }} · usia {{ $registration->age }}</p>
            <p style="margin-top:.35rem;">
                @if($registration->checked_in_at)
                    <i class="fas fa-circle-check" style="color:#4ade80;" aria-hidden="true"></i> Sudah hadir, pukul {{ $registration->checked_in_at->timezone(config('app.timezone'))->format('H.i') }}
                @else
                    <i class="fas fa-ticket" style="color:#93c5fd;" aria-hidden="true"></i> Tiket sah, belum ditandai hadir
                @endif
            </p>
            <form method="POST" action="{{ route('tickets.checkin', $registration) }}">
                @csrf
                @if($registration->checked_in_at)
                    <button class="evf-btn undo">Batalkan tanda hadir</button>
                @else
                    <button class="evf-btn"><i class="fas fa-check" aria-hidden="true"></i> Tandai hadir</button>
                @endif
            </form>
        </div>
        <a href="{{ route('admin.events.registrations', $event) }}" class="tk-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Daftar peserta event ini</a>
    @endif

    @if($isOwner)
        @unless($isStaff)
            <a href="{{ route('events.show', $event) }}" class="tk-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Halaman event</a>
        @endunless
        <h1>Tiket kamu</h1>
        <p class="tk-lead">Tunjukkan QR di bawah ke panitia saat datang. Boleh dari HP, hasil screenshot, atau dicetak.</p>

        <div class="tk-ticket">
            <div class="tk-head">
                <small>Tiket event · Gratis</small>
                <b>{{ $event->title }}</b>
                @if($event->subtitle)<span>{{ $event->subtitle }}</span>@endif
            </div>
            <div class="tk-qr" data-tour="qr">
                <div role="img" aria-label="QR code tiket {{ $registration->code }}">{!! $qr !!}</div>
                <div class="tk-code">{{ $registration->code }}</div>
                <p>Kode tiket, kalau QR tidak bisa dipindai</p>
                @if($registration->checked_in_at)
                    <span class="tk-status in"><i class="fas fa-circle-check" aria-hidden="true"></i> Sudah hadir</span>
                @elseif($past)
                    <span class="tk-status wait">Event sudah berlangsung</span>
                @endif
            </div>
            <div class="tk-rows">
                <div class="tk-row"><i class="fas fa-user" aria-hidden="true"></i><div><small>Nama peserta</small><b>{{ $registration->name }}</b></div></div>
                <div class="tk-row"><i class="fas fa-calendar-days" aria-hidden="true"></i><div><small>Tanggal</small><b>{{ $event->dateLabel() }}</b></div></div>
                <div class="tk-row"><i class="far fa-clock" aria-hidden="true"></i><div><small>Sesi</small><b>{{ $session->label }}, pukul {{ $session->timeLabel() }} {{ $event->timezone }}</b></div></div>
                <div class="tk-row">
                    <i class="fas {{ $event->isOnline() ? 'fa-video' : 'fa-location-dot' }}" aria-hidden="true"></i>
                    <div>
                        <small>{{ $event->isOnline() ? 'Tempat' : 'Lokasi' }}</small>
                        <b>{{ $event->isOnline() ? 'Online' : $event->venue }}</b>
                        @if(! $event->isOnline() && $event->address)<p>{{ $event->address }}</p>@endif
                    </div>
                </div>
            </div>
        </div>

        <div class="tk-actions">
            @if($event->isOnline() && $event->meeting_url)
                <a href="{{ $event->meeting_url }}" target="_blank" rel="noopener" class="evf-btn violet"><i class="fas fa-video" aria-hidden="true"></i> Masuk ke acara online</a>
            @endif
            <a href="{{ route('tickets.download', $registration) }}" class="evf-btn" data-tour="unduh"><i class="fas fa-download" aria-hidden="true"></i> Unduh tiket (PDF)</a>
            @unless($past)
                <a href="{{ $registration->calendarUrl() }}" target="_blank" rel="noopener" class="evf-btn ghost" data-tour="kalender"><i class="far fa-calendar-plus" aria-hidden="true"></i> Simpan ke Google Calendar</a>
            @endunless
            @if(! $event->isOnline() && $event->maps_url)
                <a href="{{ $event->maps_url }}" target="_blank" rel="noopener" class="evf-btn ghost"><i class="fas fa-map-location-dot" aria-hidden="true"></i> Petunjuk arah ke lokasi</a>
            @endif
        </div>

        @unless($past)
            <form method="POST" action="{{ route('events.cancel', $event) }}" x-data @submit="if (!$el.dataset.ok) { $event.preventDefault(); $el.dataset.ok = 1; $el.querySelector('button').textContent = 'Yakin batal? Tekan sekali lagi'; }">
                @csrf @method('DELETE')
                <button type="submit" class="tk-cancel" data-tour="batal">Tidak jadi ikut? Batalkan pendaftaran</button>
            </form>
        @endunless
    @elseif(! $isStaff)
        {{-- Someone else opened the link or scanned the QR: confirm it's real without showing personal data --}}
        <div class="evd-card tk-verify">
            <div class="big {{ $registration->checked_in_at ? 'done' : 'ok' }}"><i class="fas {{ $registration->checked_in_at ? 'fa-user-check' : 'fa-circle-check' }}" aria-hidden="true"></i></div>
            <h2>Tiket sah</h2>
            <p>{{ $event->title }}<br>{{ $event->dateLabel() }} · {{ $session->label }}, {{ $session->timeLabel() }} {{ $event->timezone }}</p>
            <p style="margin-top:.8rem;">Atas nama <b>{{ $registration->maskedName() }}</b> · kode <b>{{ $registration->code }}</b></p>
            @if($registration->checked_in_at)<p class="evd-note">Peserta sudah hadir.</p>@endif
            <a href="{{ route('events.show', $event) }}" class="evf-btn ghost" style="margin-top:1.3rem;">Lihat event</a>
        </div>
    @endif
</div>
@endsection

@if($isStaff || $isOwner)
@push('scripts')
@include('partials.tour-assets')
<script>
@if($isStaff)
KATour.page('event-checkin', [
  { element: '[data-tour="checkin"]', popover: { title: 'Mode panitia', description: 'Halaman ini terbuka setelah kamu memindai QR peserta. Pastikan nama dan sesinya cocok.', side: 'bottom' } },
  { element: '[data-tour="checkin"] form', popover: { title: 'Tandai hadir', description: 'Tekan tombol hijau ini. Status peserta langsung berubah jadi hadir di daftar peserta admin. Salah tekan? Tombolnya bisa dibatalkan.', side: 'top' } }
]);
@else
KATour.page('event-ticket', [
  { element: '[data-tour="qr"]', popover: { title: 'Ini tiketmu', description: 'Saat datang, tunjukkan QR ini ke panitia. Kalau QR tidak terbaca, sebutkan kode di bawahnya.', side: 'bottom' } },
  { element: '[data-tour="unduh"]', popover: { title: 'Simpan tiket', description: 'Unduh PDF-nya supaya tetap bisa dibuka tanpa internet. Screenshot halaman ini juga boleh.', side: 'top' } },
  { element: '[data-tour="kalender"]', popover: { title: 'Biar tidak lupa', description: 'Simpan jadwalnya ke Google Calendar, nanti HP-mu memberi pengingat.', side: 'top' } },
  { element: '[data-tour="batal"]', popover: { title: 'Berhalangan?', description: 'Batalkan di sini supaya kursimu bisa dipakai orang lain.', side: 'top' } }
]);
@endif
</script>
@endpush
@endif
