{{-- Dropdown of experts for the course instructor and the article author. Expects $experts, $label and $selected (an expert id or null). --}}
<div>
    <label for="expert_id" class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
    <select name="expert_id" id="expert_id" required
        class="w-full block px-4 py-2.5 border border-gray-300 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition">
        <option value="">Pilih pakar…</option>
        @foreach($experts as $expert)
            <option value="{{ $expert->id }}" @selected((string) old('expert_id', $selected) === (string) $expert->id)>{{ $expert->name }}</option>
        @endforeach
    </select>
    <p class="mt-1 text-xs text-gray-500">
        Tidak ada di daftar?
        @can('view course categories')<a href="{{ route('admin.experts.create') }}" target="_blank" class="text-primary-600 hover:underline">Tambah pakar</a> lalu muat ulang halaman ini.@else Minta admin menambahkannya di menu Pakar.@endcan
    </p>
    @error('expert_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
