{{-- ============================================================
     resources/views/profile/edit.blade.php
     ============================================================ --}}
@extends('layouts.app')
@section('title', 'Edit Profil — Ray Academy')

@push('styles')
<style>
.form-layout { max-width:760px; margin:3rem auto; padding:0 1.5rem 5rem; }
.form-back { display:inline-flex; align-items:center; gap:.5rem; font-size:.83rem; font-weight:600; color:var(--muted); text-decoration:none; margin-bottom:1.75rem; transition:color .18s; }
.form-back:hover { color:var(--blue); }
.form-back svg { width:15px; height:15px; }
.form-page-title { font-family:'Sora',sans-serif; font-size:1.65rem; font-weight:800; color:var(--ink); letter-spacing:-.02em; }
.form-page-subtitle { font-size:.9rem; color:var(--muted); margin-top:.4rem; }

.form-card { background:#fff; border:1.5px solid var(--border); border-radius:18px; overflow:hidden; margin-top:2rem; }
.form-card-head { padding:1.25rem 1.5rem; border-bottom:1px solid var(--border); }
.form-card-head h2 { font-family:'Sora',sans-serif; font-size:.95rem; font-weight:800; color:var(--ink); }
.form-card-body { padding:1.75rem 1.5rem; display:flex; flex-direction:column; gap:1.5rem; }
.form-card-foot { padding:1.1rem 1.5rem; background:var(--surf); border-top:1px solid var(--border); display:flex; justify-content:flex-end; align-items:center; gap:.75rem; }

.form-field label { display:block; font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--ink-2); margin-bottom:.55rem; }
.form-input {
    width:100%; font-family:'DM Sans',sans-serif; font-size:.9rem; color:var(--ink);
    background:#fff; border:1.5px solid var(--border); border-radius:10px;
    padding:.75rem 1rem; outline:none; transition:border-color .2s, box-shadow .2s;
    -webkit-appearance:none;
}
.form-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(20,116,188,.12); }
.form-input.error { border-color:#f87171; }
.form-hint { font-size:.75rem; color:var(--muted); margin-top:.45rem; line-height:1.55; }
.form-error { font-size:.78rem; color:#dc2626; margin-top:.4rem; display:flex; align-items:center; gap:.35rem; }
.form-error svg { width:13px; height:13px; flex-shrink:0; }

.form-alert-error { background:#fef2f2; border:1.5px solid #fecaca; color:#b91c1c; padding:.9rem 1.1rem; border-radius:12px; font-size:.83rem; margin-bottom:1.5rem; }
.form-alert-error li { margin:.2rem 0; }

.btn-cancel { display:inline-flex; align-items:center; gap:.4rem; font-family:'DM Sans',sans-serif; font-size:.875rem; font-weight:600; color:var(--ink-2); background:#fff; border:1.5px solid var(--border); padding:.65rem 1.25rem; border-radius:10px; text-decoration:none; transition:all .18s; }
.btn-cancel:hover { border-color:var(--muted); background:var(--surf); }
.btn-submit { display:inline-flex; align-items:center; gap:.4rem; font-family:'DM Sans',sans-serif; font-size:.875rem; font-weight:700; color:#fff; background:var(--blue); border:1.5px solid var(--blue); padding:.65rem 1.5rem; border-radius:10px; cursor:pointer; transition:all .18s; }
.btn-submit:hover { background:var(--blue-d); border-color:var(--blue-d); transform:translateY(-1px); box-shadow:0 6px 18px rgba(20,116,188,.3); }
.btn-submit svg { width:15px; height:15px; }

/* Security card link button */
.security-action { display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
.security-action-info h3 { font-family:'Sora',sans-serif; font-size:.9rem; font-weight:700; color:var(--ink); }
.security-action-info p { font-size:.8rem; color:var(--muted); margin-top:.2rem; }
</style>
@endpush

@section('content')

<div style="background:var(--surf); min-height:100vh;">
    <div class="form-layout">

        <a href="{{ route('profile.index') }}" class="form-back">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Profil
        </a>

        <div>
            <h1 class="form-page-title">Edit Profil</h1>
            <p class="form-page-subtitle">Perbarui informasi akun kamu</p>
        </div>

        @if($errors->any())
        <div class="form-alert-error rv" style="margin-top:1.5rem;">
            <ul style="list-style:disc;padding-left:1.1rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Profile form --}}
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf @method('PUT')
            <div class="form-card rv">
                <div class="form-card-head"><h2>Informasi Profil</h2></div>
                <div class="form-card-body">
                    <div class="form-field">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="form-input {{ $errors->has('name') ? 'error' : '' }}" autocomplete="name">
                        @error('name')
                            <div class="form-error"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                               class="form-input {{ $errors->has('username') ? 'error' : '' }}" autocomplete="username">
                        <p class="form-hint">3–20 karakter. Hanya huruf, angka, underscore, dan tanda hubung.</p>
                        @error('username')
                            <div class="form-error"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-field">
                        <label for="email">Alamat Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="form-input {{ $errors->has('email') ? 'error' : '' }}" autocomplete="email">
                        @error('email')
                            <div class="form-error"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-card-foot">
                    <a href="{{ route('profile.index') }}" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-submit">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        {{-- Security Section --}}
        <div class="form-card rv rv-d2" style="margin-top:1.5rem;">
            <div class="form-card-head"><h2>Keamanan Akun</h2></div>
            <div class="form-card-body">
                <div class="security-action">
                    <div class="security-action-info">
                        <h3>Password</h3>
                        <p>Perbarui password untuk menjaga keamanan akun kamu</p>
                    </div>
                    <a href="{{ route('profile.change-password') }}" class="btn-submit">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Ubah Password
                    </a>
                </div>
            </div>
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