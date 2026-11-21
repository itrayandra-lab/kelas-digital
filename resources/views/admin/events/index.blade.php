@extends('layouts.admin')

@section('title', 'Kelola Event')

@section('content')
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <p class="text-sm text-gray-600">Event gratis, offline maupun online. Peserta mendaftar dengan akun Ray Academy mereka.</p>
        <a href="{{ route('admin.events.create') }}" data-tour="add-event"
            class="inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors">
            <i class="fas fa-plus mr-2"></i> Buat Event
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs font-bold text-gray-600 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Event</th>
                        <th class="px-5 py-3 text-left">Tanggal</th>
                        <th class="px-5 py-3 text-left">Jenis</th>
                        <th class="px-5 py-3 text-left">Pendaftar</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($events as $event)
                        @php $fill = $event->capacity_total ? min(100, round($event->registrations_count / $event->capacity_total * 100)) : 0; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-14 rounded-md bg-gray-100 overflow-hidden flex-shrink-0">
                                        @if($event->cover_url)<img src="{{ $event->cover_url }}" alt="" class="w-full h-full object-cover">@endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-gray-900">{{ $event->title }}</div>
                                        <div class="text-xs text-gray-500">{{ $event->isOnline() ? 'Online' : $event->venue }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-gray-700">
                                {{ $event->event_date->locale('id')->translatedFormat('j M Y') }}
                                @if($event->isPast())<div class="text-xs text-gray-400">Sudah lewat</div>@endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $event->isOnline() ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">{{ \App\Models\Event::FORMATS[$event->format] }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-semibold text-gray-900 tabular-nums">{{ $event->registrations_count }} / {{ (int) $event->capacity_total }}</div>
                                <div class="w-24 h-1.5 bg-gray-100 rounded-full mt-1 overflow-hidden"><div class="h-full {{ $fill >= 100 ? 'bg-red-500' : 'bg-primary-600' }}" style="width:{{ $fill }}%"></div></div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs">
                                @if($event->is_published)
                                    <span class="px-2 py-0.5 rounded-full font-semibold bg-green-100 text-green-700">Tampil</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full font-semibold bg-gray-100 text-gray-600">Draf</span>
                                @endif
                                @unless($event->registration_open)
                                    <div class="mt-1 text-gray-500">Pendaftaran ditutup</div>
                                @endunless
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('admin.events.scan', $event) }}" @if($loop->first) data-tour="scan-btn" @endif class="px-3 py-1.5 text-xs font-semibold rounded-md bg-green-600 text-white hover:bg-green-700"><i class="fas fa-qrcode mr-1"></i>Scan</a>
                                    <a href="{{ route('admin.events.registrations', $event) }}" @if($loop->first) data-tour="peserta-btn" @endif class="px-3 py-1.5 text-xs font-semibold rounded-md bg-primary-50 text-primary-700 hover:bg-primary-100">Peserta</a>
                                    <a href="{{ route('admin.events.edit', $event) }}" @if($loop->first) data-tour="edit-btn" @endif class="px-3 py-1.5 text-xs font-semibold rounded-md text-gray-700 hover:bg-gray-100">Edit</a>
                                    <a href="{{ route('events.show', $event) }}" target="_blank" class="px-3 py-1.5 text-xs font-semibold rounded-md text-gray-700 hover:bg-gray-100">Lihat</a>
                                    <form method="POST" action="{{ route('admin.events.duplicate', $event) }}">
                                        @csrf
                                        <button type="submit" @if($loop->first) data-tour="salin-btn" @endif class="px-3 py-1.5 text-xs font-semibold rounded-md text-gray-700 hover:bg-gray-100" title="Buat event baru dari event ini">Salin</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.events.destroy', $event) }}" x-data
                                        @submit="if (!confirm('Hapus event {{ addslashes($event->title) }} beserta semua pendaftarnya?')) $event.preventDefault()">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-md text-red-600 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">Belum ada event. Klik <b>Buat Event</b> untuk mulai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $events->links() }}</div>
@endsection

@push('scripts')
@include('partials.tour-assets')
<script>
KATour.page('admin-events', [
  { popover: { title: 'Kelola Event', description: 'Semua event gratis (offline atau online) dibuat dan dipantau dari sini. Peserta mendaftar sendiri lewat website dengan akun Ray Academy mereka.' } },
  { element: '[data-tour="add-event"]', popover: { title: '1. Buat event baru', description: 'Isi poster, judul, deskripsi, tanggal, lokasi atau link Zoom, lalu sesi dan kuotanya.', side: 'bottom' } },
  { element: '[data-tour="salin-btn"]', popover: { title: 'Event berikutnya? Salin saja', description: 'Membuat event baru dari event ini, lengkap dengan sesinya. Hasilnya tersimpan sebagai draf, tinggal ganti tanggal dan judul.', side: 'left' } },
  { element: '[data-tour="scan-btn"]', popover: { title: 'Hari acara: Scan', description: 'Buka dari HP panitia, tekan Buka kamera, lalu arahkan ke QR tiket peserta. Kehadiran langsung tercatat.', side: 'left' } },
  { element: '[data-tour="peserta-btn"]', popover: { title: 'Peserta dan check-in', description: 'Lihat siapa saja yang mendaftar, sisa kursi tiap sesi, unduh CSV, dan tandai kehadiran di hari acara.', side: 'left' } },
  { element: '[data-tour="edit-btn"]', popover: { title: 'Ubah event', description: 'Ganti info, tambah sesi, atau tutup pendaftaran lebih awal.', side: 'left' } }
]);
</script>
@endpush
