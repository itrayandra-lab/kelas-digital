@extends('layouts.app')

@section('seo')
    {!! seo($article) !!}
@endsection

@push('styles')
<style>
/* ── Article Hero ── */
.art-hero {
    background: var(--ink); padding: 4.5rem 0 0;
    position: relative; overflow: hidden;
}
.art-hero::before {
    content:''; position:absolute; inset:0;
    background: radial-gradient(ellipse 70% 70% at 80% 30%, rgba(20,116,188,.3), transparent 65%);
    pointer-events:none;
}
.art-hero::after {
    content:''; position:absolute; inset:0;
    background-image: radial-gradient(rgba(255,255,255,.05) 1px, transparent 1px);
    background-size: 28px 28px; pointer-events:none;
}
.art-hero-inner { position:relative; z-index:1; max-width:800px; margin:0 auto; padding:0 1.5rem 4rem; text-align:center; }
.art-hero-cats { display:flex; gap:.5rem; flex-wrap:wrap; justify-content:center; margin-bottom:1rem; }
.art-hero-cat {
    font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em;
    background:rgba(255,255,255,.1); color:rgba(255,255,255,.8);
    padding:.3rem .8rem; border-radius:999px; text-decoration:none;
    transition: background .18s;
}
.art-hero-cat:hover { background:rgba(255,255,255,.18); }
.art-hero h1 {
    font-family:'Sora',sans-serif;
    font-size:clamp(1.75rem,4vw,2.75rem); font-weight:800; color:#fff;
    line-height:1.2; letter-spacing:-.025em; margin-bottom:1.25rem;
}
.art-hero-meta {
    display:flex; align-items:center; gap:1rem; justify-content:center; flex-wrap:wrap;
    font-size:.8rem; color:rgba(255,255,255,.55);
}
.art-hero-meta strong { color:rgba(255,255,255,.8); font-weight:600; }
.art-hero-meta-sep { width:3px; height:3px; border-radius:50%; background:rgba(255,255,255,.3); }

/* ── Article Body ── */
.art-body-wrap { max-width:1280px; margin:0 auto; padding:0 1.5rem; }
.art-layout { display:grid; grid-template-columns:1fr 300px; gap:4rem; padding:4rem 0 5rem; align-items:start; }
@media(max-width:1024px) { .art-layout { grid-template-columns:1fr; gap:3rem; } }

/* Thumbnail */
.art-thumb-wrap { border-radius:18px; overflow:hidden; margin-bottom:2.5rem; box-shadow:0 20px 60px rgba(10,22,40,.12); }
.art-thumb-wrap img { width:100%; max-height:480px; object-fit:cover; display:block; }

/* Rich text content */
.rich-text {
    font-family:'DM Sans',sans-serif;
    font-size:1.0625rem; line-height:1.85; color:var(--ink-2);
}
.rich-text .excerpt-block {
    font-size:1.15rem; font-weight:500; color:var(--ink);
    font-style:italic; border-left:4px solid var(--blue);
    padding-left:1.25rem; margin-bottom:2rem;
    line-height:1.7;
}
.rich-text h2 { font-family:'Sora',sans-serif; font-size:1.5rem; font-weight:800; color:var(--ink); margin:2.5rem 0 .9rem; letter-spacing:-.02em; }
.rich-text h3 { font-family:'Sora',sans-serif; font-size:1.2rem; font-weight:700; color:var(--ink); margin:2rem 0 .75rem; }
.rich-text p { margin-bottom:1.35rem; }
.rich-text ul, .rich-text ol { margin:1rem 0 1.5rem 1.5rem; }
.rich-text li { margin-bottom:.5rem; }
.rich-text img { width:100%; border-radius:12px; margin:1.5rem 0; }
.rich-text a { color:var(--blue); text-decoration:underline; }
.rich-text blockquote {
    border-left:4px solid var(--blue); padding:1rem 1.5rem;
    background:var(--blue-xl); border-radius:0 12px 12px 0; margin:2rem 0; color:var(--ink-2);
}

