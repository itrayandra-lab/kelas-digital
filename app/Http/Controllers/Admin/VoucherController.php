<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::with('course:id,title')->latest()->paginate(15);

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.form', ['voucher' => new Voucher(['discount_type' => 'percent', 'is_active' => true]), 'courses' => $this->paidCourses()]);
    }

    public function store(Request $request)
    {
        Voucher::create($this->validated($request));

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher dibuat.');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.form', ['voucher' => $voucher, 'courses' => $this->paidCourses()]);
    }

    public function update(Request $request, Voucher $voucher)
    {
        $voucher->update($this->validated($request, $voucher));

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher disimpan.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher dihapus.');
    }

    private function paidCourses()
    {
        return Course::where('course_type', 'paid')->orderBy('title')->get(['id', 'title']);
    }

    private function validated(Request $request, ?Voucher $voucher = null): array
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:40', 'regex:/^[A-Z0-9_-]+$/', Rule::unique('vouchers', 'code')->ignore($voucher)],
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => ['required', 'integer', 'min:1', $request->input('discount_type') === 'percent' ? 'max:100' : 'max:100000000'],
            'course_id' => 'nullable|exists:courses,id',
            'max_uses' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
        ], [
            'code.regex' => 'Kode hanya boleh huruf, angka, strip, dan garis bawah.',
            'discount_value.max' => 'Potongan persen maksimal 100.',
        ]);

        // The end date the admin picks still counts, so the voucher runs until that day's last second.
        if (! empty($data['expires_at'])) {
            $data['expires_at'] = Carbon::parse($data['expires_at'])->endOfDay();
        }

        return $data + ['is_active' => $request->boolean('is_active')];
    }
}
