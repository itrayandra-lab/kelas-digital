{{-- One expert card. Expects $expert (from SchoolController::experts) and $schools (all active families, keyed by slug). --}}
@once
@push('styles')
<style>
.ex-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(min(100%,250px),1fr));gap:1rem;}
.ex-card{display:flex;flex-direction:column;gap:.75rem;padding:1.25rem;background:#fff;border:1px solid #e3ecf8;border-radius:20px;box-shadow:0 12px 30px rgba(31,77,140,.08);}
.ex-top{display:flex;align-items:center;gap:.85rem;}
.ex-avatar{flex-shrink:0;width:52px;height:52px;border-radius:50%;display:grid;place-items:center;font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;color:#fff;
    background:linear-gradient(150deg,#2f6fd0,#0b2a63);box-shadow:0 6px 16px rgba(11,42,99,.25);}
.ex-name{font-family:'Sora',sans-serif;font-size:.98rem;font-weight:700;line-height:1.35;color:#0b1f4d;margin:0;}
.ex-tags{display:flex;flex-wrap:wrap;gap:.35rem;}
.ex-tags a{font-size:.8rem;font-weight:700;color:#1f4f9c;background:#e6effb;border-radius:999px;padding:.25rem .65rem;text-decoration:none;}
.ex-tags a:hover{background:#d3e3f8;}
.ex-classes{margin:0;padding-top:.7rem;border-top:1px solid #edf2f9;font-size:.82rem;color:#4a5878;}
.ex-classes b{display:block;font-size:.8rem;letter-spacing:.08em;text-transform:uppercase;color:#5b6b8a;margin-bottom:.3rem;}
.ex-classes a{color:#0b2a63;font-weight:600;text-decoration:none;}
.ex-classes a:hover{color:#1f6fd1;text-decoration:underline;}
</style>
@endpush
@endonce
<article class="ex-card">
    <div class="ex-top">
        <span class="ex-avatar" aria-hidden="true">{{ $expert->initials() }}</span>
        <h3 class="ex-name">{{ $expert->name }}</h3>
    </div>
    <div class="ex-tags">
        @foreach($expert->schools ?? [] as $slug)
            @isset($schools[$slug])<a href="{{ route('school.show', $slug) }}">{{ $schools[$slug]['name'] }}</a>@endisset
        @endforeach
    </div>
    @if($expert->courses->isNotEmpty())
        <p class="ex-classes"><b>Mengajar</b>
            @foreach($expert->courses as $course)<a href="{{ route('course.show', $course->slug) }}">{{ $course->title }}</a>@if(! $loop->last), @endif @endforeach
        </p>
    @endif
</article>
