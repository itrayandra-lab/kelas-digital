{{-- Expects: $course, $progress --}}
@php $percent = $progress->percent(); $done = $progress->isCourseFinished(); @endphp
<div class="db-row">
    <a href="{{ route('course.show', $course->slug) }}" class="db-row-thumb" tabindex="-1" aria-hidden="true">
        @if($course->thumbnail_url)<img src="{{ $course->thumbnail_url }}" alt="" loading="lazy">@endif
    </a>
    <div class="db-row-info">
        @if($done)
            <span class="db-badge done"><i class="fas fa-check" aria-hidden="true"></i> Selesai</span>
        @elseif($course->category)
            <div class="db-cat">{{ $course->category->name }}</div>
        @endif
        <h3 class="db-row-title"><a href="{{ route('course.show', $course->slug) }}">{{ $course->title }}</a></h3>
        <div class="db-row-next" title="{{ $done ? '' : $progress->nextStepLabel() }}">{{ $done ? 'Kelas sudah selesai. Selamat!' : 'Berikutnya: '.$progress->nextStepLabel() }}</div>
        <div class="db-prog-line">
            <div class="db-prog {{ $done ? 'done' : '' }}"><div style="width:{{ $percent }}%"></div></div>
            <strong>{{ $percent }}%</strong>
        </div>
    </div>
    <a href="{{ route('course.learn', $course->slug) }}" class="db-btn {{ $done || $percent > 0 ? 'ghost' : '' }}" aria-label="{{ ($done ? 'Lihat hasil ' : ($percent > 0 ? 'Lanjut belajar ' : 'Mulai belajar ')).$course->title }}">
        {{ $done ? 'Lihat Hasil' : ($percent > 0 ? 'Lanjut' : 'Mulai') }}
    </a>
</div>
