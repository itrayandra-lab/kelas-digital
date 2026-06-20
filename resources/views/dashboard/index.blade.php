@extends('layouts.app')

@section('title', 'Dashboard - Ray Academy')

@section('content')

    <div class="bg-gray-50 py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">
                        Selamat Datang, {{ auth()->user()->name }}!
                    </h1>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('profile.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-700 transition-colors">
                        <i class="fas fa-user mr-2"></i>
                        My Profile
                    </a>
                </div>
            </div>
            <p class="mt-2 text-lg text-gray-600">
                @if(auth()->user()->isAdmin())
                    Anda masuk sebagai Administrator. Kelola platform Ray Academy dari dashboard admin.
                @elseif(auth()->user()->isInstructor())
                    Anda masuk sebagai Instruktur. Kelola kursus dan materi pembelajaran Anda.
                @elseif(auth()->user()->isContentManager())
                    Anda masuk sebagai Content Manager. Kelola artikel dan konten platform Ray Academy.
                @else
                    Lanjutkan perjalanan pembelajaran Anda. Pilih kursus di bawah ini untuk memulai.
                @endif
            </p>
            
            {{-- Role-based navigation --}}
            @if(auth()->user()->isAdmin() || auth()->user()->isInstructor() || auth()->user()->isContentManager())
                <div class="mt-6">
                    @can('access admin panel')
                    <a href="{{ route('admin.dashboard') }}" 
                       class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors">
                        <i class="fas fa-cog mr-2"></i>
                        Dashboard Admin
                    </a>
                    @endcan
                    
                    @can('view courses')
                    <a href="{{ route('admin.courses.index') }}" 
                       class="ml-3 inline-flex items-center px-5 py-2.5 bg-gray-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-gray-700 transition-colors">
                        <i class="fas fa-book mr-2"></i>
                        Kelola Kursus
                    </a>
                    @endcan
                    
                    @can('view articles')
                    <a href="{{ route('admin.articles.index') }}" 
                       class="ml-3 inline-flex items-center px-5 py-2.5 bg-green-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-green-700 transition-colors">
                        <i class="fas fa-newspaper mr-2"></i>
                        Kelola Artikel
                    </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>

    <div class="py-16 md:py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Kursus Kecantikan Saya</h2>

            @if ($courses->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($courses as $course)
                        <div
                            class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-xl transition-shadow duration-300 border border-gray-100 flex flex-col">
                            <a href="{{ route('course.show', $course->slug) }}">
                                @if($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}"
                                        alt="{{ $course->title }}" class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 flex items-center justify-center" style="background:linear-gradient(135deg, #DBEAFE, #EFF6FF);">
                                        <i class="fas fa-play-circle" style="font-size:2.5rem; color:#0056D2; opacity:.35;"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="p-6 flex-grow flex flex-col">
                                <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $course->title }}</h3>
                                <p class="text-gray-600 text-sm mb-4">
                                    oleh {{ $course->instructor }}
                                </p>
                                <div class="mt-auto">
                                    <a href="{{ route('course.show', $course->slug) }}"
                                        class="w-full text-center block px-5 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
                                        Mulai Belajar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center bg-white border-2 border-dashed rounded-lg p-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900">Anda belum memiliki kursus kecantikan</h3>
                    <p class="mt-1 text-sm text-gray-500">Jelajahi katalog kami dan mulailah perjalanan kecantikan Anda hari ini.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center px-5 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors">
                            Jelajahi Kursus Kecantikan
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection
