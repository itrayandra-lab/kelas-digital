@extends('layouts.app')

@section('title', 'Semua Kursus — Ray Academy')

@push('styles')
<style>
/* ── Course Card ── */
.crs-card {
    background:#fff; border:1.5px solid var(--border); border-radius:16px;
    overflow:hidden; display:flex; flex-direction:column;
    text-decoration:none; color:inherit;
    transition:transform .3s ease, box-shadow .3s ease, border-color .25s;
}
.crs-card:hover { transform:translateY(-5px); box-shadow:0 20px 48px rgba(10,22,40,.1); border-color:#93c5fd; }

.crs-thumb { position:relative; overflow:hidden; aspect-ratio:16/9; background:linear-gradient(135deg,#dbeafe,#eff6ff); flex-shrink:0; }
.crs-thumb img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .45s; }
.crs-card:hover .crs-thumb img { transform:scale(1.07); }
.crs-thumb-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; }
.crs-badge { position:absolute; top:.75rem; left:.75rem; font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; padding:.3rem .7rem; border-radius:6px; }
.badge-free { background:#dcfce7; color:#15803d; }
.badge-paid { background:#dbeafe; color:#1d4ed8; }

.crs-body { padding:1.25rem; flex:1; display:flex; flex-direction:column; }
.crs-cat { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--blue); margin-bottom:.4rem; }
.crs-title { font-family:'Sora',sans-serif; font-size:.9375rem; font-weight:700; color:var(--ink); line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; transition:color .18s; }
.crs-card:hover .crs-title { color:var(--blue); }
.crs-instructor-row { display:flex; align-items:center; gap:.5rem; margin-top:.5rem; }
.crs-level-badge { font-size:.63rem; font-weight:600; padding:.2rem .6rem; border-radius:5px; background:var(--surf); color:var(--muted); border:1px solid var(--border); }
.crs-instructor-name { font-size:.78rem; color:var(--muted); }
.crs-meta { display:flex; align-items:center; justify-content:space-between; gap:.5rem; margin-top:auto; padding-top:.85rem; border-top:1px solid var(--border); margin-top:.85rem; }
.crs-price { font-family:'Sora',sans-serif; font-size:1rem; font-weight:800; color:var(--ink); }
.crs-price-free { color:var(--accent); }
.crs-enrolled { font-size:.72rem; color:var(--muted); display:flex; align-items:center; gap:.3rem; }

/* ── Filter tabs ── */
.f-wrap { background:#fff; border-bottom:1px solid var(--border); padding:.75rem 0; position:sticky; top:68px; z-index:10; }
.f-inner { max-width:1280px; margin:0 auto; padding:0 1.5rem; }
.f-tabs { display:flex; gap:.5rem; overflow-x:auto; -webkit-overflow-scrolling:touch; scrollbar-width:none; }
.f-tabs::-webkit-scrollbar { display:none; }
.f-tab { font-family:'DM Sans',sans-serif; font-size:.83rem; font-weight:600; cursor:pointer; padding:.45rem 1.05rem; border-radius:9px; border:1.5px solid var(--border); background:#fff; color:var(--ink-2); transition:all .18s; white-space:nowrap; flex-shrink:0; }
.f-tab:hover { border-color:var(--blue); color:var(--blue); }
.f-tab.active { background:var(--blue); color:#fff; border-color:var(--blue); box-shadow:0 4px 14px rgba(20,116,188,.25); }

/* ── Courses grid ── */
.courses-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:1.25rem; }
@media(max-width:640px) { .courses-grid { grid-template-columns:1fr 1fr; } }
@media(max-width:480px) { .courses-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="page-hero">
    <div class="page-hero-inner">
        <span class="page-hero-label">Kursus Online</span>
        <h1>Semua Kursus</h1>
        <p>Temukan kursus yang tepat dan mulai perjalanan belajarmu hari ini.</p>
    </div>
</section>

{{-- Category filter sticky bar --}}
@if(isset($courseCategories) && $courseCategories->count())
<div class="f-wrap">
    <div class="f-inner">
        <div class="f-tabs">
            <button class="f-tab active" onclick="filterCourse(this,'all')">Semua Kursus</button>
            @foreach($courseCategories as $cat)
                <button class="f-tab" onclick="filterCourse(this,'{{ $cat->slug }}')">{{ $cat->name }}</button>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Content --}}
<section style="background:var(--surf); padding:3.5rem 0 6rem;">
    <div style="max-width:1280px;margin:0 auto;padding:0 1.5rem;">

        <nav class="breadcrumb" style="margin-bottom:2rem;">
            <a href="{{ route('home') }}">Beranda</a>
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>Kursus</span>
        </nav>

        @if($courses->count() > 0)

            {{-- Count --}}
            <p class="rv" style="font-size:.85rem;color:var(--muted);margin-bottom:1.75rem;">
                Menampilkan <strong style="color:var(--ink);">{{ $courses->total() }}</strong> kursus tersedia
            </p>

            <div class="courses-grid" id="courses-grid">
                @foreach($courses as $i => $course)
                <a href="{{ route('course.show', $course->slug) }}"
                   class="crs-card rv rv-d{{ min($i % 4 + 1, 4) }}"
                   data-cat="{{ $course->category->slug ?? '' }}">

                    <div class="crs-thumb">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" loading="lazy"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                            <div class="crs-thumb-placeholder" style="display:none; position:absolute;inset:0;">
                                <svg style="width:36px;height:36px;color:var(--blue);opacity:.25;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                        @else
                            <div class="crs-thumb-placeholder">
                                <svg style="width:36px;height:36px;color:var(--blue);opacity:.25;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <span class="crs-badge {{ ($course->price ?? 0) > 0 ? 'badge-paid' : 'badge-free' }}">
                            {{ ($course->price ?? 0) > 0 ? 'Premium' : 'Gratis' }}
                        </span>
                    </div>

                    <div class="crs-body">
                        @if($course->category)
                            <span class="crs-cat">{{ $course->category->name }}</span>
                        @endif
                        <h3 class="crs-title">{{ $course->title }}</h3>

                        <div class="crs-instructor-row">
                            @if($course->level ?? null)
                                <span class="crs-level-badge">{{ $course->level }}</span>
                            @endif
                            <span class="crs-instructor-name">
                                {{ is_string($course->instructor) ? $course->instructor : ($course->instructor->name ?? '') }}
                            </span>
                        </div>

                        <div class="crs-meta">
                            <span class="crs-price {{ ($course->price ?? 0) == 0 ? 'crs-price-free' : '' }}">
                                @if(($course->price ?? 0) == 0)
                                    Gratis
                                @else
                                    Rp {{ number_format($course->price, 0, ',', '.') }}
                                @endif
                            </span>
                            @if(($course->enrollments_count ?? 0) > 0)
                            <span class="crs-enrolled">
                                <svg style="width:12px;height:12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                                {{ number_format($course->enrollments_count) }} siswa
                            </span>
                            @endif
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="pagination-wrap rv">
                {{ $courses->links() }}
            </div>

        @else
            <div class="empty-state rv">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <h3>Belum ada kursus</h3>
                <p>Kursus akan segera tersedia. Pantau terus!</p>
            </div>
        @endif
    </div>
</section>

@endsection

@push('scripts')
<script>
window.filterCourse = function(btn, cat) {
    document.querySelectorAll('.f-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.crs-card').forEach(card => {
        card.style.display = (cat === 'all' || card.dataset.cat === cat) ? '' : 'none';
    });
};
const obs = new IntersectionObserver(e => { e.forEach(x => { if(x.isIntersecting){ x.target.classList.add('in'); obs.unobserve(x.target); }}); }, { threshold:0.05, rootMargin:'0px 0px -40px 0px' });
document.querySelectorAll('.rv').forEach(el => obs.observe(el));
</script>
@endpush