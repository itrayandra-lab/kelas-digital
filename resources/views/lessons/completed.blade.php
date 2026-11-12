@extends('layouts.app')

@section('seo')
    <title>{{ 'Selamat! — ' . $course->title }} - Ray Academy</title>
    <meta name="robots" content="noindex">
@endsection

@section('content')

<div style="background:linear-gradient(135deg,#e8f4fd 0%,#f0fdf4 100%);min-height:80vh;display:flex;align-items:center;">
    <div style="max-width:640px;margin:0 auto;padding:3rem 1.5rem;text-align:center;">
        <div style="width:88px;height:88px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;">
            <i class="fas fa-trophy" style="font-size:2.4rem;color:#16a34a;"></i>
        </div>
        <h1 style="font-family:'Sora',sans-serif;font-size:clamp(1.6rem,4vw,2.2rem);font-weight:800;color:var(--ink);">Selamat, {{ auth()->user()->name }}!</h1>
        <p style="font-size:1rem;color:var(--muted);margin-top:.75rem;">Kamu telah menyelesaikan kelas <strong style="color:var(--ink);">{{ $course->title }}</strong>.</p>

        @if($progress->hasTests())
            <div style="background:#fff;border:1.5px solid var(--border);border-radius:14px;margin-top:2rem;overflow:hidden;text-align:left;">
                <div style="display:grid;grid-template-columns:1fr 90px 90px;padding:.7rem 1rem;background:var(--surf);font-size:.72rem;font-weight:700;color:var(--muted);text-transform:uppercase;">
                    <span>Bab</span><span style="text-align:center;">Pre-test</span><span style="text-align:center;">Post-test</span>
                </div>
                @foreach($progress->sections as $section)
                    @continue(! $progress->sectionHasTest($section, 'pre') && ! $progress->sectionHasTest($section, 'post'))
                    @php
                        $pre = $progress->bestScore($section, 'pre');
                        $post = $progress->bestScore($section, 'post');
                    @endphp
                    <div style="display:grid;grid-template-columns:1fr 90px 90px;padding:.75rem 1rem;border-top:1px solid var(--border);font-size:.9rem;align-items:center;">
                        <span style="color:var(--ink);">{{ $section->title }}</span>
                        <span style="text-align:center;font-family:'Sora',sans-serif;font-weight:700;color:var(--ink);">{{ $pre ?? '-' }}</span>
                        <span style="text-align:center;font-family:'Sora',sans-serif;font-weight:700;color:#16a34a;">{{ $post ?? '-' }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <div style="display:flex;gap:.75rem;justify-content:center;margin-top:2rem;flex-wrap:wrap;">
            <a href="{{ route('dashboard') }}" style="padding:.8rem 1.5rem;background:var(--blue);color:#fff;font-weight:700;border-radius:10px;text-decoration:none;">Kursus Saya</a>
            <a href="{{ route('course.index') }}" style="padding:.8rem 1.5rem;background:#fff;border:1.5px solid var(--border);color:var(--ink);font-weight:700;border-radius:10px;text-decoration:none;">Jelajahi Kelas Lain</a>
        </div>
    </div>
</div>

@endsection
