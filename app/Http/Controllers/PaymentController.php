<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function checkout(Course $course)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['error' => 'login_required', 'message' => 'Silakan login terlebih dahulu.'], 401);
        }

        if ($course->isFreeClass()) {
            return response()->json(['error' => 'free_course'], 400);
        }

        $existing = $user->enrollments()->where('course_id', $course->id)->first();

        if ($existing && $existing->payment_status === 'completed') {
            return response()->json(['error' => 'already_enrolled'], 400);
        }

        if ($existing && $existing->snap_token) {
            return response()->json(['snap_token' => $existing->snap_token, 'enrollment_id' => $existing->id]);
        }

        if ($existing) {
            $enrollment = $existing;
        } else {
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'status' => 'pending',
                'enrolled_at' => now(),
                'payment_status' => 'pending',
                'payment_method' => 'midtrans',
            ]);
        }

        $params = [
            'transaction_details' => [
                'order_id' => 'ENR-'.$enrollment->id.'-'.time(),
                'gross_amount' => (int) $course->price,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'item_details' => [
                [
                    'id' => $course->id,
                    'price' => (int) $course->price,
                    'quantity' => 1,
                    'name' => $course->title,
                ],
            ],
        ];

        try {
            $token = Snap::getSnapToken($params);
            $enrollment->update(['snap_token' => $token]);

            return response()->json(['snap_token' => $token, 'enrollment_id' => $enrollment->id]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function complete(Enrollment $enrollment)
    {
        if ($enrollment->user_id !== Auth::id()) {
            abort(403);
        }

        $enrollment->update([
            'payment_status' => 'completed',
            'status' => 'active',
            'payment_method' => 'midtrans',
        ]);

        return response()->json(['success' => true]);
    }

    public function notification(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $notif = new Notification;
        } catch (\Exception $e) {
            return response('OK', 200);
        }

        $orderId = $notif->order_id;
        $transactionId = $notif->transaction_id;
        $transactionStatus = $notif->transaction_status;

        $parts = explode('-', $orderId);
        $enrollmentId = $parts[1] ?? null;
        $enrollment = Enrollment::find($enrollmentId);

        if (! $enrollment) {
            return response('OK', 200);
        }

        $enrollment->update([
            'transaction_id' => $transactionId,
            'midtrans_response' => $notif->getResponse(),
        ]);

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            $enrollment->update([
                'payment_status' => 'completed',
                'status' => 'active',
                'payment_method' => 'midtrans',
            ]);
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire'])) {
            $enrollment->update([
                'payment_status' => 'rejected',
                'status' => 'inactive',
            ]);
        }

        return response('OK', 200);
    }

    public function finish(Request $request)
    {
        $enrollmentId = $request->query('enrollment_id');

        if ($enrollmentId) {
            $enrollment = Enrollment::find($enrollmentId);

            if ($enrollment && $enrollment->payment_status === 'completed') {
                return redirect()->route('course.show', $enrollment->course->slug)
                    ->with('message', 'Pembayaran berhasil! Selamat belajar.');
            }
        }

        return redirect()->route('home');
    }

    public function unfinish()
    {
        return redirect()->back()->with('error', 'Pembayaran belum selesai. Silakan coba lagi.');
    }

    public function error()
    {
        return redirect()->back()->with('error', 'Pembayaran gagal. Silakan coba lagi.');
    }
}
