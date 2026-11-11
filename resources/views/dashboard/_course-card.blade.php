{{-- Expects: $course, $progress --}}
@php $percent = $progress->percent(); $done = $progress->isCourseFinished(); @endphp
<div class="db-card">
    <a href="{{ route('course.show', $course->slug) }}" class="db-thumb">
        @if($course->thumbnail_url)
            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" loading="lazy">
        @endif
        @if($done)<span class="db-chip done"><i class="fas fa-check"></i> Selesai</span>@endif
    </a>
    <div class="db-body">
        @if($course->category)<div class="db-cat">{{ $course->category->name }}</div>@endif
        <h3 class="db-title"><a href="{{ route('course.show', $course->slug) }}">{{ $course->title }}</a></h3>
        <div class="db-next">
            <i class="fas {{ $done ? 'fa-trophy' : 'fa-forward' }}"></i>
            {{ $done ? 'Kelas sudah selesai' : 'Berikutnya: '.$progress->nextStepLabel() }}
        </div>
        <div class="db-prog-row"><span>Progress</span><strong>{{ $percent }}%</strong></div>
        <div class="db-prog"><div style="width:{{ $percent }}%;{{ $done ? 'background:#10b981;' : '' }}"></div></div>
        <a href="{{ route('course.learn', $course->slug) }}" class="db-btn {{ $done ? 'ghost' : '' }}">
            {{ $done ? 'Lihat Hasil' : ($percent > 0 ? 'Lanjut Belajar' : 'Mulai Belajar') }}
        </a>
    </div>
</div>
