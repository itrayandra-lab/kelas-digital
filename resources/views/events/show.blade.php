@extends('layouts.app')

@section('seo')
    <title>{{ $event->title }} - Event Ray Academy</title>
    <meta name="description" content="{{ Str::limit(trim(($event->subtitle ? $event->subtitle.'. ' : '').$event->plainDescription()), 155) }}">
    <meta property="og:title" content="{{ $event->title }}">
    <meta property="og:type" content="website">
    @if($event->cover_url)<meta property="og:image" content="{{ $event->cover_url }}">@endif
@endsection

@push('schema')
@php
    $first = $event->sessions->first();
    $last = $event->sessions->last();
    $tz = ['WIB' => '+07:00', 'WITA' => '+08:00', 'WIT' => '+09:00'][$event->timezone] ?? '+07:00';
    $at = fn (?string $time) => $time ? $event->event_date->format('Y-m-d').'T'.substr($time, 0, 5).':00'.$tz : null;
@endphp
<script type="application/ld+json">
{!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Event',
    'name' => $event->title,
    'description' => Str::limit(trim(($event->subtitle ? $event->subtitle.'. ' : '').$event->plainDescription()), 500),
    'image' => $event->cover_url ? [$event->cover_url] : null,
    'startDate' => $at($first?->starts_at),
    'endDate' => $at($last?->ends_at ?: $last?->starts_at),
    'eventStatus' => 'https://schema.org/EventScheduled',
    'eventAttendanceMode' => $event->isOnline() ? 'https://schema.org/OnlineEventAttendanceMode' : 'https://schema.org/OfflineEventAttendanceMode',
    'location' => $event->isOnline()
        ? ['@type' => 'VirtualLocation', 'url' => route('events.show', $event)]
        : array_filter(['@type' => 'Place', 'name' => $event->venue, 'address' => $event->address ? ['@type' => 'PostalAddress', 'streetAddress' => $event->address, 'addressCountry' => 'ID'] : null]),
    'organizer' => ['@type' => 'Organization', 'name' => 'RayAcademy', 'url' => url('/')],
    'offers' => [
        '@type' => 'Offer', 'price' => 0, 'priceCurrency' => 'IDR', 'url' => route('events.show', $event),
        'availability' => $event->acceptsRegistrations() && $event->seatsLeft() > 0 ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut',
        'validFrom' => $event->created_at?->toAtomString(),
    ],
    'maximumAttendeeCapacity' => (int) $event->sessions->sum('capacity'),
    'remainingAttendeeCapacity' => $event->seatsLeft(),
    'inLanguage' => 'id-ID',
]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}
</script>
@include('partials.breadcrumb-schema', ['crumbs' => ['Beranda' => url('/'), 'Event' => route('events.index'), $event->title => route('events.show', $event)]])
@endpush

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

            <div class="evd-facts" data-tour="info">
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
            <div class="evd-card evd-status" id="tiket" data-tour="status">
                @if($registration)
                    <div class="evt">
                        <div class="evt-ck"><i class="fas fa-check" aria-hidden="true"></i></div>
                        <h2 class="evd-h2">Kamu sudah terdaftar</h2>
                        <p class="evd-note">{{ $registration->session->label }}, pukul {{ $registration->session->timeLabel() }} {{ $event->timezone }} · kode tiket <b>{{ $registration->code }}</b></p>
                        @if($event->isOnline() && $event->meeting_url)
                            <a href="{{ $event->meeting_url }}" target="_blank" rel="noopener" class="evf-btn violet"><i class="fas fa-video" aria-hidden="true"></i> Masuk ke acara online</a>
                        @endif
                        <a href="{{ route('tickets.show', $registration) }}" class="evf-btn {{ $event->isOnline() && $event->meeting_url ? 'ghost' : '' }}"><i class="fas fa-ticket" aria-hidden="true"></i> Lihat tiket saya</a>
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
                        <a href="{{ route('events.join', $event) }}" class="evf-btn" data-tour="daftar">Daftar sekarang <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                        @guest<p class="evd-guest">Pakai akun Ray Academy. Belum punya? <a href="{{ route('events.join', [$event, 'baru' => 1]) }}" style="color:var(--blue);font-weight:600;">Buat akun gratis</a></p>@endguest
                    @else
                        <div class="evf-alert warn">Semua sesi sudah penuh. Kursi bisa terbuka lagi kalau ada peserta yang membatalkan.</div>
                    @endif
                @endif
            </div>

            @if($event->description)
                <div class="evd-desc" data-tour="tentang">
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

@push('scripts')
@include('partials.tour-assets')
<script>
KATour.page('event-page', [
  { popover: { title: 'Cara ikut event ini', description: 'Cukup tiga langkah: lihat info acaranya, pilih sesi, lalu daftar. Gratis, pakai akun Ray Academy.' } },
  { element: '[data-tour="info"]', popover: { title: 'Kapan dan di mana', description: 'Tanggal, jam setiap sesi, dan lokasi acara ada di sini.', side: 'bottom' } },
  { element: '[data-tour="status"]', popover: { title: @js($registration ? 'Kamu sudah terdaftar' : 'Sisa kursi'), description: @js($registration ? 'Tekan "Lihat tiket saya" untuk membuka tiket dan QR-mu.' : 'Kursi setiap sesi terbatas. Angka "Sisa" berkurang setiap ada yang mendaftar.'), side: 'top' } },
  { element: '[data-tour="daftar"]', popover: { title: 'Daftar di sini', description: 'Tekan tombol ini. Kalau belum masuk, kamu diminta login atau buat akun dulu, lalu langsung kembali ke form pendaftaran.', side: 'top' } },
  { element: '[data-tour="tentang"]', popover: { title: 'Isi acara', description: 'Baca dulu apa yang akan kamu pelajari dan apa yang perlu disiapkan.', side: 'top' } }
]);
</script>
@endpush
