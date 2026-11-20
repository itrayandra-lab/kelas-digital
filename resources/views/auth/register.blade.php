@extends('layouts.auth')

@section('title', 'Daftar')

@section('side')
    <h1>Mulai belajar bersama Ray Academy</h1>
    <p>Buat akun gratis, lalu pilih kelas yang sesuai dengan kebutuhanmu, dari marketing, branding, sampai bisnis.</p>
@endsection

@section('form')
    <h2>Buat akun baru</h2>
    <p class="au-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>

    <div id="form-errors" role="alert" aria-live="polite" @if (! $errors->any()) hidden @endif class="au-alert">
        @if ($errors->any())
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
    </div>

    <form data-ajax class="au-fields" method="POST" action="{{ route('register') }}" x-data="{ type: @js(old('origin_type', '')), show: false }">
        @csrf
        <div>
            <label for="name" class="au-label">Nama lengkap</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="mis. Budi Santoso" class="au-input">
        </div>

        <div class="au-row">
            <div>
                <label for="username" class="au-label">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autocomplete="username" spellcheck="false"
                    minlength="3" maxlength="20" pattern="[A-Za-z0-9_\-]+" placeholder="mis. budi_santoso" class="au-input" aria-describedby="username-hint">
                <p class="au-hint" id="username-hint">3–20 karakter: huruf, angka, _ atau -</p>
            </div>
            <div>
                <label for="email" class="au-label">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" spellcheck="false" placeholder="budi@gmail.com" class="au-input">
            </div>
        </div>

        <fieldset class="au-group">
            <legend>Asal kamu</legend>
            <div>
                <label for="origin_type" class="au-label">Kamu saat ini</label>
                <select id="origin_type" name="origin_type" required x-model="type" class="au-input">
                    <option value="" disabled>Pilih salah satu</option>
                    @foreach(\App\Models\User::ORIGINS as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div x-show="type !== 'umum'">
                <label for="institution" class="au-label">Nama universitas atau kantor</label>
                <input type="text" id="institution" name="institution" value="{{ old('institution') }}" maxlength="150"
                    list="institution-list" autocomplete="organization" placeholder="Nama kampus atau kantor" class="au-input" aria-describedby="institution-hint">
                <datalist id="institution-list">
                    @foreach(\App\Models\User::knownInstitutions() as $name)<option value="{{ $name }}">@endforeach
                </datalist>
                <p class="au-hint" id="institution-hint">Contoh: Universitas Padjadjaran atau PT Sinar Jaya. Tulis nama lengkapnya, atau pilih dari saran yang muncul.</p>
            </div>
        </fieldset>

        <div class="au-row">
            <div>
                <label for="password" class="au-label">Password</label>
                <div class="au-pass">
                    <input :type="show ? 'text' : 'password'" type="password" id="password" name="password" required minlength="8" autocomplete="new-password" class="au-input" aria-describedby="password-hint">
                    <button type="button" class="au-eye" @click="show = !show" :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'" aria-label="Tampilkan password">
                        <i class="fas" :class="show ? 'fa-eye-slash' : 'fa-eye'" aria-hidden="true"></i>
                    </button>
                </div>
                <p class="au-hint" id="password-hint">Minimal 8 karakter.</p>
            </div>
            <div>
                <label for="password_confirmation" class="au-label">Ulangi password</label>
                <input :type="show ? 'text' : 'password'" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" class="au-input">
            </div>
        </div>

        <button type="submit" class="au-btn">Daftar sekarang</button>
    </form>
@endsection
