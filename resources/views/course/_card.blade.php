{{-- Course card for the catalog, home and dashboard, in the same style as the search results. Expects $course; uses lessons_count / students_count when loaded. --}}
@php
    $free = $course->isFreeClass();
    $partlyFree = $course->isPartlyFree();
    $level = ['Beginner' => 'Pemula', 'Intermediate' => 'Menengah', 'Advanced' => 'Mahir'][$course->level] ?? null;
@endphp
<a href="{{ route('course.show', $course->slug) }}" class="cc">
    <div class="cc-thumb">
        @if($course->thumbnail_url)
            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" loading="lazy" onerror="this.remove()">
        @endif
        <span class="cc-badge {{ $free ? 'free' : ($partlyFree ? 'part' : '') }}">{{ $free ? 'Kelas Gratis' : ($partlyFree ? 'Sebagian Gratis' : 'Premium') }}</span>
    </div>
    <div class="cc-body">
        @if($course->category)<span class="cc-cat">{{ $course->category->name }}</span>@endif
        <h3 class="cc-title">{{ $course->title }}</h3>
        <span class="cc-ins">{{ $course->instructor ?: 'Ray Academy' }}</span>
        <div class="cc-meta">
            @if($level)<span>{{ $level }}</span>@endif
            @if($course->lessons_count)<span>{{ $course->lessons_count }} materi</span>@endif
            @if($course->students_count >= \App\Models\Course::MIN_STUDENTS_SHOWN)<span>{{ $course->students_count }} peserta</span>@endif
        </div>
        <div class="cc-foot">
            <span class="cc-price {{ $free || $partlyFree ? 'free' : '' }}">{{ $free ? 'Gratis' : ($partlyFree ? 'Mulai Gratis' : 'Rp'.number_format($course->price, 0, ',', '.')) }}</span>
            <span class="cc-go">Lihat kelas <i class="fas fa-arrow-right"></i></span>
        </div>
    </div>
</a>

@once
@push('styles')
<style>
.cc { background:#fff; border:1px solid #e3ecf8; border-radius:18px; box-shadow:0 10px 28px rgba(31,77,140,.08); overflow:hidden; display:flex; flex-direction:column; text-decoration:none; color:inherit; transition:box-shadow .2s, transform .2s; }
.cc:hover { box-shadow:0 18px 40px rgba(31,77,140,.16); transform:translateY(-3px); }
.cc-thumb { position:relative; aspect-ratio:16/8; overflow:hidden; background:linear-gradient(135deg,#EEF4FF,#DBEAFE); }
.cc-thumb img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .3s; }
.cc:hover .cc-thumb img { transform:scale(1.04); }
.cc-badge { position:absolute; top:.6rem; right:.6rem; font-size:.66rem; font-weight:700; padding:.2rem .6rem; border-radius:999px; border:1px solid #1f6fd1; background:rgba(255,255,255,.85); -webkit-backdrop-filter:blur(8px); backdrop-filter:blur(8px); color:#1f6fd1; }
.cc-badge.free { border-color:#16a34a; color:#16a34a; }
.cc-badge.part { border-color:#d97706; color:#b45309; }
.cc-body { padding:.9rem 1rem 1rem; display:flex; flex-direction:column; flex:1; }
.cc-cat { font-size:.64rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#1f6fd1; }
.cc-title { font-family:'Sora',sans-serif; font-size:.92rem; font-weight:600; line-height:1.4; color:#0b1f4d; margin:.25rem 0 .2rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.cc-ins { font-size:.75rem; color:#6B7280; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.cc-meta { display:flex; flex-wrap:wrap; font-size:.72rem; color:#6B7280; margin:.45rem 0 .85rem; }
.cc-meta span + span::before { content:'·'; margin:0 .4rem; color:#CBD5E1; }
.cc-foot { display:flex; align-items:center; justify-content:space-between; gap:.5rem; margin-top:auto; padding-top:.7rem; border-top:1px solid #F1F5F9; }
.cc-price { font-family:'Sora',sans-serif; font-weight:700; font-size:.9rem; color:#0b1f4d; }
.cc-price.free { color:#16a34a; }
.cc-go { font-size:.74rem; font-weight:600; color:#1f6fd1; }
.cc-go i { font-size:.62rem; margin-left:.15rem; transition:transform .2s; }
.cc:hover .cc-go i { transform:translateX(3px); }
.cc-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; }
@media (max-width:1100px) { .cc-grid { grid-template-columns:repeat(3,1fr); } }
@media (max-width:800px) { .cc-grid { grid-template-columns:repeat(2,1fr); } }
/* Phones: two compact cards per row so thumbnails don't fill the screen. */
@media (max-width:520px) {
    .cc-grid { gap:.65rem; }
    .cc { border-radius:14px; }
    .cc-body { padding:.65rem .7rem .75rem; }
    .cc-cat { font-size:.58rem; }
    .cc-title { font-size:.82rem; }
    .cc-ins, .cc-go { display:none; }
    .cc-meta { font-size:.66rem; margin:.3rem 0 .5rem; }
    .cc-foot { padding-top:.5rem; }
    .cc-price { font-size:.82rem; }
    .cc-badge { font-size:.58rem; top:.4rem; right:.4rem; padding:.15rem .45rem; }
}
</style>
@endpush
@endonce
