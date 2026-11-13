@extends('layouts.app')

@section('seo')
    <title>{{ 'Pratinjau '.($kind === 'pre' ? 'Pre-test' : 'Post-test').' '.$section->title }} - Ray Academy</title>
    <meta name="robots" content="noindex">
@endsection

@section('content')
{{-- Admins check a test here instead of taking it, so no attempt is ever recorded for them. --}}
<x-classroom :course="$course" :progress="$progress" :current="$kind.'-'.$section->id">
    @include('tests._header')

    <div style="background:#EEF4FF;border:1px solid #bfdbfe;border-radius:12px;padding:.85rem 1rem;font-size:.875rem;color:#1d4ed8;margin-bottom:1rem;">
        <i class="fas fa-user-shield"></i> Mode admin: pratinjau soal beserta kuncinya. Peserta mengerjakan {{ $questions->count() }} soal ini satu per satu
        dalam {{ \App\Models\QuizAttempt::minutesFor($questions) }} menit{{ $kind === 'post' ? ', nilai lulus '.$course->post_test_pass_score : '' }}.
        Ubah soal di <a href="{{ route('admin.curriculum.show', $course) }}?tab=tes" style="font-weight:700;text-decoration:underline;">Kurikulum &amp; Tes</a>.
    </div>

    @foreach($questions as $question)
        <div style="background:#fff;border:1.5px solid var(--border);border-radius:14px;padding:1.25rem;margin-bottom:.9rem;">
            <div style="font-size:.75rem;font-weight:700;color:var(--muted);margin-bottom:.4rem;">
                Soal {{ $loop->iteration }} · {{ \App\Models\CourseQuestion::TYPES[$question->type] ?? $question->type }}
            </div>
            <div style="font-size:.95rem;font-weight:600;color:var(--ink);white-space:pre-line;line-height:1.6;">{{ $question->question }}</div>

            @if($question->type === 'essay')
                <div style="margin-top:.75rem;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:.75rem .9rem;font-size:.85rem;color:#166534;white-space:pre-line;">
                    <strong>Kunci jawaban / rubrik:</strong> {{ $question->answer_key ?: 'Belum diisi, essay akan dinilai manual oleh admin.' }}
                </div>
            @else
                @foreach($question->options as $option)
                    <div style="display:flex;gap:.6rem;align-items:flex-start;padding:.6rem .8rem;margin-top:.45rem;border-radius:10px;font-size:.88rem;border:1.5px solid {{ $option->is_correct ? '#86efac' : 'var(--border)' }};background:{{ $option->is_correct ? '#f0fdf4' : '#fff' }};color:var(--ink-2);">
                        <i class="fas {{ $option->is_correct ? 'fa-check-circle' : 'fa-circle' }}" style="margin-top:.2rem;color:{{ $option->is_correct ? '#16a34a' : '#cbd5e1' }};"></i>
                        <span>{{ $option->text }}</span>
                    </div>
                @endforeach
            @endif
        </div>
    @endforeach
</x-classroom>
@endsection
