<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseQuestion;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * One page per course where admins build the whole curriculum: sections, lessons and test questions.
 */
class CurriculumController extends Controller
{
    public function show(Course $course)
    {
        $course->load(['sections.lessons', 'questions.options']);

        // shortcut: newest 200 attempts only, add pagination once a course has more learners than that
        $attempts = QuizAttempt::with('user')
            ->where('course_id', $course->id)
            ->orderByRaw("status = 'pending' desc")
            ->latest()
            ->limit(200)
            ->get();

        return view('admin.curriculum.show', compact('course', 'attempts'));
    }

    /**
     * Admin marks each essay answer right or wrong; the attempt is then scored like any other.
     */
    public function reviewAttempt(Request $request, QuizAttempt $attempt)
    {
        $course = $attempt->course;
        $questions = $course->questions()->with('options')->get();
        $essayIds = $questions->where('type', 'essay')->pluck('id')->map(fn ($id) => (string) $id);

        $data = $request->validate([
            'marks' => 'required|array',
            'marks.*' => 'required|boolean',
        ], ['marks.required' => 'Nilai semua jawaban essay dulu.']);

        if ($essayIds->diff(array_keys($data['marks']))->isNotEmpty()) {
            return $this->back($course, null, 'jawaban')->withErrors(['marks' => 'Nilai semua jawaban essay dulu.']);
        }

        $attempt->essay_marks = $essayIds->mapWithKeys(fn ($id) => [$id => (bool) $data['marks'][$id]])->all();
        $attempt->reviewed_at = now();
        $attempt->grade($questions, $course->post_test_pass_score);
        $attempt->save();

        return $this->back($course, "Penilaian {$attempt->user->name} disimpan. Nilai: {$attempt->score}.", 'jawaban');
    }

    // ---------- Sections ----------

    public function storeSection(Request $request, Course $course)
    {
        $data = $request->validate(['title' => 'required|string|max:255']);

        $course->sections()->create([
            'title' => $data['title'],
            'order' => (int) $course->sections()->max('order') + 1,
        ]);

        return $this->back($course, 'Bab ditambahkan.');
    }

    public function updateSection(Request $request, CourseSection $section)
    {
        $section->update($request->validate(['title' => 'required|string|max:255']));

        return $this->back($section->course, 'Nama bab disimpan.');
    }

    public function destroySection(CourseSection $section)
    {
        $course = $section->course;
        $section->delete();

        return $this->back($course, 'Bab dan semua materinya dihapus.');
    }

    public function moveSection(CourseSection $section, string $direction)
    {
        $this->swap($section->course->sections()->get(), $section, $direction);

        return $this->back($section->course);
    }

    // ---------- Lessons ----------

    public function storeLesson(Request $request, CourseSection $section)
    {
        $section->lessons()->create($this->lessonData($request) + [
            'course_id' => $section->course_id,
            'order' => (int) $section->lessons()->max('order') + 1,
        ]);

        return $this->back($section->course, 'Materi ditambahkan.');
    }

    public function updateLesson(Request $request, Lesson $lesson)
    {
        $lesson->update($this->lessonData($request));

        return $this->back($lesson->course, 'Materi disimpan.');
    }

    public function destroyLesson(Lesson $lesson)
    {
        $course = $lesson->course;
        $lesson->delete();

        return $this->back($course, 'Materi dihapus.');
    }

    public function moveLesson(Lesson $lesson, string $direction)
    {
        $this->swap($lesson->section->lessons()->get(), $lesson, $direction);

        return $this->back($lesson->course);
    }

    // ---------- Test questions (shared by pre- and post-test) ----------

    public function updateSettings(Request $request, Course $course)
    {
        $course->update($request->validate(['post_test_pass_score' => 'required|integer|min:1|max:100']));

        return $this->back($course, 'Nilai minimal lulus disimpan.', 'tes');
    }

    public function storeQuestion(Request $request, Course $course)
    {
        DB::transaction(function () use ($request, $course) {
            $question = $course->questions()->create($this->questionData($request) + [
                'order' => (int) $course->questions()->max('order') + 1,
            ]);
            $this->syncOptions($question, $request);
        });

        return $this->back($course, 'Soal ditambahkan.', 'tes');
    }

