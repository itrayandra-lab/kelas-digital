<?php

namespace App\Http\Controllers;

use App\Services\Raya\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RayaChatController extends Controller
{
    public function chat(Request $request, ChatService $chat): JsonResponse
    {
        // CSRF already ran; a browser on this site always sends Origin or Referer, a bare API client usually sends neither.
        $source = $request->headers->get('Origin') ?: $request->headers->get('Referer');
        if (! $source || parse_url($source, PHP_URL_HOST) !== $request->getHost()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:'.config('raya.max_message_chars')],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.role' => ['required', 'in:user,assistant'],
            'history.*.content' => ['required', 'string', 'max:2000'],
        ]);

        $message = trim($data['message']);
        if ($message === '') {
            return response()->json(['message' => 'Pesan tidak boleh kosong.'], 422);
        }

        // Site-wide daily cap, so a flood of visitors cannot run up the AI bill.
        $counter = 'raya:day:'.now()->format('Ymd');
        Cache::add($counter, 0, now()->endOfDay());
        if (Cache::increment($counter) > config('raya.limits.global_daily')) {
            return response()->json(['message' => $this->unavailable()], 503);
        }

        try {
            return response()->json($chat->reply($message, $data['history'] ?? []));
        } catch (\Throwable $e) {
            Log::error('raya unavailable', ['error' => $e->getMessage()]);

            return response()->json(['message' => $this->unavailable()], 503);
        }
    }

    private function unavailable(): string
    {
        return 'Maaf, RAYA sedang tidak bisa menjawab. Silakan coba lagi sebentar lagi, atau jelajahi kelas langsung di halaman Kursus.';
    }
}
