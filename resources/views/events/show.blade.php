@extends('layouts.app')

@section('seo')
    <title>{{ $event->title }} - Event Ray Academy</title>
    <meta name="description" content="{{ Str::limit(trim(($event->subtitle ? $event->subtitle.'. ' : '').$event->plainDescription()), 155) }}">
    <meta property="og:title" content="{{ $event->title }}">
    <meta property="og:type" content="website">
    @if($event->cover_url)<meta property="og:image" content="{{ $event->cover_url }}">@endif
@endsection

@include('partials.event-styles')
@include('events._page-styles')

@push('styles')
<style>
.evd-grid { display:grid; grid-template-columns:minmax(0,5fr) minmax(0,6fr); gap:2.75rem; align-items:start; }
.evd-poster { position:sticky; top:96px; border-radius:22px; overflow:hidden; background:linear-gradient(150deg,#0d5a96,#1474bc 60%,#10b981); box-shadow:0 18px 40px rgba(10,22,40,.14); aspect-ratio:4/5; }
.evd-poster img { width:100%; height:100%; object-fit:cover; display:block; }
.evd h1 { font-family:'Sora',sans-serif; font-size:clamp(1.75rem,3.4vw,2.5rem); font-weight:700; letter-spacing:-.02em; line-height:1.12; margin:0; }
.evd-sub { font-size:1.08rem; color:var(--ink-2); margin:.45rem 0 0; }
.evd-facts { display:grid; gap:1rem; margin-top:1.4rem; }
.evd-fact { display:flex; gap:.9rem; align-items:flex-start; }
.evd-fact small { display:block; font-size:.75rem; color:var(--muted); }
.evd-fact b { font-weight:700; }
.evd-fact p { margin:.15rem 0 0; font-size:.86rem; color:var(--muted); line-height:1.5; }
.evd-fact a { display:inline-block; margin-top:.2rem; font-size:.84rem; font-weight:600; color:var(--blue); text-decoration:none; }
.evd-fact a:hover { text-decoration:underline; }
.evd-status { margin-top:1.25rem; scroll-margin-top:96px; }
.evd-guest { font-size:.82rem; color:var(--muted); text-align:center; margin:.7rem 0 0; }

.evd-desc { margin-top:2rem; color:var(--ink-2); line-height:1.75; font-size:.98rem; }
.evd-desc > h2 { font-family:'Sora',sans-serif; font-size:1.2rem; font-weight:700; color:var(--ink); margin:0 0 .6rem; }
.evd-desc p { margin:0 0 1rem; }
.evd-desc h3 { font-family:'Sora',sans-serif; font-size:1rem; font-weight:700; color:var(--ink); margin:1.5rem 0 .6rem; }
.evd-desc ul { list-style:none; margin:0 0 1rem; padding:0; display:grid; gap:.55rem; }
.evd-desc li { position:relative; padding-left:1.9rem; }
.evd-desc li::before { content:'\f00c'; font-family:'Font Awesome 7 Free','Font Awesome 6 Free'; font-weight:900; position:absolute; left:0; top:.2rem; width:1.25rem; height:1.25rem; border-radius:50%; background:#dcfce7; color:#15803d; font-size:.62rem; display:flex; align-items:center; justify-content:center; }

/* Ticket */
.evt { text-align:center; }
.evt-ck { width:60px; height:60px; margin:0 auto .75rem; border-radius:50%; background:#dcfce7; color:#15803d; font-size:1.5rem; display:flex; align-items:center; justify-content:center; }
.evt-box { text-align:left; margin-top:1.1rem; border-radius:16px; overflow:hidden; border:1px solid var(--border); }
.evt-head { background:linear-gradient(135deg,#0d5a96,#1474bc); color:#fff; padding:.85rem 1rem; }
.evt-head small { display:block; font-size:.7rem; letter-spacing:.08em; text-transform:uppercase; opacity:.8; }
.evt-head b { font-family:'Sora',sans-serif; font-size:1rem; }
.evt-rows { padding:.9rem 1rem; display:grid; gap:.55rem; font-size:.88rem; background:#fff; border-top:2px dashed #dbe4ef; }
.evt-rows div { display:flex; justify-content:space-between; gap:1rem; }
.evt-rows span { color:var(--muted); }
.evt-rows b { text-align:right; }
.evt-cancel { background:none; border:0; color:#b91c1c; font:inherit; font-size:.84rem; font-weight:600; cursor:pointer; margin-top:.8rem; padding:.5rem; min-height:44px; }
.evt-cancel:hover { text-decoration:underline; }

/* Bottom bar on phones */
.evd-bar { display:none; }
@media (max-width: 900px) {
    .evd { padding-top:1rem; }
    .evd-grid { grid-template-columns:minmax(0,1fr); gap:1.4rem; }
    .evd-poster { position:static; max-width:460px; width:100%; margin:0 auto; border-radius:18px; }
    .evd-crumb { display:none; }
    .evd-bar { display:flex; align-items:center; gap:1rem; position:fixed; left:0; right:0; bottom:0; z-index:40;
               padding:.75rem 1rem calc(.75rem + env(safe-area-inset-bottom, 0px)); background:rgba(255,255,255,.96); border-top:1px solid var(--border); box-shadow:0 -8px 24px rgba(10,22,40,.08); backdrop-filter:blur(8px); }
    .evd-bar div { flex:1; min-width:0; line-height:1.25; }
    .evd-bar b { display:block; font-size:.95rem; }
    .evd-bar span { font-size:.78rem; color:var(--muted); }
    .evd-bar .evf-btn { width:auto; margin:0; min-height:48px; padding:.7rem 1.4rem; }
    .evd.has-bar { padding-bottom:6.5rem; }
}
</style>
@endpush

@section('content')
@php
    $open = $event->acceptsRegistrations();
    $seatsLeft = $event->seatsLeft();
    $showBar = $open && ! $registration && $seatsLeft > 0;
@endphp

<div class="ev-wrap evd {{ $showBar ? 'has-bar' : '' }}">
    <div class="evd-crumb"><a href="{{ route('home') }}">Beranda</a> › <a href="{{ route('events.index') }}">Event</a> › {{ $event->title }}</div>

    @unless($event->is_published)
        <div class="evf-alert warn" style="margin:0 0 1.25rem;"><i class="fas fa-eye-slash" aria-hidden="true"></i> Pratinjau admin: event ini belum tampil di website.</div>
    @endunless

    <div class="evd-grid">
        <div class="evd-poster">
            @if($event->cover_url)<img src="{{ $event->cover_url }}" alt="Poster {{ $event->title }}">@endif
        </div>

        <div>
            <div class="evd-tags">
                <span class="free">Gratis</span>
                <span class="{{ $event->isOnline() ? 'online' : '' }}">{{ \App\Models\Event::FORMATS[$event->format] }}</span>
                @if($event->badge)<span>{{ $event->badge }}</span>@endif
            </div>
            <h1>{{ $event->title }}</h1>
            @if($event->subtitle)<p class="evd-sub">{{ $event->subtitle }}</p>@endif

            <div class="evd-facts">
                <div class="evd-fact">
                    <div class="evd-ic c1"><i class="fas fa-calendar-days" aria-hidden="true"></i></div>
                    <div><small>Tanggal</small><b>{{ $event->dateLabel() }}</b></div>
                </div>
                <div class="evd-fact">
                    <div class="evd-ic c2"><i class="far fa-clock" aria-hidden="true"></i></div>
                    <div><small>{{ $event->sessions->count() > 1 ? $event->sessions->count().' sesi' : 'Waktu' }}</small><b>{{ $event->timesLabel() }}</b></div>
                </div>
                <div class="evd-fact">
                    <div class="evd-ic c3"><i class="fas {{ $event->isOnline() ? 'fa-video' : 'fa-location-dot' }}" aria-hidden="true"></i></div>
                    <div>
                        @if($event->isOnline())
                            <small>Tempat</small><b>Online</b>
                            <p>Link acara muncul setelah kamu mendaftar.</p>
                        @else
                            <small>Lokasi</small><b>{{ $event->venue }}</b>
                            @if($event->address)<p>{{ $event->address }}</p>@endif
                            @if($event->maps_url)<a href="{{ $event->maps_url }}" target="_blank" rel="noopener">Buka di Google Maps <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i></a>@endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sign-up status: ticket, closed, or the sessions with a button to the form --}}
            <div class="evd-card evd-status" id="tiket">
                @if($registration)
                    <div class="evt">
                        <div class="evt-ck"><i class="fas fa-check" aria-hidden="true"></i></div>
                        <h2 class="evd-h2">{{ session('event_success') ? 'Pendaftaran berhasil!' : 'Kamu sudah terdaftar' }}</h2>
                        <p class="evd-note">Simpan tiket ini. Detailnya juga kami kirim ke {{ $registration->email }}.</p>
                        <div class="evt-box">
                            <div class="evt-head"><small>Tiket event</small><b>{{ $event->title }}</b></div>
                            <div class="evt-rows">
                                <div><span>Nama</span><b>{{ $registration->name }}</b></div>
                                <div><span>Tanggal</span><b>{{ $event->dateLabel() }}</b></div>
                                <div><span>Sesi</span><b>{{ $registration->session->label }}, {{ $registration->session->timeLabel() }} {{ $event->timezone }}</b></div>
                                <div><span>{{ $event->isOnline() ? 'Tempat' : 'Lokasi' }}</span><b>{{ $event->isOnline() ? 'Online' : $event->venue }}</b></div>
                            </div>
                        </div>
                        @if($event->isOnline())
                            @if($event->meeting_url)
                                <a href="{{ $event->meeting_url }}" target="_blank" rel="noopener" class="evf-btn violet"><i class="fas fa-video" aria-hidden="true"></i> Masuk ke acara online</a>
                            @else
                                <p class="evd-note" style="margin-top:1rem;">Link acara belum tersedia. Cek lagi di sini atau di dashboard menjelang acara.</p>
                            @endif
                        @endif
                        <a href="{{ route('dashboard') }}" class="evf-btn ghost">Lihat di dashboard</a>
                        @unless($event->isPast())
                            <form method="POST" action="{{ route('events.cancel', $event) }}" x-data @submit="if (!$el.dataset.ok) { $event.preventDefault(); $el.dataset.ok = 1; $el.querySelector('button').textContent = 'Yakin? Klik sekali lagi untuk membatalkan'; }">
                                @csrf @method('DELETE')
                                <button type="submit" class="evt-cancel">Batalkan pendaftaran</button>
                            </form>
                        @endunless
                    </div>
                @elseif(! $open)
                    <div style="text-align:center;">
                        <i class="fas fa-lock" style="font-size:1.5rem;color:var(--muted);" aria-hidden="true"></i>
                        <h2 class="evd-h2" style="margin-top:.5rem;">{{ $event->isPast() ? 'Event ini sudah berlangsung' : 'Pendaftaran ditutup' }}</h2>
                        <p class="evd-note">Lihat event lain di <a href="{{ route('events.index') }}" style="color:var(--blue);font-weight:600;">halaman Event</a>.</p>
                    </div>
                @else
                    <h2 class="evd-h2">{{ $event->sessions->count() > 1 ? 'Pilih sesi yang cocok' : 'Kursi tersedia' }}</h2>
                    <p class="evd-note">Gratis · kursi terbatas, sisa {{ $seatsLeft }} dari {{ $event->sessions->sum('capacity') }}</p>
                    @if(session('event_cancelled'))<div class="evf-alert ok">Pendaftaranmu sudah dibatalkan.</div>@endif
                    @include('events._sessions')
                    @if($seatsLeft > 0)
                        <a href="{{ route('events.join', $event) }}" class="evf-btn">Daftar sekarang <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                        @guest<p class="evd-guest">Pakai akun Ray Academy. Belum punya? <a href="{{ route('events.join', [$event, 'baru' => 1]) }}" style="color:var(--blue);font-weight:600;">Buat akun gratis</a></p>@endguest
                    @else
                        <div class="evf-alert warn">Semua sesi sudah penuh. Kursi bisa terbuka lagi kalau ada peserta yang membatalkan.</div>
                    @endif
                @endif
            </div>

            @if($event->description)
                <div class="evd-desc">
                    <h2>Tentang event ini</h2>
                    {!! $event->descriptionHtml() !!}
                </div>
            @endif
        </div>
    </div>
</div>

@if($showBar)
    <div class="evd-bar">
        <div><b>Gratis</b><span>Sisa {{ $seatsLeft }} kursi · {{ $event->event_date->locale('id')->translatedFormat('j M') }}</span></div>
        <a href="{{ route('events.join', $event) }}" class="evf-btn">Daftar sekarang</a>
    </div>
@endif
@endsection
