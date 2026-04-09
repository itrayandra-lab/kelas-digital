<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - Ray Academy')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    
    <!-- Select2 CSS -->
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/select2/css/select2-bootstrap-5-theme.min.css') }}" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-rich-text::styles theme="richtextlaravel" />
    
    <!-- Custom Select2 Styling -->
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 42px !important;
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            padding: 0.625rem 1rem !important;
        }
        
        .select2-container--bootstrap-5 .select2-selection:focus {
            border-color: #1474bc !important;
            box-shadow: 0 0 0 3px rgba(20, 116, 188, 0.1) !important;
        }
        
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #1991eb !important;
            border: 1px solid #1991eb !important;
            color: #1f2937 !important;
            border-radius: 0.375rem !important;
            padding: 0.25rem 0.5rem !important;
            margin: 0.125rem !important;
            font-size: 0.875rem !important;
        }
        
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: #6b7280 !important;
            margin-right: 0.25rem !important;
        }
        
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #ef4444 !important;
        }
        
        .select2-container--bootstrap-5 .select2-dropdown {
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }
        
        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: #1991eb !important;
            color: #1f2937 !important;
        }
        
        .select2-container--bootstrap-5 .select2-results__option--selected {
            background-color: #f3f4f6 !important;
            color: #6b7280 !important;
        }
        
        .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
            border: 1px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
        }
        
        .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field:focus {
            border-color: #1474bc !important;
            box-shadow: 0 0 0 3px rgba(20, 116, 188, 0.1) !important;
            outline: none !important;
        }
        
        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        
        .select2-container .select2-selection--multiple .select2-selection__rendered {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
    </style>
</head>

<body class="h-full font-sans">
    <div x-data="{ open: false }" class="flex min-h-screen">
        <aside class="w-64 flex-shrink-0 bg-white border-r border-gray-200 flex flex-col">
            <div class="h-20 flex items-center justify-center px-6 border-b border-gray-200">
                <a href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('logo.png') }}" alt="Ray Academy" class="h-14">
                </a>
            </div>
            <nav class="flex-1 px-4 py-4 space-y-6 overflow-y-auto">
                <!-- Dashboard -->
                <div>
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i class="fas fa-tachometer-alt mr-3 text-base"></i>
                        Dashboard
                    </a>
                </div>

                <!-- My Profile -->
                <div>
                    <a href="{{ route('profile.index') }}"
                        class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('profile.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                        <i class="fas fa-user mr-3 text-base"></i>
                        My Profile
                    </a>
                </div>

                <!-- Course Management Group - Only for users with course permissions -->
                @can('view courses')
                <div x-data="{ 
                    open: localStorage.getItem('course-group-open') !== 'false' 
                }">
                    <button @click="open = !open; localStorage.setItem('course-group-open', open)" 
                        class="flex items-center justify-between w-full px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors duration-200">
                        <span>Course Management</span>
                        <i class="fas fa-chevron-down transition-transform duration-200" 
                           :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" 
                         x-collapse
                         class="space-y-1 mt-2">
                        @can('view courses')
                        <a href="{{ route('admin.courses.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.courses.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-book-open mr-3 text-base"></i>
                            Manage Courses
                        </a>
                        @endcan
                        
                        @can('view course categories')
                        <a href="{{ route('admin.course-categories.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.course-categories.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-sitemap mr-3 text-base"></i>
                            Course Categories
                        </a>
                        @endcan
                        
                        @can('view lessons')
                        <a href="{{ route('admin.lessons.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.lessons.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-list mr-3 text-base"></i>
                            Manage Lessons
                        </a>
                        @endcan
                    </div>
                </div>
                @endcan

                <!-- Content Management Group - Only for users with article permissions -->
                @can('view articles')
                <div x-data="{ 
                    open: localStorage.getItem('content-group-open') !== 'false' 
                }">
                    <button @click="open = !open; localStorage.setItem('content-group-open', open)" 
                        class="flex items-center justify-between w-full px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors duration-200">
                        <span>Content Management</span>
                        <i class="fas fa-chevron-down transition-transform duration-200" 
                           :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" 
                         x-collapse
                         class="space-y-1 mt-2">
                        @can('view articles')
                        <a href="{{ route('admin.articles.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.articles.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-file-alt mr-3 text-base"></i>
                            Manage Articles
                        </a>
                        
                        <a href="{{ route('admin.hero-slider.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.hero-slider.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-images mr-3 text-base"></i>
                            Hero Slider
                        </a>
                        @endcan
                        
                        @can('view article categories')
                        <a href="{{ route('admin.article-categories.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.article-categories.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-folder-open mr-3 text-base"></i>
                            Article Categories
                        </a>
                        @endcan
                        
                        @can('view tags')
                        <a href="{{ route('admin.tags.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.tags.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-tags mr-3 text-base"></i>
                            Tags
                        </a>
                        @endcan
                        
                        @can('manage site settings')
                        <a href="{{ route('admin.site-settings.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.site-settings.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-cog mr-3 text-base"></i>
                            Site Settings
                        </a>
                        @endcan
                    </div>
                </div>
                @endcan

                <!-- System Management Group - Only show if user has at least one system permission -->
                @if(auth()->user()->can('view users') || auth()->user()->can('manage roles and permissions') || auth()->user()->can('manage enrollments') || auth()->user()->can('view share domains'))
                <div x-data="{ 
                    open: localStorage.getItem('system-group-open') !== 'false' 
                }">
                    <button @click="open = !open; localStorage.setItem('system-group-open', open)" 
                        class="flex items-center justify-between w-full px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 transition-colors duration-200">
                        <span>System Management</span>
                        <i class="fas fa-chevron-down transition-transform duration-200" 
                           :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open"
                         x-collapse
                         class="space-y-1 mt-2">
                        @can('view users')
                        <a href="{{ route('admin.users.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-users mr-3 text-base"></i>
                            Manage Users
                        </a>
                        @endcan
                        
                        @can('manage roles and permissions')
                        <a href="{{ route('admin.roles.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.roles.*') || request()->routeIs('admin.activity-log.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-user-shield mr-3 text-base"></i>
                            Roles & Permissions
                        </a>
                        @endcan
                        
                        @can('manage enrollments')
                        <a href="{{ route('admin.payments.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.payments.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-credit-card mr-3 text-base"></i>
                            Manage Payments
                        </a>
                        @endcan
                        
                        @can('view share domains')
                        <a href="{{ route('admin.share-domains.index') }}"
                            class="flex items-center px-4 py-2.5 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('admin.share-domains.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-600 hover:bg-gray-100' }}">
                            <i class="fas fa-network-wired mr-3 text-base"></i>
                            Share Domains
                        </a>
                        @endcan
                    </div>
                </div>
                @endif
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="flex justify-between items-center h-20 px-4 sm:px-6 lg:px-8">
                    <h1 class="text-2xl font-bold text-gray-900">
                        @yield('title')
                    </h1>
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" class="flex items-center space-x-2 text-left">
                            <img class="h-10 w-10 rounded-full object-cover"
                                src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=1474bc&color=ffffff"
                                alt="User Avatar">
                            <div>
                                <span class="font-semibold text-gray-800 text-sm">{{ Auth::user()->name }}</span>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-gray-500 text-sm transition-transform duration-200" 
                               :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl py-1 z-50">
                            <a href="{{ route('home') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-150">
                                <div class="flex items-center">
                                    View Site
                                </div>
                            </a>
                            <a href="#"
                                @click.prevent="document.getElementById('logout-form').submit()"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors duration-150">
                                <div class="flex items-center">
                                    Logout
                                </div>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- jQuery and Select2 JavaScript -->
    <script src="{{ asset('vendor/jquery/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
    
    <!-- Alpine.js -->
    <script defer src="{{ asset('vendor/alpinejs/alpine.min.js') }}"></script>
    
    <!-- Custom Select2 initialization -->
    <script>
        $(document).ready(function() {
            // Initialize Select2 for categories
            $('#categories').select2({
                placeholder: 'Pilih kategori...',
                allowClear: true,
                width: '100%',
                closeOnSelect: false,
                tags: false,
                theme: 'bootstrap-5',
                language: {
                    noResults: function() {
                        return "Tidak ada kategori ditemukan";
                    }
                }
            });
            
            // Initialize Select2 for tags with tag creation enabled
            $('#tags').select2({
                placeholder: 'Pilih tag atau ketik untuk membuat tag baru...',
                allowClear: true,
                width: '100%',
                closeOnSelect: false,
                tags: true,
                tokenSeparators: [','],
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    };
                },
                theme: 'bootstrap-5',
                language: {
                    noResults: function() {
                        return "Tidak ada tag ditemukan. Ketik untuk membuat tag baru.";
                    }
                }
            });

            // Handle clearing input after new tag creation via Enter
            $('#tags').on('select2:select', function (e) {
                var data = e.params.data;
                // Check if it's a newly created tag
                if (data.newTag) {
                    // Clear the search input field for this specific Select2 instance
                    $(this).data('select2').$container.find('.select2-search__field').val('');
                }
            });
        });
    </script>

</body>

</html>