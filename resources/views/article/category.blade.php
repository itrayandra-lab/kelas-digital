@extends('layouts.app')

@section('title', $category->name . ' — Ray Academy')

@push('styles')
<style>
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
.art-card-thumb-img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .45s ease; }
.art-card:hover .art-card-thumb-img { transform:scale(1.06); }
.art-card-thumb-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#dbeafe,#eff6ff); }
.art-card-badge {
    position:absolute; top:.75rem; left:.75rem; z-index:1;
    font-size:.63rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em;
    background:#fff; color:var(--blue); padding:.3rem .7rem; border-radius:6px; cursor:pointer;
}
.art-card-body { padding:1.25rem; display:flex; flex-direction:column; flex:1; }
.art-card-date { font-size:.72rem; color:var(--muted); margin-bottom:.4rem; }
.art-card-title { font-family:'Sora',sans-serif; font-size:.9375rem; font-weight:700; color:var(--ink); line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; transition:color .18s; }
.art-card:hover .art-card-title { color:var(--blue); }
.art-card-excerpt { font-size:.8rem; color:var(--muted); line-height:1.65; margin-top:.5rem; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; flex:1; }
.art-card-footer { display:flex; align-items:center; justify-content:space-between; margin-top:.9rem; padding-top:.9rem; border-top:1px solid var(--border); }
.art-card-read { font-size:.78rem; font-weight:700; color:var(--blue); }
.art-card-views { display:flex; align-items:center; gap:.3rem; font-size:.72rem; color:var(--muted); }
.articles-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1.25rem; }
@media(max-width:640px) { .articles-grid { grid-template-columns:1fr; } }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="page-hero">
    <div class="page-hero-inner">
        <span class="page-hero-label">Kategori Artikel</span>
        <h1>{{ $category->name }}</h1>
        <p>
            @if($category->description)
                {{ $category->description }}
            @else
                Jelajahi artikel dalam kategori {{ $category->name }}
            @endif
        </p>
    </div>
</section>

{{-- Content --}}
<section style="background:var(--surf); padding:4rem 0 6rem;">
    <div style="max-width:1280px;margin:0 auto;padding:0 1.5rem;">

        <nav class="breadcrumb">
            <a href="{{ route('home') }}">Beranda</a>
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('article.index') }}">Artikel</a>
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>{{ $category->name }}</span>
        </nav>

        @if($articles->isEmpty())
            <div class="empty-state rv">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <h3>Belum ada artikel</h3>
                <p>Belum ada artikel dalam kategori ini. Kembali lagi nanti.</p>
                <a href="{{ route('article.index') }}" style="display:inline-block;margin-top:1.25rem;padding:.65rem 1.5rem;background:var(--blue);color:#fff;border-radius:10px;font-weight:700;font-size:.875rem;text-decoration:none;">Lihat Semua Artikel</a>
            </div>
        @else
            <div x-data="articleLoader('{{ $category->slug }}')" class="space-y-8">
                <div id="articles-container" class="articles-grid">
                    @include('article.partials.articles', ['articles' => $articles])
                </div>

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
<style>@keyframes spin { to { transform:rotate(360deg); } }</style>
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
const obs = new IntersectionObserver(e => { e.forEach(x => { if(x.isIntersecting){ x.target.classList.add('in'); obs.unobserve(x.target); }}); }, { threshold:0.07, rootMargin:'0px 0px -40px 0px' });
document.querySelectorAll('.rv').forEach(el => obs.observe(el));
</script>
@endpush