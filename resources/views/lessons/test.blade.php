@extends('layouts.app')

@section('seo')
    <title>{{ ($kind === 'pre' ? 'Pre-test' : 'Post-test') . ' — ' . $course->title }} - Ray Academy</title>
    <meta name="robots" content="noindex">
@endsection

@section('content')

<style>
.q-card { background:#fff; border:1.5px solid var(--border); border-radius:12px; padding:1.25rem 1.5rem; margin-bottom:1rem; }
.q-opt { display:flex; align-items:center; gap:.7rem; padding:.7rem .9rem; border:1.5px solid var(--border); border-radius:10px; margin-top:.5rem; cursor:pointer; font-size:.9rem; color:var(--ink-2); }
.q-opt:hover { background:var(--surf); }
.q-opt:has(input:checked) { border-color:var(--blue); background:var(--blue-xl); }
</style>

<div style="background:var(--surf);min-height:100vh;">
    <div style="max-width:780px;margin:0 auto;padding:1.5rem;">
        <a href="{{ route('course.show', $course->slug) }}" style="font-size:.8rem;color:var(--muted);text-decoration:none;"><i class="fas fa-arrow-left mr-1"></i> {{ Str::limit($course->title, 50) }}</a>

        <div style="margin:1rem 0 1.5rem;">
            <h1 style="font-family:'Sora',sans-serif;font-size:1.5rem;font-weight:800;color:var(--ink);">{{ $kind === 'pre' ? 'Pre-test' : 'Post-test' }}</h1>
            <p style="font-size:.9rem;color:var(--muted);margin-top:.35rem;">
                @if($kind === 'pre')
                    Kerjakan dulu untuk mengukur pemahaman awalmu. Nilainya tidak menentukan kelulusan, setelah ini materi langsung terbuka.
                @else
                    Soalnya sama dengan pre-test. Nilai minimal untuk lulus: <strong>{{ $course->post_test_pass_score }}</strong>. Kamu boleh mengulang sampai lulus.
                @endif
            </p>
        </div>

        @if(session('error'))
            <div style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:10px;padding:.75rem 1rem;font-size:.875rem;margin-bottom:1rem;">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:10px;padding:.75rem 1rem;font-size:.875rem;margin-bottom:1rem;">Semua soal wajib dijawab.</div>
        @endif
        @if($kind === 'post' && $progress->postTestPending)
            <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:2.5rem 1.5rem;text-align:center;">
                <div style="width:72px;height:72px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="fas fa-hourglass-half" style="font-size:1.8rem;color:#d97706;"></i>
                </div>
                <h2 style="font-family:'Sora',sans-serif;font-size:1.25rem;font-weight:800;color:var(--ink);">Hasilmu sedang diverifikasi</h2>
                <p style="font-size:.9rem;color:var(--muted);margin-top:.5rem;max-width:460px;margin-left:auto;margin-right:auto;">
                    Jawabanmu sudah terkirim. Karena ada soal essay, nilainya diperiksa dulu oleh admin.
                    Cek lagi halaman ini atau <strong>Kursus Saya</strong> nanti.
                </p>
                <a href="{{ route('dashboard') }}" style="display:inline-block;margin-top:1.25rem;padding:.75rem 1.5rem;background:var(--blue);color:#fff;font-weight:700;border-radius:10px;text-decoration:none;">Ke Kursus Saya</a>
            </div>
        @else
        @if($kind === 'post' && $lastAttempt && ! $lastAttempt->isPending())
            <div style="background:#fff7ed;color:#9a3412;border:1px solid #fed7aa;border-radius:10px;padding:.75rem 1rem;font-size:.875rem;margin-bottom:1rem;">Skor percobaan terakhirmu: <strong>{{ $lastAttempt->score }}</strong></div>
        @endif

        <form id="test-form" method="POST" action="{{ route('course.test.submit', [$course->slug, $kind]) }}">
            @csrf
            @foreach($questions as $q)
                @php $old = old("answers.{$q->id}"); @endphp
                <div class="q-card">
                    <div style="font-size:.75rem;color:var(--muted);margin-bottom:.35rem;">
                        Soal {{ $loop->iteration }} dari {{ $questions->count() }}
                        @if($q->type === 'multiple') · <strong>pilih semua jawaban yang benar</strong>@endif
                    </div>
                    <div style="font-weight:600;color:var(--ink);white-space:pre-line;">{{ $q->question }}</div>

                    @if($q->type === 'essay')
                        <textarea name="answers[{{ $q->id }}]" rows="4" required
                            style="width:100%;margin-top:.75rem;padding:.7rem .9rem;border:1.5px solid var(--border);border-radius:10px;font-size:.9rem;">{{ $old }}</textarea>
                    @else
                        @foreach($q->options as $opt)
                            <label class="q-opt">
                                @if($q->type === 'multiple')
                                    <input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $opt->id }}" @checked(in_array($opt->id, (array) $old))>
                                @else
                                    <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt->id }}" required @checked($old == $opt->id)>
                                @endif
                                <span>{{ $opt->text }}</span>
                            </label>
                        @endforeach
                    @endif
                </div>
            @endforeach

            <p style="font-size:.8rem;color:var(--muted);text-align:center;margin-bottom:.6rem;"><i class="fas fa-save"></i> Jawabanmu tersimpan otomatis, aman kalau halaman di-refresh.</p>
            <button type="submit" id="test-submit" data-tour="test-submit"
                style="width:100%;padding:.9rem;background:var(--blue);color:#fff;font-weight:700;font-size:.95rem;border:none;border-radius:12px;cursor:pointer;">
                Kirim Jawaban
            </button>
        </form>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
