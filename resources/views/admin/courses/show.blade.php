@extends('layouts.admin')

@section('title', 'Peserta — '.$course->title)

@section('content')
@php
    $statusStyle = [
        'paid' => 'bg-green-50 text-green-700 ring-green-200',
        'free' => 'bg-green-50 text-green-700 ring-green-200',
        'trial' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'waiting' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'failed' => 'bg-red-50 text-red-700 ring-red-200',
    ];
    $statuses = \App\Support\CourseRoster::STATUSES;
    $filterLink = fn (array $set) => route('admin.courses.show', [$course->id] + array_filter(array_merge(['q' => $search ?: null, 'status' => $status, 'sort' => $sort], $set)));
    $score = fn (?int $v) => $v === null ? '–' : $v;
    $scoreClass = fn (?int $v) => $v === null ? 'text-gray-400' : ($v >= $course->post_test_pass_score ? 'text-green-700' : 'text-red-600');
@endphp

{{-- Header --}}
<div class="flex flex-col lg:flex-row lg:items-center gap-4 mb-6">
    <div class="flex items-center gap-4 min-w-0 flex-1">
        @if($course->thumbnail_url)
            <img src="{{ $course->thumbnail_url }}" alt="" class="w-24 h-14 rounded-lg object-cover shrink-0 hidden sm:block">
        @endif
        <div class="min-w-0">
            <a href="{{ route('admin.courses.index') }}" class="text-sm text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left mr-1"></i> Daftar kelas</a>
            <h1 class="text-2xl font-bold text-gray-900 truncate">{{ $course->title }}</h1>
            <p class="text-sm text-gray-500">
                {{ $course->instructor }} · {{ $course->category?->name ?? 'Tanpa kategori' }} ·
                {{ $course->isFreeClass() ? 'Gratis' : 'Rp'.number_format($course->price, 0, ',', '.') }}
            </p>
        </div>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('course.show', $course->slug) }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-sm font-semibold rounded-lg hover:bg-gray-50"><i class="fas fa-external-link-alt mr-2"></i> Halaman kelas</a>
        @can('edit courses')
            <a href="{{ route('admin.curriculum.show', $course) }}" class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 text-sm font-semibold rounded-lg hover:bg-gray-50"><i class="fas fa-list-ol mr-2"></i> Kurikulum &amp; Tes</a>
            <a href="{{ route('admin.courses.edit', $course->id) }}" class="inline-flex items-center px-3 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700"><i class="fas fa-pen mr-2"></i> Edit kelas</a>
        @endcan
    </div>
</div>

