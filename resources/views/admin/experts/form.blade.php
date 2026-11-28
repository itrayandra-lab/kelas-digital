@extends('layouts.admin')

@section('title', $expert->exists ? 'Edit Pakar' : 'Tambah Pakar')

@section('content')

    <form action="{{ $expert->exists ? route('admin.experts.update', $expert) : route('admin.experts.store') }}" method="POST">
        @csrf
        @if($expert->exists) @method('PUT') @endif

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-8">
            <a href="{{ route('admin.experts.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left w-5 h-5 mr-2 text-sm"></i> Kembali ke Pakar
            </a>
            <button type="submit" class="mt-4 sm:mt-0 inline-flex items-center justify-center px-6 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
                Simpan Pakar
            </button>
        </div>

        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 md:p-8 space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama lengkap dengan gelar</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $expert->name) }}" required maxlength="255"
                        placeholder="Contoh: Wendra Wilendra, M.MT"
                        class="w-full block px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                    <p class="mt-1 text-xs text-gray-500">Ditulis persis seperti yang ingin tampil di kelas, artikel, dan halaman Expert.</p>
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <fieldset>
                    <legend class="block text-sm font-medium text-gray-700 mb-2">Sekolah (bidang belajar)</legend>
                    <div class="flex flex-wrap gap-x-6 gap-y-2">
                        @foreach($schools as $slug => $name)
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="schools[]" value="{{ $slug }}" class="rounded border-gray-300 text-primary-600"
                                    @checked(in_array($slug, old('schools', $expert->schools ?? []), true))>
                                {{ $name }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Pakar tampil di halaman sekolah yang dicentang dan di halaman Expert. Kosongkan untuk penulis tamu atau tim.</p>
                    @error('schools.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </fieldset>

                <div class="grid sm:grid-cols-2 gap-6">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="featured" value="1" class="rounded border-gray-300 text-primary-600" @checked(old('featured', $expert->featured))>
                        Tampil di beranda (nama di bawah foto bersama)
                    </label>
                    <div>
                        <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                        <input type="number" name="order" id="order" min="0" max="9999" value="{{ old('order', $expert->order) }}"
                            class="w-32 block px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
                        @error('order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