    public function updateQuestion(Request $request, CourseQuestion $question)
    {
        DB::transaction(function () use ($request, $question) {
            $question->update($this->questionData($request));
            $this->syncOptions($question, $request);
        });

        return $this->back($question->course, 'Soal disimpan.', 'tes');
    }

    public function destroyQuestion(CourseQuestion $question)
    {
        $course = $question->course;
        $question->delete();

        return $this->back($course, 'Soal dihapus.', 'tes');
    }

    public function moveQuestion(CourseQuestion $question, string $direction)
    {
        $this->swap($question->course->questions()->get(), $question, $direction);

        return $this->back($question->course, null, 'tes');
    }

    // ---------- Helpers ----------

    private function lessonData(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'type' => ['required', Rule::in(['video', 'text'])],
            'youtube_video_id' => 'required_if:type,video|nullable|string|max:255',
            'content' => 'required_if:type,text|nullable|string',
            'duration' => 'nullable|integer|min:0|max:1000',
            'is_preview' => 'nullable|boolean',
        ], [
            'youtube_video_id.required_if' => 'Link video YouTube wajib diisi untuk materi video.',
            'content.required_if' => 'Isi materi wajib diisi untuk materi tulisan.',
        ]);

        $data['is_preview'] = $request->boolean('is_preview');

        if ($data['type'] === 'video') {
            $data['content'] = null;
        } else {
            $data['youtube_video_id'] = null;
        }

        return $data;
    }

    private function questionData(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(CourseQuestion::TYPES))],
            'question' => 'required|string|max:5000',
            'options' => 'exclude_if:type,essay|exclude_if:type,true_false|required|array|min:2',
            'options.*.text' => 'exclude_if:type,essay|exclude_if:type,true_false|required|string|max:500',
            'correct' => 'exclude_if:type,essay|required|array|min:1',
        ], [
            'options.min' => 'Minimal 2 opsi jawaban.',
            'options.*.text.required' => 'Teks opsi jawaban tidak boleh kosong.',
            'correct.required' => 'Tandai minimal satu jawaban yang benar.',
        ]);

        if (in_array($data['type'], ['single', 'true_false']) && count($data['correct']) !== 1) {
            throw \Illuminate\Validation\ValidationException::withMessages(['correct' => 'Soal ini hanya boleh punya satu jawaban benar.']);
        }

        return ['type' => $data['type'], 'question' => $data['question']];
    }

    /**
     * Options are replaced wholesale on every save; answers in past attempts are kept as a snapshot.
     */
    private function syncOptions(CourseQuestion $question, Request $request): void
    {
        $question->options()->delete();

        if ($question->type === 'essay') {
            return;
        }

        $correct = array_map('strval', (array) $request->input('correct', []));
        $options = $question->type === 'true_false'
            ? [['key' => 'true', 'text' => 'Benar'], ['key' => 'false', 'text' => 'Salah']]
            : collect($request->input('options', []))->map(fn ($o, $i) => ['key' => (string) $i, 'text' => $o['text']])->values()->all();

        foreach ($options as $option) {
            $question->options()->create([
                'text' => $option['text'],
                'is_correct' => in_array($option['key'], $correct, true),
            ]);
        }
    }

    private function swap($items, $item, string $direction): void
    {
        $items = $items->values();
        $index = $items->search(fn ($i) => $i->id === $item->id);
        $other = $items->get($direction === 'up' ? $index - 1 : $index + 1);

        if (! $other) {
            return;
        }

        // Renumber everything first so equal/legacy order values can't make a swap a no-op.
        $items->each(fn ($i, $n) => $i->update(['order' => $n + 1]));
        [$a, $b] = [$item->fresh()->order, $other->fresh()->order];
        $item->update(['order' => $b]);
        $other->update(['order' => $a]);
    }

    private function back(Course $course, ?string $message = null, ?string $tab = null)
    {
        $redirect = redirect()->to(route('admin.curriculum.show', $course).($tab ? '?tab='.$tab : ''));

        return $message ? $redirect->with('success', $message) : $redirect;
    }
}
