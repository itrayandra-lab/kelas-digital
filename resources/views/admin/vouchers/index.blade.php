@extends('layouts.admin')

@section('title', 'Voucher')

@section('content')

    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
        <p class="flex-1 text-sm text-gray-600">Kode potongan harga yang dimasukkan peserta di halaman pembayaran kelas berbayar. Voucher 100% langsung membuka kelas tanpa Midtrans.</p>
        <a href="{{ route('admin.vouchers.create') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 bg-primary-600 text-white font-semibold text-sm rounded-lg shadow-sm hover:bg-primary-700 transition-colors duration-300">
            <i class="fas fa-plus mr-2 -ml-1 text-base"></i>
            Buat Voucher
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-sm text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        @foreach(['Kode', 'Potongan', 'Berlaku untuk', 'Dipakai', 'Masa berlaku', 'Status', ''] as $head)
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">{{ $head }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($vouchers as $voucher)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-semibold text-gray-900">{{ $voucher->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $voucher->label() }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $voucher->course?->title ?? 'Semua kelas berbayar' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $voucher->used_count }}{{ $voucher->max_uses ? ' / '.$voucher->max_uses : '' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $voucher->starts_at?->format('d M Y') ?? 'Sekarang' }} – {{ $voucher->expires_at?->format('d M Y') ?? 'tanpa batas' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php $problem = $voucher->problemFor($voucher->course ?? new \App\Models\Course); @endphp
                                @if($problem)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600" title="{{ $problem }}">Tidak bisa dipakai</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700">Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-4">
                                <a href="{{ route('admin.vouchers.edit', $voucher) }}" class="text-primary-600 hover:text-primary-800">Edit</a>
                                <form action="{{ route('admin.vouchers.destroy', $voucher) }}" method="POST" class="inline" onsubmit="return confirm('Hapus voucher {{ $voucher->code }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sm text-gray-500">Belum ada voucher. Klik "Buat Voucher" untuk membuat yang pertama.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $vouchers->links() }}
    </div>

@endsection
