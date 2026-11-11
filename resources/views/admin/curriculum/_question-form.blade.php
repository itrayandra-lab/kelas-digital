{{-- Expects: $action, $method, $question (nullable) --}}
@php
    $isTf = $question?->type === 'true_false';
    $state = [
        'type' => $question?->type ?? 'single',
        'options' => ($question && ! $isTf && $question->options->isNotEmpty())
            ? $question->options->map(fn ($o) => ['text' => $o->text])->values()
            : [['text' => ''], ['text' => ''], ['text' => ''], ['text' => '']],
        'correct' => $question
            ? ($isTf
                ? [$question->options->firstWhere('is_correct', true)?->text === 'Salah' ? 'false' : 'true']
                : $question->options->values()->filter->is_correct->keys()->map(fn ($k) => (string) $k)->values())
            : [],
    ];
@endphp
<form action="{{ $action }}" method="POST" x-data="{{ json_encode($state) }}" class="space-y-4">
    @csrf
    @method($method)

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis soal</label>
        <select name="type" x-model="type" @change="correct = []"
            class="w-full md:w-80 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            @foreach(\App\Models\CourseQuestion::TYPES as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Pertanyaan</label>
        <textarea name="question" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">{{ $question?->question }}</textarea>
    </div>

    {{-- Choice questions: option text + mark which one(s) are correct --}}
    <div x-show="type === 'single' || type === 'multiple'">
        <span class="block text-sm font-medium text-gray-700 mb-1">
            Opsi jawaban
            <span class="font-normal text-gray-500" x-text="type === 'multiple' ? '— centang semua yang benar' : '— pilih satu yang benar'"></span>
        </span>
        <template x-for="(opt, i) in options" :key="i">
            <div class="flex items-center gap-2 mb-2">
                <input :type="type === 'multiple' ? 'checkbox' : 'radio'" name="correct[]" :value="String(i)" x-model="correct"
                    :disabled="!(type === 'single' || type === 'multiple')" class="h-4 w-4" title="Jawaban benar">
                <span class="w-6 text-sm text-gray-500" x-text="String.fromCharCode(65 + i) + '.'"></span>
                <input type="text" :name="'options[' + i + '][text]'" x-model="opt.text" :required="type === 'single' || type === 'multiple'"
                    :disabled="!(type === 'single' || type === 'multiple')" placeholder="Teks opsi"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                <button type="button" x-show="options.length > 2" @click="options.splice(i, 1); correct = correct.filter(c => c !== String(i)).map(c => Number(c) > i ? String(Number(c) - 1) : c)"
                    class="text-red-500 hover:text-red-700 px-2" title="Hapus opsi"><i class="fas fa-times"></i></button>
            </div>
        </template>
        <button type="button" @click="options.push({ text: '' })" class="text-sm text-primary-600 font-semibold"><i class="fas fa-plus mr-1"></i> Tambah opsi</button>
    </div>

    <div x-show="type === 'true_false'">
        <span class="block text-sm font-medium text-gray-700 mb-1">Jawaban yang benar</span>
        <div class="flex gap-3">
            <label class="flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg cursor-pointer">
                <input type="radio" name="correct[]" value="true" x-model="correct" :disabled="type !== 'true_false'"> Benar
            </label>
            <label class="flex items-center gap-2 px-3 py-2 border border-gray-300 rounded-lg cursor-pointer">
                <input type="radio" name="correct[]" value="false" x-model="correct" :disabled="type !== 'true_false'"> Salah
            </label>
        </div>
    </div>

    <p x-show="type === 'essay'" class="text-sm text-gray-500 bg-gray-50 rounded-lg p-3">
        Essay tidak punya opsi. Jawaban dianggap benar asal peserta sudah mengisinya.
    </p>

    <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700">Simpan soal</button>
        <button type="button" @click="$dispatch('close-form')" class="px-4 py-2 bg-white border border-gray-300 text-sm rounded-lg">Batal</button>
    </div>
</form>
