{{-- Expects: $course, $section, $kind --}}
<div style="margin:0 0 1.25rem;">
    <div style="font-size:.75rem;font-weight:700;color:var(--blue);text-transform:uppercase;letter-spacing:.05em;">{{ $section->title }}</div>
    <h1 style="font-family:'Sora',sans-serif;font-size:1.5rem;font-weight:800;color:var(--ink);margin-top:.2rem;">{{ $kind === 'pre' ? 'Pre-test' : 'Post-test' }}</h1>
</div>
