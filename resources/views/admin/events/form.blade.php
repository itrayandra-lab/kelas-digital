@extends('layouts.admin')

@section('title', $event->exists ? 'Edit Event' : 'Buat Event')

@php
    $input = 'w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent';
    $label = 'block text-sm font-medium text-gray-700 mb-1.5';
    $initialSessions = old('sessions', $sessions);
@endphp

@section('content')
<form method="POST" action="{{ $event->exists ? route('admin.events.update', $event) : route('admin.events.store') }}" enctype="multipart/form-data"
      x-data="{ format: @js(old('format', $event->format)), sessions: @js(array_values($initialSessions)), preview: null }">
    @csrf
    @if($event->exists) @method('PUT') @endif

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke daftar event
        </a>
        <div class="flex gap-2">
            @if($event->exists)
                <a href="{{ route('events.show', $event) }}" target="_blank" class="px-5 py-2.5 text-sm font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Lihat halaman</a>
            @endif
            <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700">
                {{ $event->exists ? 'Simpan perubahan' : 'Buat event' }}
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg mb-6">
            <p class="font-semibold mb-1">Ada yang perlu diperbaiki:</p>
            <ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="bg-blue-50 border border-blue-100 text-sm text-blue-900 px-4 py-3 rounded-lg mb-6">
        <i class="fas fa-circle-info mr-1"></i> Isi langkah 1 sampai 4 dari atas ke bawah, lalu tekan <b>{{ $event->exists ? 'Simpan perubahan' : 'Buat event' }}</b>. Kolom bertanda <i>(opsional)</i> boleh dikosongkan. Event baru tampil di website setelah kamu centang <b>Tampilkan di website</b>.
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Basic info --}}
            <section class="bg-white rounded-lg shadow-sm p-6 space-y-5">
                <h2 class="flex items-center gap-2 font-semibold text-gray-900"><span class="w-7 h-7 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">1</span> Judul dan deskripsi</h2>
                <div>
                    <label for="title" class="{{ $label }}">Judul event</label>
                    <input id="title" name="title" value="{{ old('title', $event->title) }}" required maxlength="255" class="{{ $input }}" placeholder="mis. Kelas Formulasi">
                </div>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label for="subtitle" class="{{ $label }}">Subjudul <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <input id="subtitle" name="subtitle" value="{{ old('subtitle', $event->subtitle) }}" maxlength="255" class="{{ $input }}" placeholder="mis. Solid Perfume & Make Up Remover">
                    </div>
                    <div>
                        <label for="badge" class="{{ $label }}">Label kecil <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <input id="badge" name="badge" value="{{ old('badge', $event->badge) }}" maxlength="60" class="{{ $input }}" placeholder="mis. Pop-up Store Bali">
                    </div>
                </div>
                <div>
                    <label for="description" class="{{ $label }}">Deskripsi</label>
                    <textarea id="description" name="description" rows="7" maxlength="10000" class="{{ $input }}" placeholder="Ceritakan isi acara, siapa yang cocok ikut, dan apa yang perlu dibawa.">{{ old('description', $event->description) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Kosongkan satu baris untuk paragraf baru. Awali baris dengan <code>## </code> untuk judul kecil, dan <code>- </code> untuk daftar poin.</p>
                </div>
            </section>

            {{-- When & where --}}
            <section class="bg-white rounded-lg shadow-sm p-6 space-y-5">
                <h2 class="flex items-center gap-2 font-semibold text-gray-900"><span class="w-7 h-7 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">2</span> Kapan dan di mana</h2>
                <div>
                    <span class="{{ $label }}">Jenis event</span>
                    <div class="flex flex-wrap gap-3">
                        @foreach(['offline' => ['fa-location-dot', 'Offline', 'Datang ke lokasi, pakai sesi & kuota'], 'online' => ['fa-video', 'Online', 'Link Zoom/Meet muncul setelah daftar']] as $value => [$icon, $name, $hint])
                            <label class="flex items-center gap-2 cursor-pointer px-4 py-3 border-2 rounded-lg" :class="format === '{{ $value }}' ? 'border-primary-500 bg-primary-50' : 'border-gray-200'">
                                <input type="radio" name="format" value="{{ $value }}" x-model="format" class="sr-only">
                                <i class="fas {{ $icon }} text-primary-600"></i>
                                <span class="text-sm font-semibold">{{ $name }}</span>
                                <span class="text-xs text-gray-500">— {{ $hint }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label for="event_date" class="{{ $label }}">Tanggal</label>
                        <input type="date" id="event_date" name="event_date" value="{{ old('event_date', $event->event_date?->format('Y-m-d')) }}" required class="{{ $input }}">
                    </div>
                    <div>
                        <label for="timezone" class="{{ $label }}">Zona waktu</label>
                        <select id="timezone" name="timezone" class="{{ $input }} bg-white">
                            @foreach(\App\Models\Event::TIMEZONES as $tz)
                                <option value="{{ $tz }}" @selected(old('timezone', $event->timezone) === $tz)>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div x-show="format === 'offline'" class="space-y-5">
                    <div>
                        <label for="venue" class="{{ $label }}">Nama tempat</label>
                        <input id="venue" name="venue" value="{{ old('venue', $event->venue) }}" maxlength="255" class="{{ $input }}" placeholder="mis. EGO Corner Cafe">
                    </div>
                    <div>
                        <label for="address" class="{{ $label }}">Alamat <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <textarea id="address" name="address" rows="2" maxlength="500" class="{{ $input }}">{{ old('address', $event->address) }}</textarea>
                    </div>
                    <div>
                        <label for="maps_url" class="{{ $label }}">Link Google Maps <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <input type="url" id="maps_url" name="maps_url" value="{{ old('maps_url', $event->maps_url) }}" maxlength="500" class="{{ $input }}" placeholder="https://maps.app.goo.gl/...">
                    </div>
                </div>
                <div x-show="format === 'online'" x-cloak>
                    <label for="meeting_url" class="{{ $label }}">Link Zoom / Google Meet</label>
                    <input type="url" id="meeting_url" name="meeting_url" value="{{ old('meeting_url', $event->meeting_url) }}" maxlength="500" class="{{ $input }}" placeholder="https://zoom.us/j/...">
                    <p class="mt-1 text-xs text-gray-500">Hanya terlihat oleh peserta yang sudah mendaftar, di halaman event, dashboard, dan email konfirmasi. Boleh diisi belakangan.</p>
                </div>
            </section>

            {{-- Sessions --}}
            <section class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between gap-3 mb-1">
                    <h2 class="flex items-center gap-2 font-semibold text-gray-900"><span class="w-7 h-7 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">3</span> Sesi dan kuota peserta</h2>
                    <button type="button" @click="sessions.push({ id: null, label: 'Sesi ' + (sessions.length + 1), starts_at: '', ends_at: '', capacity: sessions.length ? sessions[sessions.length - 1].capacity : 15, registered: 0 })"
                        class="px-3 py-1.5 text-xs font-semibold rounded-md bg-primary-50 text-primary-700 hover:bg-primary-100">
                        <i class="fas fa-plus mr-1"></i> Tambah sesi
                    </button>
                </div>
                <p class="text-xs text-gray-500 mb-4" x-text="format === 'online' ? 'Event online biasanya cukup satu sesi. Kuota = jumlah maksimal peserta.' : 'Setiap sesi punya kuota sendiri. Sisa kursi tampil ke peserta saat mendaftar.'"></p>

                <div class="space-y-3">
                    <template x-for="(s, i) in sessions" :key="i">
                        <div class="grid grid-cols-2 md:grid-cols-[1.4fr_1fr_1fr_1fr_auto] gap-3 items-end p-3 rounded-lg border border-gray-200 bg-gray-50">
                            <input type="hidden" :name="`sessions[${i}][id]`" :value="s.id ?? ''">
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-xs font-medium text-gray-600 mb-1" :for="`s-label-${i}`">Nama sesi</label>
                                <input :id="`s-label-${i}`" :name="`sessions[${i}][label]`" x-model="s.label" required maxlength="60" class="{{ $input }} bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1" :for="`s-start-${i}`">Mulai</label>
                                <input type="time" :id="`s-start-${i}`" :name="`sessions[${i}][starts_at]`" x-model="s.starts_at" required class="{{ $input }} bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1" :for="`s-end-${i}`">Selesai <span class="text-gray-400">(opsional)</span></label>
                                <input type="time" :id="`s-end-${i}`" :name="`sessions[${i}][ends_at]`" x-model="s.ends_at" class="{{ $input }} bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1" :for="`s-cap-${i}`">Kuota</label>
                                <input type="number" min="1" :id="`s-cap-${i}`" :name="`sessions[${i}][capacity]`" x-model="s.capacity" required class="{{ $input }} bg-white">
                            </div>
                            <div class="flex items-center gap-2 md:pb-2">
                                <span class="text-xs text-gray-500 whitespace-nowrap" x-show="s.registered" x-text="s.registered + ' pendaftar'"></span>
                                <button type="button" @click="sessions.splice(i, 1)" x-show="sessions.length > 1 && !s.registered"
                                    class="w-9 h-9 rounded-md text-red-600 hover:bg-red-50" :aria-label="'Hapus ' + s.label">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </section>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <section class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-3">Poster / gambar cover</h2>
                <div class="aspect-[4/5] rounded-lg overflow-hidden bg-gray-100 mb-3 flex items-center justify-center">
                    <img x-show="preview" :src="preview" alt="" class="w-full h-full object-cover" x-cloak>
                    @if($event->cover_url)
                        <img x-show="!preview" src="{{ $event->cover_url }}" alt="Cover sekarang" class="w-full h-full object-cover">
                    @else
                        <span x-show="!preview" class="text-sm text-gray-400 px-6 text-center">Poster tegak (4:5) paling pas, misalnya 1080×1350 px</span>
                    @endif
                </div>
                <label for="cover" class="sr-only">Pilih gambar cover</label>
                <input type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/webp" @if(! $event->cover) required @endif
                    @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                    class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-primary-50 file:text-primary-700 file:font-semibold">
                <p class="mt-2 text-xs text-gray-500">JPG, PNG, atau WebP, maksimal 4 MB.{{ $event->cover ? ' Kosongkan kalau tidak ingin mengganti.' : '' }}</p>
            </section>

            <section class="bg-white rounded-lg shadow-sm p-6 space-y-4">
                <h2 class="flex items-center gap-2 font-semibold text-gray-900"><span class="w-7 h-7 rounded-full bg-primary-600 text-white text-sm flex items-center justify-center">4</span> Terbitkan</h2>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="hidden" name="is_published" value="0">
                    <input type="checkbox" name="is_published" value="1" @checked(old('is_published', $event->is_published)) class="mt-0.5 w-4 h-4 rounded text-primary-600">
                    <span class="text-sm"><b class="block text-gray-900">Tampilkan di website</b><span class="text-gray-500">Muncul di halaman Event dan beranda.</span></span>
                </label>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="hidden" name="registration_open" value="0">
                    <input type="checkbox" name="registration_open" value="1" @checked(old('registration_open', $event->registration_open)) class="mt-0.5 w-4 h-4 rounded text-primary-600">
                    <span class="text-sm"><b class="block text-gray-900">Pendaftaran dibuka</b><span class="text-gray-500">Matikan untuk menutup pendaftaran lebih awal. Pendaftaran tutup otomatis setelah tanggal event lewat.</span></span>
                </label>
            </section>
        </div>
    </div>
</form>
@endsection
