{{-- Expects: $course, $section, $kind, $progress. One pre-test or post-test row in the lesson sidebar. --}}
@if($progress && $progress->sectionHasTest($section, $kind))
    @php
        $done = $kind === 'pre' ? $progress->preTestDone($section) : $progress->postTestPassed($section);
        $open = ! $done && $progress->testUnlocked($section, $kind);
        $score = $progress->bestScore($section, $kind);
        $label = ($kind === 'pre' ? 'Pre-test' : 'Post-test').($done && $score !== null ? " · skor {$score}" : '');
    @endphp
    @if($open)
        <a href="{{ route('course.test', [$course->slug, $section, $kind]) }}" class="learn-item">
            <span class="learn-dot"><i class="fas {{ $kind === 'pre' ? 'fa-clipboard-list' : 'fa-clipboard-check' }}"></i></span>
            <span style="flex:1;">{{ $label }}</span>
        </a>
    @else
        <div class="learn-item {{ $done ? '' : 'is-locked' }}">
            <span class="learn-dot {{ $done ? 'done' : 'locked' }}"><i class="fas {{ $done ? 'fa-check' : 'fa-lock' }}"></i></span>
            <span style="flex:1;">{{ $label }}</span>
        </div>
    @endif
@endif
