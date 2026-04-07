{{-- ============================================================
     resources/views/tag/index.blade.php — Browse Topics
     ============================================================ --}}
@extends('layouts.app')

@section('seo')
    <title>Browse Topik — Ray Academy</title>
    <meta name="description" content="Jelajahi semua topik dan tag artikel di Ray Academy.">
@endsection

@push('styles')
<style>
.topic-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:1rem; }
@media(max-width:640px) { .topic-grid { grid-template-columns:1fr 1fr; } }
@media(max-width:400px) { .topic-grid { grid-template-columns:1fr; } }

.topic-card {
    background:#fff; border:1.5px solid var(--border); border-radius:14px;
    padding:1.35rem 1.5rem; text-decoration:none; color:inherit;
    display:flex; align-items:center; justify-content:space-between; gap:1rem;
    transition:all .25s;
}
.topic-card:hover {
    border-color:var(--blue); background:var(--blue-xl);
    transform:translateY(-3px); box-shadow:0 12px 32px rgba(20,116,188,.12);
}
.topic-card-name { font-family:'Sora',sans-serif; font-size:.95rem; font-weight:700; color:var(--ink); transition:color .18s; }
.topic-card:hover .topic-card-name { color:var(--blue); }
.topic-card-count { font-size:.75rem; color:var(--muted); margin-top:.2rem; }
.topic-card-arrow { flex-shrink:0; width:32px; height:32px; border-radius:9px; background:var(--surf); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; transition:all .18s; }
.topic-card:hover .topic-card-arrow { background:var(--blue); border-color:var(--blue); }
.topic-card:hover .topic-card-arrow svg { color:#fff; }
.topic-card-arrow svg { width:14px; height:14px; color:var(--muted); }
</style>
@endpush

@section('content')

<section class="page-hero">
    <div class="page-hero-inner">
        <span class="page-hero-label">Topik & Tag</span>
        <h1>Browse Topik</h1>
        <p>Temukan artikel berdasarkan topik yang kamu minati</p>
    </div>
</section>

<section style="background:var(--surf);padding:4rem 0 6rem;">
    <div style="max-width:1280px;margin:0 auto;padding:0 1.5rem;">

        <nav class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>Topik</span>
        </nav>

        @if($tags->isNotEmpty())
            <div class="topic-grid">
                @foreach($tags as $i => $tag)
                <a href="{{ route('tag.show', $tag->slug) }}" class="topic-card rv rv-d{{ min($i % 4 + 1, 4) }}">
                    <div>
                        <div class="topic-card-name">{{ $tag->name }}</div>
                        <div class="topic-card-count">{{ $tag->articles_count }} {{ $tag->articles_count === 1 ? 'artikel' : 'artikel' }}</div>
                    </div>
                    <div class="topic-card-arrow">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </a>
                @endforeach
            </div>
        @else
            <div class="empty-state rv">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                <h3>Belum ada topik</h3>
                <p>Topik akan tersedia segera.</p>
            </div>
        @endif
    </div>
</section>

@endsection

@push('scripts')
<script>
const obs = new IntersectionObserver(e => { e.forEach(x => { if(x.isIntersecting){ x.target.classList.add('in'); obs.unobserve(x.target); }}); }, { threshold:0.07, rootMargin:'0px 0px -40px 0px' });
document.querySelectorAll('.rv').forEach(el => obs.observe(el));
</script>
@endpush