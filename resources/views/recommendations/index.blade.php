@extends('layouts.app')

@section('title', 'Recommended Articles - Ray Academy')

@section('content')
    <div class="bg-primary-500 py-16 md:py-24 text-center">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
                Recommendation for You
            </h1>
            <p class="text-lg text-white opacity-90 max-w-2xl mx-auto">
                Artikel pilihan editor khusus untuk Anda. Konten berkualitas yang dipilih dengan cermat untuk memberikan wawasan terbaik tentang kecantikan.
            </p>
        </div>
    </div>

    <section class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            @if($articles->isEmpty())
                <div class="text-center py-16">
                    <p class="text-xl text-gray-600">Belum ada artikel yang direkomendasikan saat ini.</p>
                    <p class="text-gray-500 mt-2">Kembali lagi nanti untuk melihat rekomendasi artikel terbaru kami.</p>
                    <a href="{{ route('home') }}"
                       class="inline-block mt-6 px-6 py-3 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition">
                        Kembali ke Beranda
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($articles as $article)
                        <div class="bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition">
                            <img src="{{ $article->thumbnail ? asset($article->thumbnail) : 'https://via.placeholder.com/600x400' }}"
                                 alt="{{ $article->title }}"
                                 class="w-full h-48 object-cover">
                            <div class="p-6">
                                @if($article->categories->isNotEmpty())
                                    <a href="{{ route('article.category', $article->categories->first()->slug) }}"
                                       class="inline-block text-xs font-bold uppercase tracking-widest text-primary-600 mb-2 hover:underline">
                                        {{ $article->categories->first()->name }}
                                    </a>
                                @endif
                                <p class="text-sm text-gray-500 mb-2">{{ $article->published_at->format('d M Y') }}</p>
                                <h2 class="text-xl font-bold text-gray-800 mb-3 line-clamp-2">
                                    <a href="{{ route('article.show', $article->slug) }}" class="hover:text-primary-600 transition">
                                        {{ $article->title }}
                                    </a>
                                </h2>
                                @if($article->excerpt)
                                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $article->excerpt }}</p>
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
                </div>

                {{-- Pagination --}}
                <div class="mt-12">
                    {{ $articles->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
