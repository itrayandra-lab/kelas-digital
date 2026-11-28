<?php

namespace App\Http\Controllers;

use App\Models\CourseCategory;
use App\Models\Expert;
use Illuminate\Support\Collection;

/** Learning Family ("school") pages and the experts page. Schools live in config/homepage.php, experts in the database. */
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
            'experts' => $this->expertList()->filter(fn (Expert $e) => in_array($slug, $e->schools ?? [], true))->values(),
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
                'experts' => $experts->filter(fn (Expert $e) => in_array($school['slug'], $e->schools ?? [], true))->values(),
            ])->filter(fn ($g) => $g['experts']->isNotEmpty())->values(),
            'total' => $experts->filter(fn (Expert $e) => $e->schools)->count(),
        ]);
    }

    private function schools(): Collection
    {
        return collect(config('homepage.families'))->where('active', true)->sortBy('order')->values();
    }

    private function expertList(): Collection
    {
        return Expert::with(['courses' => fn ($q) => $q->select('id', 'title', 'slug', 'expert_id')])->orderBy('order')->orderBy('name')->get();
    }
}
