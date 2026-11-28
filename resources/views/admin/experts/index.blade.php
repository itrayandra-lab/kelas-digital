@extends('layouts.admin')

@section('title', 'Pakar')

@section('content')

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <p class="text-sm text-gray-600 max-w-2xl">Instruktur kelas dan penulis artikel dipilih dari daftar ini, jadi satu orang selalu tertulis dengan nama yang sama. Mengganti nama di sini ikut mengganti namanya di semua kelas dan artikel.</p>
        <a href="{{ route('admin.experts.create') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300 shrink-0">
            <i class="fas fa-plus mr-2 -ml-1 text-base"></i>
            Tambah Pakar
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg mb-6">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Sekolah</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Artikel</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($experts as $expert)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $expert->name }}
                                @if($expert->featured)<span class="ml-2 inline-block px-2 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-700">Beranda</span>@endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ collect($expert->schools)->map(fn ($slug) => collect(config('homepage.families'))->firstWhere('slug', $slug)['name'] ?? $slug)->implode(', ') ?: '–' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $expert->courses_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $expert->articles_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-4">
                                <a href="{{ route('admin.experts.edit', $expert) }}" class="text-primary-600 hover:text-primary-800">Edit</a>
                                @if(! $expert->courses_count && ! $expert->articles_count)
                                    <form action="{{ route('admin.experts.destroy', $expert) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pakar ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">Belum ada pakar. Klik "Tambah Pakar".</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $experts->links() }}</div>

@endsection
