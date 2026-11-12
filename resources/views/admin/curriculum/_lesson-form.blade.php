{{-- Expects: $action, $method, $lesson (nullable), $formId --}}
<form action="{{ $action }}" method="POST" x-data="{ type: '{{ $lesson?->type ?? 'video' }}' }" class="space-y-4">
    @csrf
    @method($method)

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Judul materi</label>
        <input type="text" name="title" value="{{ $lesson?->title }}" required placeholder="mis. Pengenalan Personal Branding"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
    </div>

    <div>
        <span class="block text-sm font-medium text-gray-700 mb-1">Jenis materi</span>
        <div class="flex gap-3">
            <label class="flex items-center gap-2 px-3 py-2 border rounded-lg cursor-pointer" :class="type === 'video' ? 'border-primary-500 bg-primary-50' : 'border-gray-300'">
                <input type="radio" name="type" value="video" x-model="type"> <i class="fas fa-play-circle text-primary-600"></i> Video
            </label>
            <label class="flex items-center gap-2 px-3 py-2 border rounded-lg cursor-pointer" :class="type === 'text' ? 'border-primary-500 bg-primary-50' : 'border-gray-300'">
                <input type="radio" name="type" value="text" x-model="type"> <i class="fas fa-file-alt text-primary-600"></i> Tulisan
            </label>
        </div>
    </div>

    <div x-show="type === 'video'">
        <label class="block text-sm font-medium text-gray-700 mb-1">Link video YouTube</label>
        <input type="text" name="youtube_video_id" value="{{ $lesson?->youtube_video_id ? 'https://youtu.be/'.$lesson->youtube_video_id : '' }}"
            :required="type === 'video'" placeholder="Tempel link YouTube, mis. https://youtu.be/xxxx"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
        <p class="mt-1 text-xs text-gray-500">Peserta wajib menonton minimal 90% video dan tidak bisa melompati bagian yang belum ditonton.</p>
    </div>

    <div x-show="type === 'text'">
        <label class="block text-sm font-medium text-gray-700 mb-1">Isi materi</label>
        <x-trix-input :id="$formId.'_content'" name="content" :value="$lesson?->content ?? ''" class="min-h-[220px]" />
        <p class="mt-1 text-xs text-gray-500">Bisa berisi judul, daftar, link, dan gambar. Peserta harus membaca sampai bawah untuk lanjut.</p>
    </div>

    <div class="flex flex-wrap items-end gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (menit, opsional)</label>
            <p class="text-xs text-gray-500 mb-1">Hanya keterangan untuk peserta. Panjang video asli diambil otomatis dari YouTube.</p>
            <input type="number" name="duration" min="0" value="{{ $lesson?->duration }}"
                class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-700 pb-2">
            <input type="checkbox" name="is_preview" value="1" @checked($lesson?->is_preview) class="h-4 w-4 rounded border-gray-300">
            Preview gratis (bisa dibuka sebelum daftar)
        </label>
    </div>

    <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700">Simpan materi</button>
        <button type="button" @click="$dispatch('close-form')" class="px-4 py-2 bg-white border border-gray-300 text-sm rounded-lg">Batal</button>
    </div>
</form>
