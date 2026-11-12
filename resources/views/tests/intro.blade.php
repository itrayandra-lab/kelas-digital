@extends('layouts.app')

@section('seo')
    <title>{{ ($kind === 'pre' ? 'Pre-test' : 'Post-test').' '.$section->title.' — '.$course->title }} - Ray Academy</title>
    <meta name="robots" content="noindex">
@endsection

@section('content')
<div style="background:var(--surf);min-height:100vh;">
    <div style="max-width:680px;margin:0 auto;padding:1.5rem;">
        @include('tests._header')

        @if(session('error'))
            <div style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:10px;padding:.75rem 1rem;font-size:.875rem;margin-bottom:1rem;">{{ session('error') }}</div>
        @endif

        @if($pending)
            <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:2.5rem 1.5rem;text-align:center;">
                <div style="width:72px;height:72px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                    <i class="fas fa-hourglass-half" style="font-size:1.8rem;color:#d97706;"></i>
                </div>
                <h2 style="font-family:'Sora',sans-serif;font-size:1.25rem;font-weight:800;color:var(--ink);">Hasilmu sedang diverifikasi</h2>
                <p style="font-size:.9rem;color:var(--muted);margin:.5rem auto 0;max-width:460px;">
                    Jawabanmu sudah terkirim. Jawaban essay sedang diperiksa, jadi nilainya belum bisa ditampilkan.
                    Cek lagi halaman ini atau <strong>Kursus Saya</strong> nanti.
                </p>
                <a href="{{ route('dashboard') }}" style="display:inline-block;margin-top:1.25rem;padding:.75rem 1.5rem;background:var(--blue);color:#fff;font-weight:700;border-radius:10px;text-decoration:none;">Ke Kursus Saya</a>
            </div>
        @else
            <div style="background:#fff;border:1.5px solid var(--border);border-radius:16px;padding:1.75rem;">
                <p style="font-size:.95rem;color:var(--ink-2);line-height:1.7;">
                    @if($kind === 'pre')
                        Pre-test mengukur pemahaman awalmu sebelum mempelajari bab ini. Nilainya tidak menentukan kelulusan.
                    @else
                        Post-test menguji pemahamanmu setelah mempelajari bab ini. Kamu perlu nilai minimal
                        <strong>{{ $course->post_test_pass_score }}</strong> untuk membuka bab berikutnya.
                    @endif
                </p>

                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:.75rem;margin:1.25rem 0;">
                    <div style="background:var(--surf);border-radius:12px;padding:.9rem;text-align:center;">
                        <div style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;color:var(--ink);">{{ $questions->count() }}</div>
                        <div style="font-size:.75rem;color:var(--muted);">soal</div>
                    </div>
                    <div style="background:var(--surf);border-radius:12px;padding:.9rem;text-align:center;">
                        <div style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;color:var(--ink);">{{ \App\Models\QuizAttempt::SECONDS_PER_QUESTION }}</div>
                        <div style="font-size:.75rem;color:var(--muted);">detik per soal</div>
                    </div>
                    <div style="background:var(--surf);border-radius:12px;padding:.9rem;text-align:center;">
                        <div style="font-family:'Sora',sans-serif;font-size:1.4rem;font-weight:800;color:var(--ink);">{{ $kind === 'pre' ? '-' : $course->post_test_pass_score }}</div>
                        <div style="font-size:.75rem;color:var(--muted);">nilai lulus</div>
                    </div>
                </div>

                <ul style="font-size:.85rem;color:var(--ink-2);line-height:1.9;padding-left:1.1rem;list-style:disc;">
                    <li>Soal muncul satu per satu. Kamu tidak bisa kembali ke soal sebelumnya.</li>
                    <li>Setiap soal punya waktu {{ \App\Models\QuizAttempt::SECONDS_PER_QUESTION }} detik. Kalau waktunya habis, soal itu dianggap kosong dan langsung lanjut.</li>
                    <li>Sudah yakin dengan jawabanmu? Klik <strong>Lanjut</strong> tanpa menunggu waktu habis.</li>
                    <li>Me-refresh atau menutup halaman tidak menghentikan waktu.</li>
                    @if($kind === 'post' && $questions->contains('type', 'essay'))
                        <li>Jawaban essay dinilai otomatis dengan mencocokkan maknanya ke kunci jawaban.</li>
                    @endif
                </ul>

                @if($kind === 'post' && $lastAttempt && ! $lastAttempt->isInProgress() && ! $lastAttempt->isPending())
                    <div style="background:#fff7ed;color:#9a3412;border:1px solid #fed7aa;border-radius:10px;padding:.75rem 1rem;font-size:.875rem;margin-top:1rem;">
                        Skor percobaan terakhirmu: <strong>{{ $lastAttempt->score }}</strong>. Kamu bisa mencoba lagi.
                    </div>
                @endif

                <form method="POST" action="{{ route('course.test.start', [$course->slug, $section, $kind]) }}" style="margin-top:1.5rem;"
                      onsubmit="const b = this.querySelector('button'); if (b.disabled) return false; b.disabled = true; b.textContent = 'Memulai...';">
                    @csrf
                    <button type="submit" style="width:100%;padding:.9rem;background:var(--blue);color:#fff;font-weight:700;font-size:.95rem;border:none;border-radius:12px;cursor:pointer;">
                        Mulai {{ $kind === 'pre' ? 'Pre-test' : 'Post-test' }}
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