// Keeps unsent answers in this browser so a refresh doesn't wipe them, and lets the form submit only once.
(function () {
    const form = document.getElementById('test-form');
    if (!form) return;

    const key = 'ka_test_draft_{{ auth()->id() }}_{{ $course->id }}_{{ $kind }}';
    const hasServerErrors = {{ $errors->any() ? 'true' : 'false' }};

    function save() {
        const data = {};
        new FormData(form).forEach((value, name) => {
            if (name === '_token') return;
            (data[name] = data[name] || []).push(value);
        });
        try { localStorage.setItem(key, JSON.stringify(data)); } catch (e) {}
    }

    function restore() {
        let data;
        try { data = JSON.parse(localStorage.getItem(key) || 'null'); } catch (e) { data = null; }
        if (!data || hasServerErrors) return;
        form.querySelectorAll('input[name^="answers"], textarea[name^="answers"]').forEach(el => {
            const values = data[el.name] || [];
            if (el.type === 'radio' || el.type === 'checkbox') el.checked = values.includes(el.value);
            else el.value = values[0] || '';
        });
    }

    restore();
    form.addEventListener('change', save);
    form.addEventListener('input', save);

    form.addEventListener('submit', function (e) {
        const btn = document.getElementById('test-submit');
        if (btn.disabled || !confirm('Kirim jawaban sekarang? Jawaban tidak bisa diubah setelah dikirim.')) {
            e.preventDefault();
            return;
        }
        btn.disabled = true;
        btn.style.opacity = '.6';
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
        try { localStorage.removeItem(key); } catch (err) {}
    });
})();
</script>
@include('partials.tour-assets')
<script>
// Opened only from the "Panduan" button: an auto-opened tour would cover the questions.
KATour.page('course-test', [
  { element: '.q-card', popover: { title: 'Jawab semua soal', description: 'Pilihan ganda: pilih satu. Soal bertanda "pilih semua jawaban yang benar": centang semua yang benar. Essay: tulis jawabanmu.', side: 'bottom' } },
  { element: '[data-tour="test-submit"]', popover:{ title: 'Kirim jawaban', description: {{ Js::from($kind === 'pre' ? 'Pre-test tidak menentukan kelulusan. Setelah dikirim, materi langsung terbuka.' : 'Post-test harus mencapai nilai minimal. Kalau belum lulus, kamu bisa mengulang.') }}, side: 'top' } }
], { auto: false });
</script>
@endpush
