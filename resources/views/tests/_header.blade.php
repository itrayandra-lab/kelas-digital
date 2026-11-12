{{-- Expects: $course, $section, $kind --}}
<a href="{{ route('course.show', $course->slug) }}" style="font-size:.8rem;color:var(--muted);text-decoration:none;"><i class="fas fa-arrow-left mr-1"></i> {{ Str::limit($course->title, 50) }}</a>
<div style="margin:1rem 0 1.25rem;">
    <div style="font-size:.75rem;font-weight:700;color:var(--blue);text-transform:uppercase;letter-spacing:.05em;">{{ $section->title }}</div>
    <h1 style="font-family:'Sora',sans-serif;font-size:1.5rem;font-weight:800;color:var(--ink);margin-top:.2rem;">{{ $kind === 'pre' ? 'Pre-test' : 'Post-test' }}</h1>
</div>
