@extends('layouts.app')

@section('seo')
    <title>{{ 'Pengenalan Kelas — '.$course->title }} - Ray Academy</title>
    <meta name="robots" content="noindex">
@endsection

@push('styles')
<style>
.ci-card { background:#fff; border:1px solid var(--border); border-radius:14px; padding:1.5rem; margin-bottom:1.25rem; }
.ci-h2 { font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:800; color:var(--ink); margin-bottom:.9rem; }
.ci-learn li { display:flex; gap:.6rem; font-size:.92rem; color:var(--ink-2); padding:.3rem 0; line-height:1.5; }
.ci-learn i { color:#10b981; margin-top:.25rem; }
.ci-steps { counter-reset:step; }
.ci-step { display:flex; gap:1rem; padding:.75rem 0; border-top:1px dashed var(--border); }
.ci-step:first-child { border-top:0; padding-top:0; }
.ci-step-n { width:34px; height:34px; border-radius:50%; background:var(--blue); color:#fff; font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-family:'Sora',sans-serif; }
.ci-step strong { display:block; color:var(--ink); font-size:.95rem; }
.ci-step p { color:var(--muted); font-size:.85rem; margin-top:.15rem; line-height:1.6; }
.ci-bab { display:flex; gap:1rem; align-items:center; padding:.8rem 0; border-top:1px solid var(--border); font-size:.9rem; }
.ci-bab:first-of-type { border-top:0; }
.ci-bab span.n { width:30px; height:30px; border-radius:8px; background:var(--blue-xl); color:var(--blue); font-weight:800; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.ci-chip { font-size:.72rem; padding:.2rem .55rem; border-radius:999px; background:var(--surf); color:var(--muted); white-space:nowrap; }
</style>
@endpush

@section('content')
@php
    $learnPoints = collect(preg_split('/\r?\n/', (string) $course->benefits))
        ->map(fn ($l) => trim(preg_replace('/^[\s•\-\*✅✔️☑️]+/u', '', $l)))->filter()->values();
@endphp

<x-classroom :course="$course" :progress="$progress" current="intro">
    <div style="font-size:.75rem;font-weight:700;color:var(--blue);text-transform:uppercase;letter-spacing:.05em;">Persiapan Belajar</div>
    <h1 style="font-family:'Sora',sans-serif;font-size:clamp(1.4rem,3vw,1.9rem);font-weight:800;color:var(--ink);margin:.3rem 0 1.25rem;">Pengenalan Kelas</h1>

    <div class="ci-card">
        <h2 class="ci-h2">Tentang kelas ini</h2>
        <div style="font-size:.93rem;color:var(--ink-2);line-height:1.8;">{!! nl2br(e($course->description)) !!}</div>
        <div style="display:flex;gap:1.25rem;flex-wrap:wrap;margin-top:1rem;font-size:.85rem;color:var(--muted);">
            <span><i class="fas fa-chalkboard-teacher" style="color:var(--blue);"></i> {{ $course->instructor }}</span>
            <span><i class="fas fa-signal" style="color:var(--blue);"></i> {{ $course->level }}</span>
            <span><i class="fas fa-layer-group" style="color:var(--blue);"></i> {{ $progress->sections->count() }} bab · {{ $progress->lessons->count() }} materi</span>
        </div>
    </div>

    @if($learnPoints->isNotEmpty())
        <div class="ci-card">
            <h2 class="ci-h2">Yang akan kamu pelajari</h2>
            <ul class="ci-learn">
                @foreach($learnPoints as $point)<li><i class="fas fa-check"></i><span>{{ $point }}</span></li>@endforeach
            </ul>
        </div>
    @endif

    <div class="ci-card">
        <h2 class="ci-h2">Materi yang akan dibahas</h2>
        @foreach($progress->sections as $section)
            <div class="ci-bab">
                <span class="n">{{ $loop->iteration }}</span>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;color:var(--ink);">{{ $section->title }}</div>
                    <div style="font-size:.8rem;color:var(--muted);margin-top:.15rem;">{{ $section->lessons->pluck('title')->implode(' · ') }}</div>
                </div>
                <div style="display:flex;gap:.35rem;flex-wrap:wrap;justify-content:flex-end;">
                    @if($section->pre_count)<span class="ci-chip">Pre-test</span>@endif
                    <span class="ci-chip">{{ $section->lessons->count() }} materi</span>
                    @if($section->post_count)<span class="ci-chip">Post-test</span>@endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="ci-card">
        <h2 class="ci-h2">Mekanisme belajar</h2>
        <div class="ci-steps">
            <div class="ci-step"><span class="ci-step-n">1</span><div><strong>Kerjakan pre-test bab</strong><p>Setiap bab dibuka dengan pre-test untuk mengukur pemahaman awalmu. Nilainya tidak menentukan kelulusan.</p></div></div>
            <div class="ci-step"><span class="ci-step-n">2</span><div><strong>Pelajari materi secara berurutan</strong><p>Tonton video sampai minimal 90% atau baca materi sampai bawah, lalu klik <em>Tandai Selesai</em>. Materi berikutnya terbuka setelah materi sebelumnya selesai.</p></div></div>
            <div class="ci-step"><span class="ci-step-n">3</span><div><strong>Lulus post-test bab</strong><p>Kerjakan post-test di akhir bab dengan nilai minimal <strong>{{ $course->post_test_pass_score }}</strong>. Kalau belum lulus, kamu bisa mengulang.</p></div></div>
            <div class="ci-step"><span class="ci-step-n">4</span><div><strong>Lanjut ke bab berikutnya</strong><p>Bab berikutnya terbuka setelah post-test lulus. Ulangi sampai semua bab selesai.</p></div></div>
            <div class="ci-step"><span class="ci-step-n">5</span><div><strong>Selesaikan kelas</strong><p>Setelah semua bab tuntas, kamu akan melihat ringkasan nilai pre-test dan post-test di setiap bab.</p></div></div>
        </div>
    </div>

    <div class="ci-card">
        <h2 class="ci-h2">Aturan pre-test dan post-test</h2>
        <ul style="font-size:.88rem;color:var(--ink-2);line-height:1.9;padding-left:1.1rem;list-style:disc;">
            <li>Soal muncul satu per satu, dan kamu tidak bisa kembali ke soal sebelumnya.</li>
            <li>Setiap soal pilihan punya waktu {{ \App\Models\QuizAttempt::SECONDS_PER_CHOICE }} detik dan setiap soal essay {{ \App\Models\QuizAttempt::SECONDS_PER_ESSAY / 60 }} menit. Kalau waktunya habis, soal dianggap kosong dan langsung lanjut.</li>
            <li>Me-refresh atau menutup halaman tidak menghentikan waktu.</li>
            <li>Pre-test berisi soal pilihan dan benar/salah. Post-test bisa berisi essay yang dinilai dari ketepatan konsep dan kualitas argumenmu.</li>
        </ul>
    </div>

    <a href="{{ route('course.learn', $course->slug) }}" style="display:flex;align-items:center;justify-content:center;gap:.5rem;padding:.95rem;background:var(--blue);color:#fff;font-weight:700;border-radius:12px;text-decoration:none;">
        {{ $progress->percent() > 0 ? 'Lanjut Belajar' : 'Mulai Belajar' }} <i class="fas fa-arrow-right"></i>
    </a>
</x-classroom>
@endsection
