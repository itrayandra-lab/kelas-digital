{{-- Expects: $section, $kind. The question list and "add question" form of one section's pre-test or post-test. --}}
@php $questions = $section->questions->where('kind', $kind)->values(); @endphp
<div class="border border-gray-200 rounded-lg" data-tour="{{ $kind }}-block">
    <div class="flex items-center justify-between px-3 py-2 bg-gray-50 border-b border-gray-200 rounded-t-lg">
        <div class="text-sm font-semibold text-gray-900">
            <i class="fas {{ $kind === 'pre' ? 'fa-clipboard-list text-blue-600' : 'fa-clipboard-check text-purple-600' }} mr-1"></i>
            {{ $kind === 'pre' ? 'Pre-test' : 'Post-test' }}
            <span class="font-normal text-gray-500">· {{ $questions->count() }} soal</span>
        </div>
        <span class="text-xs text-gray-500">{{ $kind === 'pre' ? 'Pilihan ganda & benar/salah' : 'Boleh essay (dinilai AI)' }}</span>
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($questions as $question)
            <div x-data="{ editing: false }" @close-form="editing = false">
                <div class="flex items-start gap-2 p-3">
                    <div class="flex flex-col pt-0.5">
                        <form method="POST" action="{{ route('admin.questions.move', [$question, 'up']) }}">@csrf
                            <button class="text-gray-300 hover:text-gray-700 disabled:opacity-30" @disabled($loop->first)><i class="fas fa-chevron-up text-xs"></i></button>
                        </form>
                        <form method="POST" action="{{ route('admin.questions.move', [$question, 'down']) }}">@csrf
                            <button class="text-gray-300 hover:text-gray-700 disabled:opacity-30" @disabled($loop->last)><i class="fas fa-chevron-down text-xs"></i></button>
                        </form>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-gray-500 mb-0.5">{{ $loop->iteration }}. {{ \App\Models\CourseQuestion::TYPES[$question->type] ?? $question->type }}</div>
                        <div class="text-sm text-gray-900 whitespace-pre-line">{{ $question->question }}</div>
                        @if($question->type === 'essay')
                            <div class="mt-1 text-xs text-gray-600"><span class="font-semibold text-green-700">Kunci:</span> {{ Str::limit($question->answer_key, 140) }}</div>
                        @elseif($question->options->isNotEmpty())
                            <div class="mt-1 text-xs text-green-700"><i class="fas fa-check-circle"></i> {{ $question->options->where('is_correct', true)->pluck('text')->implode(', ') }}</div>
                        @endif
                    </div>
                    <button type="button" @click="editing = !editing" class="text-sm text-gray-500 hover:text-primary-600"><i class="fas fa-pen"></i></button>
                    <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('Hapus soal ini?')">
                        @csrf @method('DELETE')
                        <button class="text-sm text-gray-500 hover:text-red-600"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
                <div x-show="editing" x-cloak class="px-3 pb-3 pt-2 bg-gray-50">
                    @include('admin.curriculum._question-form', [
                        'action' => route('admin.questions.update', $question),
                        'method' => 'PUT',
                        'question' => $question,
                    ])
                </div>
            </div>
        @empty
            <p class="p-3 text-sm text-gray-500">Belum ada soal. Tanpa soal, {{ $kind === 'pre' ? 'pre-test' : 'post-test' }} modul ini dilewati.</p>
        @endforelse
    </div>

    <div class="p-3 border-t border-gray-100" x-data="{ adding: false }" @close-form="adding = false">
        <button type="button" x-show="!adding" @click="adding = true" class="text-sm font-semibold text-primary-600 hover:text-primary-700" data-tour="add-question">
            <i class="fas fa-plus mr-1"></i> Tambah soal {{ $kind === 'pre' ? 'pre-test' : 'post-test' }}
        </button>
        <div x-show="adding" x-cloak>
            @include('admin.curriculum._question-form', [
                'action' => route('admin.questions.store', [$section, $kind]),
                'method' => 'POST',
                'question' => null,
            ])
        </div>

        @if($questions->isEmpty())
            <form method="POST" action="{{ route('admin.questions.import', [$section, $kind]) }}" enctype="multipart/form-data" x-show="!adding" class="mt-3 pt-3 border-t border-dashed border-gray-200">
                @csrf
                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-file-word text-blue-600 mr-1"></i> Atau impor semua soal dari file Word</label>
                <div class="flex flex-wrap items-center gap-2">
                    <input type="file" name="docx" accept=".docx" required
                        class="text-sm text-gray-700 file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-primary-50 file:text-primary-700">
                    <button class="px-3 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700">Impor soal</button>
                </div>
                <p class="mt-1 text-xs text-gray-500">
                    Ikuti template tes RayAcademy: soal bernomor <strong>1. ...</strong>, opsi <strong>A. ...</strong> sampai <strong>D. ...</strong>, lalu tabel kunci jawaban (kolom No dan Kunci).
                    @if($kind === 'post') Essay ditulis dengan judul <strong>SOAL ESSAY 1: ...</strong> dan panduan penilaiannya <strong>PANDUAN JAWABAN IDEAL ESSAY 1 ...</strong>; panduan ini dipakai AI untuk menilai. @endif
                    Kalau ada yang tidak terbaca, tidak ada soal yang disimpan dan nomor soal yang bermasalah ditampilkan.
                </p>
            </form>
        @endif
    </div>
</div>
