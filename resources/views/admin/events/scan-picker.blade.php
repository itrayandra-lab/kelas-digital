@extends('layouts.admin')

@section('title', 'Scan Tiket')

@section('content')
    <p class="text-sm text-gray-600 mb-5">Pilih event yang sedang berlangsung. Pemindai terbuka di layar penuh, cocok dipakai dari HP panitia di pintu masuk.</p>

    <div class="grid gap-4" style="grid-template-columns:repeat(auto-fill,minmax(260px,1fr));">
        @forelse($events as $event)
            <a href="{{ route('admin.events.scan', $event) }}" class="bg-white rounded-lg shadow-sm p-5 flex items-center gap-4 ring-2 ring-transparent hover:ring-primary-500">
                <div class="w-12 h-12 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center text-xl flex-shrink-0"><i class="fas fa-qrcode"></i></div>
                <div class="min-w-0">
                    <div class="font-semibold text-gray-900">{{ $event->title }}</div>
                    <div class="text-xs text-gray-500">{{ $event->event_date->isToday() ? 'Hari ini' : $event->event_date->locale('id')->translatedFormat('l, j M Y') }} · {{ $event->registrations_count }} pendaftar</div>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500">Belum ada event yang akan datang.</div>
        @endforelse
    </div>
@endsection
