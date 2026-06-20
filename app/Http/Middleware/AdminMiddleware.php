<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu untuk mengakses halaman ini.');
        }

        $user = Auth::user();

        // Check if user has admin or super-admin role
        if (! $user->hasAnyRole(['admin', 'Super-Admin'])) {
            // Log unauthorized access attempt (optional)
            \Log::warning('Unauthorized admin access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'time' => now(),
            ]);

            // Return 403 Forbidden for better security
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengakses area admin.');
        }

        return $next($request);
    }
}
