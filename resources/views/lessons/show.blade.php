@extends('layouts.app')

@section('title', $lesson->title . ' — ' . $course->title)

@section('content')

<div style="background:var(--surf);min-height:100vh;">
    <div style="max-width:1280px;margin:0 auto;padding:1.5rem;">

        {{-- Breadcrumb --}}
        <nav style="font-size:.8rem;color:var(--muted);margin-bottom:1.5rem;display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;">
            <a href="{{ route('home') }}" style="color:var(--muted);text-decoration:none;">Beranda</a>
            <span>›</span>
            <a href="{{ route('course.index') }}" style="color:var(--muted);text-decoration:none;">Kursus</a>
            <span>›</span>
            <a href="{{ route('course.show', $course->slug) }}" style="color:var(--muted);text-decoration:none;">{{ Str::limit($course->title, 40) }}</a>
            <span>›</span>
            <span style="color:var(--ink);font-weight:500;">{{ Str::limit($lesson->title, 40) }}</span>
        </nav>

        <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start;">

            {{-- Main: Video Player --}}
            <div>
                <div style="border-radius:12px;overflow:hidden;box-shadow:0 8px 32px rgba(10,22,40,.12);background:#000;">
                    <div style="aspect-ratio:16/9;">
                        <iframe src="https://www.youtube.com/embed/{{ $lesson->youtube_video_id }}"
                                width="100%" height="100%"
                                title="{{ $lesson->title }}" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen style="width:100%;height:100%;display:block;"></iframe>
                    </div>
                </div>

                <div style="margin-top:1.25rem;background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:1.25rem 1.5rem;">
                    <h1 style="font-family:'Sora',sans-serif;font-size:1.15rem;font-weight:700;color:var(--ink);margin-bottom:.35rem;">{{ $lesson->title }}</h1>
                    <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;font-size:.8rem;color:var(--muted);">
                        <span><i class="fas fa-folder mr-1"></i> {{ $lesson->module }}</span>
                        @if($lesson->duration)
                            <span><i class="fas fa-clock mr-1"></i> {{ $lesson->duration }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar: Daftar Lesson --}}
            <div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;overflow:hidden;position:sticky;top:88px;">
                <div style="padding:.85rem 1.1rem;background:var(--surf);border-bottom:1px solid var(--border);">
                    <h3 style="font-family:'Sora',sans-serif;font-size:.85rem;font-weight:700;color:var(--ink);margin:0;">
                        <i class="fas fa-list mr-1"></i> Daftar Video
                        <span style="font-weight:400;color:var(--muted);font-size:.75rem;">({{ $lessons->count() }})</span>
                    </h3>
                </div>
                <div style="max-height:60vh;overflow-y:auto;">
                    @foreach($lessons as $item)
                        <a href="{{ route('course.lesson', [$course->slug, $item]) }}"
                           style="display:flex;align-items:center;gap:.65rem;padding:.75rem 1.1rem;text-decoration:none;border-bottom:1px solid var(--border);transition:background .12s;{{ $item->id === $lesson->id ? 'background:var(--blue-xl);' : '' }}"
                           onmouseover="this.style.background='{{ $item->id === $lesson->id ? 'var(--blue-xl)' : 'var(--surf)' }}'"
                           onmouseout="this.style.background='{{ $item->id === $lesson->id ? 'var(--blue-xl)' : '' }}'">
                            <div style="width:30px;height:30px;border-radius:50%;background:{{ $item->id === $lesson->id ? 'var(--blue)' : 'var(--blue-xl)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="fas {{ $item->id === $lesson->id ? 'fa-play' : 'fa-play' }}" style="color:{{ $item->id === $lesson->id ? '#fff' : 'var(--blue)' }};font-size:.65rem;margin-left:1px;"></i>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:.82rem;font-weight:{{ $item->id === $lesson->id ? '700' : '500' }};color:{{ $item->id === $lesson->id ? 'var(--blue)' : 'var(--ink)' }};line-height:1.3;">{{ $item->title }}</div>
                                @if($item->duration)
                                    <div style="font-size:.7rem;color:var(--muted);margin-top:2px;">{{ $item->duration }}</div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
