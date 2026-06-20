{{-- resources/views/article/partials/articles.blade.php --}}
{{-- Usage: @include('article.partials.articles', ['articles' => $articles]) --}}

@foreach($articles as $article)
<a href="{{ route('article.show', $article->slug) }}" class="art-card rv rv-d{{ min(($loop->index % 4) + 1, 4) }}">

    {{-- Thumbnail --}}
    <div class="art-card-thumb-wrap">
        @if($article->thumbnail ?? $article->cover_image ?? null)
            <img src="{{ asset($article->thumbnail ?? $article->cover_image) }}"
                 alt="{{ $article->title }}"
                 class="art-card-thumb-img"
                 loading="lazy"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="art-card-thumb-placeholder" style="display:none;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:36px;height:36px;color:var(--blue);opacity:.25;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        @else
            <div class="art-card-thumb-placeholder">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:36px;height:36px;color:var(--blue);opacity:.25;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        @endif

        {{-- Category badge over image --}}
        @if($article->categories->isNotEmpty())
            @php $cat = $article->categories->first(); @endphp
            <span class="art-card-badge" onclick="event.preventDefault();window.location='{{ route('article.category', $cat->slug) }}'">
                {{ $cat->name }}
            </span>
        @endif
    </div>

    {{-- Body --}}
    <div class="art-card-body">
        <p class="art-card-date">
            @if($article->published_at) {{ $article->published_at->isoFormat('D MMM YYYY') }} @endif
        </p>

        <h2 class="art-card-title">{{ $article->title }}</h2>

        @if($article->excerpt)
            <p class="art-card-excerpt">{{ $article->excerpt }}</p>
        @elseif($article->content ?? null)
            <p class="art-card-excerpt">{{ Str::limit(strip_tags($article->content), 110) }}</p>
        @endif

        <div class="art-card-footer">
            <span class="art-card-read">Baca Selengkapnya →</span>
            @if($article->formatted_views ?? $article->views_count ?? null)
            <span class="art-card-views">
                <svg viewBox="0 0 20 20" fill="currentColor" style="width:12px;height:12px;">
                    <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z"/>
                    <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41z" clip-rule="evenodd"/>
                </svg>
                {{ $article->formatted_views ?? number_format($article->views_count ?? 0) }}
            </span>
            @endif
        </div>
    </div>
</a>
@endforeach