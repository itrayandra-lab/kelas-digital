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
.cc { background:#fff; border:1px solid #E2E8F0; border-radius:12px; overflow:hidden; display:flex; flex-direction:column; text-decoration:none; color:inherit; transition:box-shadow .2s, transform .2s; }
.cc:hover { box-shadow:0 8px 24px rgba(0,0,0,.1); transform:translateY(-3px); }
.cc-thumb { position:relative; aspect-ratio:16/9; overflow:hidden; background:linear-gradient(135deg,#EEF4FF,#DBEAFE); }
.cc-thumb img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .3s; }
.cc:hover .cc-thumb img { transform:scale(1.04); }
.cc-badge { position:absolute; top:.6rem; right:.6rem; font-size:.66rem; font-weight:700; padding:.2rem .6rem; border-radius:999px; border:1px solid #0056D2; background:#fff; color:#0056D2; }
.cc-badge.free { border-color:#16a34a; color:#16a34a; }
.cc-badge.part { border-color:#d97706; color:#b45309; }
.cc-body { padding:.9rem 1rem 1rem; display:flex; flex-direction:column; flex:1; }
.cc-cat { font-size:.64rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#0056D2; }
.cc-title { font-family:'Sora',sans-serif; font-size:.92rem; font-weight:600; line-height:1.4; color:#0F1114; margin:.25rem 0 .2rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.cc-ins { font-size:.75rem; color:#6B7280; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.cc-meta { display:flex; flex-wrap:wrap; font-size:.72rem; color:#6B7280; margin:.45rem 0 .85rem; }
.cc-meta span + span::before { content:'·'; margin:0 .4rem; color:#CBD5E1; }
.cc-foot { display:flex; align-items:center; justify-content:space-between; gap:.5rem; margin-top:auto; padding-top:.7rem; border-top:1px solid #F1F5F9; }
.cc-price { font-family:'Sora',sans-serif; font-weight:700; font-size:.9rem; color:#0F1114; }
.cc-price.free { color:#16a34a; }
.cc-go { font-size:.74rem; font-weight:600; color:#0056D2; }
.cc-go i { font-size:.62rem; margin-left:.15rem; transition:transform .2s; }
.cc:hover .cc-go i { transform:translateX(3px); }
.cc-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; }
@media (max-width:1100px) { .cc-grid { grid-template-columns:repeat(3,1fr); } }
@media (max-width:800px) { .cc-grid { grid-template-columns:repeat(2,1fr); } }
@media (max-width:520px) { .cc-grid { grid-template-columns:1fr; } }
</style>
@endpush
@endonce
