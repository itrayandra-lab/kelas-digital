@extends('layouts.app')

@section('seo')
    {!! seo($course) !!}
@endsection

@push('styles')
<style>
/* ── PAID COURSE HERO ── */
.course-hero {
    background: linear-gradient(135deg, var(--ink) 0%, #1a2942 100%);
    position: relative;
    overflow: hidden;
}
.course-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse 70% 60% at 30% 50%, rgba(20,116,188,.12) 0%, transparent 65%);
    pointer-events: none;
}
.course-hero-inner {
    position: relative;
    z-index: 1;
    max-width: 1280px;
    margin: 0 auto;
    padding: 2.5rem 1.5rem;
}
/* Hero content dibatasi selebar kolom kiri body agar sejajar */
.course-hero-content {
    max-width: calc(100% - 380px - 3rem);
}
@media (max-width: 1024px) {
    .course-hero-content { max-width: 100%; }
}
.course-hero-breadcrumb {
    display: flex;
    align-items: center;
    gap: .4rem;
    font-size: .8rem;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
}
.course-hero-breadcrumb a { color: rgba(255,255,255,.6); text-decoration: none; transition: color .18s; }
.course-hero-breadcrumb a:hover { color: #fff; }
.course-hero-breadcrumb span { color: rgba(255,255,255,.4); }
.course-hero-breadcrumb .current { color: rgba(255,255,255,.85); }
.course-hero h1 {
    font-family: 'Sora', sans-serif;
    font-size: clamp(1.5rem, 3vw, 2.25rem);
    font-weight: 800;
    color: #fff;
    line-height: 1.25;
    margin-bottom: .75rem;
}
.course-hero-subtitle {
    font-size: 1rem;
    color: rgba(255,255,255,.75);
    margin-bottom: 1rem;
    line-height: 1.6;
}
.course-hero-badges { display: flex; gap: .5rem; flex-wrap: wrap; margin-bottom: 1rem; }
.course-badge {
    font-size: .7rem;
    font-weight: 700;
    padding: .25rem .7rem;
    border-radius: 5px;
    text-transform: uppercase;
    letter-spacing: .05em;
}
.badge-level-beginner { background: #dcfce7; color: #15803d; }
.badge-level-intermediate { background: #fef9c3; color: #854d0e; }
.badge-level-advanced { background: #fee2e2; color: #991b1b; }
.badge-premium { background: var(--blue); color: #fff; }
.course-hero-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1.25rem;
    font-size: .82rem;
    color: rgba(255,255,255,.65);
    margin-bottom: 1.25rem;
}
.course-hero-meta-item { display: flex; align-items: center; gap: .4rem; }
.course-hero-meta-item i { font-size: .8rem; }
.course-hero-instructor {
    font-size: .875rem;
    color: rgba(255,255,255,.75);
}
.course-hero-instructor a { color: #93c5fd; text-decoration: none; }
.course-hero-instructor a:hover { text-decoration: underline; }

/* ── SIDEBAR CARD (hero) ── */
.course-sidebar-card {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 24px 64px rgba(0,0,0,.35);
}
.course-sidebar-preview {
    position: relative;
    aspect-ratio: 16/9;
    background: #000;
    cursor: pointer;
}
.course-sidebar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: .75;
    transition: opacity .2s;
}
.course-sidebar-preview:hover img { opacity: .6; }
.course-sidebar-play {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: .5rem;
    color: #fff;
}
.course-sidebar-play .play-btn {
    width: 56px;
    height: 56px;
    background: rgba(255,255,255,.2);
    backdrop-filter: blur(8px);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid rgba(255,255,255,.5);
    transition: all .2s;
}
.course-sidebar-preview:hover .play-btn {
    background: rgba(255,255,255,.35);
    transform: scale(1.08);
}
.course-sidebar-play span { font-size: .8rem; font-weight: 600; }
.course-sidebar-body { padding: 1.5rem; }
.course-sidebar-price {
    font-family: 'Sora', sans-serif;
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--ink);
    margin-bottom: .75rem;
}
.course-sidebar-price .original {
    font-size: .85rem;
    font-weight: 400;
    color: var(--muted);
    text-decoration: line-through;
    margin-left: .4rem;
}
.btn-enroll {
    display: block;
    width: 100%;
    padding: .9rem 1.5rem;
    text-align: center;
    font-family: 'Sora', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    color: #fff;
    background: var(--blue);
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all .2s;
    text-decoration: none;
    margin-bottom: .75rem;
}
.btn-enroll:hover { background: var(--blue-d); transform: translateY(-1px); box-shadow: 0 8px 24px rgba(20,116,188,.35); }
.btn-enroll-success {
    background: #10b981;
}
.btn-enroll-success:hover { background: #059669; box-shadow: 0 8px 24px rgba(16,185,129,.35); }
.course-sidebar-includes {
    margin-top: 1.25rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--border);
}
.course-sidebar-includes h4 {
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--muted);
    margin-bottom: .75rem;
}
.course-sidebar-includes ul { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: .55rem; }
.course-sidebar-includes li { display: flex; align-items: center; gap: .6rem; font-size: .82rem; color: var(--ink-2); }
.course-sidebar-includes li i { width: 16px; color: var(--blue); font-size: .8rem; flex-shrink: 0; }

/* ── MAIN CONTENT AREA ── */
.course-body {
    max-width: 1280px;
    margin: 0 auto;
    padding: 2.5rem 1.5rem 5rem;
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 3rem;
    position: relative;
}
.course-body > div:first-child {
    min-width: 0;
}
.course-body-sidebar {
    position: sticky;
    top: 96px;
    align-self: start;
    height: fit-content;
}
@media (max-width: 1024px) {
    .course-body { grid-template-columns: 1fr; }
    .course-body-sidebar { display: none; position: static; }
}

/* ── SECTION BLOCKS ── */
.course-section {
    margin-bottom: 2.5rem;
}
.course-section-title {
    font-family: 'Sora', sans-serif;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: 1rem;
    padding-bottom: .6rem;
    border-bottom: 2px solid var(--border);
}

/* ── WHAT YOU'LL LEARN ── */
.learn-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .6rem .75rem;
    background: var(--surf);
    border: 1.5px solid var(--border);
    border-radius: 12px;
    padding: 1.25rem 1.5rem;
}
@media (max-width: 640px) { .learn-grid { grid-template-columns: 1fr; } }
.learn-item {
    display: flex;
    align-items: flex-start;
    gap: .6rem;
    font-size: .875rem;
    color: var(--ink-2);
    line-height: 1.5;
}
.learn-item i { color: var(--blue); margin-top: 2px; flex-shrink: 0; font-size: .8rem; }

/* ── CURRICULUM ── */
.curriculum-summary {
    font-size: .82rem;
    color: var(--muted);
    margin-bottom: 1rem;
}
.curriculum-summary strong { color: var(--ink); }
.module-block {
    border: 1.5px solid var(--border);
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: .6rem;
}
.module-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: .9rem 1.1rem;
    background: var(--surf);
    cursor: pointer;
    user-select: none;
    transition: background .15s;
}
.module-header:hover { background: #eef2f8; }
.module-header-left { display: flex; align-items: center; gap: .65rem; }
.module-header-left i { color: var(--blue); font-size: .85rem; transition: transform .25s; }
.module-title {
    font-family: 'Sora', sans-serif;
    font-size: .9rem;
    font-weight: 700;
    color: var(--ink);
}
.module-count {
    font-size: .75rem;
    color: var(--muted);
    flex-shrink: 0;
}
.lesson-list { border-top: 1px solid var(--border); }
.lesson-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .7rem 1.1rem;
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.lesson-item:last-child { border-bottom: none; }
.lesson-item.clickable { cursor: pointer; }
.lesson-item.clickable:hover { background: #f0f7ff; }
.lesson-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: .7rem;
}
.lesson-icon-play { background: #dbeafe; color: var(--blue); }
.lesson-icon-lock { background: #f3f4f6; color: #9ca3af; }
.lesson-title {
    flex: 1;
    font-size: .85rem;
    color: var(--ink-2);
    line-height: 1.4;
}
.lesson-title.accessible { color: var(--ink); }
.lesson-badges { display: flex; align-items: center; gap: .5rem; flex-shrink: 0; }
.lesson-preview-badge {
    font-size: .65rem;
    font-weight: 700;
    color: var(--blue);
    background: #dbeafe;
    padding: .15rem .5rem;
    border-radius: 4px;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.lesson-duration {
    font-size: .75rem;
    color: var(--muted);
}

/* ── DESCRIPTION ── */
.course-description {
    font-size: .9rem;
    color: var(--ink-2);
    line-height: 1.8;
}
.course-description-fade {
    position: relative;
    max-height: 160px;
    overflow: hidden;
}
.course-description-fade::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: linear-gradient(transparent, #fff);
}
.course-description-fade.expanded { max-height: none; }
.course-description-fade.expanded::after { display: none; }
.btn-show-more {
    background: none;
    border: none;
    color: var(--blue);
    font-size: .875rem;
    font-weight: 600;
    cursor: pointer;
    padding: .5rem 0;
    display: flex;
    align-items: center;
    gap: .35rem;
    margin-top: .5rem;
}
.btn-show-more:hover { color: var(--blue-d); }

/* ── INSTRUCTOR ── */
.instructor-card {
    display: flex;
    align-items: flex-start;
    gap: 1.25rem;
    padding: 1.25rem;
    background: var(--surf);
    border: 1.5px solid var(--border);
    border-radius: 12px;
}
.instructor-avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: var(--blue-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-family: 'Sora', sans-serif;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--blue);
}
.instructor-name {
    font-family: 'Sora', sans-serif;
    font-size: 1rem;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: .2rem;
}
.instructor-bio {
    font-size: .85rem;
    color: var(--muted);
}

/* ── RELATED COURSES ── */
.related-scroll {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
}
.related-card {
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: transform .25s, box-shadow .25s, border-color .2s;
    display: flex;
    flex-direction: column;
}
.related-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 40px rgba(10,22,40,.1);
    border-color: #93c5fd;
}
.related-thumb {
    aspect-ratio: 16/9;
    background: var(--blue-xl);
    overflow: hidden;
}
.related-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s; }
.related-card:hover .related-thumb img { transform: scale(1.06); }
.related-body { padding: .85rem 1rem; flex: 1; display: flex; flex-direction: column; }
.related-cat { font-size: .65rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--blue); margin-bottom: .3rem; }
.related-title { font-family: 'Sora', sans-serif; font-size: .82rem; font-weight: 700; color: var(--ink); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.related-price { font-family: 'Sora', sans-serif; font-size: .85rem; font-weight: 800; color: var(--blue); margin-top: auto; padding-top: .6rem; }

/* ── MOBILE STICKY BUY BAR ── */
.mobile-buy-bar {
    display: none;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    border-top: 1px solid var(--border);
    padding: .85rem 1.25rem;
    z-index: 500;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    box-shadow: 0 -4px 20px rgba(10,22,40,.08);
}
@media (max-width: 1024px) { .mobile-buy-bar { display: flex; } }
.mobile-buy-price { font-family: 'Sora', sans-serif; font-size: 1rem; font-weight: 800; color: var(--ink); }
.mobile-buy-btn {
    padding: .75rem 1.75rem;
    background: var(--blue);
    color: #fff;
    font-weight: 700;
    font-size: .9rem;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: background .2s;
    white-space: nowrap;
}
.mobile-buy-btn:hover { background: var(--blue-d); }

/* ── FREE CLASS RESPONSIVE ── */
@media (max-width: 768px) {
    .fc-grid { grid-template-columns: 1fr !important; }
    .fc-hero-grid { grid-template-columns: 1fr !important; }
    .fc-nav { display: none !important; }
}
</style>
@endpush

@section('content')

    {{-- ===================== FREE CLASS LAYOUT ===================== --}}
    @if($course->isFreeClass())

    {{-- FREE CLASS: HERO BANNER --}}
    <div style="background:linear-gradient(135deg,#e8f4fd 0%,#f0fdf4 100%);border-bottom:1px solid #e4eaf2;padding:2.5rem 0;">
        <div style="max-width:1280px;margin:0 auto;padding:0 1.5rem;">
            <nav style="font-size:.8rem;color:var(--muted);margin-bottom:1.5rem;display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;">
                <a href="{{ route('home') }}" style="color:var(--muted);text-decoration:none;">Beranda</a>
                <span>›</span>
                <a href="{{ route('course.index') }}" style="color:var(--muted);text-decoration:none;">Kursus</a>
                @if($course->category)
                    <span>›</span>
                    <a href="{{ route('course.index', ['category' => $course->category->slug]) }}" style="color:var(--muted);text-decoration:none;">{{ $course->category->name }}</a>
                @endif
                <span>›</span>
                <span style="color:var(--ink);">{{ Str::limit($course->title, 50) }}</span>
            </nav>
            <div style="display:grid;grid-template-columns:320px 1fr;gap:2.5rem;align-items:start;" class="fc-hero-grid">
                <div style="border-radius:16px;overflow:hidden;box-shadow:0 8px 32px rgba(10,22,40,.12);">
                    @if($course->thumbnail && $course->thumbnail !== 'default-course.jpg')
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" style="width:100%;display:block;object-fit:cover;">
                    @else
                        <div style="aspect-ratio:4/3;background:linear-gradient(135deg,#1474bc,#10b981);display:flex;align-items:center;justify-content:center;">
                            <div style="text-align:center;color:#fff;">
                                <i class="fas fa-bolt" style="font-size:3rem;margin-bottom:.5rem;display:block;"></i>
                                <span style="font-family:'Sora',sans-serif;font-weight:700;font-size:1.1rem;">Kelas Gratis</span>
                            </div>
                        </div>
                    @endif
                </div>
                <div>
                    <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem;flex-wrap:wrap;">
                        <span style="background:#dcfce7;color:#15803d;font-size:.7rem;font-weight:700;padding:.3rem .8rem;border-radius:999px;text-transform:uppercase;letter-spacing:.06em;"><i class="fas fa-bolt mr-1"></i> Kelas Gratis</span>
                        @if($course->category)
                            <span style="background:#dbeafe;color:#1d4ed8;font-size:.7rem;font-weight:700;padding:.3rem .8rem;border-radius:999px;text-transform:uppercase;letter-spacing:.06em;">{{ $course->category->name }}</span>
                        @endif
                    </div>
                    <h1 style="font-family:'Sora',sans-serif;font-size:clamp(1.6rem,3vw,2.5rem);font-weight:800;color:var(--ink);line-height:1.2;margin-bottom:.6rem;">{{ $course->title }}</h1>
                    <p style="font-size:.95rem;color:var(--muted);margin-bottom:1.25rem;">{{ $course->instructor }}</p>
                    @if($course->schedule_start)
                    <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem;">
                        <div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:.85rem 1.1rem;min-width:180px;flex:1;">
                            <div style="font-size:.7rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem;">Jadwal Mulai</div>
                            <div style="font-family:'Sora',sans-serif;font-size:.95rem;font-weight:700;color:var(--ink);">{{ $course->schedule_start->translatedFormat('d M Y') }}</div>
                            <div style="font-size:.8rem;color:var(--muted);">{{ $course->schedule_start->format('H:i') }} WIB{{ $course->meeting_platform ? ' · ' . $course->meeting_platform : '' }}</div>
                        </div>
                        @if($course->schedule_end)
                        <div style="background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:.85rem 1.1rem;min-width:180px;flex:1;">
                            <div style="font-size:.7rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem;">Jadwal Selesai</div>
                            <div style="font-family:'Sora',sans-serif;font-size:.95rem;font-weight:700;color:var(--ink);">{{ $course->schedule_end->translatedFormat('d M Y') }}</div>
                            <div style="font-size:.8rem;color:var(--muted);">{{ $course->schedule_end->format('H:i') }} WIB</div>
                        </div>
                        @endif
                    </div>
                    @endif
                    <div style="display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
                        @if(auth()->check())
                            @if($userHasAccess)
                                <div style="display:flex;align-items:center;gap:.6rem;background:#dcfce7;border:1.5px solid #86efac;border-radius:12px;padding:.75rem 1.25rem;">
                                    <i class="fas fa-check-circle" style="color:#16a34a;font-size:1.1rem;"></i>
                                    <span style="font-weight:700;color:#15803d;font-size:.9rem;">Anda Sudah Terdaftar!</span>
                                </div>
                            @else
                                <form method="post" action="{{ route('course.enroll', $course->slug) }}" style="margin:0;">
                                    @csrf
                                    <button type="submit" style="display:inline-flex;align-items:center;gap:.5rem;padding:.85rem 1.75rem;background:#f59e0b;color:#fff;font-family:'Sora',sans-serif;font-size:.95rem;font-weight:700;border:none;border-radius:12px;cursor:pointer;box-shadow:0 4px 16px rgba(245,158,11,.35);">
                                        <i class="fas fa-bolt"></i> Daftar Sekarang!
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;gap:.5rem;padding:.85rem 1.75rem;background:#f59e0b;color:#fff;font-family:'Sora',sans-serif;font-size:.95rem;font-weight:700;border-radius:12px;text-decoration:none;box-shadow:0 4px 16px rgba(245,158,11,.35);">
                                <i class="fas fa-bolt"></i> Daftar Sekarang!
                            </a>
                        @endif
                        <span style="font-size:.8rem;color:var(--muted);"><i class="fas fa-users mr-1"></i> {{ number_format($enrolledStudentsCount) }}+ peserta</span>
                    </div>
                    @if(session('message'))
                        <div style="margin-top:.75rem;padding:.6rem 1rem;background:#dcfce7;border:1px solid #86efac;border-radius:8px;color:#15803d;font-size:.85rem;">{{ session('message') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- FREE CLASS: BODY --}}
    <div style="max-width:1280px;margin:0 auto;padding:2.5rem 1.5rem 5rem;display:grid;grid-template-columns:220px 1fr;gap:2.5rem;align-items:start;" class="fc-grid">

        {{-- Sidebar Nav --}}
        <div style="position:sticky;top:88px;" class="fc-nav">
            <div style="background:#fff;border:1.5px solid var(--border);border-radius:14px;overflow:hidden;">
                <div style="padding:.75rem 1rem;background:var(--surf);border-bottom:1px solid var(--border);">
                    <span style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);">Detail</span>
                </div>
                <nav style="padding:.5rem 0;">
                    @foreach([
                        ['fc-tentang',   'Tentang Short Class'],
                        ['fc-materi',    'Yang Akan Kamu Pelajari'],
                        ['fc-benefit',   'Benefit Kelas'],
                        ['fc-jadwal',    'Jadwal & Platform'],
                        ['fc-video',     'Video Pembelajaran'],
                        ['fc-instruktur','Instruktur'],
                    ] as [$id, $label])
                    <a href="#{{ $id }}" style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1rem;font-size:.85rem;color:var(--ink-2);text-decoration:none;transition:background .15s;border-left:3px solid transparent;"
                       onmouseover="this.style.background='var(--surf)';this.style.color='var(--blue)'"
                       onmouseout="this.style.background='';this.style.color='var(--ink-2)'">
                        <i class="fas fa-chevron-right" style="font-size:.6rem;opacity:.4;"></i> {{ $label }}
                    </a>
                    @endforeach
                </nav>
                <div style="padding:1rem;border-top:1px solid var(--border);">
                    @if(auth()->check())
                        @if($userHasAccess)
                            <div style="text-align:center;padding:.6rem;background:#dcfce7;border-radius:8px;font-size:.8rem;font-weight:700;color:#15803d;"><i class="fas fa-check-circle mr-1"></i> Sudah Terdaftar</div>
                        @else
                            <form method="post" action="{{ route('course.enroll', $course->slug) }}" style="margin:0;">
                                @csrf
                                <button type="submit" style="width:100%;padding:.7rem;background:#f59e0b;color:#fff;font-weight:700;font-size:.85rem;border:none;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:.4rem;">
                                    <i class="fas fa-bolt"></i> Daftar Sekarang
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" style="display:flex;align-items:center;justify-content:center;gap:.4rem;width:100%;padding:.7rem;background:#f59e0b;color:#fff;font-weight:700;font-size:.85rem;border-radius:10px;text-decoration:none;">
                            <i class="fas fa-bolt"></i> Daftar Sekarang
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Content --}}
        <div style="min-width:0;">
            <div id="fc-tentang" style="margin-bottom:2.5rem;scroll-margin-top:100px;">
                <h2 style="font-family:'Sora',sans-serif;font-size:1.15rem;font-weight:700;color:var(--ink);margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;"><i class="fas fa-chevron-right" style="color:var(--blue);font-size:.85rem;"></i> Tentang Short Class</h2>
                <div style="font-size:.9rem;color:var(--ink-2);line-height:1.85;">{!! nl2br(e($course->description)) !!}</div>
            </div>
            @if($course->topics_preview)
            <div id="fc-materi" style="margin-bottom:2.5rem;scroll-margin-top:100px;">
                <h2 style="font-family:'Sora',sans-serif;font-size:1.15rem;font-weight:700;color:var(--ink);margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;"><i class="fas fa-chevron-right" style="color:var(--blue);font-size:.85rem;"></i> Yang Akan Kamu Pelajari</h2>
                <div style="font-size:.9rem;color:var(--ink-2);line-height:1.85;white-space:pre-line;">{{ $course->topics_preview }}</div>
            </div>
            @endif
            @if($course->benefits)
            <div id="fc-benefit" style="margin-bottom:2.5rem;scroll-margin-top:100px;">
                <h2 style="font-family:'Sora',sans-serif;font-size:1.15rem;font-weight:700;color:var(--ink);margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;"><i class="fas fa-chevron-right" style="color:var(--blue);font-size:.85rem;"></i> Benefit Kelas</h2>
                <div style="font-size:.9rem;color:var(--ink-2);line-height:1.85;white-space:pre-line;">{{ $course->benefits }}</div>
            </div>
            @endif
            @if($course->schedule_start)
            <div id="fc-jadwal" style="margin-bottom:2.5rem;scroll-margin-top:100px;">
                <h2 style="font-family:'Sora',sans-serif;font-size:1.15rem;font-weight:700;color:var(--ink);margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;"><i class="fas fa-chevron-right" style="color:var(--blue);font-size:.85rem;"></i> Jadwal & Platform</h2>
                <div style="background:var(--surf);border:1.5px solid var(--border);border-radius:12px;padding:1.25rem 1.5rem;display:flex;flex-direction:column;gap:.75rem;">
                    <div style="display:flex;align-items:center;gap:.75rem;font-size:.9rem;color:var(--ink-2);"><i class="fas fa-calendar-alt" style="color:var(--blue);width:18px;"></i><span><strong>Mulai:</strong> {{ $course->schedule_start->translatedFormat('l, d F Y') }} pukul {{ $course->schedule_start->format('H:i') }} WIB</span></div>
                    @if($course->schedule_end)
                    <div style="display:flex;align-items:center;gap:.75rem;font-size:.9rem;color:var(--ink-2);"><i class="fas fa-calendar-check" style="color:var(--blue);width:18px;"></i><span><strong>Selesai:</strong> {{ $course->schedule_end->translatedFormat('l, d F Y') }} pukul {{ $course->schedule_end->format('H:i') }} WIB</span></div>
                    @endif
                    @if($course->meeting_platform)
                    <div style="display:flex;align-items:center;gap:.75rem;font-size:.9rem;color:var(--ink-2);"><i class="fas fa-video" style="color:var(--blue);width:18px;"></i><span><strong>Platform:</strong> {{ $course->meeting_platform }}</span></div>
                    @endif
                </div>
            </div>
            @endif
            <div id="fc-instruktur" style="margin-bottom:2.5rem;scroll-margin-top:100px;">
                <h2 style="font-family:'Sora',sans-serif;font-size:1.15rem;font-weight:700;color:var(--ink);margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;"><i class="fas fa-chevron-right" style="color:var(--blue);font-size:.85rem;"></i> Instruktur</h2>
                <div style="display:flex;align-items:center;gap:1rem;background:var(--surf);border:1.5px solid var(--border);border-radius:12px;padding:1.25rem 1.5rem;">
                    <div style="width:52px;height:52px;border-radius:50%;background:var(--blue-xl);display:flex;align-items:center;justify-content:center;font-family:'Sora',sans-serif;font-size:1.3rem;font-weight:700;color:var(--blue);flex-shrink:0;">{{ strtoupper(substr($course->instructor, 0, 1)) }}</div>
                    <div>
                        <div style="font-family:'Sora',sans-serif;font-weight:700;color:var(--ink);font-size:.95rem;">{{ $course->instructor }}</div>
                        <div style="font-size:.82rem;color:var(--muted);">Instruktur {{ $course->category?->name ?? 'Kelas Digital' }}</div>
                    </div>
                </div>
            </div>

            {{-- FREE: VIDEO PLAYER --}}
            <div id="fc-video" style="margin-bottom:2.5rem;scroll-margin-top:100px;">
                <h2 style="font-family:'Sora',sans-serif;font-size:1.15rem;font-weight:700;color:var(--ink);margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;"><i class="fas fa-chevron-right" style="color:var(--blue);font-size:.85rem;"></i> Video Pembelajaran</h2>
                @if($course->lessons->count() > 0)
                    <div style="border:1.5px solid var(--border);border-radius:12px;overflow:hidden;">
                        @foreach($course->lessons as $lesson)
                            <a href="{{ route('course.lesson', [$course->slug, $lesson]) }}"
                               style="display:flex;align-items:center;gap:.75rem;padding:.9rem 1.1rem;text-decoration:none;border-bottom:1px solid var(--border);transition:background .15s;"
                               onmouseover="this.style.background='var(--surf)'"
                               onmouseout="this.style.background=''">
                                <div style="width:34px;height:34px;border-radius:50%;background:var(--blue-xl);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <i class="fas fa-play" style="color:var(--blue);font-size:.75rem;margin-left:2px;"></i>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:.875rem;font-weight:500;color:var(--ink);">{{ $lesson->title }}</div>
                                    @if($lesson->duration)
                                        <div style="font-size:.75rem;color:var(--muted);margin-top:2px;">{{ $lesson->duration }}</div>
                                    @endif
                                </div>
                                <i class="fas fa-chevron-right" style="color:var(--muted);font-size:.8rem;"></i>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.9rem;background:var(--surf);border-radius:10px;border:1.5px dashed var(--border);">
                        Materi video akan segera ditambahkan.
                    </div>
                @endif
            </div>
        </div>
    </div>
    {{-- ===================== PAID COURSE LAYOUT ===================== --}}
    @else

    {{-- HERO SECTION --}}
    <div class="course-hero">
        <div class="course-hero-inner">

            {{-- Left: Info --}}
            <div class="course-hero-content">
                {{-- Breadcrumb --}}
                <nav class="course-hero-breadcrumb">
                    <a href="{{ route('home') }}">Beranda</a>
                    <span>/</span>
                    <a href="{{ route('course.index') }}">Kursus</a>
                    @if($course->category)
                        <span>/</span>
                        <a href="{{ route('course.index', ['category' => $course->category->slug]) }}">{{ $course->category->name }}</a>
                    @endif
                    <span>/</span>
                    <span class="current">{{ Str::limit($course->title, 40) }}</span>
                </nav>

                {{-- Badges --}}
                <div class="course-hero-badges">
                    <span class="course-badge badge-premium"><i class="fas fa-crown mr-1"></i> Premium</span>
                    <span class="course-badge badge-level-{{ strtolower($course->level) }}">{{ $course->level }}</span>
                </div>

                <h1>{{ $course->title }}</h1>

                @if($course->description)
                    <p class="course-hero-subtitle">{{ Str::limit(strip_tags($course->description), 160) }}</p>
                @endif

                {{-- Meta stats --}}
                <div class="course-hero-meta">
                    <div class="course-hero-meta-item">
                        <i class="fas fa-users"></i>
                        <span>{{ number_format($enrolledStudentsCount) }} siswa terdaftar</span>
                    </div>
                    <div class="course-hero-meta-item">
                        <i class="fas fa-play-circle"></i>
                        <span>{{ $totalVideos }} video</span>
                    </div>
                    <div class="course-hero-meta-item">
                        <div class="flex text-yellow-400 gap-0.5">
                            @for($i = 0; $i < 5; $i++)<i class="fas fa-star text-xs"></i>@endfor
                        </div>
                        <span>4.8</span>
                    </div>
                </div>

                <p class="course-hero-instructor">
                    Instruktur: <a href="#">{{ $course->instructor }}</a>
                </p>
            </div>

        </div>
    </div>

    {{-- PREVIEW MODAL (di luar course-body agar fixed benar-benar full screen) --}}
    <div id="preview-modal"
         style="display:none;position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.9);align-items:center;justify-content:center;padding:1.5rem;"
         onclick="if(event.target===this)closePreviewModal()">
        <div style="width:100%;max-width:900px;position:relative;">
            <button onclick="closePreviewModal()"
                    style="position:absolute;top:-2.75rem;right:0;background:none;border:none;color:#fff;font-size:1.75rem;cursor:pointer;line-height:1;padding:.25rem;"
                    aria-label="Tutup">
                <i class="fas fa-times"></i>
            </button>
            <div style="aspect-ratio:16/9;border-radius:12px;overflow:hidden;background:#000;" id="preview-iframe-wrap">
            </div>
            <p style="text-align:center;color:rgba(255,255,255,.5);font-size:.78rem;margin-top:.75rem;">
                Video preview — berbeda dengan konten kelas penuh
            </p>
        </div>
    </div>

    {{-- MAIN BODY --}}
    <div class="course-body" x-data="{
        currentVideoId: '{{ $initialVideoId }}',
        descExpanded: false,
        switchVideo(videoId) { this.currentVideoId = videoId; document.getElementById('main-video').scrollIntoView({behavior:'smooth'}); }
    }">

        {{-- LEFT COLUMN --}}
        <div>

            {{-- Video Player --}}
            <div class="course-section" id="main-video">
                <div style="border-radius:12px;overflow:hidden;box-shadow:0 8px 32px rgba(10,22,40,.12);background:#000;">
                    <div style="aspect-ratio:16/9;">
                        <iframe :src="`https://www.youtube.com/embed/${currentVideoId}`"
                                width="100%" height="100%"
                                title="Course Video" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen style="width:100%;height:100%;display:block;"></iframe>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            @if($course->description)
            <div class="course-section">
                <h2 class="course-section-title">Tentang Kelas Ini</h2>
                <div class="course-description">
                    <div class="course-description-fade" :class="{ 'expanded': descExpanded }" id="desc-block">
                        {!! nl2br(e($course->description)) !!}
                    </div>
                    <button class="btn-show-more" @click="descExpanded = !descExpanded">
                        <span x-text="descExpanded ? 'Tampilkan lebih sedikit' : 'Tampilkan selengkapnya'"></span>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="{'rotate-180': descExpanded}"></i>
                    </button>
                </div>
            </div>
            @endif

            {{-- Curriculum --}}
            <div class="course-section">
                <h2 class="course-section-title">Kurikulum Kelas</h2>
                <p class="curriculum-summary">
                    <strong>{{ $lessonsByModule->count() }}</strong> modul &bull;
                    <strong>{{ $totalVideos }}</strong> video
                </p>

                @forelse($lessonsByModule as $module => $lessons)
                <div class="module-block" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                    <div class="module-header" @click="open = !open">
                        <div class="module-header-left">
                            <i class="fas fa-chevron-right" :class="{ 'rotate-90': open }"></i>
                            <span class="module-title">{{ $module }}</span>
                        </div>
                        <span class="module-count">{{ $lessons->count() }} video</span>
                    </div>
                    <div class="lesson-list" x-show="open" x-collapse>
                        @foreach($lessons as $lesson)
                        <div class="lesson-item {{ ($userHasAccess || $lesson->is_preview) ? 'clickable' : '' }}"
                             @if($userHasAccess || $lesson->is_preview)
                                 @click="switchVideo('{{ $lesson->youtube_video_id }}')"
                             @endif>
                            <div class="lesson-icon {{ ($userHasAccess || $lesson->is_preview) ? 'lesson-icon-play' : 'lesson-icon-lock' }}">
                                <i class="fas {{ ($userHasAccess || $lesson->is_preview) ? 'fa-play' : 'fa-lock' }}"></i>
                            </div>
                            <span class="lesson-title {{ ($userHasAccess || $lesson->is_preview) ? 'accessible' : '' }}">
                                {{ $lesson->title }}
                            </span>
                            <div class="lesson-badges">
                                @if($lesson->is_preview && !$userHasAccess)
                                    <span class="lesson-preview-badge">Preview</span>
                                @endif
                                @if($lesson->duration)
                                    <span class="lesson-duration">{{ $lesson->duration }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                    <div style="padding:2rem;text-align:center;color:var(--muted);font-size:.9rem;background:var(--surf);border-radius:10px;border:1.5px dashed var(--border);">
                        Materi pembelajaran akan segera ditambahkan.
                    </div>
                @endforelse
            </div>

            {{-- Instructor --}}
            <div class="course-section">
                <h2 class="course-section-title">Instruktur</h2>
                <div class="instructor-card">
                    <div class="instructor-avatar">{{ strtoupper(substr($course->instructor, 0, 1)) }}</div>
                    <div>
                        <div class="instructor-name">{{ $course->instructor }}</div>
                        <div class="instructor-bio">Instruktur berpengalaman di bidang {{ $course->category?->name ?? 'teknologi' }}</div>
                    </div>
                </div>
            </div>

            {{-- Related Courses --}}
            @if($relatedCourses->count())
            <div class="course-section">
                <h2 class="course-section-title">Kelas Lainnya yang Mungkin Kamu Suka</h2>
                <div class="related-scroll">
                    @foreach($relatedCourses as $related)
                    <a href="{{ route('course.show', $related->slug) }}" class="related-card">
                        <div class="related-thumb">
                            @if($related->thumbnail && $related->thumbnail !== 'default-course.jpg')
                                <img src="{{ asset('storage/' . $related->thumbnail) }}" alt="{{ $related->title }}" loading="lazy">
                            @else
                                <div style="width:100%;height:100%;background:linear-gradient(135deg,#dbeafe,#eff6ff);display:flex;align-items:center;justify-content:center;">
                                    <i class="fas fa-play-circle" style="font-size:2rem;color:var(--blue);opacity:.3;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="related-body">
                            @if($related->category)
                                <span class="related-cat">{{ $related->category->name }}</span>
                            @endif
                            <h4 class="related-title">{{ $related->title }}</h4>
                            <div class="related-price">
                                @if($related->course_type === 'free')
                                    <span style="color:#10b981;">Gratis!</span>
                                @else
                                    Rp{{ number_format($related->price, 0, ',', '.') }}
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- RIGHT COLUMN (sticky sidebar, desktop) --}}
        <div class="course-body-sidebar">
            <div class="course-sidebar-card">
                <div class="course-sidebar-preview" onclick="openPreviewModal('{{ $course->trailer_video_id }}')" style="cursor:pointer;">
                    @if($course->thumbnail && $course->thumbnail !== 'default-course.jpg')
                        <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}">
                    @else
                        <div style="width:100%;height:100%;background:linear-gradient(135deg,#1e3a5f,#1474bc);"></div>
                    @endif
                    <div class="course-sidebar-play">
                        <div class="play-btn"><i class="fas fa-play" style="margin-left:3px;font-size:1.1rem;"></i></div>
                        <span>Preview Kelas</span>
                    </div>
                </div>
                <div class="course-sidebar-body">
                    <div class="course-sidebar-price">
                        Rp{{ number_format($course->price, 0, ',', '.') }}
                        <span class="original">Rp{{ number_format($course->price * 1.2, 0, ',', '.') }}</span>
                    </div>

                    @if(auth()->check())
                        @if($userHasAccess)
                            <a href="#main-video" class="btn-enroll btn-enroll-success">
                                <i class="fas fa-play mr-2"></i> Mulai Belajar
                            </a>
                            <p class="text-center text-sm text-green-600 font-semibold mt-1">✓ Anda sudah terdaftar</p>
                        @elseif($userEnrollment && $userEnrollment->payment_status === 'pending')
                            <div class="text-center p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm">
                                <p class="font-semibold text-yellow-800">Menunggu Pembayaran</p>
                                <p class="text-yellow-600 text-xs mt-1">Selesaikan pembayaran untuk mengakses kelas.</p>
                                <button type="button" class="btn-enroll mt-2 btn-pay" data-slug="{{ $course->slug }}" style="font-size:.85rem;padding:.6rem 1rem;">
                                    <i class="fas fa-credit-card mr-1"></i> Bayar Sekarang
                                </button>
                            </div>
                        @else
                            <button type="button" class="btn-enroll btn-pay" data-slug="{{ $course->slug }}">
                                Gabung Kelas Ini
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-enroll">Login untuk Gabung</a>
                    @endif

                    @if(session('message'))
                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm text-center">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="course-sidebar-includes">
                        <h4>Termasuk dalam kelas ini</h4>
                        <ul>
                            <li><i class="fas fa-infinity"></i> Akses seumur hidup</li>
                            <li><i class="fas fa-play-circle"></i> {{ $totalVideos }} video pembelajaran</li>
                            <li><i class="fas fa-mobile-alt"></i> Akses di semua perangkat</li>
                            <li><i class="fas fa-certificate"></i> Sertifikat penyelesaian</li>
                            <li><i class="fas fa-comments"></i> Konsultasi dengan instruktur</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- MOBILE STICKY BUY BAR --}}
    <div class="mobile-buy-bar">
        <div>
            <div class="mobile-buy-price">Rp{{ number_format($course->price, 0, ',', '.') }}</div>
            @if($userHasAccess)
                <div style="font-size:.75rem;color:#10b981;font-weight:600;">✓ Sudah terdaftar</div>
            @endif
        </div>
        @if(auth()->check())
            @if($userHasAccess)
                <a href="#main-video" class="mobile-buy-btn" style="background:#10b981;">Mulai Belajar</a>
            @elseif($userEnrollment && $userEnrollment->payment_status === 'pending')
                <button type="button" class="mobile-buy-btn btn-pay" data-slug="{{ $course->slug }}" style="background:#f59e0b;border:none;cursor:pointer;">
                    <i class="fas fa-credit-card mr-1"></i> Bayar
                </button>
            @else
                <button type="button" class="mobile-buy-btn btn-pay" data-slug="{{ $course->slug }}">Gabung Kelas</button>
            @endif
        @else
            <a href="{{ route('login') }}" class="mobile-buy-btn">Login untuk Gabung</a>
        @endif
    </div>

    @endif

