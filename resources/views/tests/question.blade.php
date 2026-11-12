@extends('layouts.app')

@section('seo')
    <title>{{ 'Soal '.($attempt->current_index + 1).' — '.($kind === 'pre' ? 'Pre-test' : 'Post-test').' '.$section->title }} - Ray Academy</title>
    <meta name="robots" content="noindex">
@endsection

@push('styles')
<style>
.q-opt { display:flex; align-items:center; gap:.7rem; padding:.8rem 1rem; border:1.5px solid var(--border); border-radius:12px; margin-top:.6rem; cursor:pointer; font-size:.92rem; color:var(--ink-2); background:#fff; }
.q-opt:hover { background:var(--surf); }
.q-opt:has(input:checked) { border-color:var(--blue); background:var(--blue-xl); }
.q-timer { width:64px; height:64px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-family:'Sora',sans-serif; font-weight:800; font-size:1.25rem; color:var(--ink); background:conic-gradient(var(--blue) calc(var(--p) * 1%), var(--border) 0); position:relative; flex-shrink:0; }
.q-timer::before { content:''; position:absolute; inset:6px; background:#fff; border-radius:50%; }
.q-timer span { position:relative; }
.q-timer.low { background:conic-gradient(#dc2626 calc(var(--p) * 1%), var(--border) 0); color:#dc2626; }
</style>
@endpush

@section('content')
@php
    $limit = \App\Models\QuizAttempt::SECONDS_PER_QUESTION;
    $left = $attempt->secondsLeft();
    $number = $attempt->current_index + 1;
    $isLast = $number >= $total;
@endphp
<div style="background:var(--surf);min-height:100vh;">
    <div style="max-width:680px;margin:0 auto;padding:1.5rem;">
        @include('tests._header')

        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
            <div style="flex:1;">
                <div style="display:flex;justify-content:space-between;font-size:.8rem;color:var(--muted);margin-bottom:.35rem;">
                    <span>Soal <strong style="color:var(--ink);">{{ $number }}</strong> dari {{ $total }}</span>
                    @if($question->type === 'multiple')<span style="font-weight:700;color:var(--blue);">Pilih semua yang benar</span>@endif
                </div>
                <div style="height:6px;background:var(--border);border-radius:999px;overflow:hidden;">
                    <div style="height:100%;width:{{ ($number - 1) / $total * 100 }}%;background:var(--blue);"></div>
                </div>
            </div>
            <div class="q-timer" id="q-timer" style="--p: {{ $left / $limit * 100 }};" role="timer" aria-live="off" aria-label="Sisa waktu">
                <span id="q-seconds">{{ $left }}</span>
            </div>
        </div>

        <form id="q-form" method="POST" action="{{ route('course.test.answer', [$course->slug, $section, $kind]) }}"
              style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:1.5rem;">
            @csrf
            <input type="hidden" name="index" value="{{ $attempt->current_index }}">

            <div style="font-size:1.05rem;font-weight:600;color:var(--ink);white-space:pre-line;line-height:1.6;">{{ $question->question }}</div>

            @if($question->type === 'essay')
                <textarea name="answer" rows="5" maxlength="5000" autofocus placeholder="Tulis jawabanmu di sini..."
                    style="width:100%;margin-top:1rem;padding:.8rem 1rem;border:1.5px solid var(--border);border-radius:12px;font-size:.92rem;"></textarea>
            @else
                @foreach($question->options as $opt)
                    <label class="q-opt">
                        <input type="{{ $question->type === 'multiple' ? 'checkbox' : 'radio' }}" name="{{ $question->type === 'multiple' ? 'answer[]' : 'answer' }}" value="{{ $opt->id }}">
                        <span>{{ $opt->text }}</span>
                    </label>
                @endforeach
            @endif

            <button type="submit" id="q-next"
                style="width:100%;margin-top:1.25rem;padding:.9rem;background:var(--blue);color:#fff;font-weight:700;font-size:.95rem;border:none;border-radius:12px;cursor:pointer;">
                {{ $isLast ? 'Selesai & Kirim' : 'Lanjut' }} <i class="fas fa-arrow-right ml-1"></i>
            </button>
            <p style="font-size:.75rem;color:var(--muted);text-align:center;margin-top:.6rem;">Kamu tidak bisa kembali ke soal ini setelah lanjut.</p>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// The countdown is cosmetic: the server decides whether the answer arrived in time.
// When it reaches zero the current selection is submitted (an empty one counts as unanswered).
(function () {
    const form = document.getElementById('q-form');
    const btn = document.getElementById('q-next');
    const timer = document.getElementById('q-timer');
    const label = document.getElementById('q-seconds');
    const limit = {{ $limit }};
    const deadline = Date.now() + {{ $left }} * 1000;
    let sent = false;

    function send() {
        if (sent) return;
        sent = true;
        btn.disabled = true;
        btn.style.opacity = '.6';
        btn.innerHTML = {{ Js::from($isLast && $kind === 'post' ? 'Mengirim dan menilai jawaban...' : 'Mengirim...') }};
        form.submit();
    }

    form.addEventListener('submit', function (e) { e.preventDefault(); send(); });

    const tick = setInterval(function () {
        const left = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));
        label.textContent = left;
        timer.style.setProperty('--p', left / limit * 100);
        timer.classList.toggle('low', left <= 10);
        if (left === 0) { clearInterval(tick); send(); }
    }, 250);
})();
</script>
@endpush
