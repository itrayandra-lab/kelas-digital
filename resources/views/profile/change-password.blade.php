{{-- ============================================================
     resources/views/profile/change-password.blade.php
     ============================================================ --}}
@extends('layouts.app')
@section('title', 'Ubah Password — Ray Academy')

@push('styles')
<style>
.form-layout { max-width:560px; margin:3rem auto; padding:0 1.5rem 5rem; }
.form-back { display:inline-flex; align-items:center; gap:.5rem; font-size:.83rem; font-weight:600; color:var(--muted); text-decoration:none; margin-bottom:1.75rem; transition:color .18s; }
.form-back:hover { color:var(--blue); }
.form-page-title { font-family:'Sora',sans-serif; font-size:1.65rem; font-weight:800; color:var(--ink); letter-spacing:-.02em; }
.form-page-subtitle { font-size:.9rem; color:var(--muted); margin-top:.4rem; }
.form-card { background:#fff; border:1.5px solid var(--border); border-radius:18px; overflow:hidden; margin-top:2rem; }
.form-card-head { padding:1.25rem 1.5rem; border-bottom:1px solid var(--border); }
.form-card-head h2 { font-family:'Sora',sans-serif; font-size:.95rem; font-weight:800; color:var(--ink); }
.form-card-body { padding:1.75rem 1.5rem; display:flex; flex-direction:column; gap:1.5rem; }
.form-card-foot { padding:1.1rem 1.5rem; background:var(--surf); border-top:1px solid var(--border); display:flex; justify-content:flex-end; align-items:center; gap:.75rem; }
.form-field label { display:block; font-size:.78rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--ink-2); margin-bottom:.55rem; }
.form-input { width:100%; font-family:'DM Sans',sans-serif; font-size:.9rem; color:var(--ink); background:#fff; border:1.5px solid var(--border); border-radius:10px; padding:.75rem 1rem; outline:none; transition:border-color .2s, box-shadow .2s; -webkit-appearance:none; }
.form-input:focus { border-color:var(--blue); box-shadow:0 0 0 3px rgba(20,116,188,.12); }
.form-input.error { border-color:#f87171; }
.form-hint { font-size:.75rem; color:var(--muted); margin-top:.45rem; line-height:1.55; }
.form-error { font-size:.78rem; color:#dc2626; margin-top:.4rem; display:flex; align-items:center; gap:.35rem; }
.form-error svg { width:13px; height:13px; flex-shrink:0; }
.form-alert-error { background:#fef2f2; border:1.5px solid #fecaca; color:#b91c1c; padding:.9rem 1.1rem; border-radius:12px; font-size:.83rem; margin-bottom:1.5rem; }
.btn-cancel { display:inline-flex; align-items:center; font-family:'DM Sans',sans-serif; font-size:.875rem; font-weight:600; color:var(--ink-2); background:#fff; border:1.5px solid var(--border); padding:.65rem 1.25rem; border-radius:10px; text-decoration:none; transition:all .18s; }
.btn-cancel:hover { border-color:var(--muted); background:var(--surf); }
.btn-submit { display:inline-flex; align-items:center; gap:.4rem; font-family:'DM Sans',sans-serif; font-size:.875rem; font-weight:700; color:#fff; background:var(--blue); border:1.5px solid var(--blue); padding:.65rem 1.5rem; border-radius:10px; cursor:pointer; transition:all .18s; }
.btn-submit:hover { background:var(--blue-d); border-color:var(--blue-d); transform:translateY(-1px); box-shadow:0 6px 18px rgba(20,116,188,.3); }

/* Password strength */
.password-input-wrap { position:relative; }
.password-toggle { position:absolute; right:.85rem; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--muted); padding:0; display:flex; align-items:center; }
.password-toggle:hover { color:var(--ink); }
.strength-bar { height:4px; border-radius:999px; background:var(--border); margin-top:.6rem; overflow:hidden; }
.strength-fill { height:100%; border-radius:999px; transition:width .35s, background .35s; width:0%; }

/* Tips card */
.tips-card { background:var(--blue-xl); border:1.5px solid rgba(20,116,188,.18); border-radius:14px; padding:1.25rem; margin-top:1.5rem; }
.tips-card-title { font-family:'Sora',sans-serif; font-size:.83rem; font-weight:800; color:var(--blue); margin-bottom:.75rem; display:flex; align-items:center; gap:.45rem; }
.tips-list { display:flex; flex-direction:column; gap:.45rem; }
.tips-list li { font-size:.8rem; color:var(--blue-d); display:flex; align-items:center; gap:.5rem; }
.tips-list li svg { width:13px; height:13px; flex-shrink:0; color:var(--blue); }
</style>
@endpush

@section('content')

<div style="background:var(--surf); min-height:100vh;">
    <div class="form-layout">

        <a href="{{ route('profile.index') }}" class="form-back">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Profil
        </a>

        <div>
            <h1 class="form-page-title">Ubah Password</h1>
            <p class="form-page-subtitle">Perbarui password untuk menjaga keamanan akun</p>
        </div>

        @if($errors->any())
        <div class="form-alert-error rv" style="margin-top:1.5rem;">
            <ul style="list-style:disc;padding-left:1.1rem;">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('profile.update-password') }}" method="POST">
            @csrf @method('PUT')
            <div class="form-card rv">
                <div class="form-card-head"><h2>Password Baru</h2></div>
                <div class="form-card-body">

                    {{-- Current password --}}
                    <div class="form-field">
                        <label for="current_password">Password Saat Ini</label>
                        <div class="password-input-wrap">
                            <input type="password" id="current_password" name="current_password" required
                                   class="form-input {{ $errors->has('current_password') ? 'error' : '' }}"
                                   autocomplete="current-password">
                            <button type="button" class="password-toggle" onclick="togglePassword('current_password',this)">
                                <svg id="eye-current" style="width:17px;height:17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        @error('current_password')
                            <div class="form-error"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- New password --}}
                    <div class="form-field">
                        <label for="password">Password Baru</label>
                        <div class="password-input-wrap">
                            <input type="password" id="password" name="password" required
                                   class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                                   autocomplete="new-password"
                                   oninput="checkStrength(this.value)">
                            <button type="button" class="password-toggle" onclick="togglePassword('password',this)">
                                <svg style="width:17px;height:17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        <div class="strength-bar"><div class="strength-fill" id="strength-fill"></div></div>
                        <p class="form-hint">Minimal 8 karakter.</p>
                        @error('password')
                            <div class="form-error"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirm password --}}
                    <div class="form-field">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <div class="password-input-wrap">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   class="form-input {{ $errors->has('password_confirmation') ? 'error' : '' }}"
                                   autocomplete="new-password">
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation',this)">
                                <svg style="width:17px;height:17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <div class="form-error"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-card-foot">
                    <a href="{{ route('profile.index') }}" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-submit">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Perbarui Password
                    </button>
                </div>
            </div>
        </form>

        {{-- Tips --}}
        <div class="tips-card rv rv-d2">
            <div class="tips-card-title">
                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Tips Keamanan Password
            </div>
            <ul class="tips-list">
                @foreach(['Minimal 8 karakter', 'Kombinasi huruf besar & kecil', 'Tambahkan angka & simbol', 'Jangan gunakan informasi pribadi', 'Gunakan password unik untuk akun ini'] as $tip)
                <li>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    {{ $tip }}
                </li>
                @endforeach
            </ul>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.querySelector('svg').style.opacity = isPass ? '.5' : '1';
}

function checkStrength(val) {
    const fill = document.getElementById('strength-fill');
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const colors = ['', '#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
    fill.style.width = (score * 25) + '%';
    fill.style.background = colors[score] || '';
}

const obs = new IntersectionObserver(e => { e.forEach(x => { if(x.isIntersecting){ x.target.classList.add('in'); obs.unobserve(x.target); }}); }, { threshold:0.07 });
document.querySelectorAll('.rv').forEach(el => obs.observe(el));
</script>
@endpush