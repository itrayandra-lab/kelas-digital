@foreach($articles as $article)
    <div class="bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition">
        <a href="{{ route('article.show', $article->slug) }}">
            <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : 'https://via.placeholder.com/600x400.png' }}" alt="{{ $article->title }}"
                class="w-full h-48 object-cover">
        </a>
        <div class="p-6">
            @if ($article->categories->isNotEmpty())
                @php $primaryCategory = $article->categories->first(); @endphp
                <a href="{{ route('article.category', $primaryCategory->slug) }}" class="inline-block text-xs font-bold uppercase tracking-widest text-primary-600 mb-2 hover:underline">
                    {{ $primaryCategory->name }}
                </a>
            @endif
            <p class="text-sm text-gray-500 mb-2">{{ $article->published_at->format('d M Y') }}</p>
            <h2 class="text-xl font-bold text-gray-800 mb-3 line-clamp-2">
                <a href="{{ route('article.show', $article->slug) }}" class="hover:text-primary-600 transition">
                    {{ $article->title }}
                </a>
            </h2>
            @if ($article->excerpt)
                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $article->excerpt }}</p>
            @elseif ($article->content)
                <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ strip_tags($article->content) }}</p>
            @endif
            <div class="flex items-center justify-between">
                <a href="{{ route('article.show', $article->slug) }}"
                    class="font-semibold text-primary-600 hover:underline text-sm">
                    Baca Selengkapnya
                </a>
                <span class="flex items-center gap-1 text-gray-400 text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                        <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-7.893-2.66-9.336-6.41z" clip-rule="evenodd" />
                    </svg>
                    {{ $article->formatted_views }}
                </span>
            </div>
        </div>
    </div>
@endforeach