/* ── Footer bar ── */
.art-footer-bar {
    display:flex; align-items:center; justify-content:space-between; gap:1.5rem;
    padding:1.5rem 0; border-top:1.5px solid var(--border); margin-top:3rem;
    flex-wrap:wrap;
}
.art-tags { display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
.art-tag {
    font-size:.72rem; font-weight:700; padding:.3rem .8rem; border-radius:999px;
    background:var(--blue-xl); color:var(--blue); text-decoration:none; transition:background .18s;
}
.art-tag:hover { background:#bfdbfe; }
.art-share { display:flex; align-items:center; gap:.75rem; }
.art-share-label { font-size:.78rem; font-weight:600; color:var(--ink-2); white-space:nowrap; }
.art-share a {
    width:34px; height:34px; border-radius:9px; border:1.5px solid var(--border);
    display:flex; align-items:center; justify-content:center;
    color:var(--muted); font-size:.85rem; text-decoration:none; transition:all .18s;
}
.art-share a:hover { border-color:var(--blue); color:var(--blue); background:var(--blue-xl); }

/* ── Sidebar ── */
.art-sidebar { display:flex; flex-direction:column; gap:1.5rem; }
.sidebar-card { background:#fff; border:1.5px solid var(--border); border-radius:16px; overflow:hidden; }
.sidebar-card-head { padding:1.1rem 1.25rem; border-bottom:1px solid var(--border); }
.sidebar-card-head h3 { font-family:'Sora',sans-serif; font-size:.875rem; font-weight:700; color:var(--ink); }
.sidebar-card-body { padding:1.1rem 1.25rem; }

/* Related article mini card */
.rel-art {
    display:flex; gap:.75rem; align-items:flex-start; text-decoration:none; color:inherit;
    padding:.65rem 0; border-bottom:1px solid var(--border); transition:opacity .18s;
}
.rel-art:last-child { border-bottom:none; }
.rel-art:hover { opacity:.75; }
.rel-art-thumb { width:64px; height:52px; border-radius:8px; object-fit:cover; flex-shrink:0; background:var(--border); }
.rel-art-title { font-family:'Sora',sans-serif; font-size:.78rem; font-weight:700; color:var(--ink); line-height:1.35; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.rel-art-date { font-size:.67rem; color:var(--muted); margin-top:.25rem; }

/* ── Related articles section ── */
.related-section { background:#fff; padding:4rem 0; border-top:1.5px solid var(--border); }
.related-section .articles-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:1.25rem; }

.art-card { display:flex; flex-direction:column; background:#fff; border:1.5px solid var(--border); border-radius:16px; overflow:hidden; text-decoration:none; color:inherit; transition:transform .3s ease, box-shadow .3s ease, border-color .25s; }
.art-card:hover { transform:translateY(-5px); box-shadow:0 20px 48px rgba(10,22,40,.09); border-color:#93c5fd; }
.art-card-thumb-wrap { position:relative; overflow:hidden; height:180px; background:linear-gradient(135deg,#dbeafe,#eff6ff); flex-shrink:0; }
.art-card-thumb-img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .45s; }
.art-card:hover .art-card-thumb-img { transform:scale(1.06); }
.art-card-thumb-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; }
.art-card-badge { position:absolute; top:.65rem; left:.65rem; z-index:1; font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; background:#fff; color:var(--blue); padding:.25rem .65rem; border-radius:5px; cursor:pointer; }
.art-card-body { padding:1.1rem; display:flex; flex-direction:column; flex:1; }
.art-card-date { font-size:.7rem; color:var(--muted); margin-bottom:.35rem; }
.art-card-title { font-family:'Sora',sans-serif; font-size:.875rem; font-weight:700; color:var(--ink); line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; transition:color .18s; }
.art-card:hover .art-card-title { color:var(--blue); }
.art-card-excerpt { font-size:.78rem; color:var(--muted); line-height:1.6; margin-top:.4rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; flex:1; }
.art-card-footer { display:flex; align-items:center; justify-content:space-between; margin-top:.85rem; padding-top:.85rem; border-top:1px solid var(--border); }
.art-card-read { font-size:.75rem; font-weight:700; color:var(--blue); }
.art-card-views { display:flex; align-items:center; gap:.25rem; font-size:.7rem; color:var(--muted); }
</style>
@endpush

@section('content')

{{-- Article Hero --}}
<section class="art-hero">
    <div class="art-hero-inner">
        {{-- Breadcrumb di hero --}}
        <nav style="display:flex;align-items:center;gap:.4rem;flex-wrap:wrap;justify-content:center;font-size:.78rem;color:rgba(255,255,255,.5);margin-bottom:1.25rem;">
            <a href="{{ route('home') }}" style="color:rgba(255,255,255,.55);text-decoration:none;transition:color .18s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.55)'">Beranda</a>
            <svg style="width:12px;height:12px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('article.index') }}" style="color:rgba(255,255,255,.55);text-decoration:none;transition:color .18s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.55)'">Artikel</a>
            <svg style="width:12px;height:12px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span style="color:rgba(255,255,255,.75);">{{ Str::limit($article->title, 45) }}</span>
        </nav>
        @if($article->categories->isNotEmpty())
        <div class="art-hero-cats">
            @foreach($article->categories as $cat)
                <a href="{{ route('article.category', $cat->slug) }}" class="art-hero-cat">{{ $cat->name }}</a>
            @endforeach
        </div>
        @endif

        <h1>{{ $article->title }}</h1>

        <div class="art-hero-meta">
            <span>Oleh <strong>{{ $article->author ?? 'Admin' }}</strong></span>
            <span class="art-hero-meta-sep"></span>
            <span>{{ $article->published_at->isoFormat('D MMMM YYYY') }}</span>
            @if($article->formatted_views ?? $article->views_count ?? null)
            <span class="art-hero-meta-sep"></span>
            <span style="display:flex;align-items:center;gap:.35rem;">
                <svg viewBox="0 0 20 20" fill="currentColor" style="width:13px;height:13px;">
                    <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/>
                    <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41z" clip-rule="evenodd"/>
                </svg>
                {{ $article->formatted_views ?? number_format($article->views_count ?? 0) }}
            </span>
            @endif
        </div>
    </div>
</section>

{{-- Article Body --}}
<div style="background:var(--surf);">
    <div class="art-body-wrap">
        <div class="art-layout">

            {{-- Main Content --}}
            <article>

                {{-- Thumbnail --}}
                @if($article->thumbnail)
                    <div class="art-thumb-wrap" style="margin-top:2rem;">
                        <img src="{{ asset('storage/' . $article->thumbnail) }}"
                             alt="{{ $article->title }}"
                             onerror="this.parentElement.style.display='none'">
                    </div>
                @endif

                {{-- Content --}}
                <div class="rich-text">
                    @if(!empty($article->excerpt))
                        <p class="excerpt-block">{{ $article->excerpt }}</p>
                    @endif
                    {!! $article->processed_content !!}
                </div>

                {{-- Footer: tags + share --}}
                <div class="art-footer-bar">
                    @if($article->tags->isNotEmpty())
                    <div class="art-tags">
                        <span style="font-size:.78rem;font-weight:600;color:var(--ink-2);">Tags:</span>
                        @foreach($article->tags as $tag)
                            <a href="{{ route('tag.show', $tag->slug) }}" class="art-tag">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                    @endif

                    <div class="art-share">
                        <span class="art-share-label">Bagikan:</span>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}" target="_blank" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . url()->current()) }}" target="_blank" aria-label="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </article>

            {{-- Sidebar --}}
            <aside class="art-sidebar" style="position:sticky;top:88px;">

                {{-- Related articles in sidebar --}}
                @if(isset($relatedArticles) && $relatedArticles->isNotEmpty())
                <div class="sidebar-card rv">
                    <div class="sidebar-card-head">
                        <h3>Artikel Terkait</h3>
                    </div>
                    <div class="sidebar-card-body" style="padding:.75rem 1.1rem;">
                        @foreach($relatedArticles->take(4) as $rel)
                        <a href="{{ route('article.show', $rel->slug) }}" class="rel-art">
                            @if($rel->thumbnail ?? $rel->cover_image ?? null)
                                <img src="{{ asset('storage/' . ($rel->thumbnail ?? $rel->cover_image)) }}" alt="{{ $rel->title }}" class="rel-art-thumb" onerror="this.style.background='var(--border)'">
                            @else
                                <div class="rel-art-thumb" style="display:flex;align-items:center;justify-content:center;background:var(--blue-xl);">
                                    <svg style="width:20px;height:20px;color:var(--blue);opacity:.4;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                            @endif
                            <div style="flex:1;min-width:0;">
                                <div class="rel-art-title">{{ $rel->title }}</div>
                                @if($rel->published_at)
                                    <div class="rel-art-date">{{ $rel->published_at->isoFormat('D MMM YYYY') }}</div>
                                @endif
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Categories --}}
                @if(isset($articleCategories) && $articleCategories->count())
                <div class="sidebar-card rv rv-d2">
                    <div class="sidebar-card-head"><h3>Kategori</h3></div>
                    <div class="sidebar-card-body" style="display:flex;flex-direction:column;gap:.4rem;">
                        @foreach($articleCategories->take(8) as $cat)
                        <a href="{{ route('article.category', $cat->slug) }}"
                           style="display:flex;align-items:center;justify-content:space-between;padding:.55rem .75rem;border-radius:9px;background:var(--surf);text-decoration:none;font-size:.83rem;color:var(--ink-2);font-weight:500;transition:all .18s;"
                           onmouseover="this.style.background='var(--blue-xl)';this.style.color='var(--blue)'"
                           onmouseout="this.style.background='var(--surf)';this.style.color='var(--ink-2)'">
                            {{ $cat->name }}
                            <svg style="width:13px;height:13px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

            </aside>
        </div>
    </div>
</div>

{{-- Related articles bottom (mobile-friendly grid) --}}
@if(isset($relatedArticles) && $relatedArticles->isNotEmpty())
<section class="related-section">
    <div style="max-width:1280px;margin:0 auto;padding:0 1.5rem;">
        <div style="margin-bottom:2rem;" class="rv">
            <span class="stag">Artikel Terkait</span>
            <h2 style="font-family:'Sora',sans-serif;font-size:1.6rem;font-weight:800;color:var(--ink);letter-spacing:-.02em;margin-top:.4rem;">Baca Juga</h2>
        </div>
        <div class="articles-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1.25rem;">
            @include('article.partials.articles', ['articles' => $relatedArticles])
        </div>
    </div>
</section>
@endif

@endsection

@push('scripts')
<script>
const obs = new IntersectionObserver(e => { e.forEach(x => { if(x.isIntersecting){ x.target.classList.add('in'); obs.unobserve(x.target); }}); }, { threshold:0.07 });
document.querySelectorAll('.rv').forEach(el => obs.observe(el));
</script>
@endpush