{{-- Expects: $course, $section, $progress. Sits where the admin placed the module's pre-test. --}}
<div class="cd-row">
    <i class="fas {{ $progress?->preTestDone($section) ? 'fa-check-circle ok' : 'fa-clipboard-list' }} ic"></i>
    <span class="t"><strong>Pre-test</strong> · {{ $section->pre_count }} soal</span>
    @if($progress?->reviewer)<a href="{{ route('course.test', [$course->slug, $section, 'pre']) }}">Buka</a>@endif
</div>
