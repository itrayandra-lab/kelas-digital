{{-- Course card for the catalog, home and dashboard. Expects $course; uses lessons_count / students_count when loaded. --}}
@php
    $free = $course->isFreeClass();
    $level = ['Beginner' => 'Pemula', 'Intermediate' => 'Menengah', 'Advanced' => 'Mahir'][$course->level] ?? null;
@endphp
<a href="{{ route('course.show', $course->slug) }}" class="cc">
    <div class="cc-thumb">
        @if($course->thumbnail_url)
            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" loading="lazy" onerror="this.remove()">
        @endif
        <span class="cc-badge {{ $free ? 'free' : '' }}">{{ $free ? 'Kelas Gratis' : 'Premium' }}</span>
    </div>
    <div class="cc-body">
        @if($course->category)<span class="cc-cat">{{ $course->category->name }}</span>@endif
        <h3 class="cc-title">{{ $course->title }}</h3>
        <div class="cc-meta">
            @if($level)<span><i class="fas fa-signal"></i> {{ $level }}</span>@endif
            @if($course->lessons_count)<span><i class="fas fa-play-circle"></i> {{ $course->lessons_count }} materi</span>@endif
            @if($course->students_count)<span><i class="fas fa-user-graduate"></i> {{ $course->students_count }} peserta</span>@endif
        </div>
        <div class="cc-foot">
            <span class="cc-ins">{{ $course->instructor ?: 'Ray Academy' }}</span>
            <span class="cc-price {{ $free ? 'free' : '' }}">{{ $free ? 'Gratis' : 'Rp'.number_format($course->price, 0, ',', '.') }}</span>
        </div>
    </div>
</a>

@once
@push('styles')
<style>
.cc { background:#fff; border:1px solid var(--border); border-radius:14px; overflow:hidden; display:flex; flex-direction:column; text-decoration:none; color:inherit; transition:transform .25s, box-shadow .25s, border-color .25s; }
.cc:hover { transform:translateY(-4px); box-shadow:0 16px 36px rgba(10,22,40,.1); border-color:#93c5fd; }
.cc-thumb { position:relative; aspect-ratio:16/9; background:linear-gradient(135deg,#1474bc,#10b981); overflow:hidden; }
.cc-thumb img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .4s; }
.cc:hover .cc-thumb img { transform:scale(1.05); }
.cc-badge { position:absolute; top:.65rem; left:.65rem; font-size:.66rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; padding:.28rem .6rem; border-radius:6px; background:#dbeafe; color:#1d4ed8; }
.cc-badge.free { background:#dcfce7; color:#15803d; }
.cc-body { padding:1rem 1.1rem 1.1rem; display:flex; flex-direction:column; flex:1; }
.cc-cat { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--blue); }
.cc-title { font-family:'Sora',sans-serif; font-size:.95rem; font-weight:700; line-height:1.4; color:var(--ink); margin:.3rem 0 .55rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.cc-meta { display:flex; flex-wrap:wrap; gap:.35rem .8rem; font-size:.74rem; color:var(--muted); }
.cc-meta i { color:#94a3b8; margin-right:.15rem; }
.cc-foot { display:flex; align-items:center; gap:.5rem; margin-top:auto; padding-top:.8rem; border-top:1px solid var(--border); margin-top:.9rem; }
.cc-ins { flex:1; font-size:.75rem; color:var(--muted); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.cc-price { font-family:'Sora',sans-serif; font-weight:800; font-size:.92rem; color:var(--ink); }
.cc-price.free { color:var(--accent); }
.cc-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:1.25rem; }
@media (max-width:560px) { .cc-grid { grid-template-columns:1fr; } }
</style>
@endpush
@endonce
