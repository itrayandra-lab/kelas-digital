<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use App\Support\TicketQr;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * The QR on every ticket opens this page. The owner sees their full ticket, event staff see a check-in button,
 * and anyone else only sees whether the ticket is valid, with the name masked.
 */
class TicketController extends Controller
{
    public function show(Request $request, EventRegistration $registration)
    {
        $registration->load('event', 'session');
        $user = $request->user();
        $isOwner = $user?->id === $registration->user_id;
        $isStaff = (bool) $user?->can('manage events');
        $qr = TicketQr::svg(route('tickets.show', $registration), 240);

        return view('tickets.show', compact('registration', 'isOwner', 'isStaff', 'qr'));
    }

    public function download(Request $request, EventRegistration $registration)
    {
        $this->ownerOrStaff($request, $registration);

        return self::pdf($registration)->download('tiket-'.$registration->code.'.pdf');
    }

    public function checkIn(Request $request, EventRegistration $registration)
    {
        $registration->update(['checked_in_at' => $registration->checked_in_at ? null : now()]);

        return back()->with('checkin', $registration->checked_in_at ? 'hadir' : 'batal');
    }

    /** Staff type the code printed under the QR when the camera can't read it. */
    public function lookup(Request $request)
    {
        $code = strtoupper(preg_replace('/\s+/', '', (string) $request->query('kode')));
        $code = str_starts_with($code, 'RA-') ? $code : 'RA-'.ltrim($code, '-');
        $registration = EventRegistration::where('code', $code)->first();

        return $registration
            ? redirect()->route('tickets.show', $registration)
            : back()->with('lookup_error', "Tiket dengan kode {$code} tidak ditemukan. Periksa lagi hurufnya.");
    }

    /** Shared by the download button and the confirmation email attachment. */
    public static function pdf(EventRegistration $registration)
    {
        $registration->loadMissing('event', 'session');

        return Pdf::loadView('tickets.pdf', [
            'registration' => $registration,
            'qr' => TicketQr::dataUri(route('tickets.show', $registration), 220),
        ])->setPaper('a5')->setOption('isFontSubsettingEnabled', true);
    }

    private function ownerOrStaff(Request $request, EventRegistration $registration): void
    {
        abort_unless($request->user()?->id === $registration->user_id || $request->user()?->can('manage events'), 403);
    }
}
