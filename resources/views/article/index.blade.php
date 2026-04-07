@extends('layouts.app')

@section('title', 'Semua Artikel — Ray Academy')

@push('styles')
<style>
/* ── Article card ── */
.art-card {
    display:flex; flex-direction:column;
    background:#fff; border:1.5px solid var(--border); border-radius:16px;
    overflow:hidden; text-decoration:none; color:inherit;
    transition:transform .3s ease, box-shadow .3s ease, border-color .25s;
}
.art-card:hover { transform:translateY(-5px); box-shadow:0 20px 48px rgba(10,22,40,.09); border-color:#93c5fd; }

.art-card-thumb-wrap {
    position:relative; overflow:hidden;
    height:200px; background:linear-gradient(135deg,#dbeafe,#eff6ff); flex-shrink:0;
}
.art-card-thumb-img {
    width:100%; height:100%; object-fit:cover; display:block;
    transition:transform .45s ease;
}
.art-card:hover .art-card-thumb-img { transform:scale(1.06); }
.art-card-thumb-placeholder {
    width:100%; height:100%; display:flex; align-items:center; justify-content:center;
    background:linear-gradient(135deg,#dbeafe,#eff6ff);
}
.art-card-badge {
    position:absolute; top:.75rem; left:.75rem; z-index:1;
    font-size:.63rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em;
    background:#fff; color:var(--blue); padding:.3rem .7rem; border-radius:6px;
    cursor:pointer; transition:background .18s;
}
.art-card-badge:hover { background:var(--blue-xl); }

.art-card-body { padding:1.25rem; display:flex; flex-direction:column; flex:1; }
.art-card-date { font-size:.72rem; color:var(--muted); margin-bottom:.4rem; }
.art-card-title {
    font-family:'Sora',sans-serif; font-size:.9375rem; font-weight:700; color:var(--ink);
    line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
    transition:color .18s;
}
.art-card:hover .art-card-title { color:var(--blue); }
.art-card-excerpt {
    font-size:.8rem; color:var(--muted); line-height:1.65; margin-top:.5rem;
    display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;
    flex:1;
}
.art-card-footer {
    display:flex; align-items:center; justify-content:space-between;
    margin-top:.9rem; padding-top:.9rem; border-top:1px solid var(--border);
}
.art-card-read { font-size:.78rem; font-weight:700; color:var(--blue); }
.art-card-views { display:flex; align-items:center; gap:.3rem; font-size:.72rem; color:var(--muted); }

/* ── Category filter chips ── */
.cat-chips { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:2.5rem; }
.cat-chip {
    font-family:'DM Sans',sans-serif; font-size:.8rem; font-weight:600;
    padding:.45rem 1.05rem; border-radius:9px; border:1.5px solid var(--border);
    background:#fff; color:var(--ink-2); cursor:pointer; transition:all .18s;
    text-decoration:none; display:inline-block;
}
.cat-chip:hover { border-color:var(--blue); color:var(--blue); background:var(--blue-xl); }
.cat-chip.active { background:var(--blue); color:#fff; border-color:var(--blue); box-shadow:0 4px 14px rgba(20,116,188,.25); }

/* ── Articles grid ── */
.articles-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1.25rem; }
@media(max-width:640px) { .articles-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="page-hero">
    <div class="page-hero-inner">
        <span class="page-hero-label">Artikel & Tips</span>
        <h1>Semua Artikel</h1>
        <p>Konten berkualitas dari instruktur & ahli kami untuk tingkatkan pengetahuanmu setiap hari.</p>
    </div>
</section>

{{-- Content --}}
<section style="background:var(--surf); padding:4rem 0 6rem;">
    <div style="max-width:1280px;margin:0 auto;padding:0 1.5rem;">

        {{-- Breadcrumb --}}
        <nav class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <svg class="breadcrumb-sep" style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>Artikel</span>
        </nav>

        {{-- Category filter --}}
        @if(isset($articleCategories) && $articleCategories->count())
        <div class="cat-chips rv">
            <a href="{{ route('article.index') }}" class="cat-chip {{ !request('category') ? 'active' : '' }}">Semua</a>
            @foreach($articleCategories as $cat)
                <a href="{{ route('article.category', $cat->slug) }}"
                   class="cat-chip {{ request()->is('*/'.$cat->slug.'*') ? 'active' : '' }}">
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>
        @endif

        @if($articles->isEmpty())
            <div class="empty-state rv">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <h3>Belum ada artikel</h3>
                <p>Kembali lagi nanti untuk membaca artikel terbaru kami.</p>
            </div>
        @else
            <div x-data="articleLoader()" class="space-y-8">
                <div id="articles-container" class="articles-grid">
                    @include('article.partials.articles', ['articles' => $articles])
                </div>

                {{-- Load More --}}
                @if($articles->hasMorePages())
                <div style="text-align:center;margin-top:3rem;" class="rv">
                    <button @click="loadMore()" :disabled="loading" class="btn-load-more">
                        <svg x-show="loading" style="width:16px;height:16px;animation:spin 1s linear infinite;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span x-show="!loading">Muat Lebih Banyak</span>
                        <span x-show="loading">Memuat...</span>
                    </button>
                </div>
                @endif
            </div>
        @endif

    </div>
</section>

@endsection

@push('scripts')
<style>
@keyframes spin { to { transform:rotate(360deg); } }
</style>
<script>
function articleLoader(categorySlug = null) {
    return {
        loading: false,
        page: {{ $articles->currentPage() + 1 }},
        hasMore: {{ $articles->hasMorePages() ? 'true' : 'false' }},
        categorySlug: categorySlug,
        async loadMore() {
            if (this.loading || !this.hasMore) return;
            this.loading = true;
            try {
                const params = { page: this.page };
                if (this.categorySlug) params.category_slug = this.categorySlug;
                const response = await axios.get('{{ route("article.load-more") }}', { params });
                const data = response.data;
                document.getElementById('articles-container').insertAdjacentHTML('beforeend', data.articles_html);
                if (data.has_more) { this.page++; } else { this.hasMore = false; }
            } catch(e) { console.error(e); }
            finally { this.loading = false; }
        }
    }
}

// Scroll reveal
const obs = new IntersectionObserver((entries) => {
    entries.forEach(e => { if(e.isIntersecting){ e.target.classList.add('in'); obs.unobserve(e.target); } });
}, { threshold: 0.07, rootMargin:'0px 0px -40px 0px' });
document.querySelectorAll('.rv').forEach(el => obs.observe(el));
</script>
@endpush