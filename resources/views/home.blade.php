@extends('layouts.app')

@section('title', 'Beautyversity.id - Where Beauty Meets Science | Platform Edukasi Kecantikan Berbasis Bukti')

@section('content')
    {{-- Hero Slider Section --}}
    <section id="hero-slider" class="splide" aria-label="Featured Articles">
        <div class="splide__track">
            <ul class="splide__list">
                @foreach($heroArticles as $article)
                    <li class="splide__slide">
                        <div class="relative bg-gray-900 h-screen flex items-center">
                            <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : 'https://via.placeholder.com/1920x600' }}"
                                 alt="{{ $article->title }}"
                                 class="absolute inset-0 w-full h-full object-cover">
                                 <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-transparent"></div>
                            <div class="relative container mx-auto px-4 sm:px-6 lg:px-8 z-10">
                                <div class="max-w-3xl">
                                    @if($article->categories->isNotEmpty())
                                        <span class="inline-block px-3 py-1 bg-primary-600 text-white text-xs font-bold uppercase tracking-wider rounded-full mb-4">
                                            {{ $article->categories->first()->name }}
                                        </span>
                                    @endif
                                    <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">
                                        {{ $article->title }}
                                    </h2>
                                    @if($article->excerpt)
                                        <p class="text-lg text-gray-200 mb-6 line-clamp-2">
                                            {{ $article->excerpt }}
                                        </p>
                                    @endif
                                    <div class="flex items-center gap-4 text-sm text-gray-300 mb-6">
                                        <span>{{ $article->author }}</span>
                                        <span>•</span>
                                        <span>{{ $article->published_at->format('d M Y') }}</span>
                                    </div>
                                    <a href="{{ route('article.show', $article->slug) }}"
                                       class="inline-block px-6 py-3 bg-white text-primary-600 font-semibold rounded-md hover:bg-gray-100 transition">
                                        Baca Artikel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Latest Article Section --}}
    <section id="latest-articles" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Latest Article</h2>
                <p class="text-gray-600">Artikel terbaru dari Beautyversity</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-10 gap-4 md:gap-6 lg:gap-8">
                {{-- Left column: All articles (show all 6 on mobile/tablet, first 3 on desktop) --}}
                <div class="lg:col-span-7 space-y-4 md:space-y-6">
                    @foreach($latestArticles as $article)
                        <div class="flex flex-col md:flex-row gap-4 bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition {{ $loop->index >= 3 ? 'lg:hidden' : '' }}">
                            <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : 'https://via.placeholder.com/300x200' }}"
                                 alt="{{ $article->title }}"
                                 class="w-full md:w-40 h-40 md:h-40 object-cover flex-shrink-0">
                            <div class="p-4 md:py-4 md:pr-4 flex-1">
                                {{-- 1. Category badge --}}
                                @if($article->categories->isNotEmpty())
                                    <span class="text-xs font-bold uppercase text-primary-600">
                                        {{ $article->categories->first()->name }}
                                    </span>
                                @endif

                                {{-- 2. Date (right after category) --}}
                                <p class="text-xs text-gray-500 mt-1">{{ $article->published_at->format('d M Y') }}</p>

                                {{-- 3. Title --}}
                                <h3 class="text-lg font-bold text-gray-800 mt-3 mb-2 line-clamp-2">
                                    <a href="{{ route('article.show', $article->slug) }}" class="hover:text-primary-600 transition">
                                        {{ $article->title }}
                                    </a>
                                </h3>

                                {{-- 4. Excerpt --}}
                                @if($article->excerpt)
                                    <p class="text-sm text-gray-600 line-clamp-1 mb-3">{{ Str::limit($article->excerpt, 120) }}</p>
                                @endif

                                {{-- 5. CTA (standalone, only on mobile) --}}
                                <a href="{{ route('article.show', $article->slug) }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700 transition md:hidden inline-block">
                                    Baca Selengkapnya
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Right column: 3 articles (30%) - Desktop only --}}
                <div class="hidden lg:block lg:col-span-3 space-y-6">
                    {{-- Article #1: Vertical card --}}
                    @php $rightArticle = $latestArticles->slice(3)->first(); @endphp
                    @if($rightArticle)
                        <div class="bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition">
                            <img src="{{ $rightArticle->thumbnail ? asset('storage/' . $rightArticle->thumbnail) : 'https://via.placeholder.com/400x250' }}"
                                 alt="{{ $rightArticle->title }}"
                                 class="w-full h-40 object-cover">
                            <div class="p-4">
                                @if($rightArticle->categories->isNotEmpty())
                                    <span class="text-xs font-bold uppercase text-primary-600">
                                        {{ $rightArticle->categories->first()->name }}
                                    </span>
                                @endif
                                <h3 class="text-base font-bold text-gray-800 mt-2 mb-2 line-clamp-2">
                                    <a href="{{ route('article.show', $rightArticle->slug) }}" class="hover:text-primary-600 transition">
                                        {{ $rightArticle->title }}
                                    </a>
                                </h3>
                            </div>
                        </div>
                    @endif

                    {{-- Articles #2-3: Horizontal compact list --}}
                    @foreach($latestArticles->slice(4)->take(2) as $article)
                        <div class="flex gap-3 bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition">
                            <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : 'https://via.placeholder.com/150x100' }}"
                                 alt="{{ $article->title }}"
                                 class="w-24 h-20 object-cover flex-shrink-0">
                            <div class="py-2 pr-3 flex-1 flex items-center">
                                <h3 class="text-sm font-bold text-gray-800 mb-1 line-clamp-2">
                                    <a href="{{ route('article.show', $article->slug) }}" class="hover:text-primary-600 transition">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Terpopuler Section --}}
    <section id="terpopuler" class="py-16 md:py-24 bg-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-2">Terpopuler</h2>
                <p class="text-gray-300">Artikel dengan views terbanyak sepanjang masa</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($popularArticles as $article)
                    <div class="bg-gray-900 rounded-lg overflow-hidden border border-gray-700 hover:shadow-lg transition">
                        <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : 'https://via.placeholder.com/400x300' }}"
                             alt="{{ $article->title }}"
                             class="w-full h-48 object-cover">
                        <div class="p-6">
                            {{-- Category badge --}}
                            @if($article->categories->isNotEmpty())
                                <span class="text-xs font-bold uppercase text-primary-300">
                                    {{ $article->categories->first()->name }}
                                </span>
                            @endif

                            {{-- Title --}}
                            <h3 class="text-lg font-bold text-white mt-2 mb-3 line-clamp-2">
                                <a href="{{ route('article.show', $article->slug) }}" class="hover:text-primary-300 transition">
                                    {{ $article->title }}
                                </a>
                            </h3>

                            {{-- Excerpt --}}
                            @if($article->excerpt)
                                <p class="text-sm text-gray-300 line-clamp-2 mb-3">{{ $article->excerpt }}</p>
                            @endif

                            {{-- Date --}}
                            <div class="text-xs text-gray-400">
                                {{ $article->published_at->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Recommendation Section --}}
    <section id="recommendations" class="py-16 md:py-24 bg-gray-50">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-12">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Recommendation for You</h2>
                    <p class="text-gray-600">Artikel pilihan editor khusus untuk Anda</p>
                </div>
                <a href="{{ route('recommendations.index') }}"
                   class="hidden md:inline-block px-6 py-3 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition">
                    Lihat Lainnya
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($recommendedArticles as $article)
                    <div class="bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition">
                        <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : 'https://via.placeholder.com/400x300' }}"
                             alt="{{ $article->title }}"
                             class="w-full h-48 object-cover">
                        <div class="p-6">
                            @if($article->categories->isNotEmpty())
                                <span class="text-xs font-bold uppercase text-primary-600">
                                    {{ $article->categories->first()->name }}
                                </span>
                            @endif
                            <h3 class="text-lg font-bold text-gray-800 mt-2 mb-3 line-clamp-2">
                                <a href="{{ route('article.show', $article->slug) }}" class="hover:text-primary-600 transition">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            @if($article->excerpt)
                                <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $article->excerpt }}</p>
                            @endif
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span>{{ $article->published_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-8 md:hidden">
                <a href="{{ route('recommendations.index') }}"
                   class="inline-block px-6 py-3 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition">
                    Lihat Lainnya
                </a>
            </div>
        </div>
    </section>

    {{-- Featured Courses Section --}}
    @if($featuredCourses->isNotEmpty())
        <section id="featured-courses" class="py-16 md:py-24 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center mb-12">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                            {{ $isFeaturedFallback ? 'Latest Courses' : 'Featured Courses' }}
                        </h2>
                        <p class="text-gray-600">Structured learning programs by expert instructors</p>
                    </div>
                    <a href="{{ route('course.index') }}"
                       class="hidden md:inline-block px-6 py-3 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition">
                        Browse All Courses
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 lg:gap-8">
                    @foreach($featuredCourses as $course)
                        <a href="{{ route('course.show', $course->slug) }}"
                           class="bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition block">
                            <img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : 'https://via.placeholder.com/400x300' }}"
                                 alt="{{ $course->title }}"
                                 class="w-full h-48 object-cover">
                            <div class="p-6">
                                {{-- Badge --}}
                                <span class="inline-block text-xs font-bold uppercase text-primary-600 mb-2">
                                    COURSE
                                </span>

                                {{-- Title --}}
                                <h3 class="text-lg font-bold text-gray-800 mt-2 mb-3 line-clamp-2 hover:text-primary-600 transition">
                                    {{ $course->title }}
                                </h3>

                                {{-- Instructor & Level --}}
                                <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                                    <span>{{ $course->instructor }}</span>
                                    <span>•</span>
                                    <span>{{ $course->level }}</span>
                                </div>

                                {{-- Price & Enrollment --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-xl font-bold text-primary-600">
                                        @if($course->price > 0)
                                            Rp {{ number_format($course->price, 0, ',', '.') }}
                                        @else
                                            Free
                                        @endif
                                    </span>
                                    @if($course->enrollments_count > 0)
                                        <span class="text-xs text-gray-500">
                                            {{ $course->enrollments_count }} enrolled
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Mobile CTA --}}
                <div class="text-center mt-8 md:hidden">
                    <a href="{{ route('course.index') }}"
                       class="inline-block px-6 py-3 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition">
                        Browse All Courses
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- Trending Section --}}
    <section id="trending" class="py-16 md:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Trending</h2>
                <p class="text-gray-600">Artikel paling banyak dibaca 30 hari terakhir</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-10 gap-8">
                {{-- Left column: 3 horizontal cards (70%) --}}
                <div class="lg:col-span-7 space-y-6">
                    @foreach($trendingArticles->take(3) as $article)
                        <div class="flex gap-4 bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition">
                            <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : 'https://via.placeholder.com/300x200' }}"
                                 alt="{{ $article->title }}"
                                 class="w-40 h-40 object-cover flex-shrink-0">
                            <div class="py-4 pr-4 flex-1">
                                @if($article->categories->isNotEmpty())
                                    <span class="text-xs font-bold uppercase text-primary-600">
                                        {{ $article->categories->first()->name }}
                                    </span>
                                @endif
                                <h3 class="text-lg font-bold text-gray-800 mt-2 mb-2 line-clamp-2">
                                    <a href="{{ route('article.show', $article->slug) }}" class="hover:text-primary-600 transition">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                @if($article->excerpt)
                                    <p class="text-sm text-gray-600 line-clamp-1">{{ $article->excerpt }}</p>
                                @endif
                                <div class="flex items-center gap-2 text-xs text-gray-500 mt-2">
                                    <span>{{ $article->published_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Right column: 3 articles (30%) --}}
                <div class="lg:col-span-3 space-y-6">
                    {{-- Article #1: Vertical card --}}
                    @php $rightArticle = $trendingArticles->slice(3)->first(); @endphp
                    @if($rightArticle)
                        <div class="bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition">
                            <img src="{{ $rightArticle->thumbnail ? asset('storage/' . $rightArticle->thumbnail) : 'https://via.placeholder.com/400x250' }}"
                                 alt="{{ $rightArticle->title }}"
                                 class="w-full h-40 object-cover">
                            <div class="p-4">
                                @if($rightArticle->categories->isNotEmpty())
                                    <span class="text-xs font-bold uppercase text-primary-600">
                                        {{ $rightArticle->categories->first()->name }}
                                    </span>
                                @endif
                                <h3 class="text-base font-bold text-gray-800 mt-2 mb-2 line-clamp-2">
                                    <a href="{{ route('article.show', $rightArticle->slug) }}" class="hover:text-primary-600 transition">
                                        {{ $rightArticle->title }}
                                    </a>
                                </h3>
                            </div>
                        </div>
                    @endif

                    {{-- Articles #2-3: Horizontal compact list --}}
                    @foreach($trendingArticles->slice(4)->take(2) as $article)
                        <div class="flex gap-3 bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition">
                            <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : 'https://via.placeholder.com/150x100' }}"
                                 alt="{{ $article->title }}"
                                 class="w-24 h-20 object-cover flex-shrink-0">
                            <div class="py-2 pr-3 flex-1 flex items-center">
                                <h3 class="text-sm font-bold text-gray-800 mb-1 line-clamp-2">
                                    <a href="{{ route('article.show', $article->slug) }}" class="hover:text-primary-600 transition">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Featured Category Section --}}
    @if($featuredCategory && $featuredCategoryArticles->isNotEmpty())
        <section id="featured-category" class="py-16 md:py-24 {{ $featuredCategory->theme_color }}">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-2">{{ $featuredCategory->name }}</h2>
                    @if($featuredCategory->description)
                        <p class="text-white opacity-90">{{ $featuredCategory->description }}</p>
                    @endif
                </div>

                <div class="space-y-4">
                    @foreach($featuredCategoryArticles as $article)
                        <div class="relative bg-gray-900 rounded-lg overflow-hidden h-48 md:h-64 hover:shadow-2xl transition group">
                            <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : 'https://via.placeholder.com/1200x400' }}"
                                 alt="{{ $article->title }}"
                                 class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-70 transition">
                            <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-transparent"></div>
                            <div class="relative h-full flex items-center px-8">
                                <div class="max-w-2xl">
                                    <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-bold uppercase tracking-wider rounded-full mb-3">
                                        {{ $featuredCategory->name }}
                                    </span>
                                    <h3 class="text-2xl md:text-3xl font-bold text-white mb-2 group-hover:text-primary-300 transition">
                                        <a href="{{ route('article.show', $article->slug) }}">
                                            {{ $article->title }}
                                        </a>
                                    </h3>
                                    <div class="flex items-center gap-3 text-sm text-gray-200">
                                        <span>{{ $article->published_at->format('d M Y') }}</span>
                                        <span>•</span>
                                        <span>{{ number_format($article->views_count) }} views</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- More Articles Section --}}
    @if($moreArticles->isNotEmpty())
        <section id="more-articles" class="py-16 md:py-24 bg-gray-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">More Articles</h2>
                    <p class="text-gray-600">Artikel menarik lainnya untuk Anda</p>
                </div>

                <div class="space-y-6">
                    @foreach($moreArticles as $article)
                        <div class="flex gap-6 bg-white rounded-lg overflow-hidden border border-gray-100 hover:shadow-lg transition">
                            <img src="{{ $article->thumbnail ? asset('storage/' . $article->thumbnail) : 'https://via.placeholder.com/400x300' }}"
                                 alt="{{ $article->title }}"
                                 class="w-48 md:w-64 h-40 object-cover flex-shrink-0">
                            <div class="py-6 pr-6 flex-1">
                                @if($article->categories->isNotEmpty())
                                    <span class="text-xs font-bold uppercase text-primary-600">
                                        {{ $article->categories->first()->name }}
                                    </span>
                                @endif
                                <h3 class="text-xl font-bold text-gray-800 mt-2 mb-3 line-clamp-2">
                                    <a href="{{ route('article.show', $article->slug) }}" class="hover:text-primary-600 transition">
                                        {{ $article->title }}
                                    </a>
                                </h3>
                                @if($article->excerpt)
                                    <p class="text-base text-gray-600 line-clamp-2 mb-4">{{ $article->excerpt }}</p>
                                @endif
                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                    <span>{{ $article->author }}</span>
                                    <span>•</span>
                                    <span>{{ $article->published_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center mt-12">
                    <a href="{{ route('article.index') }}"
                       class="inline-block px-8 py-3 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition">
                        See More
                    </a>
                </div>
            </div>
        </section>
    @endif
@endsection
