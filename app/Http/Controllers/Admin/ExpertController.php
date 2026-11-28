<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expert;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Experts: class instructors and article authors, chosen from a dropdown in the course and article forms. */
class ExpertController extends Controller
{
    public function index()
    {
        $experts = Expert::withCount(['courses', 'articles'])->orderByDesc('featured')->orderBy('order')->orderBy('name')->paginate(50);

        return view('admin.experts.index', compact('experts'));
    }

    public function create()
    {
        return view('admin.experts.form', ['expert' => new Expert(['order' => Expert::max('order') + 1]), 'schools' => $this->schools()]);
    }

    public function store(Request $request)
    {
        Expert::create($this->data($request));

        return redirect()->route('admin.experts.index')->with('success', 'Pakar ditambahkan.');
    }

    public function edit(Expert $expert)
    {
        return view('admin.experts.form', ['expert' => $expert, 'schools' => $this->schools()]);
    }

    public function update(Request $request, Expert $expert)
    {
        $expert->update($this->data($request, $expert));

        return redirect()->route('admin.experts.index')->with('success', 'Pakar disimpan. Namanya ikut berubah di kelas dan artikelnya.');
    }

    public function destroy(Expert $expert)
    {
        if ($expert->courses()->exists() || $expert->articles()->exists()) {
            return back()->with('error', "{$expert->name} masih tercatat di kelas atau artikel. Ganti pakarnya di sana dulu.");
        }

        $expert->delete();

        return back()->with('success', 'Pakar dihapus.');
    }

    private function data(Request $request, ?Expert $expert = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('experts', 'name')->ignore($expert)],
            'schools' => ['nullable', 'array'],
            'schools.*' => [Rule::in($this->schools()->keys())],
            'order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ], ['name.unique' => 'Pakar dengan nama ini sudah ada.']);

        return [
            'name' => trim($data['name']),
            'schools' => array_values($data['schools'] ?? []),
            'featured' => $request->boolean('featured'),
            'order' => (int) ($data['order'] ?? 0),
        ];
    }

    /** Learning Families from config/homepage.php, slug => name. */
    private function schools()
    {
        return collect(config('homepage.families'))->where('active', true)->sortBy('order')->pluck('name', 'slug');
    }
}
