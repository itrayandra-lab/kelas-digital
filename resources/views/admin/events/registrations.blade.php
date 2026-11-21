@extends('layouts.admin')

@section('title', 'Peserta: '.$event->title)

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <div>
            <a href="{{ route('admin.events.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke daftar event
            </a>
            <p class="text-sm text-gray-600 mt-2">{{ $event->dateLabel() }} · {{ $event->isOnline() ? 'Online' : $event->venue }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.events.edit', $event) }}" class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Edit event</a>
            <a href="{{ route('admin.events.registrations.export', $event) }}" class="px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-700">
                <i class="fas fa-download mr-1"></i> Unduh CSV
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
    @endif

    {{-- Seats per session --}}
    <div class="grid gap-4 mb-6" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-xs text-gray-500">Total pendaftar</p>
            <p class="text-2xl font-bold text-gray-900 tabular-nums">{{ $event->sessions->sum('registrations_count') }} <span class="text-sm font-medium text-gray-400">/ {{ $event->sessions->sum('capacity') }}</span></p>
        </div>
        @foreach($event->sessions as $s)
            @php $full = $s->registrations_count >= $s->capacity; $pct = min(100, round($s->registrations_count / max(1, $s->capacity) * 100)); @endphp
            <a href="{{ route('admin.events.registrations', [$event, 'sesi' => $session?->id === $s->id ? null : $s->id]) }}"
               class="bg-white rounded-lg shadow-sm p-4 block ring-2 {{ $session?->id === $s->id ? 'ring-primary-500' : 'ring-transparent hover:ring-gray-200' }}">
                <p class="text-xs text-gray-500">{{ $s->label }} · {{ $s->timeLabel() }} {{ $event->timezone }}</p>
                <p class="text-2xl font-bold text-gray-900 tabular-nums">{{ $s->registrations_count }} <span class="text-sm font-medium text-gray-400">/ {{ $s->capacity }}</span></p>
                <div class="h-1.5 bg-gray-100 rounded-full mt-2 overflow-hidden"><div class="h-full {{ $full ? 'bg-red-500' : 'bg-primary-600' }}" style="width:{{ $pct }}%"></div></div>
                <p class="text-xs mt-1 {{ $full ? 'text-red-600 font-semibold' : 'text-gray-500' }}">{{ $full ? 'Penuh' : 'Sisa '.($s->capacity - $s->registrations_count).' kursi' }}</p>
            </a>
        @endforeach
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <form method="GET" class="p-4 border-b border-gray-100 flex flex-wrap gap-2 items-center">
            @if($session)<input type="hidden" name="sesi" value="{{ $session->id }}">@endif
            <label for="q" class="sr-only">Cari peserta</label>
            <input id="q" type="search" name="q" value="{{ $search }}" placeholder="Cari nama, email, atau nomor WhatsApp…"
                class="flex-1 min-w-[12rem] px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500">
            <button class="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-800 text-white">Cari</button>
            @if($session || $search !== '')
                <a href="{{ route('admin.events.registrations', $event) }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900">Reset</a>
            @endif
        </form>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-5 py-3 text-left">#</th>
                        <th class="px-5 py-3 text-left">Peserta</th>
                        <th class="px-5 py-3 text-left">WhatsApp</th>
                        <th class="px-5 py-3 text-left">Usia</th>
                        <th class="px-5 py-3 text-left">Asal</th>
                        <th class="px-5 py-3 text-left">Sesi</th>
                        <th class="px-5 py-3 text-left">Daftar</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($registrations as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 text-gray-400 tabular-nums">{{ $loop->iteration }}</td>
                            <td class="px-5 py-3">
                                <div class="font-semibold text-gray-900">{{ $r->name }}</div>
                                <div class="text-xs text-gray-500">{{ $r->email }}</div>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <a href="{{ $r->whatsappLink() }}" target="_blank" rel="noopener" class="text-green-700 font-medium hover:underline">
                                    <i class="fab fa-whatsapp mr-1"></i>{{ $r->whatsapp }}
                                </a>
                            </td>
                            <td class="px-5 py-3 tabular-nums">{{ $r->age }}</td>
                            <td class="px-5 py-3 text-gray-700">{{ $r->user?->origin() ?? '–' }}</td>
                            <td class="px-5 py-3 whitespace-nowrap">{{ $r->session->label }}</td>
                            <td class="px-5 py-3 whitespace-nowrap text-gray-500">{{ $r->created_at->locale('id')->translatedFormat('j M, H.i') }}</td>
                            <td class="px-5 py-3 text-right">
                                <form method="POST" action="{{ route('admin.events.registrations.destroy', $r) }}" x-data
                                    @submit="if (!confirm('Hapus pendaftaran {{ addslashes($r->name) }}? Kursinya akan terbuka lagi.')) $event.preventDefault()">
                                    @csrf @method('DELETE')
                                    <button class="text-xs font-semibold text-red-600 hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-gray-500">{{ $search !== '' || $session ? 'Tidak ada peserta yang cocok.' : 'Belum ada pendaftar.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
