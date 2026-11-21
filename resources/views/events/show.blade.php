@extends('layouts.app')

@section('seo')
    <title>{{ $event->title }} - Event Ray Academy</title>
    <meta name="description" content="{{ Str::limit(trim(($event->subtitle ? $event->subtitle.'. ' : '').$event->description), 155) }}">
    <meta property="og:title" content="{{ $event->title }}">
    <meta property="og:type" content="website">
    @if($event->cover_url)<meta property="og:image" content="{{ $event->cover_url }}">@endif
@endsection

@include('partials.event-styles')

@push('styles')
<style>
.evd { padding-block:2rem 4rem; }
.evd-crumb { font-size:.78rem; color:var(--muted); margin-bottom:1.25rem; }
.evd-crumb a { color:var(--muted); text-decoration:none; }
.evd-crumb a:hover { color:var(--blue); }
.evd-grid { display:grid; grid-template-columns:minmax(0,5fr) minmax(0,6fr); gap:2.5rem; align-items:start; }
.evd-poster { position:sticky; top:96px; border-radius:20px; overflow:hidden; background:linear-gradient(150deg,#0d5a96,#1474bc 60%,#10b981); box-shadow:0 18px 40px rgba(10,22,40,.14); aspect-ratio:4/5; }
.evd-poster img { width:100%; height:100%; object-fit:cover; display:block; }
.evd-tags { display:flex; flex-wrap:wrap; gap:.45rem; margin-bottom:.8rem; }
.evd-tags span { font-size:.72rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; padding:.3rem .7rem; border-radius:999px; background:var(--blue-xl); color:var(--blue-d); }
.evd-tags .free { background:#dcfce7; color:#15803d; }
.evd-tags .online { background:#efeafe; color:#5b3cc4; }
.evd h1 { font-family:'Sora',sans-serif; font-size:clamp(1.7rem,3.4vw,2.4rem); font-weight:700; letter-spacing:-.02em; line-height:1.15; margin:0; }
.evd-sub { font-size:1.08rem; color:var(--ink-2); margin:.4rem 0 0; }
.evd-card { background:#fff; border:1px solid var(--border); border-radius:18px; padding:1.25rem 1.35rem; margin-top:1.4rem; }
.evd-info { display:flex; flex-direction:column; gap:1rem; }
.evd-row { display:flex; gap:.9rem; align-items:flex-start; }
.evd-ic { width:42px; height:42px; flex-shrink:0; border-radius:13px; display:flex; align-items:center; justify-content:center; font-size:1rem; }
.evd-ic.c1 { background:#e3f0fb; color:#1474bc; } .evd-ic.c2 { background:#fff1db; color:#b45f06; } .evd-ic.c3 { background:#dff6ec; color:#0f8a5f; }
.evd-row small { display:block; font-size:.75rem; color:var(--muted); }
.evd-row b { font-weight:700; }
.evd-row p { margin:.15rem 0 0; font-size:.86rem; color:var(--muted); }
.evd-row a { font-size:.84rem; font-weight:600; color:var(--blue); text-decoration:none; }
.evd-row a:hover { text-decoration:underline; }
.evd-desc { margin-top:1.6rem; color:var(--ink-2); line-height:1.75; font-size:.97rem; }
.evd-desc h2 { font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:700; color:var(--ink); margin:0 0 .5rem; }

.evf h2 { font-family:'Sora',sans-serif; font-size:1.2rem; font-weight:700; margin:0; }
.evf-note { font-size:.84rem; color:var(--muted); margin:.25rem 0 0; }
.evf-acc { display:flex; align-items:center; gap:.8rem; background:var(--surf); border:1px solid var(--border); border-radius:14px; padding:.8rem .95rem; margin-top:1.1rem; }
.evf-acc .av { width:40px; height:40px; flex-shrink:0; border-radius:50%; background:var(--blue); color:#fff; font-weight:700; display:flex; align-items:center; justify-content:center; }
.evf-acc b { display:block; font-size:.92rem; }
.evf-acc span { font-size:.8rem; color:var(--muted); word-break:break-all; }
.evf-label { display:block; font-weight:600; font-size:.88rem; margin:1.1rem 0 .45rem; }
.evf-input { width:100%; min-height:48px; padding:.7rem .95rem; border:1px solid var(--border); border-radius:12px; font:inherit; font-size:.95rem; background:#fff; }
.evf-input:focus { outline:none; border-color:var(--blue); box-shadow:0 0 0 3px rgba(20,116,188,.18); }
.evf-input[aria-invalid="true"] { border-color:#ef4444; }
.evf-opts { display:grid; gap:.6rem; }
.evf-opt { position:relative; display:block; cursor:pointer; }
.evf-opt input { position:absolute; opacity:0; pointer-events:none; }
.evf-box { display:flex; justify-content:space-between; align-items:center; gap:.75rem; min-height:52px; padding:.75rem 1rem; border:1.5px solid var(--border); border-radius:12px; transition:border-color .15s, background .15s; }
.evf-box b { font-size:.92rem; }
.evf-box em { font-style:normal; font-size:.78rem; font-weight:700; color:#0f8a5f; white-space:nowrap; }
.evf-box em.low { color:#b45309; }
.evf-opt input:checked + .evf-box { border-color:var(--blue); background:var(--blue-xl); box-shadow:0 0 0 3px rgba(20,116,188,.12); }
.evf-opt input:focus-visible + .evf-box { outline:2px solid var(--blue); outline-offset:2px; }
.evf-opt.full { cursor:not-allowed; }
.evf-opt.full .evf-box { background:#f3f4f6; opacity:.7; }
.evf-opt.full em { color:#b91c1c; }
.evf-btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; width:100%; min-height:52px; margin-top:1.4rem; border:0; border-radius:14px; background:var(--blue); color:#fff; font:inherit; font-size:1rem; font-weight:700; cursor:pointer; text-decoration:none; transition:background .15s; }
.evf-btn:hover { background:var(--blue-d); color:#fff; }
.evf-btn.ghost { background:#fff; color:var(--ink); border:1px solid var(--border); margin-top:.6rem; }
.evf-btn.ghost:hover { background:var(--surf); color:var(--ink); }
.evf-btn:disabled { background:#cbd5e1; cursor:not-allowed; }
.evf-btn:focus-visible { outline:2px solid var(--blue); outline-offset:2px; }
.evf-err { background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; border-radius:12px; padding:.75rem 1rem; font-size:.88rem; margin-top:1rem; }
.evf-hint { font-size:.78rem; color:var(--muted); margin:.35rem 0 0; }
.evf-done { text-align:center; }
.evf-done .ck { width:64px; height:64px; margin:0 auto .8rem; border-radius:50%; background:#dcfce7; color:#15803d; font-size:1.6rem; display:flex; align-items:center; justify-content:center; }
.evf-ticket { text-align:left; background:var(--surf); border:1px dashed #c7d3e2; border-radius:14px; padding:.9rem 1rem; margin-top:1rem; display:grid; gap:.45rem; font-size:.88rem; }
.evf-ticket div { display:flex; justify-content:space-between; gap:1rem; }
.evf-ticket span { color:var(--muted); }
.evf-ticket b { text-align:right; }
.evf-cancel { background:none; border:0; color:#b91c1c; font:inherit; font-size:.84rem; font-weight:600; cursor:pointer; margin-top:1rem; padding:.5rem; min-height:44px; }
.evf-cancel:hover { text-decoration:underline; }
.evf-closed { text-align:center; color:var(--muted); }
.evf-closed i { font-size:1.6rem; color:var(--muted); margin-bottom:.5rem; }

@media (prefers-reduced-motion: reduce) { .evf-box, .evf-btn { transition:none; } }
@media (max-width: 900px) {
    .evd-grid { grid-template-columns:minmax(0,1fr); gap:1.5rem; }
    .evd-poster { position:static; max-width:440px; width:100%; margin:0 auto; }
}
</style>
@endpush

@section('content')
@php
    $user = auth()->user();
    $open = $event->acceptsRegistrations();
    $anySeat = $event->sessions->contains(fn ($s) => $s->seatsLeft() > 0);
    $maxCapacity = $event->sessions->max('capacity');
@endphp

<div class="ev-wrap evd">
    <div class="evd-crumb"><a href="{{ route('home') }}">Beranda</a> › <a href="{{ route('events.index') }}">Event</a> › {{ $event->title }}</div>

    @unless($event->is_published)
        <div class="evf-err" style="margin:0 0 1.25rem;background:#fffbeb;border-color:#fcd34d;color:#92400e;">
            <i class="fas fa-eye-slash" aria-hidden="true"></i> Pratinjau admin: event ini belum tampil di website.
        </div>
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

            <div class="evd-card evd-info">
                <div class="evd-row">
                    <div class="evd-ic c1"><i class="fas fa-calendar-days" aria-hidden="true"></i></div>
                    <div><small>Tanggal</small><b>{{ $event->dateLabel() }}</b></div>
                </div>
                <div class="evd-row">
                    <div class="evd-ic c2"><i class="far fa-clock" aria-hidden="true"></i></div>
                    <div><small>{{ $event->sessions->count() > 1 ? 'Sesi' : 'Waktu' }}</small><b>{{ $event->timesLabel() }}</b></div>
                </div>
                <div class="evd-row">
                    <div class="evd-ic c3"><i class="fas {{ $event->isOnline() ? 'fa-video' : 'fa-location-dot' }}" aria-hidden="true"></i></div>
                    <div>
                        <small>{{ $event->isOnline() ? 'Tempat' : 'Lokasi' }}</small>
                        @if($event->isOnline())
                            <b>Online</b>
                            <p>Link acara muncul di halaman ini dan di dashboard-mu setelah kamu mendaftar.</p>
                        @else
                            <b>{{ $event->venue }}</b>
                            @if($event->address)<p>{{ $event->address }}</p>@endif
                            @if($event->maps_url)<a href="{{ $event->maps_url }}" target="_blank" rel="noopener">Buka di Google Maps <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i></a>@endif
                        @endif
                    </div>
                </div>
            </div>

            {{-- Registration --}}
            <div class="evd-card evf" id="daftar" style="scroll-margin-top:96px;">
                @if($registration)
                    <div class="evf-done">
                        <div class="ck"><i class="fas fa-check" aria-hidden="true"></i></div>
                        <h2>{{ session('event_success') ? 'Pendaftaran berhasil!' : 'Kamu sudah terdaftar' }}</h2>
                        <p class="evf-note">Detailnya juga kami kirim ke {{ $registration->email }}.</p>
                        <div class="evf-ticket">
                            <div><span>Nama</span><b>{{ $registration->name }}</b></div>
                            <div><span>Tanggal</span><b>{{ $event->dateLabel() }}</b></div>
                            <div><span>Sesi</span><b>{{ $registration->session->label }}, {{ $registration->session->timeLabel() }} {{ $event->timezone }}</b></div>
                            @unless($event->isOnline())<div><span>Lokasi</span><b>{{ $event->venue }}</b></div>@endunless
                        </div>
                        @if($event->isOnline())
                            @if($event->meeting_url)
                                <a href="{{ $event->meeting_url }}" target="_blank" rel="noopener" class="evf-btn"><i class="fas fa-video" aria-hidden="true"></i> Masuk ke acara online</a>
                            @else
                                <p class="evf-hint" style="margin-top:1rem;">Link acara belum tersedia. Cek lagi di sini atau di dashboard menjelang acara.</p>
                            @endif
                        @endif
                        <a href="{{ route('dashboard') }}" class="evf-btn ghost">Lihat di dashboard</a>
                        @unless($event->isPast())
                            <form method="POST" action="{{ route('events.cancel', $event) }}" x-data @submit="if (!$el.dataset.ok) { $event.preventDefault(); $el.dataset.ok = 1; $el.querySelector('button').textContent = 'Klik sekali lagi untuk membatalkan'; }">
                                @csrf @method('DELETE')
                                <button type="submit" class="evf-cancel">Batalkan pendaftaran</button>
                            </form>
                        @endunless
                    </div>
                @elseif(! $open)
                    <div class="evf-closed">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <h2>{{ $event->isPast() ? 'Event ini sudah berlangsung' : 'Pendaftaran ditutup' }}</h2>
                        <p class="evf-note">Lihat event lain yang akan datang di <a href="{{ route('events.index') }}" style="color:var(--blue);font-weight:600;">halaman Event</a>.</p>
                    </div>
                @else
                    <h2>Form pendaftaran</h2>
                    <p class="evf-note">Gratis · maksimal {{ $maxCapacity }} peserta per sesi</p>
                    @if(session('event_cancelled'))
                        <p class="evf-hint" style="color:#0f8a5f;font-weight:600;">Pendaftaranmu sudah dibatalkan.</p>
                    @endif

                    @guest
                        <p style="margin:1.1rem 0 0;color:var(--ink-2);font-size:.92rem;">Untuk mendaftar, masuk dulu ke akun Ray Academy-mu. Nama dan email akan terisi otomatis dari akunmu.</p>
                        @unless($anySeat)<div class="evf-err" style="background:#fffbeb;border-color:#fcd34d;color:#92400e;">Semua sesi sudah penuh.</div>@endunless
                        <a href="{{ route('events.join', $event) }}" class="evf-btn">Masuk untuk mendaftar</a>
                        <a href="{{ route('events.join', [$event, 'baru' => 1]) }}" class="evf-btn ghost">Belum punya akun? Buat akun gratis</a>
                    @else
                        @if($errors->any())
                            <div class="evf-err" role="alert">{{ $errors->first() }}</div>
                        @endif
                        <form method="POST" action="{{ route('events.register', $event) }}" x-data="{ busy: false }" @submit="busy = true">
                            @csrf
                            <div class="evf-acc">
                                <div class="av" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
                                <div style="min-width:0;"><b>{{ $user->name }}</b><span>{{ $user->email }}</span></div>
                            </div>
                            <p class="evf-hint">Nama dan email diambil dari akunmu. Ubah di <a href="{{ route('profile.edit') }}" style="color:var(--blue);">profil</a> kalau perlu.</p>

                            <label class="evf-label" for="whatsapp">Nomor WhatsApp</label>
                            <input class="evf-input" type="tel" id="whatsapp" name="whatsapp" inputmode="tel" autocomplete="tel" required
                                placeholder="08xxxxxxxxxx" value="{{ old('whatsapp', $user->phone) }}" @if($errors->has('whatsapp')) aria-invalid="true" @endif>

                            <span class="evf-label" id="sesi-label">Pilih sesi</span>
                            <div class="evf-opts" role="radiogroup" aria-labelledby="sesi-label">
                                @foreach($event->sessions as $session)
                                    @php $left = $session->seatsLeft(); @endphp
                                    <label class="evf-opt {{ $left === 0 ? 'full' : '' }}">
                                        <input type="radio" name="event_session_id" value="{{ $session->id }}" required @disabled($left === 0)
                                            @checked(old('event_session_id') == $session->id || ($event->sessions->count() === 1 && $left > 0))>
                                        <span class="evf-box">
                                            <b>{{ $session->label }} — {{ $session->timeLabel() }} {{ $event->timezone }}</b>
                                            <em class="{{ $left > 0 && $left <= 5 ? 'low' : '' }}">{{ $left === 0 ? 'Penuh' : 'Sisa '.$left.' kursi' }}</em>
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            <label class="evf-label" for="age">Usia</label>
                            <input class="evf-input" type="number" id="age" name="age" min="10" max="100" inputmode="numeric" required
                                value="{{ old('age') }}" style="max-width:10rem;" @if($errors->has('age')) aria-invalid="true" @endif>

                            <button type="submit" class="evf-btn" :disabled="busy" @disabled(! $anySeat)>
                                <span x-text="busy ? 'Memproses…' : @js($anySeat ? 'Daftar sekarang' : 'Semua sesi sudah penuh')">{{ $anySeat ? 'Daftar sekarang' : 'Semua sesi sudah penuh' }}</span>
                            </button>
                        </form>
                    @endguest
                @endif
            </div>

            @if($event->description)
                <div class="evd-desc">
                    <h2>Tentang event ini</h2>
                    {!! nl2br(e($event->description)) !!}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
