<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @hasSection('seo')
        @yield('seo')
    @else
        {!! seo() !!}
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome for social icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-rich-text::styles theme="richtextlaravel" />
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-800" x-data="{ mobileMenuOpen: false }">
    <div class="min-h-screen flex flex-col">

        <header>
            <!-- Top Bar -->
            <div class="bg-gray-800 text-white text-sm">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center py-2">
                    <div>
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <span>{{ $settings['contact_address'] ?? 'Bandung, Jawa Barat, Indonesia' }}</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if(!empty($settings['social_facebook']))
                            <a href="{{ $settings['social_facebook'] }}" class="hover:text-primary-400 transition" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        @endif
                        @if(!empty($settings['social_twitter']))
                            <a href="{{ $settings['social_twitter'] }}" class="hover:text-primary-400 transition" target="_blank"><i class="fab fa-twitter"></i></a>
                        @endif
                        @if(!empty($settings['social_instagram']))
                            <a href="{{ $settings['social_instagram'] }}" class="hover:text-primary-400 transition" target="_blank"><i class="fab fa-instagram"></i></a>
                        @endif
                        @if(!empty($settings['social_youtube']))
                            <a href="{{ $settings['social_youtube'] }}" class="hover:text-primary-400 transition" target="_blank"><i class="fab fa-youtube"></i></a>
                        @endif
                        @if(!empty($settings['social_tiktok']))
                            <a href="{{ $settings['social_tiktok'] }}" class="hover:text-primary-400 transition" target="_blank"><i class="fab fa-tiktok"></i></a>
                        @endif
                        @if(!empty($settings['social_whatsapp']))
                            <a href="{{ $settings['social_whatsapp'] }}" class="hover:text-primary-400 transition" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        @endif
                        @if(!empty($settings['social_linkedin']))
                            <a href="{{ $settings['social_linkedin'] }}" class="hover:text-primary-400 transition" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Header (Branding) -->
            <div class="bg-white py-6">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 flex justify-center items-center">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('logo.webp') }}" alt="Logo" class="h-12">
                    </a>
                </div>
            </div>

            <!-- Navigation Bar (Sticky on Scroll) -->
            <nav id="main-header" class="bg-white border-b border-gray-200 relative transition-all duration-300">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <!-- Mobile Menu Button -->
                        <div class="lg:hidden">
                            <button @click="mobileMenuOpen = true" class="text-gray-600 p-2 rounded-md">
                                <i class="fas fa-bars h-6 w-6"></i>
                            </button>
                        </div>

                        <!-- Desktop Navigation Links -->
                        <div class="hidden lg:flex lg:items-center lg:space-x-8">
                            <a href="{{ route('home') }}"
                                class="nav-link text-gray-700 text-sm font-semibold uppercase tracking-wider hover:text-primary-600 transition">
                                Beranda
                            </a>
                            
                            @if (isset($articleCategories))
                            <!-- Article Categories Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @mouseenter="open = true" 
                                        @mouseleave="open = false"
                                        class="flex items-center space-x-1 text-gray-700 text-sm font-semibold uppercase tracking-wider hover:text-primary-600 transition">
                                    <span>Artikel Ilmiah</span>
                                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" :class="{ 'rotate-180': open }"></i>
                                </button>
                                
                                <div x-show="open" 
                                     @mouseenter="open = true"
                                     @mouseleave="open = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute top-full left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                                    @foreach ($articleCategories as $category)
                                        <a href="{{ route('article.category', $category->slug) }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                            {{ $category->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Search and Auth Links -->
                        <div class="flex items-center space-x-4">
                            <form action="{{ route('search') }}" method="GET" class="hidden lg:flex items-center bg-gray-100 rounded-full px-4 py-2 focus-within:ring-2 focus-within:ring-primary-500">
                                <label for="header-search" class="sr-only">Cari</label>
                                <input
                                    type="search"
                                    id="header-search"
                                    name="q"
                                    class="bg-transparent focus:outline-none text-sm text-gray-600 placeholder-gray-400 w-40"
                                    placeholder="Cari kursus kecantikan atau artikel ilmiah..."
                                >
                                <button type="submit" class="text-gray-500 hover:text-primary-600 transition">
                                    <i class="fas fa-search h-4 w-4"></i>
                                </button>
                            </form>
                            <a href="{{ route('search') }}" class="lg:hidden text-gray-600 hover:text-primary-600">
                                <i class="fas fa-search h-5 w-5"></i>
                            </a>
                            @auth
                            <!-- User Dropdown Menu - Desktop (lg and up) -->
                                <div class="relative hidden lg:block" x-data="{ open: false }">
                                    <button @click="open = !open" class="flex items-center space-x-1 text-gray-700 text-sm font-medium hover:text-primary-600 transition">
                                        <span class="hidden lg:inline">{{ Auth::user()->name }}</span>
                                        <i class="fas fa-user-circle ml-2"></i>
                                        <i class="fas fa-chevron-down ml-1"></i>
                                    </button>
                                    
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 scale-95"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="opacity-100 scale-100"
                                         x-transition:leave-end="opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                                        <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-user mr-2"></i>My Profile
                                        </a>
                                        
                                        <a href="{{ Auth::user()->hasRole('student') ? route('dashboard') : route('admin.dashboard') }}" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                           title="{{ Auth::user()->hasRole('student') ? 'Student Dashboard' : 'Admin Panel' }}">
                                            <i class="{{ Auth::user()->hasRole('student') ? 'fas fa-th-large' : 'fas fa-cog' }} mr-2"></i>
                                            {{ Auth::user()->hasRole('student') ? 'Dashboard' : 'Admin Panel' }}
                                        </a>
                                        <div class="border-t border-gray-100"></div>
                                        <form method="POST" action="{{ route('logout') }}" class="block">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Mobile User Menu (hidden on lg screens and above - 1024px+) -->
                                <div class="lg:hidden flex items-center space-x-2">
                                    <a href="{{ Auth::user()->hasRole('student') ? route('dashboard') : route('admin.dashboard') }}" 
                                       class="text-gray-700 hover:text-primary-600 transition"
                                       title="{{ Auth::user()->hasRole('student') ? 'Student Dashboard' : 'Admin Panel' }}">
                                        <i class="{{ Auth::user()->hasRole('student') ? 'fas fa-th-large' : 'fas fa-cog' }} h-5 w-5"></i>
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-700 hover:text-primary-600 transition">
                                            <i class="fas fa-sign-out-alt h-5 w-5"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('login') }}"
                                    class="auth-link text-gray-700 text-sm font-medium hover:text-primary-600 transition">Log
                                    in</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <!-- Mobile Menu Overlay -->
        <div x-cloak
             x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-white z-50 p-4">
            <div class="flex justify-between items-center mb-8">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('logo.webp') }}" alt="Logo" class="h-10">
                </a>
                <button @click="mobileMenuOpen = false" class="text-gray-800">
                    <i class="fas fa-times h-6 w-6"></i>
                </button>
            </div>
            <form action="{{ route('search') }}" method="GET" class="mb-6">
                <label for="mobile-search" class="sr-only">Cari</label>
                <div class="flex items-center gap-2 border border-gray-200 rounded-lg px-3 py-2">
                    <i class="fas fa-search text-gray-400"></i>
                    <input
                        type="search"
                        id="mobile-search"
                        name="q"
                        class="flex-1 focus:outline-none text-sm text-gray-700 placeholder-gray-400"
                        placeholder="Cari kelas atau artikel..."
                    >
                </div>
            </form>
            <nav class="flex flex-col space-y-4">
                @auth
                    <div class="border-b border-gray-200 pb-4 mb-4">
                        <div class="flex items-center space-x-3 mb-3">
                            <i class="fas fa-user-circle text-2xl text-gray-600"></i>
                            <div>
                                <p class="font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                                <p class="text-sm text-gray-600">{{ Auth::user()->username ?? Auth::user()->email }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('profile.index') }}" class="text-gray-700 hover:text-primary-600 transition flex items-center">
                                <i class="fas fa-user mr-2"></i>My Profile
                            </a>
                            <a href="{{ Auth::user()->hasRole('student') ? route('dashboard') : route('admin.dashboard') }}" 
                               class="text-gray-700 hover:text-primary-600 transition flex items-center"
                               title="{{ Auth::user()->hasRole('student') ? 'Student Dashboard' : 'Admin Panel' }}">
                                <i class="{{ Auth::user()->hasRole('student') ? 'fas fa-th-large' : 'fas fa-cog' }} mr-2"></i>
                                {{ Auth::user()->hasRole('student') ? 'Dashboard' : 'Admin Panel' }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-gray-700 hover:text-primary-600 transition flex items-center w-full text-left">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
                
                @if (isset($articleCategories))
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="text-gray-600 text-sm font-semibold uppercase tracking-wider mb-3">Artikel</h3>
                        <div class="space-y-2">
                            @foreach ($articleCategories as $category)
                                <a href="{{ route('article.category', $category->slug) }}"
                                    class="block text-gray-700 hover:text-primary-600 transition pl-4">{{ $category->name }}</a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </nav>
        </div>

        <main class="flex-grow">
            @yield('content')
        </main>

        <footer style="background-color: #E6B4B8;" class="text-white">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <!-- Main Footer Content -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                    
                    <!-- Brand & About Section -->
                    <div class="lg:col-span-1">
                        <div class="mb-6">
                            <a href="{{ route('home') }}" class="flex items-center mb-4">
                                <img src="{{ asset('logo.webp') }}" alt="Kelas Digital" class="h-10 w-auto">
                            </a>
                            <h3 class="text-xl font-bold mb-3">Beautyversity.id</h3>
                            <p class="text-sm opacity-90 leading-relaxed">
                                Where Beauty Meets Science. Platform edukasi kecantikan berbasis bukti dari Mahasiswa S2 Farmasi UNPAD. 
                                Pelajari ilmu kecantikan yang benar dan aman.
                            </p>
                        </div>
                        
                        <!-- Newsletter Subscription -->
                        <div class="mt-6">
                            <h4 class="text-sm font-semibold mb-3 uppercase tracking-wider">Update Kecantikan</h4>
                            <form class="space-y-3" action="#" method="POST">
                                @csrf
                                <input type="email" 
                                       placeholder="Email untuk update" 
                                       class="w-full px-4 py-3 text-sm text-gray-900 bg-white border border-gray-200 rounded-lg placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-70 focus:border-white transition-all duration-200 min-h-[48px]"
                                       required>
                                <button type="submit" 
                                        class="w-full bg-white text-gray-800 px-4 py-3 text-sm font-semibold rounded-lg hover:bg-gray-50 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 min-h-[48px] flex items-center justify-center">
                                    <i class="fas fa-paper-plane mr-2"></i>Daftar Update
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-sm font-semibold mb-4 uppercase tracking-wider">Navigasi</h4>
                        <ul class="space-y-3">
                            <li><a href="{{ route('home') }}" class="text-sm opacity-90 hover:opacity-100 hover:text-white transition">Beranda</a></li>
                            <li><a href="{{ route('search') }}" class="text-sm opacity-90 hover:opacity-100 hover:text-white transition">Pencarian</a></li>
                            {{-- kalo masih guest, hidden aja --}}
                            @auth
                            <li><a href="{{ Auth::user()->hasRole('student') ? route('dashboard') : route('admin.dashboard') }}" class="text-sm opacity-90 hover:opacity-100 hover:text-white transition">Dashboard</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="text-sm opacity-90 hover:opacity-100 hover:text-white transition">Masuk</a></li>
                                <li><a href="{{ route('register') }}" class="text-sm opacity-90 hover:opacity-100 hover:text-white transition">Daftar</a></li>
                            @endauth
                            @auth
                                <li><a href="{{ route('profile.index') }}" class="text-sm opacity-90 hover:opacity-100 hover:text-white transition">Profil Saya</a></li>
                            @endauth
                        </ul>
                    </div>

                    <!-- Article Categories -->
                    <div>
                        <h4 class="text-sm font-semibold mb-4 uppercase tracking-wider">Artikel Ilmiah</h4>
                        <ul class="space-y-3">
                            @if (isset($articleCategories))
                                @foreach ($articleCategories->take(6) as $category)
                                    <li>
                                        <a href="{{ route('article.category', $category->slug) }}" 
                                           class="text-sm opacity-90 hover:opacity-100 hover:text-white transition">
                                            {{ $category->name }}
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li><a href="#" class="text-sm opacity-90 hover:opacity-100 hover:text-white transition">Skincare</a></li>
                                <li><a href="#" class="text-sm opacity-90 hover:opacity-100 hover:text-white transition">Haircare</a></li>
                                <li><a href="#" class="text-sm opacity-90 hover:opacity-100 hover:text-white transition">Makeup</a></li>
                                <li><a href="#" class="text-sm opacity-90 hover:opacity-100 hover:text-white transition">Personal Care</a></li>
                            @endif
                        </ul>
                    </div>

                    <!-- Contact & Social -->
                    <div>
                        <h4 class="text-sm font-semibold mb-4 uppercase tracking-wider">Kontak & Sosial</h4>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-envelope text-sm mt-1"></i>
                                <div>
                                    <p class="text-sm opacity-90">Email</p>
                                    <p class="text-xs opacity-75">{{ $settings['contact_email'] ?? 'info@kelasdigital.com' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-phone text-sm mt-1"></i>
                                <div>
                                    <p class="text-sm opacity-90">Telepon</p>
                                    <p class="text-xs opacity-75">{{ $settings['contact_phone'] ?? '+62 123 456 7890' }}</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-3">
                                <i class="fas fa-map-marker-alt text-sm mt-1"></i>
                                <div>
                                    <p class="text-sm opacity-90">Alamat</p>
                                    <p class="text-xs opacity-75">{{ $settings['contact_address'] ?? 'Bandung, Jawa Barat, Indonesia' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Social Media -->
                        <div class="mt-6">
                            <h5 class="text-sm font-semibold mb-3 uppercase tracking-wider">Ikuti Kami</h5>
                            <div class="flex space-x-4">
                                @if(!empty($settings['social_facebook']))
                                    <a href="{{ $settings['social_facebook'] }}" target="_blank"
                                       class="text-white hover:text-gray-200 transition-colors duration-200">
                                        <i class="fab fa-facebook-f text-lg"></i>
                                    </a>
                                @endif
                                @if(!empty($settings['social_twitter']))
                                    <a href="{{ $settings['social_twitter'] }}" target="_blank"
                                       class="text-white hover:text-gray-200 transition-colors duration-200">
                                        <i class="fab fa-twitter text-lg"></i>
                                    </a>
                                @endif
                                @if(!empty($settings['social_instagram']))
                                    <a href="{{ $settings['social_instagram'] }}" target="_blank"
                                       class="text-white hover:text-gray-200 transition-colors duration-200">
                                        <i class="fab fa-instagram text-lg"></i>
                                    </a>
                                @endif
                                @if(!empty($settings['social_youtube']))
                                    <a href="{{ $settings['social_youtube'] }}" target="_blank"
                                       class="text-white hover:text-gray-200 transition-colors duration-200">
                                        <i class="fab fa-youtube text-lg"></i>
                                    </a>
                                @endif
                                @if(!empty($settings['social_tiktok']))
                                    <a href="{{ $settings['social_tiktok'] }}" target="_blank"
                                       class="text-white hover:text-gray-200 transition-colors duration-200">
                                        <i class="fab fa-tiktok text-lg"></i>
                                    </a>
                                @endif
                                @if(!empty($settings['social_whatsapp']))
                                    <a href="{{ $settings['social_whatsapp'] }}" target="_blank"
                                       class="text-white hover:text-gray-200 transition-colors duration-200">
                                        <i class="fab fa-whatsapp text-lg"></i>
                                    </a>
                                @endif
                                @if(!empty($settings['social_linkedin']))
                                    <a href="{{ $settings['social_linkedin'] }}" target="_blank"
                                       class="text-white hover:text-gray-200 transition-colors duration-200">
                                        <i class="fab fa-linkedin-in text-lg"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Footer -->
                <div class="border-t border-white border-opacity-20 pt-8">
                    <div class="flex flex-col md:flex-row justify-center items-center space-y-4 md:space-y-0">
                        <div class="text-center md:text-left">
                            <p class="text-sm opacity-90">
                                Copyright © {{ date('Y') }}, Beautyversity.id. All Rights Reserved.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
