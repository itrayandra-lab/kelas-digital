{{-- Article card in the same style as the course card. Expects $article with categories loaded. --}}
@php $category = $activeCategory ?? $article->categories->first(); @endphp
<a href="{{ route('article.show', $article->slug) }}" class="ac">
    <div class="ac-thumb">
        @if($article->thumbnail_url)
            <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" loading="lazy" onerror="this.remove()">
        @endif
        <i class="far fa-file-alt" aria-hidden="true"></i>
    </div>
    <div class="ac-body">
        <div class="ac-top">
            @if($category)<span class="ac-cat">{{ $category->name }}</span>@endif
            @if($article->published_at)<span class="ac-date">{{ $article->published_at->isoFormat('D MMM YYYY') }}</span>@endif
        </div>
        <h3 class="ac-title">{{ $article->title }}</h3>
        <p class="ac-excerpt">{{ $article->excerpt ?: Str::limit(strip_tags((string) $article->content), 140) }}</p>
        <div class="ac-foot">
            <span class="ac-go">Baca artikel <i class="fas fa-arrow-right"></i></span>
            @if($article->views_count)<span class="ac-views"><i class="far fa-eye"></i> {{ $article->formatted_views }}</span>@endif
        </div>
    </div>
</a>

@once
@push('styles')
<style>
.ac { background:#fff; border:1px solid #e3ecf8; border-radius:18px; box-shadow:0 10px 28px rgba(31,77,140,.08); overflow:hidden; display:flex; flex-direction:column; text-decoration:none; color:inherit; transition:box-shadow .2s, transform .2s; }
.ac:hover { box-shadow:0 18px 40px rgba(31,77,140,.16); transform:translateY(-3px); }
.ac-thumb { position:relative; aspect-ratio:16/9; overflow:hidden; background:linear-gradient(135deg,#EEF4FF,#DBEAFE); display:flex; align-items:center; justify-content:center; }
.ac-thumb i { font-size:1.6rem; color:#1f6fd1; opacity:.25; }
.ac-thumb img { position:absolute; inset:0; z-index:1; width:100%; height:100%; object-fit:cover; transition:transform .3s; }
.ac:hover .ac-thumb img { transform:scale(1.04); }
.ac-body { padding:.9rem 1rem 1rem; display:flex; flex-direction:column; flex:1; }
.ac-top { display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
.ac-cat { font-size:.64rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:#1f6fd1; }
.ac-date { font-size:.7rem; color:#9CA3AF; }
.ac-cat + .ac-date::before { content:'·'; margin-right:.5rem; color:#CBD5E1; }
.ac-title { font-family:'Sora',sans-serif; font-size:.92rem; font-weight:600; line-height:1.4; color:#0b1f4d; margin:.3rem 0 .35rem; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.ac:hover .ac-title { color:#1f6fd1; }
.ac-excerpt { font-size:.78rem; line-height:1.6; color:#6B7280; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; margin-bottom:.85rem; }
.ac-foot { display:flex; align-items:center; justify-content:space-between; gap:.5rem; margin-top:auto; padding-top:.7rem; border-top:1px solid #F1F5F9; }
.ac-go { font-size:.74rem; font-weight:600; color:#1f6fd1; }
.ac-go i { font-size:.62rem; margin-left:.15rem; transition:transform .2s; }
.ac:hover .ac-go i { transform:translateX(3px); }
.ac-views { font-size:.72rem; color:#9CA3AF; }
.ac-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
@media (max-width:1100px) { .ac-grid { grid-template-columns:repeat(2,1fr); } }
@media (max-width:560px) { .ac-grid { grid-template-columns:1fr; } }
</style>
@endpush
@endonce
