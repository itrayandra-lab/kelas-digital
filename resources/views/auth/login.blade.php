@extends('layouts.auth')

@section('title', 'Masuk')

@section('side')
    <h1>Selamat datang kembali!</h1>
    <p>Masuk untuk melanjutkan kelasmu dari langkah terakhir yang kamu kerjakan.</p>
@endsection

@section('form')
    <h2>Masuk ke akunmu</h2>
    <p class="au-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar gratis</a></p>

    @if (session('error'))
        <div class="au-alert">{{ session('error') }}</div>
    @endif
    <div id="form-errors" role="alert" aria-live="polite" @if (! $errors->any()) hidden @endif class="au-alert">
        @if ($errors->any())
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <form data-ajax class="au-fields" method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <label for="login" class="au-label">Username atau email</label>
            <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                autocomplete="username" spellcheck="false" placeholder="mis. budi_santoso atau budi@gmail.com" class="au-input">
        </div>

        <div x-data="{ show: false }">
            <label for="password" class="au-label">Password</label>
            <div class="au-pass">
                <input :type="show ? 'text' : 'password'" type="password" id="password" name="password" required autocomplete="current-password" class="au-input">
                <button type="button" class="au-eye" @click="show = !show" :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'" aria-label="Tampilkan password">
                    <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="au-btn">Masuk</button>
    </form>
@endsection
