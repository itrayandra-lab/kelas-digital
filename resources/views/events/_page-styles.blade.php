{{-- Shared by the event page and its sign-up page. --}}
@push('styles')
<style>
.evd { padding-block:2rem 4rem; }
.evd-crumb { font-size:.78rem; color:var(--muted); margin-bottom:1.25rem; }
.evd-crumb a { color:var(--muted); text-decoration:none; }
.evd-crumb a:hover { color:var(--blue); }
.evd-tags { display:flex; flex-wrap:wrap; gap:.45rem; margin-bottom:.8rem; }
.evd-tags span { font-size:.72rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; padding:.3rem .7rem; border-radius:999px; background:var(--blue-xl); color:var(--blue-d); }
.evd-tags .free { background:#dcfce7; color:#15803d; }
.evd-tags .online { background:#efeafe; color:#5b3cc4; }
.evd-card { background:#fff; border:1px solid var(--border); border-radius:18px; padding:1.25rem 1.35rem; }
.evd-ic { width:42px; height:42px; flex-shrink:0; border-radius:13px; display:flex; align-items:center; justify-content:center; font-size:1rem; }
.evd-ic.c1 { background:#e3f0fb; color:#1474bc; } .evd-ic.c2 { background:#fff1db; color:#b45f06; } .evd-ic.c3 { background:#dff6ec; color:#0f8a5f; }
.evd-h2 { font-family:'Sora',sans-serif; font-size:1.2rem; font-weight:700; margin:0; }
.evd-note { font-size:.86rem; color:var(--muted); margin:.3rem 0 0; }

.evf-btn { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; width:100%; min-height:52px; margin-top:1.2rem; padding:.75rem 1.25rem; border:0; border-radius:14px; background:var(--blue); color:#fff; font:inherit; font-size:1rem; font-weight:700; cursor:pointer; text-decoration:none; transition:background .15s; }
.evf-btn:hover { background:var(--blue-d); color:#fff; }
.evf-btn.ghost { background:#fff; color:var(--ink); border:1px solid var(--border); margin-top:.6rem; }
.evf-btn.ghost:hover { background:var(--surf); color:var(--ink); }
.evf-btn.violet { background:#6d4ed8; } .evf-btn.violet:hover { background:#5b3cc4; }
.evf-btn:disabled { background:#cbd5e1; cursor:not-allowed; }
.evf-btn:focus-visible, .evd a:focus-visible { outline:2px solid var(--blue); outline-offset:2px; }
.evf-alert { border-radius:12px; padding:.75rem 1rem; font-size:.88rem; margin-top:1rem; background:#fef2f2; border:1px solid #fecaca; color:#b91c1c; }
.evf-alert.warn { background:#fffbeb; border-color:#fcd34d; color:#92400e; }
.evf-alert.ok { background:#ecfdf5; border-color:#a7f3d0; color:#047857; }

/* Session list with remaining seats */
.evs { display:grid; gap:.6rem; margin-top:1rem; }
.evs-row { display:flex; align-items:center; gap:.9rem; padding:.8rem .95rem; border:1px solid var(--border); border-radius:14px; background:#fff; }
.evs-time { font-family:'Sora',sans-serif; font-weight:700; font-size:1rem; min-width:3.6rem; }
.evs-info { flex:1; min-width:0; }
.evs-info b { display:block; font-size:.86rem; }
.evs-bar { height:6px; background:#e4eaf2; border-radius:999px; overflow:hidden; margin-top:.4rem; }
.evs-bar div { height:100%; background:#10b981; border-radius:999px; }
.evs-bar.low div { background:#f59e0b; } .evs-bar.full div { background:#ef4444; }
.evs-left { font-size:.78rem; font-weight:700; white-space:nowrap; color:#0f8a5f; }
.evs-left.low { color:#b45309; } .evs-left.full { color:#b91c1c; }
</style>
@endpush
