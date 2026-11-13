<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;
use Midtrans\Transaction;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Checks a voucher code for the checkout panel without creating an order.
     */
    public function voucher(Request $request, Course $course)
    {
        $price = (int) $course->price;
        $voucher = Voucher::findByCode($request->input('voucher'));
        $problem = $voucher ? $voucher->problemFor($course) : 'Kode voucher tidak ditemukan.';
        $discount = $problem ? 0 : $voucher->discountFor($price);
        $rupiah = fn (int $amount) => 'Rp'.number_format($amount, 0, ',', '.');

        return response()->json([
            'ok' => $problem === null,
            'message' => $problem ?? 'Voucher dipakai: potongan '.$voucher->label().'.',
            'discount_label' => $rupiah($discount),
            'total_label' => $rupiah($price - $discount),
        ]);
    }

    public function checkout(Request $request, Course $course)
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json(['error' => 'login_required', 'message' => 'Silakan login terlebih dahulu.'], 401);
        }

        if ($course->isFreeClass()) {
            return response()->json(['error' => 'free_course'], 400);
        }

        // The voucher is checked again here: the checkout panel's check can be skipped or stale.
        $voucher = null;
        if (trim((string) $request->input('voucher')) !== '') {
            $voucher = Voucher::findByCode($request->input('voucher'));
            $problem = $voucher ? $voucher->problemFor($course) : 'Kode voucher tidak ditemukan.';

            if ($problem) {
                return response()->json(['error' => 'voucher', 'message' => $problem], 422);
            }
        }

        $amount = (int) $course->price - ($voucher?->discountFor((int) $course->price) ?? 0);
        $existing = $user->enrollments()->where('course_id', $course->id)->first();

        if ($existing && $existing->payment_status === 'completed') {
            return response()->json(['error' => 'already_enrolled'], 400);
        }

        $order = ['payment_status' => 'pending', 'status' => 'pending', 'voucher_id' => $voucher?->id, 'paid_amount' => $amount];

        if ($existing) {
            // Old tokens expire or belong to failed orders, so every checkout gets a fresh order.
            // A "Mulai gratis" row keeps payment_method 'trial' until paid, so its free chapters stay open meanwhile.
            $enrollment = $existing;
            $enrollment->update($order);
        } else {
            $enrollment = Enrollment::create($order + [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'payment_method' => 'midtrans',
            ]);
        }

        if ($amount === 0) {
            $enrollment->markPaid(['payment_method' => 'voucher']);

            return response()->json(['paid_by_voucher' => true]);
        }

        $params = [
            'transaction_details' => [
                'order_id' => 'ENR-'.$enrollment->id.'-'.time(),
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
            'item_details' => [
                [
                    'id' => $course->id,
                    'price' => $amount,
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

    public function complete(Request $request, Enrollment $enrollment)
    {
        if ($enrollment->user_id !== Auth::id()) {
            abort(403);
        }

        $orderId = (string) $request->input('order_id');

        if (! str_starts_with($orderId, 'ENR-'.$enrollment->id.'-')) {
            abort(422);
        }

        // The browser's success callback can be faked, so ask Midtrans for the real status.
        try {
            $status = Transaction::status($orderId);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 422);
        }

        if (! in_array($status->transaction_status ?? null, ['capture', 'settlement'])) {
            return response()->json(['success' => false], 422);
        }

        $enrollment->markPaid([
            'transaction_id' => $status->transaction_id ?? null,
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
            $enrollment->markPaid(['payment_method' => 'midtrans']);
        } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire']) && $enrollment->payment_status !== 'completed') {
            // An old abandoned order expiring must not revoke a course that was already paid.
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