@endsection

@push('scripts')
<script>
function openPreviewModal(videoId) {
    if (!videoId) return;
    const modal = document.getElementById('preview-modal');
    const wrap = document.getElementById('preview-iframe-wrap');
    wrap.innerHTML = '<iframe src="https://www.youtube.com/embed/' + videoId + '?autoplay=1" width="100%" height="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width:100%;height:100%;display:block;"></iframe>';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closePreviewModal() {
    const modal = document.getElementById('preview-modal');
    const wrap = document.getElementById('preview-iframe-wrap');
    wrap.innerHTML = '';
    modal.style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePreviewModal();
});

// Midtrans Snap
const snapClientKey = '{{ config('midtrans.client_key') }}';
const snapUrl = '{{ config('midtrans.snap_url') }}';
let snapScriptLoaded = false;

function loadSnapScript(callback) {
    if (window.snap) { callback(); return; }
    if (snapScriptLoaded) { setTimeout(() => loadSnapScript(callback), 100); return; }
    snapScriptLoaded = true;
    const script = document.createElement('script');
    script.src = snapUrl + '?client_key=' + snapClientKey;
    script.onload = callback;
    document.head.appendChild(script);
}

function setLoading(btn, loading) {
    if (!btn) return;
    if (loading) {
        btn._origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class=\"fas fa-spinner fa-spin mr-1\"></i> Memproses...';
    } else {
        btn.disabled = false;
        btn.innerHTML = btn._origHtml || 'Gabung Kelas Ini';
    }
}

