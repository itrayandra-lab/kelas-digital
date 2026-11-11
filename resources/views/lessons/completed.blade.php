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

        @if($progress->hasTests)
            <div style="display:flex;gap:1rem;justify-content:center;margin-top:2rem;flex-wrap:wrap;">
                <div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:1rem 1.5rem;min-width:150px;">
                    <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase;font-weight:700;">Pre-test</div>
                    <div style="font-family:'Sora',sans-serif;font-size:1.8rem;font-weight:800;color:var(--ink);">{{ $preTest?->isPending() ? 'Diverifikasi' : ($preTest?->score ?? '-') }}</div>
                </div>
                <div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:1rem 1.5rem;min-width:150px;">
                    <div style="font-size:.75rem;color:var(--muted);text-transform:uppercase;font-weight:700;">Post-test</div>
                    <div style="font-family:'Sora',sans-serif;font-size:1.8rem;font-weight:800;color:#16a34a;">{{ $progress->bestPostTest?->score }}</div>
                </div>
            </div>
        @endif

        <div style="display:flex;gap:.75rem;justify-content:center;margin-top:2rem;flex-wrap:wrap;">
            <a href="{{ route('dashboard') }}" style="padding:.8rem 1.5rem;background:var(--blue);color:#fff;font-weight:700;border-radius:10px;text-decoration:none;">Kursus Saya</a>
            <a href="{{ route('course.index') }}" style="padding:.8rem 1.5rem;background:#fff;border:1.5px solid var(--border);color:var(--ink);font-weight:700;border-radius:10px;text-decoration:none;">Jelajahi Kelas Lain</a>
        </div>
    </div>
</div>

@endsection
