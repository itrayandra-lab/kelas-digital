@extends('layouts.admin')

@section('title', $voucher->exists ? 'Edit Voucher' : 'Buat Voucher')

@section('content')
@php $field = 'w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500'; @endphp

<form method="POST" action="{{ $voucher->exists ? route('admin.vouchers.update', $voucher) : route('admin.vouchers.store') }}"
      class="bg-white rounded-lg shadow-sm p-6 max-w-2xl space-y-5" x-data="{ type: '{{ old('discount_type', $voucher->discount_type) }}' }">
    @csrf
    @if($voucher->exists) @method('PUT') @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-sm text-red-700 px-4 py-3 rounded-lg">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <div>
        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode voucher</label>
        <input type="text" name="code" id="code" value="{{ old('code', $voucher->code) }}" required maxlength="40" placeholder="mis. RAYHEMAT50" class="{{ $field }} uppercase font-mono">
        <p class="mt-1 text-xs text-gray-500">Huruf, angka, strip, dan garis bawah. Peserta mengetiknya di halaman pembayaran.</p>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <span class="block text-sm font-medium text-gray-700 mb-1">Jenis potongan</span>
            <div class="flex gap-2">
                @foreach(['percent' => 'Persen (%)', 'fixed' => 'Nominal (Rp)'] as $value => $label)
                    <label class="flex-1 flex items-center gap-2 px-3 py-2.5 border rounded-lg cursor-pointer text-sm" :class="type === '{{ $value }}' ? 'border-primary-500 bg-primary-50' : 'border-gray-300'">
                        <input type="radio" name="discount_type" value="{{ $value }}" x-model="type"> {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>
        <div>
            <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-1">Besar potongan</label>
            <input type="number" name="discount_value" id="discount_value" min="1" :max="type === 'percent' ? 100 : null" value="{{ old('discount_value', $voucher->discount_value) }}" required class="{{ $field }}">
            <p class="mt-1 text-xs text-gray-500" x-text="type === 'percent' ? '100 berarti kelas gratis.' : 'Kalau melebihi harga kelas, kelas jadi gratis.'"></p>
        </div>
    </div>

    <div>
        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Berlaku untuk</label>
        <select name="course_id" id="course_id" class="{{ $field }}">
            <option value="">Semua kelas berbayar</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}" @selected(old('course_id', $voucher->course_id) == $course->id)>{{ $course->title }}</option>
            @endforeach
        </select>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
        <div>
            <label for="max_uses" class="block text-sm font-medium text-gray-700 mb-1">Kuota pemakaian</label>
            <input type="number" name="max_uses" id="max_uses" min="1" value="{{ old('max_uses', $voucher->max_uses) }}" placeholder="Tanpa batas" class="{{ $field }}">
        </div>
        <div>
            <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">Mulai berlaku</label>
            <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at', $voucher->starts_at?->format('Y-m-d')) }}" class="{{ $field }}">
        </div>
        <div>
            <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-1">Berakhir</label>
            <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at', $voucher->expires_at?->format('Y-m-d')) }}" class="{{ $field }}">
            <p class="mt-1 text-xs text-gray-500">Masih berlaku sampai akhir hari itu.</p>
        </div>
    </div>

    <label class="flex items-center gap-2 text-sm text-gray-700">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $voucher->is_active)) class="h-4 w-4 rounded border-gray-300">
        Aktif (matikan untuk menghentikan voucher tanpa menghapusnya)
    </label>

    @if($voucher->exists)
        <p class="text-sm text-gray-600">Sudah dipakai <strong>{{ $voucher->used_count }}</strong> kali.</p>
    @endif

    <div class="flex gap-2">
        <button type="submit" class="px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700">Simpan</button>
        <a href="{{ route('admin.vouchers.index') }}" class="px-5 py-2.5 bg-white border border-gray-300 text-sm rounded-lg">Batal</a>
    </div>
</form>
@endsection
