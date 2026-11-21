@extends('layouts.app')

@section('seo')
    <title>Daftar {{ $event->title }} - Ray Academy</title>
    <meta name="robots" content="noindex">
@endsection

@include('events._page-styles')

@push('styles')
<style>
.evr { max-width:620px; margin:0 auto; padding:1.5rem 1.25rem 4rem; }
.evr-back { display:inline-flex; align-items:center; gap:.45rem; min-height:44px; font-size:.88rem; font-weight:600; color:var(--muted); text-decoration:none; }
.evr-back:hover { color:var(--blue); }
.evr-sum { display:flex; gap:1rem; align-items:center; margin-top:.5rem; }
.evr-thumb { width:76px; aspect-ratio:4/5; flex-shrink:0; border-radius:12px; overflow:hidden; background:linear-gradient(150deg,#0d5a96,#1474bc 60%,#10b981); }
.evr-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
.evr-sum h1 { font-family:'Sora',sans-serif; font-size:1.3rem; font-weight:700; line-height:1.25; margin:0; }
.evr-sum p { margin:.25rem 0 0; font-size:.84rem; color:var(--muted); line-height:1.5; }
.evr-form { margin-top:1.4rem; }
.evr-step { display:flex; align-items:center; gap:.6rem; font-family:'Sora',sans-serif; font-weight:700; font-size:1rem; margin:1.6rem 0 .75rem; }
.evr-step:first-child { margin-top:0; }
.evr-step span { width:26px; height:26px; flex-shrink:0; border-radius:50%; background:var(--blue); color:#fff; font-size:.78rem; display:flex; align-items:center; justify-content:center; }
.evr-acc { display:flex; align-items:center; gap:.8rem; background:var(--surf); border:1px solid var(--border); border-radius:14px; padding:.8rem .95rem; }
.evr-acc .av { width:42px; height:42px; flex-shrink:0; border-radius:50%; background:var(--blue); color:#fff; font-weight:700; display:flex; align-items:center; justify-content:center; }
.evr-acc b { display:block; font-size:.92rem; }
.evr-acc span { display:block; font-size:.8rem; color:var(--muted); overflow-wrap:anywhere; }
.evr-acc i.fa-circle-check { margin-left:auto; color:#10b981; font-size:1.1rem; }
.evr-hint { font-size:.78rem; color:var(--muted); margin:.45rem 0 0; }
.evr-label { display:block; font-weight:600; font-size:.88rem; margin:1rem 0 .4rem; }
.evr-input { width:100%; min-height:50px; padding:.7rem .95rem; border:1px solid var(--border); border-radius:12px; font:inherit; font-size:1rem; background:#fff; }
.evr-input:focus { outline:none; border-color:var(--blue); box-shadow:0 0 0 3px rgba(20,116,188,.18); }
.evr-input[aria-invalid="true"] { border-color:#ef4444; }
.evr-err { font-size:.8rem; color:#b91c1c; margin:.35rem 0 0; }
.evr-opts { display:grid; gap:.6rem; }
.evr-opt { position:relative; display:block; cursor:pointer; }
.evr-opt input { position:absolute; opacity:0; pointer-events:none; }
.evr-box { display:flex; align-items:center; gap:.85rem; min-height:60px; padding:.75rem 1rem; border:1.5px solid var(--border); border-radius:14px; background:#fff; transition:border-color .15s, background .15s; }
.evr-radio { width:20px; height:20px; flex-shrink:0; border-radius:50%; border:2px solid #cbd5e1; display:flex; align-items:center; justify-content:center; }
.evr-box b { font-family:'Sora',sans-serif; font-size:1rem; }
.evr-box small { display:block; font-size:.78rem; color:var(--muted); }
.evr-box em { margin-left:auto; font-style:normal; font-size:.78rem; font-weight:700; color:#0f8a5f; white-space:nowrap; }
.evr-box em.low { color:#b45309; }
.evr-opt input:checked + .evr-box { border-color:var(--blue); background:var(--blue-xl); }
.evr-opt input:checked + .evr-box .evr-radio { border-color:var(--blue); }
.evr-opt input:checked + .evr-box .evr-radio::after { content:''; width:10px; height:10px; border-radius:50%; background:var(--blue); }
.evr-opt input:focus-visible + .evr-box { outline:2px solid var(--blue); outline-offset:2px; }
.evr-opt.full { cursor:not-allowed; }
.evr-opt.full .evr-box { background:#f3f4f6; opacity:.65; }
.evr-opt.full em { color:#b91c1c; }
.evr-free { display:flex; gap:.6rem; align-items:flex-start; margin-top:1.2rem; font-size:.82rem; color:var(--muted); line-height:1.5; }
.evr-free i { color:#10b981; margin-top:.2rem; }
@media (prefers-reduced-motion: reduce) { .evr-box { transition:none; } }
</style>
@endpush

@section('content')
@php $anySeat = $event->sessions->contains(fn ($s) => $s->seatsLeft() > 0); @endphp
<div class="evr">
    <a href="{{ route('events.show', $event) }}" class="evr-back"><i class="fas fa-arrow-left" aria-hidden="true"></i> Detail event</a>

    <div class="evr-sum">
        <div class="evr-thumb">@if($event->cover_url)<img src="{{ $event->cover_url }}" alt="">@endif</div>
        <div>
            <h1>{{ $event->title }}</h1>
            <p>{{ $event->dateLabel() }}<br>{{ $event->isOnline() ? 'Online' : $event->venue }}</p>
        </div>
    </div>

    @if($errors->has('event_session_id'))
        <div class="evf-alert" role="alert">{{ $errors->first('event_session_id') }}</div>
    @endif

    <form method="POST" action="{{ route('events.register', $event) }}" class="evd-card evr-form" x-data="{ busy: false }" @submit="busy = true">
        @csrf
        <h2 class="evr-step"><span>1</span> Data kamu</h2>
        <div class="evr-acc" data-tour="akun">
            <div class="av" aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</div>
            <div style="min-width:0;"><b>{{ $user->name }}</b><span>{{ $user->email }}</span></div>
            <i class="fas fa-circle-check" aria-hidden="true"></i>
        </div>
        <p class="evr-hint">Diambil dari akunmu. Salah nama? Ubah di <a href="{{ route('profile.edit') }}" style="color:var(--blue);">profil</a>.</p>

        <label class="evr-label" for="whatsapp">Nomor WhatsApp</label>
        <input class="evr-input" type="tel" id="whatsapp" data-tour="wa" name="whatsapp" inputmode="tel" autocomplete="tel" required
            placeholder="08xxxxxxxxxx" value="{{ old('whatsapp', $user->phone) }}" @if($errors->has('whatsapp')) aria-invalid="true" aria-describedby="wa-err" @endif>
        @error('whatsapp')<p class="evr-err" id="wa-err">{{ $message }}</p>@else<p class="evr-hint">Untuk info mendadak dari panitia, misalnya perubahan jadwal.</p>@enderror

        <label class="evr-label" for="age">Usia</label>
        <input class="evr-input" type="number" id="age" data-tour="usia" name="age" min="10" max="100" inputmode="numeric" required style="max-width:9rem;"
            value="{{ old('age') }}" @if($errors->has('age')) aria-invalid="true" aria-describedby="age-err" @endif>
        @error('age')<p class="evr-err" id="age-err">{{ $message }}</p>@enderror

        <h2 class="evr-step" id="sesi-label"><span>2</span> Pilih sesi</h2>
        <div class="evr-opts" role="radiogroup" aria-labelledby="sesi-label" data-tour="sesi">
            @foreach($event->sessions as $session)
                @php $left = $session->seatsLeft(); @endphp
                <label class="evr-opt {{ $left === 0 ? 'full' : '' }}">
                    <input type="radio" name="event_session_id" value="{{ $session->id }}" required @disabled($left === 0)
                        @checked(old('event_session_id') == $session->id || ($event->sessions->count() === 1 && $left > 0))>
                    <span class="evr-box">
                        <span class="evr-radio" aria-hidden="true"></span>
                        <span><b>{{ $session->timeLabel() }} {{ $event->timezone }}</b><small>{{ $session->label }}</small></span>
                        <em class="{{ $left > 0 && $left <= 5 ? 'low' : '' }}">{{ $left === 0 ? 'Penuh' : 'Sisa '.$left.' kursi' }}</em>
                    </span>
                </label>
            @endforeach
        </div>

        <button type="submit" class="evf-btn" data-tour="kirim" :disabled="busy" @disabled(! $anySeat) style="margin-top:1.5rem;">
            <span x-text="busy ? 'Menyimpan…' : @js($anySeat ? 'Daftar sekarang' : 'Semua sesi sudah penuh')">{{ $anySeat ? 'Daftar sekarang' : 'Semua sesi sudah penuh' }}</span>
        </button>
        <p class="evr-free"><i class="fas fa-circle-check" aria-hidden="true"></i> Gratis. Kalau nanti berhalangan, kamu bisa membatalkan pendaftaran dari halaman event supaya kursinya terbuka untuk orang lain.</p>
    </form>
</div>
@endsection

@push('scripts')
@include('partials.tour-assets')
<script>
KATour.page('event-form', [
  { element: '[data-tour="akun"]', popover: { title: 'Nama dan email sudah terisi', description: 'Diambil dari akunmu, jadi tidak perlu diketik lagi.', side: 'bottom' } },
  { element: '[data-tour="wa"]', popover: { title: 'Nomor WhatsApp', description: 'Isi nomor yang aktif, contohnya 081234567890. Panitia memakainya kalau ada info mendadak.', side: 'bottom' } },
  { element: '[data-tour="usia"]', popover: { title: 'Usia', description: 'Cukup tulis angkanya, misalnya 35.', side: 'bottom' } },
  { element: '[data-tour="sesi"]', popover: { title: 'Pilih satu sesi', description: 'Ketuk sesi yang masih ada kursinya. Sesi yang penuh tidak bisa dipilih.', side: 'top' } },
  { element: '[data-tour="kirim"]', popover: { title: 'Selesai, tekan Daftar', description: 'Setelah ditekan, tiketmu langsung muncul dan juga dikirim ke email.', side: 'top' } }
]);
</script>
@endpush
