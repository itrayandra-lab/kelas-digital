@extends('layouts.admin')

@section('title', 'Kurikulum — ' . $course->title)

@section('content')

<div x-data="{ tab: '{{ in_array(request('tab'), ['tes', 'jawaban']) ? request('tab') : 'kurikulum' }}' }">

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <div>
            <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke daftar kelas
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $course->title }}</h1>
            <p class="text-sm text-gray-500">
                {{ $course->sections->count() }} bab · {{ $course->sections->sum(fn ($s) => $s->lessons->count()) }} materi · {{ $course->questions->count() }} soal tes
            </p>
        </div>
        <a href="{{ route('course.show', $course->slug) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-sm font-semibold rounded-lg hover:bg-gray-50">
            <i class="fas fa-external-link-alt mr-2"></i> Lihat halaman kelas
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 text-sm rounded-lg">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    {{-- Alur yang dilihat peserta --}}
    <div data-tour="flow" class="mb-6 p-4 bg-blue-50 border border-blue-100 rounded-lg text-sm text-blue-900">
        <strong>Alur peserta:</strong>
        {{ $course->questions->isNotEmpty() ? 'Pre-test →' : '' }}
        materi bab demi bab (harus berurutan)
        {{ $course->questions->isNotEmpty() ? '→ Post-test (lulus minimal '.$course->post_test_pass_score.')' : '' }}
        → Selamat, kelas selesai.
        @if($course->questions->isEmpty())
            <span class="text-blue-700">Tambahkan soal di tab "Soal Tes" untuk mengaktifkan pre-test dan post-test.</span>
        @endif
    </div>

    <div class="flex gap-1 border-b border-gray-200 mb-6">
        <button type="button" data-tour="tab-kurikulum" @click="tab = 'kurikulum'" class="px-4 py-2 text-sm font-semibold border-b-2 -mb-px"
            :class="tab === 'kurikulum' ? 'border-primary-600 text-primary-700' : 'border-transparent text-gray-500'">
            <i class="fas fa-list-ol mr-1"></i> Kurikulum
        </button>
        <button type="button" data-tour="tab-tes" @click="tab = 'tes'" class="px-4 py-2 text-sm font-semibold border-b-2 -mb-px"
            :class="tab === 'tes' ? 'border-primary-600 text-primary-700' : 'border-transparent text-gray-500'">
            <i class="fas fa-clipboard-list mr-1"></i> Soal Tes ({{ $course->questions->count() }})
        </button>
        @php $pendingCount = $attempts->where('status', 'pending')->count(); @endphp
        <button type="button" data-tour="tab-jawaban" @click="tab = 'jawaban'" class="px-4 py-2 text-sm font-semibold border-b-2 -mb-px"
            :class="tab === 'jawaban' ? 'border-primary-600 text-primary-700' : 'border-transparent text-gray-500'">
            <i class="fas fa-user-check mr-1"></i> Jawaban Peserta
            @if($pendingCount)<span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-800">{{ $pendingCount }} perlu dinilai</span>@endif
        </button>
    </div>

    {{-- ===================== TAB: KURIKULUM ===================== --}}
    <div x-show="tab === 'kurikulum'" class="space-y-4">
        @forelse($course->sections as $section)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ renaming: false, adding: false }">
                <div class="flex items-center gap-3 px-4 py-3 bg-gray-50 border-b border-gray-200 rounded-t-lg">
                    <div class="flex flex-col">
                        <form method="POST" action="{{ route('admin.sections.move', [$section, 'up']) }}">@csrf
                            <button class="text-gray-400 hover:text-gray-700 disabled:opacity-30" @disabled($loop->first) title="Naikkan"><i class="fas fa-chevron-up text-xs"></i></button>
                        </form>
                        <form method="POST" action="{{ route('admin.sections.move', [$section, 'down']) }}">@csrf
                            <button class="text-gray-400 hover:text-gray-700 disabled:opacity-30" @disabled($loop->last) title="Turunkan"><i class="fas fa-chevron-down text-xs"></i></button>
                        </form>
                    </div>
                    <div class="flex-1">
                        <div x-show="!renaming" class="font-semibold text-gray-900">
                            <span class="text-gray-500 font-normal">Bab {{ $loop->iteration }}:</span> {{ $section->title }}
                        </div>
                        <form x-show="renaming" x-cloak method="POST" action="{{ route('admin.sections.update', $section) }}" class="flex gap-2">
                            @csrf @method('PUT')
                            <input type="text" name="title" value="{{ $section->title }}" required class="flex-1 px-3 py-1.5 border border-gray-300 rounded-lg text-sm">
                            <button class="px-3 py-1.5 bg-primary-600 text-white text-sm rounded-lg">Simpan</button>
                            <button type="button" @click="renaming = false" class="px-3 py-1.5 border border-gray-300 text-sm rounded-lg">Batal</button>
                        </form>
                    </div>
                    <div x-show="!renaming" class="flex items-center gap-3 text-sm">
                        <button type="button" @click="renaming = true" class="text-gray-500 hover:text-primary-600" title="Ganti nama"><i class="fas fa-pen"></i></button>
                        <form method="POST" action="{{ route('admin.sections.destroy', $section) }}" onsubmit="return confirm('Hapus bab ini beserta semua materinya?')">
                            @csrf @method('DELETE')
                            <button class="text-gray-500 hover:text-red-600" title="Hapus bab"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($section->lessons as $lesson)
                        <div x-data="{ editing: false }" @close-form="editing = false">
                            <div class="flex items-center gap-3 px-4 py-3">
                                <div class="flex flex-col">
                                    <form method="POST" action="{{ route('admin.curriculum.lessons.move', [$lesson, 'up']) }}">@csrf
                                        <button class="text-gray-300 hover:text-gray-700 disabled:opacity-30" @disabled($loop->first)><i class="fas fa-chevron-up text-xs"></i></button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.curriculum.lessons.move', [$lesson, 'down']) }}">@csrf
                                        <button class="text-gray-300 hover:text-gray-700 disabled:opacity-30" @disabled($loop->last)><i class="fas fa-chevron-down text-xs"></i></button>
                                    </form>
                                </div>
                                <i class="fas {{ $lesson->isVideo() ? 'fa-play-circle text-red-500' : 'fa-file-alt text-blue-500' }}"></i>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm text-gray-900">{{ $loop->parent->iteration }}.{{ $loop->iteration }} {{ $lesson->title }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $lesson->isVideo() ? 'Video' : 'Tulisan' }}
                                        @if($lesson->duration) · {{ $lesson->duration }} menit @endif
                                        @if($lesson->is_preview) · <span class="text-green-600 font-semibold">Preview gratis</span> @endif
                                    </div>
                                </div>
                                <button type="button" @click="editing = !editing" class="text-sm text-gray-500 hover:text-primary-600"><i class="fas fa-pen"></i></button>
                                <form method="POST" action="{{ route('admin.curriculum.lessons.destroy', $lesson) }}" onsubmit="return confirm('Hapus materi ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-sm text-gray-500 hover:text-red-600"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                            <div x-show="editing" x-cloak class="px-4 pb-4 pt-1 bg-gray-50">
                                @include('admin.curriculum._lesson-form', [
                                    'action' => route('admin.curriculum.lessons.update', $lesson),
                                    'method' => 'PUT',
                                    'lesson' => $lesson,
                                    'formId' => 'lesson_'.$lesson->id,
                                ])
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-4 py-3 border-t border-gray-100" @close-form="adding = false">
                    <button type="button" data-tour="add-lesson" x-show="!adding" @click="adding = true" class="text-sm font-semibold text-primary-600 hover:text-primary-700">
                        <i class="fas fa-plus mr-1"></i> Tambah materi di bab ini
                    </button>
                    <div x-show="adding" x-cloak>
                        @include('admin.curriculum._lesson-form', [
                            'action' => route('admin.curriculum.lessons.store', $section),
                            'method' => 'POST',
                            'lesson' => null,
                            'formId' => 'new_lesson_'.$section->id,
                        ])
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500 bg-white rounded-lg border-2 border-dashed border-gray-200">
                Belum ada bab. Mulai dengan membuat bab pertama di bawah.
            </div>
        @endforelse

        <form data-tour="add-section" method="POST" action="{{ route('admin.sections.store', $course) }}" class="flex gap-2 bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            @csrf
            <input type="text" name="title" required placeholder="Nama bab baru, mis. Bab 1: Dasar-dasar"
                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            <button class="px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700"><i class="fas fa-plus mr-1"></i> Tambah Bab</button>
        </form>
    </div>

    {{-- ===================== TAB: SOAL TES ===================== --}}
    <div x-show="tab === 'tes'" x-cloak class="space-y-4">
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row md:items-center gap-4 justify-between">
            <p class="text-sm text-gray-600">Soal yang sama dipakai untuk <strong>pre-test</strong> (cukup dikerjakan) dan <strong>post-test</strong> (harus lulus).</p>
            <form data-tour="pass-score" method="POST" action="{{ route('admin.curriculum.settings', $course) }}" class="flex items-center gap-2">
                @csrf @method('PUT')
                <label class="text-sm text-gray-700">Nilai minimal lulus post-test</label>
                <input type="number" name="post_test_pass_score" min="1" max="100" value="{{ $course->post_test_pass_score }}" class="w-20 px-2 py-1.5 border border-gray-300 rounded-lg text-sm">
                <button class="px-3 py-1.5 bg-primary-600 text-white text-sm rounded-lg">Simpan</button>
            </form>
        </div>

        @foreach($course->questions as $question)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ editing: false }" @close-form="editing = false">
                <div class="flex items-start gap-3 p-4">
                    <div class="flex flex-col pt-1">
                        <form method="POST" action="{{ route('admin.questions.move', [$question, 'up']) }}">@csrf
                            <button class="text-gray-300 hover:text-gray-700 disabled:opacity-30" @disabled($loop->first)><i class="fas fa-chevron-up text-xs"></i></button>
                        </form>
                        <form method="POST" action="{{ route('admin.questions.move', [$question, 'down']) }}">@csrf
                            <button class="text-gray-300 hover:text-gray-700 disabled:opacity-30" @disabled($loop->last)><i class="fas fa-chevron-down text-xs"></i></button>
                        </form>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-gray-500 mb-1">Soal {{ $loop->iteration }} · {{ \App\Models\CourseQuestion::TYPES[$question->type] ?? $question->type }}</div>
                        <div class="text-sm font-medium text-gray-900 whitespace-pre-line">{{ $question->question }}</div>
                        @if($question->options->isNotEmpty())
                            <ul class="mt-2 space-y-1">
                                @foreach($question->options as $opt)
                                    <li class="text-sm {{ $opt->is_correct ? 'text-green-700 font-semibold' : 'text-gray-600' }}">
                                        <i class="fas {{ $opt->is_correct ? 'fa-check-circle' : 'fa-circle text-gray-300' }} mr-1 text-xs"></i> {{ $opt->text }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <button type="button" @click="editing = !editing" class="text-sm text-gray-500 hover:text-primary-600"><i class="fas fa-pen"></i></button>
                    <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('Hapus soal ini?')">
                        @csrf @method('DELETE')
                        <button class="text-sm text-gray-500 hover:text-red-600"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
                <div x-show="editing" x-cloak class="px-4 pb-4 border-t border-gray-100 pt-4 bg-gray-50">
                    @include('admin.curriculum._question-form', [
                        'action' => route('admin.questions.update', $question),
                        'method' => 'PUT',
                        'question' => $question,
                    ])
                </div>
            </div>
        @endforeach

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4" x-data="{ adding: {{ $course->questions->isEmpty() ? 'true' : 'false' }} }" @close-form="adding = false">
            <button type="button" data-tour="add-question" x-show="!adding" @click="adding = true" class="text-sm font-semibold text-primary-600 hover:text-primary-700">
                <i class="fas fa-plus mr-1"></i> Tambah soal
            </button>
            <div x-show="adding" x-cloak>
                <h3 class="font-semibold text-gray-900 mb-3">Soal baru</h3>
                @include('admin.curriculum._question-form', [
                    'action' => route('admin.questions.store', $course),
                    'method' => 'POST',
                    'question' => null,
                ])
            </div>
        </div>
    </div>

    {{-- ===================== TAB: JAWABAN PESERTA ===================== --}}
    <div x-show="tab === 'jawaban'" x-cloak class="space-y-3">
        <p class="text-sm text-gray-600">
            Jawaban pilihan ganda dan benar/salah dinilai otomatis. Kalau ada <strong>essay</strong>, nilai peserta berstatus
            <em>sedang diverifikasi</em> sampai kamu menandai essay-nya benar atau salah di sini.
        </p>

        @php $questionsById = $course->questions->keyBy('id'); @endphp
        @forelse($attempts as $attempt)
            <div class="bg-white rounded-lg shadow-sm border {{ $attempt->isPending() ? 'border-amber-300' : 'border-gray-200' }}" x-data="{ open: {{ $attempt->isPending() && $loop->first ? 'true' : 'false' }} }">
                <button type="button" @click="open = !open" class="w-full flex flex-wrap items-center gap-3 px-4 py-3 text-left">
                    <div class="w-9 h-9 rounded-full bg-primary-50 text-primary-700 font-bold flex items-center justify-center">{{ strtoupper(substr($attempt->user?->name ?? '?', 0, 1)) }}</div>
                    <div class="flex-1 min-w-[180px]">
                        <div class="text-sm font-semibold text-gray-900">{{ $attempt->user?->name ?? 'Pengguna dihapus' }}</div>
                        <div class="text-xs text-gray-500">{{ $attempt->user?->email }} · {{ $attempt->created_at->translatedFormat('d M Y H:i') }}</div>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $attempt->kind === 'pre' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">{{ $attempt->kind === 'pre' ? 'Pre-test' : 'Post-test' }}</span>
                    @if($attempt->isPending())
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800"><i class="fas fa-hourglass-half mr-1"></i> Perlu dinilai</span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $attempt->kind === 'pre' ? 'bg-gray-100 text-gray-700' : ($attempt->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-700') }}">
                            Nilai {{ $attempt->score }}{{ $attempt->kind === 'post' ? ($attempt->passed ? ' · Lulus' : ' · Belum lulus') : '' }}
                        </span>
                    @endif
                    <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform" :class="open && 'rotate-180'"></i>
                </button>

                <div x-show="open" x-cloak class="border-t border-gray-100 px-4 py-4">
                    <form method="POST" action="{{ route('admin.attempts.review', $attempt) }}" class="space-y-4">
                        @csrf @method('PUT')
                        @foreach($attempt->answers as $questionId => $answer)
                            @php $q = $questionsById->get($questionId); @endphp
                            @continue(! $q)
                            <div class="text-sm">
                                <div class="font-medium text-gray-900">{{ $loop->iteration }}. {{ $q->question }}</div>
                                @if($q->type === 'essay')
                                    <div class="mt-1 p-3 bg-gray-50 rounded-lg text-gray-800 whitespace-pre-line">{{ $answer }}</div>
                                    @php $mark = $attempt->essay_marks[$q->id] ?? null; @endphp
                                    <div class="mt-2 flex gap-2">
                                        <label class="flex items-center gap-2 px-3 py-1.5 border rounded-lg cursor-pointer {{ $mark === true ? 'border-green-500 bg-green-50' : 'border-gray-300' }}">
                                            <input type="radio" name="marks[{{ $q->id }}]" value="1" required @checked($mark === true)> Benar
                                        </label>
                                        <label class="flex items-center gap-2 px-3 py-1.5 border rounded-lg cursor-pointer {{ $mark === false ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                                            <input type="radio" name="marks[{{ $q->id }}]" value="0" required @checked($mark === false)> Salah
                                        </label>
                                    </div>
                                @else
                                    @php
                                        $picked = $q->options->whereIn('id', collect((array) $answer)->map(fn ($id) => (int) $id))->pluck('text');
                                        $right = $q->isAnsweredCorrectly($answer);
                                    @endphp
                                    <div class="mt-1 flex items-start gap-2 {{ $right ? 'text-green-700' : 'text-red-600' }}">
                                        <i class="fas {{ $right ? 'fa-check-circle' : 'fa-times-circle' }} mt-0.5"></i>
                                        <span>{{ $picked->implode(', ') ?: '(tidak dijawab)' }}</span>
                                    </div>
                                    @unless($right)
                                        <div class="text-xs text-gray-500 ml-6">Jawaban benar: {{ $q->options->where('is_correct', true)->pluck('text')->implode(', ') }}</div>
                                    @endunless
                                @endif
                            </div>
                        @endforeach

                        @if($course->questions->contains('type', 'essay'))
                            <button class="px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700">
                                {{ $attempt->isPending() ? 'Simpan Penilaian' : 'Ubah Penilaian' }}
                            </button>
                        @endif
                    </form>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500 bg-white rounded-lg border-2 border-dashed border-gray-200">Belum ada peserta yang mengerjakan tes.</div>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
@include('partials.tour-assets')
<script>
KATour.page('admin-curriculum', [
  { element: '[data-tour="flow"]', popover: { title: 'Alur yang dilihat peserta', description: 'Peserta mengerjakan pre-test, lalu membuka materi satu per satu sesuai urutan di sini, lalu post-test. Setelah lulus muncul halaman Selamat.', side: 'bottom' } },
  { element: '[data-tour="add-section"]', popover: { title: 'Buat bab', description: 'Bab mengelompokkan materi (mis. "Bab 1: Dasar-dasar"). Urutan bab bisa diubah dengan panah naik/turun.', side: 'top' } },
  { element: '[data-tour="add-lesson"]', popover: { title: 'Tambah materi', description: 'Pilih Video (tempel link YouTube) atau Tulisan (editor teks, bisa gambar). Centang "Preview gratis" agar bisa dicoba sebelum daftar.', side: 'top' } },
  { element: '[data-tour="tab-tes"]', popover: { title: 'Soal tes', description: 'Di tab ini buat soal pilihan ganda (1 atau lebih jawaban), benar/salah, atau essay, dan atur nilai minimal lulus. Soal yang sama dipakai untuk pre-test dan post-test.', side: 'bottom' } },
  { popover: { title: 'Cek hasilnya', description: 'Klik "Lihat halaman kelas" di kanan atas untuk melihat tampilan dari sisi peserta. Tombol Panduan di pojok kanan bawah bisa diklik kapan saja untuk mengulang tur ini.' } }
]);
</script>
@endpush
