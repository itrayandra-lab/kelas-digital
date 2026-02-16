<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ray Academy</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-800">
    <div class="min-h-screen flex">
        <div class="hidden lg:flex w-1/2 bg-cover bg-center"
            style="background-image: url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=2071&auto=format&fit=crop');">
            <div
                class="flex flex-col items-center justify-center w-full h-full bg-primary-600 bg-opacity-70 p-12 text-white text-center">
                <a href="{{ route('home') }}" class="mb-6">
                    <img src="{{ asset('logo-white.png') }}" alt="Ray Academy" class="h-16 md:h-20 mb-4">
                </a>
                <h1 class="text-4xl font-bold leading-tight mb-4">Selamat Datang Kembali!</h1>
                <p class="text-lg opacity-90">
                    Masuk untuk melanjutkan perjalanan kecantikan Anda dan akses semua kursus ilmiah terbaik kami.
                </p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center bg-white p-8">
            <div class="max-w-md w-full">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Login ke Akun Anda</h2>
                    <p class="mt-2 text-gray-600">
                        Belum punya akun? <a href="{{ route('register') }}"
                            class="font-medium text-primary-600 hover:underline">Daftar di sini</a>
                    </p>
                </div>

                @if (session('error'))
                    <div class="mt-6 bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mt-6 bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div>
                        <label for="login" class="block text-sm font-medium text-gray-700 mb-2">Username atau Email</label>
                        <input type="text" id="login" name="login" value="{{ old('login') }}" required
                            autocomplete="username"
                            placeholder="Masukkan username atau email Anda"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-lg font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300">
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
