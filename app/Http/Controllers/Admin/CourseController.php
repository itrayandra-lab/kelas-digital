<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\User;
use App\Support\CourseRoster;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseController extends Controller
{
    public static function middleware(): array
    {
        return [
            'auth',
            'can:view courses',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::with('category')->withCount('enrollments')->latest()->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create courses');
        $categories = CourseCategory::orderBy('name')->get();

        return view('admin.courses.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create courses');
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'description' => 'required',
            'course_type' => 'required|in:paid,free',
            'price' => 'exclude_if:course_type,free|required|integer|min:1000',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'trailer_video_id' => 'nullable|string',
            'course_category_id' => 'required|exists:course_categories,id',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'benefits' => 'nullable|string',
            'topics_preview' => 'nullable|string',
            'schedule_start' => 'nullable|date',
            'schedule_end' => 'nullable|date|after_or_equal:schedule_start',
            'meeting_platform' => 'nullable|string|max:100',
            'meeting_link' => 'nullable|url|max:500',
        ]);

        $thumbnail = 'default-course.jpg';
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail')->store('courses', 'public');
        }

        $course = Course::create([
            'title' => $data['title'],
            'instructor' => $data['instructor'],
            'description' => $data['description'],
            'course_type' => $data['course_type'],
            'price' => $data['course_type'] === 'free' ? 0 : ($data['price'] ?? 0),
            'thumbnail' => $thumbnail,
            'trailer_video_id' => $data['trailer_video_id'] ?? '',
            'course_category_id' => $data['course_category_id'],
            'level' => $data['level'],
            'benefits' => $data['benefits'] ?? null,
            'topics_preview' => $data['topics_preview'] ?? null,
            'schedule_start' => $data['schedule_start'] ?? null,
            'schedule_end' => $data['schedule_end'] ?? null,
            'meeting_platform' => $data['meeting_platform'] ?? null,
            'meeting_link' => $data['meeting_link'] ?? null,
        ]);

        // Next step after creating a course is building its curriculum, so go straight there.
        return redirect()->route('admin.curriculum.show', $course)
            ->with('success', 'Kelas dibuat. Sekarang susun modul, materi, serta soal pre-test dan post-test-nya.');
    }

    /**
     * Display the specified resource.
     */
    /** The course's class overview: who enrolled, how far each learner got, and how each module went. */
    public function show(Request $request, string $id)
    {
        $course = Course::with('category')->findOrFail($id);
        $rows = CourseRoster::rows($course);

        $stats = [
            'total' => $rows->count(),
            'statuses' => $rows->countBy('status'),
            'finished' => $rows->where('finished', true)->count(),
            'avg_percent' => (int) round($rows->avg('percent') ?? 0),
            'new_week' => $rows->filter(fn ($r) => $r['enrollment']->created_at->gt(now()->subWeek()))->count(),
            'revenue' => $rows->where('status', 'paid')->sum(fn ($r) => (int) $r['enrollment']->paid_amount),
            // Biggest group first, ties A-Z, "Belum diisi" always last.
            'origins' => $rows->countBy('origin')->sortKeys(SORT_NATURAL | SORT_FLAG_CASE)->sortByDesc(fn ($n, $origin) => $origin === CourseRoster::NO_ORIGIN ? -1 : $n),
        ];
        $modules = CourseRoster::modules($rows);

        $search = is_string($request->query('q')) ? trim($request->query('q')) : '';
        $status = array_key_exists($request->query('status'), CourseRoster::STATUSES) ? $request->query('status') : null;
        $sort = in_array($request->query('sort'), ['progress', 'progress_low', 'active', 'origin'], true) ? $request->query('sort') : null;
        $origin = $stats['origins']->has($request->query('origin')) ? $request->query('origin') : null;

        $filtered = $rows
            ->when($search !== '', fn ($c) => $c->filter(fn ($r) => str_contains(mb_strtolower($r['user']->name.' '.$r['user']->email.' '.$r['user']->username.' '.$r['origin']), mb_strtolower($search))))
            ->when($status, fn ($c) => $c->where('status', $status))
            ->when($origin, fn ($c) => $c->where('origin', $origin))
            ->when($sort === 'origin', fn ($c) => $c->sortBy([['origin', 'asc'], [fn ($a, $b) => strcasecmp($a['user']->name, $b['user']->name)]]))
            ->when($sort === 'progress', fn ($c) => $c->sortByDesc('percent'))
            ->when($sort === 'progress_low', fn ($c) => $c->sortBy('percent'))
            ->when($sort === 'active', fn ($c) => $c->sortByDesc('last_active'))
            ->values();

        $perPage = 25;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $learners = new LengthAwarePaginator($filtered->forPage($page, $perPage)->values(), $filtered->count(), $perPage, $page, [
            'path' => $request->url(),
            'query' => $request->query(),
        ]);

        return view('admin.courses.show', compact('course', 'stats', 'modules', 'learners', 'search', 'status', 'sort', 'origin'));
    }

    /** Learner list as CSV (opens in Excel / Google Sheets), with the best pre- and post-test score per module. */
    public function exportLearners(Course $course): StreamedResponse
    {
        $rows = CourseRoster::rows($course);
        $sections = CourseRoster::modules($rows)->pluck('section');

        return response()->streamDownload(function () use ($rows, $sections) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM so Excel reads the names as UTF-8
            $header = ['Nama', 'Email', 'Username', 'Asal', 'Universitas / kantor', 'Status', 'Tanggal daftar', 'Progres (%)', 'Langkah berikutnya', 'Rata-rata post-test', 'Aktivitas terakhir'];
            foreach ($sections as $section) {
                $header[] = 'Pre-test '.$section->title;
                $header[] = 'Post-test '.$section->title;
            }
            // Semicolons: Excel with Indonesian regional settings splits columns on ";" and puts a comma-separated file in one column.
            fputcsv($out, $header, ';');

            foreach ($rows as $r) {
                $line = [
                    $r['user']->name, $r['user']->email, $r['user']->username,
                    User::ORIGINS[$r['user']->origin_type] ?? CourseRoster::NO_ORIGIN, $r['user']->institution, CourseRoster::STATUSES[$r['status']],
                    $r['enrollment']->created_at->format('Y-m-d H:i'), $r['percent'], $r['next'], $r['post_avg'],
                    $r['last_active']?->format('Y-m-d H:i'),
                ];
                foreach ($sections as $section) {
                    $line[] = $r['progress']->firstScore($section, 'pre');
                    $line[] = $r['progress']->postScore($section);
                }
                fputcsv($out, $line, ';');
            }
            fclose($out);
        }, 'peserta-'.$course->slug.'-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('edit courses');
        $course = Course::with('category')->findOrFail($id);
        $categories = CourseCategory::orderBy('name')->get();

        return view('admin.courses.edit', compact('course', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('edit courses');
        $course = Course::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'description' => 'required',
            'course_type' => 'required|in:paid,free',
            'price' => 'exclude_if:course_type,free|required|integer|min:1000',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'trailer_video_id' => 'nullable|string',
            'course_category_id' => 'required|exists:course_categories,id',
            'level' => 'required|in:Beginner,Intermediate,Advanced',
            'benefits' => 'nullable|string',
            'topics_preview' => 'nullable|string',
            'schedule_start' => 'nullable|date',
            'schedule_end' => 'nullable|date|after_or_equal:schedule_start',
            'meeting_platform' => 'nullable|string|max:100',
            'meeting_link' => 'nullable|url|max:500',
        ]);

        $payload = [
            'title' => $data['title'],
            'instructor' => $data['instructor'],
            'description' => $data['description'],
            'course_type' => $data['course_type'],
            'price' => $data['course_type'] === 'free' ? 0 : ($data['price'] ?? $course->price),
            'trailer_video_id' => $data['trailer_video_id'] ?? $course->trailer_video_id,
            'course_category_id' => $data['course_category_id'],
            'level' => $data['level'],
            'benefits' => $data['benefits'] ?? null,
            'topics_preview' => $data['topics_preview'] ?? null,
            'schedule_start' => $data['schedule_start'] ?? null,
            'schedule_end' => $data['schedule_end'] ?? null,
            'meeting_platform' => $data['meeting_platform'] ?? null,
            'meeting_link' => $data['meeting_link'] ?? null,
        ];

        if ($request->hasFile('thumbnail')) {
            $payload['thumbnail'] = $request->file('thumbnail')->store('courses', 'public');
        }

        $course->update($payload);

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete courses');
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }
}
