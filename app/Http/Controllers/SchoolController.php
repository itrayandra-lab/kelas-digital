<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Support\Collection;

/** Learning Family ("school") pages and the experts page. Schools and experts live in config/homepage.php. */
class SchoolController extends Controller
{
    public function show(string $slug)
    {
        $schools = $this->schools();
        $school = $schools->firstWhere('slug', $slug) ?? abort(404);

        // One block per program (course category) of the school, each with its classes.
        $programs = CourseCategory::whereIn('slug', $school['categories'])
            ->with(['courses' => fn ($q) => $q->with('category')->withCount('lessons')->latest()])
            ->get()
            ->sortBy(fn ($cat) => array_search($cat->slug, $school['categories'], true))
            ->filter(fn ($cat) => $cat->courses->isNotEmpty())
            ->values();

        return view('schools.show', [
            'school' => $school,
            'programs' => $programs,
            'experts' => $this->expertList()->filter(fn ($e) => in_array($slug, $e['schools'], true))->values(),
            'otherSchools' => $schools->where('slug', '!=', $slug)->values(),
        ]);
    }

    public function experts()
    {
        $schools = $this->schools();
        $experts = $this->expertList();

        return view('schools.experts', [
            'groups' => $schools->map(fn ($school) => [
                'school' => $school,
                'experts' => $experts->filter(fn ($e) => in_array($school['slug'], $e['schools'], true))->values(),
            ])->filter(fn ($g) => $g['experts']->isNotEmpty())->values(),
            'total' => $experts->count(),
        ]);
    }

    private function schools(): Collection
    {
        return collect(config('homepage.families'))->where('active', true)->sortBy('order')->values();
    }

    /** Experts with initials and the classes they teach (instructor name contains every word of their name). */
    private function expertList(): Collection
    {
        $courses = Course::select('title', 'slug', 'instructor')->whereNotNull('instructor')->get();

        return collect(config('homepage.experts'))->map(function (array $expert) use ($courses) {
            $words = self::nameWords($expert['name']);

            return $expert + [
                'initials' => mb_strtoupper(mb_substr($words[0] ?? '', 0, 1).mb_substr(end($words) ?: '', 0, 1)),
                'courses' => $words ? $courses->filter(fn ($c) => collect($words)->every(fn ($w) => mb_stripos($c->instructor, $w) !== false))->values() : collect(),
            ];
        });
    }

    /** "Prof. Dr. apt. Sriwidodo, M.Si" → ["Sriwidodo"]: the part before the degrees, without titles or initials. */
    public static function nameWords(string $name): array
    {
        $words = preg_split('/\s+/', trim(explode(',', $name)[0]));

        return array_values(array_filter($words, fn ($w) => ! str_contains($w, '.')));
    }
}
