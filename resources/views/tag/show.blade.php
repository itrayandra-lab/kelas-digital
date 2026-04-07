@extends('layouts.app')

@section('seo')
    {!! seo($course) !!}
@endsection

@push('styles')
<style>
/* ── Course Hero ── */
.course-hero {
    background:var(--ink); padding:3.5rem 0 3rem;
    position:relative; overflow:hidden;
}
.course-hero::before {
    content:''; position:absolute; inset:0;
    background:radial-gradient(ellipse 60% 80% at 90% 50%, rgba(20,116,188,.3), transparent 60%);
    pointer-events:none;
}
.course-hero::after {
    content:''; position:absolute; inset:0;
    background-image:radial-gradient(rgba(255,255,255,.04) 1px, transparent 1px);
    background-size:28px 28px; pointer-events:none;
}
.course-hero-inner { position:relative; z-index:1; max-width:1280px; margin:0 auto; padding:0 1.5rem; }

.ch-cat { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:rgba(255,255,255,.6); background:rgba(255,255,255,.1); padding:.3rem .8rem; border-radius:999px; display:inline-block; margin-bottom:.9rem; }
.ch-title { font-family:'Sora',sans-serif; font-size:clamp(1.75rem,3.5vw,2.6rem); font-weight:800; color:#fff; line-height:1.2; letter-spacing:-.025em; margin-bottom:.9rem; max-width:750px; }
.ch-meta { display:flex; gap:1.5rem; flex-wrap:wrap; margin-top:.9rem; }
.ch-meta-item { display:flex; align-items:center; gap:.45rem; font-size:.83rem; color:rgba(255,255,255,.65); }
.ch-meta-item svg { width:15px; height:15px; flex-shrink:0; }
.ch-stars { display:flex; gap:1px; }
.ch-stars svg { width:14px; height:14px; fill:#fbbf24; }

/* ── Layout ── */
.course-layout { max-width:1280px; margin:0 auto; padding:0 1.5rem; }
.course-grid { display:grid; grid-template-columns:1fr 340px; gap:3rem; padding:3rem 0 5rem; align-items:start; }
@media(max-width:1024px) { .course-grid { grid-template-columns:1fr; } }

/* ── Video Player ── */
.video-wrap {
    border-radius:16px; overflow:hidden;
    box-shadow:0 16px 48px rgba(10,22,40,.15);
    margin-bottom:2.5rem; background:#000;
    aspect-ratio:16/9;
}
.video-wrap iframe { width:100%; height:100%; display:block; }

/* ── Curriculum ── */
.curriculum-title { font-family:'Sora',sans-serif; font-size:1.4rem; font-weight:800; color:var(--ink); margin-bottom:1.5rem; }
.module-block { border:1.5px solid var(--border); border-radius:14px; overflow:hidden; margin-bottom:.75rem; }
.module-header {
    background:var(--surf); padding:1rem 1.25rem;
    display:flex; align-items:center; justify-content:space-between; cursor:pointer;
    transition:background .18s; user-select:none;
}
.module-header:hover { background:#f1f5f9; }
.module-header h3 { font-family:'Sora',sans-serif; font-size:.9rem; font-weight:700; color:var(--ink); }
.module-header-meta { font-size:.72rem; color:var(--muted); margin-top:.15rem; }
.module-chevron { width:18px; height:18px; color:var(--muted); transition:transform .25s; flex-shrink:0; }
.module-chevron.open { transform:rotate(180deg); }
.module-lessons { border-top:1px solid var(--border); }
.lesson-item {
    display:flex; align-items:center; gap:.85rem; padding:.9rem 1.25rem;
    border-bottom:1px solid var(--border); cursor:default;
    transition:background .18s;
}
.lesson-item:last-child { border-bottom:none; }
.lesson-item.accessible { cursor:pointer; }
.lesson-item.accessible:hover { background:var(--blue-xl); }
.lesson-item.active { background:var(--blue-xl); }
.lesson-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.lesson-icon.play { background:var(--blue-xl); }
.lesson-icon.play svg { color:var(--blue); }
.lesson-icon.lock { background:var(--surf); }
.lesson-icon.lock svg { color:var(--muted); }
.lesson-title { font-size:.83rem; font-weight:600; color:var(--ink); flex:1; line-height:1.3; }
.lesson-preview-badge { font-size:.62rem; font-weight:700; padding:.2rem .55rem; border-radius:5px; background:var(--acc-bg); color:var(--accent); flex-shrink:0; }

/* ── Sidebar card ── */
.course-sidebar { display:flex; flex-direction:column; gap:1.5rem; }
.enroll-card { background:#fff; border:1.5px solid var(--border); border-radius:18px; overflow:hidden; box-shadow:0 8px 32px rgba(10,22,40,.08); }
@media(max-width:1024px) { .enroll-card { max-width:460px; } }
.enroll-card-thumb { width:100%; height:180px; object-fit:cover; display:block; background:linear-gradient(135deg,#dbeafe,#eff6ff); }
.enroll-card-body { padding:1.5rem; }
.enroll-price-orig { font-size:.85rem; color:var(--muted); text-decoration:line-through; }
.enroll-price { font-family:'Sora',sans-serif; font-size:2rem; font-weight:800; color:var(--ink); line-height:1.1; margin:.3rem 0 1.25rem; }
.enroll-price em { color:var(--blue); font-style:normal; }

.btn-enroll {
    display:block; width:100%; padding:1rem; text-align:center;
    font-family:'DM Sans',sans-serif; font-weight:800; font-size:1rem;
    border-radius:12px; text-decoration:none; transition:all .22s; cursor:pointer; border:none;
}
.btn-enroll-primary {
    background:var(--blue); color:#fff;
    box-shadow:0 6px 20px rgba(20,116,188,.35);
}
.btn-enroll-primary:hover { background:var(--blue-d); transform:translateY(-2px); box-shadow:0 10px 30px rgba(20,116,188,.45); }
.btn-enroll-success { background:#f0fdf4; color:#15803d; border:2px solid #bbf7d0; cursor:default; }
.btn-enroll-pending { background:#fefce8; color:#a16207; border:2px solid #fef08a; cursor:default; }
.btn-enroll-login { background:var(--blue); color:#fff; box-shadow:0 6px 20px rgba(20,116,188,.35); }
.btn-enroll-login:hover { background:var(--blue-d); }

.enroll-perks { margin-top:1.25rem; display:flex; flex-direction:column; gap:.6rem; }
.enroll-perk { display:flex; align-items:center; gap:.6rem; font-size:.8rem; color:var(--ink-2); }
.enroll-perk svg { width:15px; height:15px; color:var(--accent); flex-shrink:0; }

/* Mobile sticky enroll button */
.mobile-enroll-bar {
    display:none; position:fixed; bottom:0; left:0; right:0; z-index:100;
    background:#fff; border-top:1px solid var(--border);
    padding:1rem 1.5rem; box-shadow:0 -8px 32px rgba(10,22,40,.1);
}
@media(max-width:1024px) { .mobile-enroll-bar { display:block; } }
</style>
@endpush

@section('content')

{{-- Course Hero --}}
<section class="course-hero">
    <div class="course-hero-inner">

        <nav class="breadcrumb" style="margin-bottom:1.25rem;">
            <a href="{{ route('home') }}" style="color:rgba(255,255,255,.5);">Beranda</a>
            <svg style="width:14px;height:14px;color:rgba(255,255,255,.3);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('course.index') }}" style="color:rgba(255,255,255,.5);">Kursus</a>
            <svg style="width:14px;height:14px;color:rgba(255,255,255,.3);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span style="color:rgba(255,255,255,.8);">{{ Str::limit($course->title, 35) }}</span>
        </nav>

        @if($course->category)
            <span class="ch-cat">{{ $course->category->name }}</span>
        @endif

        <h1 class="ch-title">{{ $course->title }}</h1>

        <div class="ch-meta">
            <div class="ch-meta-item">
                <div class="ch-stars">
                    @for($i=1;$i<=5;$i++)
                        <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    @endfor
                </div>
                <span>(4.8)</span>
            </div>
            <div class="ch-meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                {{ $totalVideos }} Video
            </div>
            <div class="ch-meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                {{ number_format($enrolledStudentsCount) }} Siswa Terdaftar
            </div>
            <div class="ch-meta-item">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Instruktur: {{ is_string($course->instructor) ? $course->instructor : ($course->instructor->name ?? 'N/A') }}
            </div>
        </div>
    </div>
</section>

{{-- Course Content --}}
<div style="background:var(--surf);">
    <div class="course-layout">
        <div class="course-grid" x-data="{
            currentVideoId: '{{ $initialVideoId }}',
            switchVideo(id) { this.currentVideoId = id; window.scrollTo({top:0,behavior:'smooth'}); }
        }">

            {{-- Main: Video + Curriculum --}}
            <div>
                {{-- Video Player --}}
                <div class="video-wrap rv">
                    <iframe :src="`https://www.youtube.com/embed/${currentVideoId}`"
                            title="{{ $course->title }}" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                </div>

                {{-- Curriculum --}}
                <div class="rv rv-d2">
                    <h2 class="curriculum-title">Materi Pembelajaran</h2>

                    @forelse($lessonsByModule as $module => $lessons)
                    <div class="module-block" x-data="{ open: true }">
                        <div class="module-header" @click="open = !open">
                            <div>
                                <h3>{{ $module }}</h3>
                                <div class="module-header-meta">{{ count($lessons) }} pelajaran</div>
                            </div>
                            <svg class="module-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>

                        <div x-show="open" x-collapse class="module-lessons">
                            @foreach($lessons as $lesson)
                            <div class="lesson-item {{ ($userHasAccess || $lesson->is_preview) ? 'accessible' : '' }} {{ $lesson->youtube_video_id === $initialVideoId ? 'active' : '' }}"
                                 @if($userHasAccess || $lesson->is_preview)
                                    @click="switchVideo('{{ $lesson->youtube_video_id }}')"
                                 @endif>
                                <div class="lesson-icon {{ ($userHasAccess || $lesson->is_preview) ? 'play' : 'lock' }}">
                                    @if($userHasAccess || $lesson->is_preview)
                                        <svg style="width:14px;height:14px;" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    @else
                                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    @endif
                                </div>
                                <span class="lesson-title">{{ $lesson->title }}</span>
                                @if($lesson->is_preview && !$userHasAccess)
                                    <span class="lesson-preview-badge">Preview</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                        <p style="color:var(--muted);font-size:.9rem;">Materi pembelajaran akan segera ditambahkan.</p>
                    @endforelse
                </div>
            </div>

            {{-- Sidebar --}}
            <aside class="course-sidebar" style="position:sticky;top:88px;">
                <div class="enroll-card rv">
                    @if($course->thumbnail)
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="enroll-card-thumb">
                    @endif
                    <div class="enroll-card-body">
                        @if(($course->price ?? 0) > 0)
                            <div class="enroll-price-orig">Rp {{ number_format($course->price * 1.2, 0, ',', '.') }}</div>
                        @endif
                        <div class="enroll-price">
                            @if(($course->price ?? 0) == 0)
                                <em>Gratis</em>
                            @else
                                Rp <em>{{ number_format($course->price, 0, ',', '.') }}</em>
                            @endif
                        </div>

                        {{-- Enrollment action --}}
                        @auth
                            @if($userHasAccess)
                                <div class="btn-enroll btn-enroll-success">
                                    <svg style="width:16px;height:16px;display:inline;margin-right:.4rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Sudah Terdaftar — Mulai Belajar
                                </div>
                            @elseif($userEnrollment && $userEnrollment->payment_status === 'pending')
                                <div class="btn-enroll btn-enroll-pending">
                                    <svg style="width:16px;height:16px;display:inline;margin-right:.4rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Menunggu Verifikasi Pembayaran
                                </div>
                            @else
                                <form method="post" action="{{ route('course.enroll', $course->slug) }}">
                                    @csrf
                                    <button type="submit" class="btn-enroll btn-enroll-primary">
                                        Gabung Kelas Ini
                                        <svg style="width:16px;height:16px;display:inline;margin-left:.35rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn-enroll btn-enroll-login">Login untuk Bergabung</a>
                        @endauth

                        {{-- Perks --}}
                        <div class="enroll-perks">
                            @foreach(['Akses seumur hidup', 'Materi lengkap & terstruktur', 'Video HD berkualitas', 'Konsultasi dengan instruktur', 'Sertifikat resmi'] as $perk)
                            <div class="enroll-perk">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                {{ $perk }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

        </div>
    </div>
</div>

{{-- Mobile sticky enroll bar --}}
<div class="mobile-enroll-bar" x-data>
    @auth
        @if($userHasAccess)
            <div class="btn-enroll btn-enroll-success" style="max-width:480px;margin:0 auto;">✓ Sudah Terdaftar</div>
        @elseif(!($userEnrollment && $userEnrollment->payment_status === 'pending'))
            <form method="post" action="{{ route('course.enroll', $course->slug) }}" style="max-width:480px;margin:0 auto;">
                @csrf
                <button type="submit" class="btn-enroll btn-enroll-primary">
                    Gabung — @if(($course->price??0)==0)Gratis @else Rp {{ number_format($course->price,0,',','.') }} @endif
                </button>
            </form>
        @endif
    @else
        <div style="max-width:480px;margin:0 auto;">
            <a href="{{ route('login') }}" class="btn-enroll btn-enroll-login">Login untuk Bergabung</a>
        </div>
    @endauth
</div>

@endsection

@push('scripts')
<script>
const obs = new IntersectionObserver(e => { e.forEach(x => { if(x.isIntersecting){ x.target.classList.add('in'); obs.unobserve(x.target); }}); }, { threshold:0.05 });
document.querySelectorAll('.rv').forEach(el => obs.observe(el));
</script>
@endpush