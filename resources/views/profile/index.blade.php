{{-- ============================================================
     resources/views/profile/index.blade.php
     ============================================================ --}}
@extends('layouts.app')
@section('title', 'Profil Saya — Ray Academy')

@push('styles')
<style>
.profile-layout { display:grid; grid-template-columns:280px 1fr; gap:2rem; padding:3rem 0 5rem; max-width:1100px; margin:0 auto; }
@media(max-width:900px) { .profile-layout { grid-template-columns:1fr; } }

/* ── Avatar card ── */
.avatar-card { background:#fff; border:1.5px solid var(--border); border-radius:20px; padding:2rem 1.5rem; text-align:center; position:sticky; top:88px; }
.avatar-circle {
    width:80px; height:80px; border-radius:50%; margin:0 auto 1rem;
    background:linear-gradient(135deg,var(--blue-d),var(--blue));
    display:flex; align-items:center; justify-content:center;
    font-family:'Sora',sans-serif; font-size:1.75rem; font-weight:800; color:#fff;
    box-shadow:0 8px 24px rgba(20,116,188,.3);
}
.avatar-card img { width:80px; height:80px; border-radius:50%; object-fit:cover; margin:0 auto 1rem; display:block; border:3px solid var(--blue-xl); }
.avatar-name { font-family:'Sora',sans-serif; font-size:1.05rem; font-weight:800; color:var(--ink); }
.avatar-username { font-size:.8rem; color:var(--muted); margin-top:.25rem; }
.avatar-role { display:inline-block; margin-top:.65rem; font-size:.72rem; font-weight:700; padding:.3rem .8rem; border-radius:999px; }
.role-admin { background:#fee2e2; color:#991b1b; }
.role-instructor { background:#dbeafe; color:#1d4ed8; }
.role-content_manager { background:#dcfce7; color:#166534; }
.role-student { background:var(--blue-xl); color:var(--blue); }

.avatar-nav { margin-top:1.5rem; border-top:1px solid var(--border); padding-top:1.25rem; display:flex; flex-direction:column; gap:.35rem; }
.avatar-nav-link {
    display:flex; align-items:center; gap:.75rem; padding:.7rem .9rem; border-radius:10px;
    font-size:.875rem; font-weight:500; color:var(--ink-2); text-decoration:none; transition:all .18s;
}
.avatar-nav-link:hover { background:var(--surf); color:var(--blue); }
.avatar-nav-link.active { background:var(--blue-xl); color:var(--blue); font-weight:700; }
.avatar-nav-link svg { width:16px; height:16px; flex-shrink:0; }

/* ── Info card ── */
.info-card { background:#fff; border:1.5px solid var(--border); border-radius:18px; overflow:hidden; margin-bottom:1.5rem; }
.info-card-head { padding:1.25rem 1.5rem; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:1rem; }
.info-card-head h2 { font-family:'Sora',sans-serif; font-size:1rem; font-weight:800; color:var(--ink); }
.info-card-body { padding:1.5rem; }
.info-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem 2rem; }
@media(max-width:600px) { .info-grid { grid-template-columns:1fr; } }
.info-field dt { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); margin-bottom:.35rem; }
.info-field dd { font-size:.9rem; font-weight:500; color:var(--ink); }

/* Enrolled course card */
.enrolled-course {
    display:flex; align-items:center; gap:.9rem; padding:.9rem; border-radius:12px;
    border:1.5px solid var(--border); text-decoration:none; color:inherit; transition:all .2s;
}
.enrolled-course:hover { border-color:#93c5fd; background:var(--blue-xl); transform:translateX(3px); }
.enrolled-course-thumb { width:52px; height:42px; border-radius:8px; object-fit:cover; background:var(--border); flex-shrink:0; }
.enrolled-course-title { font-family:'Sora',sans-serif; font-size:.83rem; font-weight:700; color:var(--ink); line-height:1.35; flex:1; }
.enrolled-course-instructor { font-size:.72rem; color:var(--muted); margin-top:.15rem; }
.enrolled-courses-grid { display:grid; grid-template-columns:1fr 1fr; gap:.75rem; }
@media(max-width:640px) { .enrolled-courses-grid { grid-template-columns:1fr; } }

/* ── Edit/CTA button ── */
.btn-edit {
    display:inline-flex; align-items:center; gap:.45rem;
    font-family:'DM Sans',sans-serif; font-size:.83rem; font-weight:700;
    color:var(--blue); background:var(--blue-xl); border:1.5px solid rgba(20,116,188,.22);
    padding:.5rem 1rem; border-radius:9px; text-decoration:none; transition:all .18s;
}
.btn-edit:hover { background:#bfdbfe; border-color:var(--blue); }
</style>
@endpush

@section('content')

<div style="background:var(--surf); min-height:100vh;">
    <div style="max-width:1280px;margin:0 auto;padding:0 1.5rem;">

        {{-- Alert --}}
        @if(session('success'))
        <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;color:#15803d;padding:.85rem 1.1rem;border-radius:12px;font-size:.875rem;font-weight:500;margin-top:1.5rem;display:flex;align-items:center;gap:.6rem;">
            <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
        @endif

        <div class="profile-layout">

            {{-- Sidebar --}}
            <aside>
                <div class="avatar-card rv">
                    @if($user->avatar ?? null)
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}">
                    @else
                        <div class="avatar-circle">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    @endif
                    <div class="avatar-name">{{ $user->name }}</div>
                    <div class="avatar-username">@{{ $user->username ?? '' }}</div>
                    <span class="avatar-role role-{{ strtolower($user->getRoleNames()->first() ?? 'student') }}">
                        @if($user->isAdmin()) Administrator
                        @elseif($user->isInstructor()) Instructor
                        @elseif($user->isContentManager()) Content Manager
                        @else Student
                        @endif
                    </span>

                    <nav class="avatar-nav">
                        <a href="{{ route('profile.index') }}" class="avatar-nav-link active">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil Saya
                        </a>
                        <a href="{{ route('profile.edit') }}" class="avatar-nav-link">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit Profil
                        </a>
                        <a href="{{ route('profile.change-password') }}" class="avatar-nav-link">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Ubah Password
                        </a>
                        <a href="{{ Auth::user()->hasRole('student') ? route('dashboard') : route('admin.dashboard') }}" class="avatar-nav-link">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            {{ Auth::user()->hasRole('student') ? 'Dashboard' : 'Admin Panel' }}
                        </a>
                    </nav>
                </div>
            </aside>

            {{-- Main --}}
            <main>
                {{-- Profile Info --}}
                <div class="info-card rv">
                    <div class="info-card-head">
                        <h2>Informasi Profil</h2>
                        <a href="{{ route('profile.edit') }}" class="btn-edit">
                            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                    </div>
                    <div class="info-card-body">
                        <dl class="info-grid">
                            <div class="info-field">
                                <dt>Nama Lengkap</dt>
                                <dd>{{ $user->name }}</dd>
                            </div>
                            <div class="info-field">
                                <dt>Username</dt>
                                <dd>@{{ $user->username ?? '-' }}</dd>
                            </div>
                            <div class="info-field">
                                <dt>Email</dt>
                                <dd>{{ $user->email }}</dd>
                            </div>
                            <div class="info-field">
                                <dt>Bergabung Sejak</dt>
                                <dd>{{ $user->created_at->isoFormat('D MMMM YYYY') }}</dd>
                            </div>
                            @if($user->last_login ?? null)
                            <div class="info-field">
                                <dt>Login Terakhir</dt>
                                <dd>{{ $user->last_login->isoFormat('D MMM YYYY, HH:mm') }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                {{-- Enrolled Courses --}}
                @if(($user->enrolledCourses ?? collect())->count() > 0)
                <div class="info-card rv rv-d2">
                    <div class="info-card-head">
                        <h2>Kursus Saya</h2>
                        <a href="{{ route('course.index') }}" class="btn-edit">Lihat Semua</a>
                    </div>
                    <div class="info-card-body">
                        <div class="enrolled-courses-grid">
                            @foreach($user->enrolledCourses as $course)
                            <a href="{{ route('course.show', $course->slug) }}" class="enrolled-course">
                                @if($course->thumbnail ?? null)
                                    <img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" class="enrolled-course-thumb" onerror="this.style.background='var(--border)'">
                                @else
                                    <div class="enrolled-course-thumb" style="display:flex;align-items:center;justify-content:center;background:var(--blue-xl);">
                                        <svg style="width:18px;height:18px;color:var(--blue);opacity:.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </div>
                                @endif
                                <div style="flex:1;min-width:0;">
                                    <div class="enrolled-course-title">{{ Str::limit($course->title, 50) }}</div>
                                    <div class="enrolled-course-instructor">{{ is_string($course->instructor) ? $course->instructor : ($course->instructor->name ?? '') }}</div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </main>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const obs = new IntersectionObserver(e => { e.forEach(x => { if(x.isIntersecting){ x.target.classList.add('in'); obs.unobserve(x.target); }}); }, { threshold:0.07 });
document.querySelectorAll('.rv').forEach(el => obs.observe(el));
</script>
@endpush