{{-- KPI cards --}}
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 mb-6">
    @php
        $cards = [
            ['Total pendaftar', $stats['total'], 'fa-users', $stats['new_week'] ? '+'.$stats['new_week'].' minggu ini' : 'Belum ada yang baru minggu ini'],
            [$course->isFreeClass() ? 'Terdaftar' : 'Sudah bayar', ($stats['statuses']['paid'] ?? 0) + ($stats['statuses']['free'] ?? 0), 'fa-check-circle', 'Akses penuh'],
            ['Belajar modul gratis', $stats['statuses']['trial'] ?? 0, 'fa-gift', ($stats['statuses']['waiting'] ?? 0).' menunggu bayar'],
            ['Rata-rata progres', $stats['avg_percent'].'%', 'fa-chart-line', 'Dari semua pendaftar'],
            ['Selesai kelas', $stats['finished'], 'fa-trophy', $stats['total'] ? round($stats['finished'] / $stats['total'] * 100).'% dari pendaftar' : '–'],
        ];
    @endphp
    @foreach($cards as [$label, $value, $icon, $hint])
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500"><i class="fas {{ $icon }} text-primary-600"></i> {{ $label }}</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">{{ $value }}</div>
            <div class="text-xs text-gray-500 mt-0.5">{{ $hint }}</div>
        </div>
    @endforeach
    @can('manage enrollments')
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500"><i class="fas fa-wallet text-primary-600"></i> Pendapatan</div>
            <div class="text-2xl font-bold text-gray-900 mt-1">Rp{{ number_format($stats['revenue'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-0.5">Dari pembayaran lunas</div>
        </div>
    @endcan
</div>

{{-- Per module --}}
@if($modules->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-900">Perkembangan per modul</h2>
            <p class="text-xs text-gray-500 mt-0.5">Nilai memakai skor terbaik tiap peserta. Selisih pre-test dan post-test menunjukkan seberapa banyak peserta belajar di modul itu.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-5 py-3 text-left">Modul</th>
                        <th class="px-4 py-3 text-right">Mulai</th>
                        <th class="px-4 py-3 text-right">Lulus post-test</th>
                        <th class="px-4 py-3 text-right">Rata-rata pre</th>
                        <th class="px-4 py-3 text-right">Rata-rata post</th>
                        <th class="px-5 py-3 text-right">Kenaikan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($modules as $m)
                        @php $gain = $m['pre_avg'] !== null && $m['post_avg'] !== null ? $m['post_avg'] - $m['pre_avg'] : null; @endphp
                        <tr>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $m['section']->title }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">{{ $m['started'] }}</td>
                            <td class="px-4 py-3 text-right tabular-nums">
                                {{ $m['passed'] }}
                                @if($m['started'])<span class="text-xs text-gray-400">({{ round($m['passed'] / $m['started'] * 100) }}%)</span>@endif
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums {{ $m['pre_avg'] === null ? 'text-gray-400' : '' }}">{{ $score($m['pre_avg']) }}</td>
                            <td class="px-4 py-3 text-right tabular-nums {{ $scoreClass($m['post_avg']) }}">{{ $score($m['post_avg']) }}</td>
                            <td class="px-5 py-3 text-right tabular-nums font-semibold {{ $gain === null ? 'text-gray-400' : ($gain >= 0 ? 'text-green-700' : 'text-red-600') }}">
                                {{ $gain === null ? '–' : ($gain >= 0 ? '+' : '').$gain }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Learners --}}
<div class="bg-white rounded-xl border border-gray-200">
    <div class="px-5 py-4 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center gap-3">
        <div class="flex-1">
            <h2 class="font-bold text-gray-900">Daftar peserta</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $learners->total() }} dari {{ $stats['total'] }} peserta</p>
        </div>
        <form method="GET" action="{{ route('admin.courses.show', $course->id) }}" class="flex flex-wrap gap-2" role="search">
            @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            <label class="sr-only" for="learner-q">Cari peserta</label>
            <input id="learner-q" type="search" name="q" value="{{ $search }}" placeholder="Cari nama atau email…" autocomplete="off"
                class="w-56 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-primary-600 focus:ring-2 focus:ring-blue-100 focus:outline-none">
            <label class="sr-only" for="learner-sort">Urutkan</label>
            <select id="learner-sort" name="sort" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white">
                <option value="">Pendaftar terbaru</option>
                <option value="active" @selected($sort === 'active')>Aktivitas terakhir</option>
                <option value="progress" @selected($sort === 'progress')>Progres tertinggi</option>
                <option value="progress_low" @selected($sort === 'progress_low')>Progres terendah</option>
            </select>
            <button class="px-3 py-2 text-sm font-semibold bg-gray-900 text-white rounded-lg hover:bg-gray-700">Cari</button>
            <a href="{{ route('admin.courses.learners.export', $course) }}" class="inline-flex items-center px-3 py-2 text-sm font-semibold border border-gray-300 rounded-lg hover:bg-gray-50"><i class="fas fa-file-csv mr-2 text-green-700"></i> Ekspor CSV</a>
        </form>
    </div>

    {{-- Status filter chips --}}
    <div class="px-5 py-3 flex flex-wrap gap-2 border-b border-gray-100">
        <a href="{{ $filterLink(['status' => null]) }}" class="px-3 py-1 text-xs font-semibold rounded-full ring-1 {{ $status ? 'ring-gray-200 text-gray-600 hover:bg-gray-50' : 'ring-primary-600 bg-primary-600 text-white' }}">Semua ({{ $stats['total'] }})</a>
        @foreach($statuses as $key => $label)
            @if($stats['statuses'][$key] ?? 0)
                <a href="{{ $filterLink(['status' => $key]) }}" class="px-3 py-1 text-xs font-semibold rounded-full ring-1 {{ $status === $key ? 'ring-primary-600 bg-primary-600 text-white' : 'ring-gray-200 text-gray-600 hover:bg-gray-50' }}">{{ $label }} ({{ $stats['statuses'][$key] }})</a>
            @endif
        @endforeach
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <tr>
                    <th class="px-5 py-3 text-left">Peserta</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left min-w-[220px]">Progres</th>
                    <th class="px-4 py-3 text-right">Nilai post-test</th>
                    <th class="px-4 py-3 text-left">Terdaftar</th>
                    <th class="px-5 py-3 text-left">Aktivitas terakhir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($learners as $r)
                    <tr class="hover:bg-gray-50 align-top">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-50 text-primary-700 font-bold flex items-center justify-center shrink-0">{{ strtoupper(mb_substr($r['user']->name, 0, 1)) }}</div>
                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900">{{ $r['user']->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $r['user']->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2.5 py-1 text-xs font-semibold rounded-full ring-1 {{ $statusStyle[$r['status']] }}">{{ $statuses[$r['status']] }}</span>
                            @if($r['enrollment']->voucher_id)<div class="text-xs text-gray-500 mt-1"><i class="fas fa-ticket-alt"></i> pakai voucher</div>@endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden" role="progressbar" aria-valuenow="{{ $r['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="Progres {{ $r['user']->name }}">
                                    <div class="h-full rounded-full {{ $r['finished'] ? 'bg-green-600' : 'bg-primary-600' }}" style="width: {{ $r['percent'] }}%"></div>
                                </div>
                                <span class="text-xs font-semibold tabular-nums w-9 text-right">{{ $r['percent'] }}%</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                @if($r['finished'])<span class="text-green-700 font-semibold"><i class="fas fa-trophy"></i> Kelas selesai</span>
                                @else Berikutnya: {{ \Illuminate\Support\Str::limit($r['next'], 60) }}
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right tabular-nums font-semibold {{ $scoreClass($r['post_avg']) }}">{{ $score($r['post_avg']) }}</td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $r['enrollment']->created_at->locale('id')->translatedFormat('d M Y') }}</td>
                        <td class="px-5 py-3 text-gray-600 whitespace-nowrap" title="{{ $r['last_active']?->locale('id')->translatedFormat('d M Y H:i') }}">{{ $r['last_active']?->locale('id')->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-500">
                            {{ $stats['total'] ? 'Tidak ada peserta yang cocok dengan pencarian atau filter ini.' : 'Belum ada yang mendaftar di kelas ini.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($learners->hasPages())
    <div class="mt-4">{{ $learners->links() }}</div>
@endif

@if(Gate::check('edit courses') && $stats['total'])
    <p class="text-xs text-gray-500 mt-4">Jawaban tes setiap peserta, termasuk essay yang perlu dinilai, ada di <a href="{{ route('admin.curriculum.show', [$course, 'tab' => 'jawaban']) }}" class="text-primary-600 font-semibold hover:underline">Kurikulum &amp; Tes → Jawaban Peserta</a>.</p>
@endif
@endsection
