<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pendingEnrollments = Enrollment::where('payment_status', 'pending')
            ->with(['user', 'course'])
            ->latest()
            ->get();

        $allEnrollments = Enrollment::with(['user', 'course'])
            ->latest()
            ->paginate(20);

        return view('admin.payments.index', compact('pendingEnrollments', 'allEnrollments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Approve a payment
     */
    public function approve(Request $request, string $id)
    {
        $enrollment = Enrollment::findOrFail($id);

        $enrollment->update([
            'payment_status' => 'completed',
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'Payment approved successfully.');
    }
}
