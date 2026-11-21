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
            <a href="{{ route('admin.events.registrations.export', $event) }}" data-tour="csv" class="px-4 py-2 text-sm font-semibold rounded-lg bg-primary-600 text-white hover:bg-primary-700">
                <i class="fas fa-download mr-1"></i> Unduh CSV
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg mb-6">{{ session('success') }}</div>
    @endif

    {{-- Door check-in --}}
    <div class="bg-white rounded-lg shadow-sm p-5 mb-6 grid md:grid-cols-2 gap-5 items-center" data-tour="cek-tiket">
        <div class="min-w-0">
            <h2 class="font-semibold text-gray-900"><i class="fas fa-qrcode text-primary-600 mr-1"></i> Cek tiket di pintu masuk</h2>
            <p class="mt-1 text-sm text-gray-600">Buka dari HP panitia, tekan <b>Buka kamera</b>, lalu arahkan ke QR tiket peserta. Hijau berarti hadir tercatat, kuning berarti sudah check-in sebelumnya, merah berarti tiket tidak valid.</p>
            <a href="{{ route('admin.events.scan', $event) }}" class="mt-3 inline-flex items-center gap-2 px-5 py-3 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700">
                <i class="fas fa-camera"></i> Buka pemindai
            </a>
        </div>
        <form method="GET" action="{{ route('tickets.lookup') }}" class="min-w-0">
            <label for="kode" class="block text-sm font-medium text-gray-700 mb-1.5">QR tidak terbaca? Ketik kode tiketnya</label>
            <div class="flex gap-2">
                <input id="kode" name="kode" required placeholder="RA-7K3P9Q" autocomplete="off" autocapitalize="characters"
                    size="10" class="flex-1 min-w-0 px-4 py-3 border border-gray-300 rounded-lg text-lg font-semibold tracking-widest uppercase focus:outline-none focus:ring-2 focus:ring-primary-500">
                <button class="px-5 py-3 rounded-lg bg-primary-600 text-white font-semibold hover:bg-primary-700">Cek</button>
            </div>
            @if(session('lookup_error'))<p class="mt-2 text-sm text-red-600">{{ session('lookup_error') }}</p>@endif
        </form>
    </div>

    {{-- Seats per session --}}
    <div class="grid gap-4 mb-6" data-tour="kuota" style="grid-template-columns:repeat(auto-fit,minmax(200px,1fr));">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <p class="text-xs text-gray-500">Total pendaftar</p>
            <p class="text-2xl font-bold text-gray-900 tabular-nums">{{ $event->sessions->sum('registrations_count') }} <span class="text-sm font-medium text-gray-400">/ {{ $event->sessions->sum('capacity') }}</span></p>
            <p class="text-xs mt-1 text-green-700 font-semibold"><i class="fas fa-user-check mr-1"></i>{{ $checkedIn }} sudah hadir</p>
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

    <div class="bg-white rounded-lg shadow-sm overflow-hidden" data-tour="tabel">
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
        {{-- Phones: one card per participant --}}
        <ul class="md:hidden divide-y divide-gray-100">
            @forelse($registrations as $r)
                <li class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="font-semibold text-gray-900">{{ $r->name }}</div>
                            <div class="text-sm text-gray-500">{{ $r->session->label }} · usia {{ $r->age }}{{ $r->user?->origin() ? ' · '.$r->user->origin() : '' }}</div>
                            <a href="{{ route('tickets.show', $r) }}" class="text-sm font-mono font-semibold text-primary-700">{{ $r->code }}</a>
                        </div>
                        @if($r->checked_in_at)
                            <span class="flex-shrink-0 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700"><i class="fas fa-check mr-1"></i>Hadir {{ $r->checked_in_at->timezone(config('app.timezone'))->format('H.i') }}</span>
                        @endif
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <a href="{{ $r->whatsappLink() }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 h-11 rounded-lg bg-green-600 text-white text-sm font-semibold"><i class="fab fa-whatsapp"></i> Kirim pesan</a>
                        <form method="POST" action="{{ route('tickets.checkin', $r) }}">
                            @csrf
                            <button class="w-full h-11 rounded-lg text-sm font-semibold {{ $r->checked_in_at ? 'border border-gray-200 text-gray-500' : 'border border-primary-200 bg-primary-50 text-primary-700' }}">
                                {{ $r->checked_in_at ? 'Batalkan hadir' : 'Tandai hadir' }}
                            </button>
                        </form>
                    </div>
                </li>
            @empty
                <li class="p-8 text-center text-gray-500">{{ $search !== '' || $session ? 'Tidak ada peserta yang cocok.' : 'Belum ada pendaftar.' }}</li>
            @endforelse
        </ul>

        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-5 py-3 text-left">#</th>
                        <th class="px-5 py-3 text-left">Peserta</th>
                        <th class="px-5 py-3 text-left">Tiket</th>
                        <th class="px-5 py-3 text-left">Kehadiran</th>
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
                                <a href="{{ route('tickets.show', $r) }}" class="font-mono font-semibold text-primary-700 hover:underline">{{ $r->code }}</a>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <form method="POST" action="{{ route('tickets.checkin', $r) }}">
                                    @csrf
                                    @if($r->checked_in_at)
                                        <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 hover:bg-green-200" title="Klik untuk membatalkan">
                                            <i class="fas fa-check"></i> Hadir {{ $r->checked_in_at->timezone(config('app.timezone'))->format('H.i') }}
                                        </button>
                                    @else
                                        <button class="px-3 py-1.5 rounded-full text-xs font-semibold border border-gray-300 text-gray-700 hover:bg-gray-50">Tandai hadir</button>
                                    @endif
                                </form>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <div class="text-gray-700 tabular-nums">{{ $r->whatsapp }}</div>
                                <a href="{{ $r->whatsappLink() }}" target="_blank" rel="noopener" @if($loop->first) data-tour="wa-btn" @endif
                                    class="mt-1 inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-green-600 text-white text-xs font-semibold hover:bg-green-700">
                                    <i class="fab fa-whatsapp"></i> Kirim pesan
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
                        <tr><td colspan="10" class="px-5 py-10 text-center text-gray-500">{{ $search !== '' || $session ? 'Tidak ada peserta yang cocok.' : 'Belum ada pendaftar.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
@include('partials.tour-assets')
<script>
KATour.page('admin-event-peserta', [
  { element: '[data-tour="kuota"]', popover: { title: 'Sisa kursi', description: 'Jumlah pendaftar dan sisa kursi tiap sesi. Klik kotak sesi untuk melihat peserta sesi itu saja.', side: 'bottom' } },
  { element: '[data-tour="cek-tiket"]', popover: { title: 'Di hari acara', description: 'Tekan Buka pemindai di HP panitia, lalu arahkan kamera ke QR tiket peserta. Kehadiran langsung tercatat. Kalau QR tidak terbaca, ketik kodenya di sini.', side: 'bottom' } },
  { element: '[data-tour="tabel"]', popover: { title: 'Daftar peserta', description: 'Tombol Tandai hadir juga bisa ditekan dari sini.', side: 'top' } },
  { element: '[data-tour="wa-btn"]', popover: { title: 'Kirim pesan WhatsApp', description: 'WhatsApp terbuka dengan pesan konfirmasi yang sudah terisi nama, sesi, dan link tiket peserta. Tinggal tekan kirim. Isi pesannya bisa diubah di Edit event.', side: 'left' } },
  { element: '[data-tour="csv"]', popover: { title: 'Unduh data', description: 'Data peserta lengkap dengan kode tiket dan jam hadir, langsung rapi saat dibuka di Excel.', side: 'left' } }
]);
</script>
@endpush
