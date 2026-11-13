{{--
    Classroom shell (Dicoding-style): module list on the left, full content in the middle,
    previous/next navigation at the bottom.
    Props: $course, $progress (CourseProgress or null for previews), $current (item key, e.g. "intro",
    "pre-12", "lesson-40", "post-12").
--}}
@props(['course', 'progress' => null, 'current' => 'intro'])

@php
    $sections = $progress?->sections ?? $course->sections()->with('lessons')->get();

    // Every module in study order, with where it links and whether the learner may open it.
    $items = collect([[
        'key' => 'intro', 'group' => null, 'label' => 'Pengenalan Kelas', 'icon' => 'fa-flag',
        'url' => route('course.intro', $course->slug), 'open' => (bool) $progress, 'done' => $progress && $progress->percent() > 0,
    ]]);

    foreach ($sections as $sIndex => $section) {
        $no = $sIndex + 1;
        $test = function (string $kind) use ($progress, $section, $course, $no) {
            $done = $kind === 'pre' ? $progress?->preTestDone($section) : $progress?->postTestPassed($section);
            $score = $done ? $progress->bestScore($section, $kind) : null;

            return [
                'key' => "{$kind}-{$section->id}", 'group' => $section, 'no' => $no,
                'label' => ($kind === 'pre' ? 'Pre-test' : 'Post-test').($score !== null ? " · skor {$score}" : ''),
                'icon' => $kind === 'pre' ? 'fa-clipboard-list' : 'fa-clipboard-check',
                'url' => route('course.test', [$course->slug, $section, $kind]),
                'open' => (bool) $progress?->testUnlocked($section, $kind), 'done' => (bool) $done,
            ];
        };

        if ($progress?->sectionHasTest($section, 'pre')) {
            $items->push($test('pre'));
        }

        foreach ($section->lessons as $lIndex => $lesson) {
            $items->push([
                'key' => "lesson-{$lesson->id}", 'group' => $section, 'no' => $no,
                'label' => $no.'.'.($lIndex + 1).' '.$lesson->title,
                'icon' => $lesson->isVideo() ? 'fa-play-circle' : 'fa-file-alt',
                'url' => route('course.lesson', [$course->slug, $lesson]),
                'open' => (bool) $progress?->isUnlocked($lesson),
                'done' => (bool) $progress?->isCompleted($lesson),
                'meta' => $lesson->duration ? $lesson->duration.' mnt' : null,
            ]);
        }

        if ($progress?->sectionHasTest($section, 'post')) {
            $items->push($test('post'));
        }
    }

    $position = $items->search(fn ($i) => $i['key'] === $current);
    $prev = $position > 0 ? $items->get($position - 1) : null;
    $next = $position !== false ? $items->get($position + 1) : null;
    $percent = $progress?->percent() ?? 0;
@endphp