function checkoutAndPay(slug, btn) {
    setLoading(btn, true);

    fetch('{{ url('/payment/checkout') }}/' + slug, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
    })
    .then(r => {
        if (r.status === 401) {
            window.location.href = '{{ route('login') }}';
            return;
        }
        return r.json();
    })
    .then(data => {
        setLoading(btn, false);
        if (!data) return;
        if (data.error === 'already_enrolled') {
            window.location.reload();
            return;
        }
        if (data.error === 'free_course') {
            window.location.reload();
            return;
        }
        if (data.snap_token) {
            loadSnapScript(function () {
                window.snap.pay(data.snap_token, {
                    onSuccess: function (result) {
                        // Mark payment completed immediately via AJAX
                        fetch('{{ url('/payment/complete') }}/' + data.enrollment_id, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                        })
                        .then(() => {
                            window.location.href = '{{ route('course.show', $course->slug) }}?payment=success';
                        })
                        .catch(() => {
                            window.location.href = '{{ route('course.show', $course->slug) }}?payment=success';
                        });
                    },
                    onPending: function () {
                        window.location.reload();
                    },
                    onError: function () {
                        window.location.reload();
                    },
                    onClose: function () {
                        window.location.reload();
                    }
                });
            });
        } else {
            alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
        }
    })
    .catch(e => {
        setLoading(btn, false);
        alert('Terjadi kesalahan: ' + e.message);
    });
}

document.querySelectorAll('.btn-pay').forEach(btn => {
    btn.addEventListener('click', function () {
        checkoutAndPay(this.dataset.slug, this);
    });
});

// Handle ?payment=success query param
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('payment') === 'success') {
    const alertBox = document.createElement('div');
    alertBox.style.cssText = 'position:fixed;top:1rem;right:1rem;z-index:9999;background:#059669;color:#fff;padding:1rem 1.5rem;border-radius:12px;font-weight:600;box-shadow:0 8px 32px rgba(5,150,105,.35);animation:fadeIn .3s ease;';
    alertBox.innerHTML = '<i class=\"fas fa-check-circle mr-2\"></i> Pembayaran berhasil! Selamat belajar.';
    document.body.appendChild(alertBox);
    setTimeout(() => { alertBox.style.opacity = '0'; alertBox.style.transition = 'opacity .3s'; setTimeout(() => alertBox.remove(), 400); }, 5000);
    // Clean URL
    window.history.replaceState({}, document.title, window.location.pathname);
}
</script>
@endpush
