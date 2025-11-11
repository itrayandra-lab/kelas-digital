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
            </div>
        </div>
    </div>
@endforeach