@once
@push('styles')
<style>
.cr { display:grid; grid-template-columns:320px minmax(0,1fr); min-height:calc(100vh - 72px); background:var(--surf); }
.cr-side { background:#fff; border-right:1px solid var(--border); position:sticky; top:72px; height:calc(100vh - 72px); overflow-y:auto; }
.cr-side-head { padding:1.1rem 1.1rem 1rem; border-bottom:1px solid var(--border); }
.cr-side-head a { font-size:.78rem; color:var(--muted); text-decoration:none; }
.cr-side-title { font-family:'Sora',sans-serif; font-weight:700; color:var(--ink); font-size:.95rem; line-height:1.35; margin:.4rem 0 .75rem; }
.cr-bar { height:6px; background:var(--surf); border-radius:999px; overflow:hidden; }
.cr-bar div { height:100%; background:var(--blue); }
.cr-group { padding:.75rem 1.1rem .4rem; display:flex; justify-content:space-between; gap:.5rem; font-size:.72rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; }
.cr-item { display:flex; align-items:flex-start; gap:.6rem; padding:.6rem 1.1rem; font-size:.83rem; color:var(--ink-2); text-decoration:none; border-left:3px solid transparent; line-height:1.4; }
.cr-item:hover { background:var(--surf); }
.cr-item.is-current { background:var(--blue-xl); border-left-color:var(--blue); color:var(--blue); font-weight:700; }
.cr-item.is-locked { color:#a0aec0; cursor:not-allowed; }
.cr-item i.st { width:16px; text-align:center; margin-top:.15rem; color:var(--blue); }
.cr-item.is-done i.st { color:#16a34a; }
.cr-item.is-locked i.st { color:#cbd5e1; }
.cr-item small { margin-left:auto; color:var(--muted); font-weight:400; white-space:nowrap; }
.cr-main { min-width:0; padding:1.75rem 2rem 3rem; }
.cr-inner { max-width:820px; margin:0 auto; }
.cr-nav { display:flex; justify-content:space-between; gap:1rem; margin-top:2rem; padding-top:1.25rem; border-top:1px solid var(--border); }
.cr-nav a, .cr-nav .cr-dis { display:inline-flex; align-items:center; gap:.5rem; padding:.7rem 1.1rem; border-radius:10px; font-size:.85rem; font-weight:600; text-decoration:none; max-width:48%; }
.cr-nav a { background:#fff; border:1px solid var(--border); color:var(--ink); }
.cr-nav a:hover { border-color:var(--blue); color:var(--blue); }
.cr-nav .cr-dis { color:#a0aec0; border:1px dashed var(--border); }
.cr-nav .lbl { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
.cr-toggle { display:none; }
.cr-flash { border:1px solid; border-radius:10px; padding:.75rem 1rem; font-size:.875rem; margin-bottom:1rem; }
@media (max-width: 1024px) {
    .cr { grid-template-columns:1fr; }
    .cr-side { position:fixed; inset:0 auto 0 0; width:min(320px, 88vw); z-index:1000; height:100vh; transform:translateX(-100%); transition:transform .2s; box-shadow:0 0 40px rgba(10,22,40,.25); }
    .cr-side.open { transform:none; }
    .cr-toggle { display:inline-flex; align-items:center; gap:.5rem; margin-bottom:1rem; padding:.55rem .9rem; background:#fff; border:1px solid var(--border); border-radius:10px; font-size:.85rem; font-weight:600; color:var(--ink); cursor:pointer; }
    .cr-main { padding:1.25rem 1rem 3rem; }
}
</style>
@endpush
@endonce

<div class="cr" x-data="{ menu: false }" @keydown.escape.window="menu = false">
    <aside class="cr-side" :class="menu && 'open'" aria-label="Daftar modul">
        <div class="cr-side-head">
            <a href="{{ route('course.show', $course->slug) }}"><i class="fas fa-arrow-left mr-1"></i> Halaman kelas</a>
            <div class="cr-side-title">{{ $course->title }}</div>
            @if($progress)
                <div style="display:flex;justify-content:space-between;font-size:.75rem;color:var(--muted);margin-bottom:.35rem;">
                    <span>Progress belajar</span><strong style="color:var(--blue);">{{ $percent }}% selesai</strong>
                </div>
                <div class="cr-bar"><div style="width:{{ $percent }}%"></div></div>
            @endif
        </div>

        @php $lastGroup = false; @endphp
        @foreach($items as $item)
            @if($item['group'] !== $lastGroup)
                @php
                    $lastGroup = $item['group'];
                    $groupItems = $items->filter(fn ($i) => $i['group'] === $item['group']);
                @endphp
                <div class="cr-group">
                    <span>{{ $item['group'] ? $item['group']->title : 'Persiapan Belajar' }}</span>
                    @if($item['group'] && $progress)<span>{{ $groupItems->where('done', true)->count() }}/{{ $groupItems->count() }}</span>@endif
                </div>
            @endif

            @php
                $state = $item['key'] === $current ? 'is-current' : ($item['done'] ? 'is-done' : ($item['open'] ? '' : 'is-locked'));
                $icon = $item['done'] ? 'fa-check-circle' : ($item['open'] || $item['key'] === $current ? $item['icon'] : 'fa-lock');
            @endphp
            @if($item['open'] || $item['done'])
                <a href="{{ $item['url'] }}" class="cr-item {{ $state }}">
            @else
                <div class="cr-item {{ $state }}">
            @endif
                <i class="fas {{ $icon }} st"></i>
                <span>{{ $item['label'] }}</span>
                @if(! empty($item['meta']))<small>{{ $item['meta'] }}</small>@endif
            @if($item['open'] || $item['done'])</a>@else</div>@endif
        @endforeach
    </aside>
    <div x-show="menu" x-cloak @click="menu = false" style="position:fixed;inset:0;background:rgba(10,22,40,.4);z-index:999;"></div>

    <main class="cr-main">
        <div class="cr-inner">
            <button type="button" class="cr-toggle" @click="menu = true"><i class="fas fa-list"></i> Daftar Modul</button>

            @foreach (['error' => 'background:#fef2f2;color:#b91c1c;border-color:#fecaca', 'message' => 'background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe'] as $key => $style)
                @if(session($key))<div class="cr-flash" style="{{ $style }}">{{ session($key) }}</div>@endif
            @endforeach

            {{ $slot }}

            @if($progress)
                <nav class="cr-nav" aria-label="Navigasi modul">
                    @if($prev && ($prev['open'] || $prev['done']))
                        <a href="{{ $prev['url'] }}"><i class="fas fa-arrow-left"></i> <span class="lbl">{{ $prev['label'] }}</span></a>
                    @else
                        <div></div>
                    @endif
                    @if($next && ($next['open'] || $next['done']))
                        <a href="{{ $next['url'] }}" style="margin-left:auto;"><span class="lbl">{{ $next['label'] }}</span> <i class="fas fa-arrow-right"></i></a>
                    @elseif($next)
                        <span class="cr-dis" style="margin-left:auto;" title="Selesaikan modul ini dulu"><i class="fas fa-lock"></i> <span class="lbl">{{ $next['label'] }}</span></span>
                    @endif
                </nav>
            @endif
        </div>
    </main>
</div>
