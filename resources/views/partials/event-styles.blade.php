{{-- Event cards and pages; used by the home page, the event list and the event page. --}}
@once
@push('styles')
<style>
.ev-wrap { max-width:1200px; margin:0 auto; padding:0 1.5rem; }
.ev-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(270px,1fr)); gap:1.5rem; }
.ev-card { display:flex; flex-direction:column; background:#fff; border:1px solid var(--border); border-radius:18px; overflow:hidden; text-decoration:none; color:var(--ink); transition:box-shadow .2s, transform .2s; }
.ev-card:hover { box-shadow:0 14px 34px rgba(10,22,40,.12); transform:translateY(-3px); }
.ev-card:focus-visible { outline:2px solid var(--blue); outline-offset:3px; }
.ev-poster { position:relative; aspect-ratio:4/5; background:linear-gradient(150deg,#0d5a96,#1474bc 60%,#10b981); overflow:hidden; }
.ev-poster img { width:100%; height:100%; object-fit:cover; display:block; }
.ev-tags { position:absolute; top:.75rem; left:.75rem; right:.75rem; display:flex; flex-wrap:wrap; gap:.4rem; }
.ev-tag { font-size:.7rem; font-weight:700; letter-spacing:.03em; padding:.28rem .65rem; border-radius:999px; background:#fff; color:var(--ink); box-shadow:0 2px 8px rgba(0,0,0,.12); }
.ev-tag.free { background:#10b981; color:#fff; }
.ev-tag.online { background:#6d4ed8; color:#fff; }
.ev-date { position:absolute; left:.75rem; bottom:.75rem; background:#fff; border-radius:12px; padding:.35rem .7rem; text-align:center; line-height:1.1; box-shadow:0 4px 14px rgba(0,0,0,.18); }
.ev-date b { display:block; font-family:'Sora',sans-serif; font-size:1.35rem; color:var(--ink); }
.ev-date span { font-size:.68rem; font-weight:700; text-transform:uppercase; color:var(--blue); letter-spacing:.06em; }
.ev-body { padding:1rem 1.1rem 1.15rem; display:flex; flex-direction:column; gap:.45rem; flex:1; }
.ev-title { font-family:'Sora',sans-serif; font-size:1.02rem; font-weight:700; line-height:1.35; margin:0; }
.ev-sub { font-size:.82rem; color:var(--muted); margin:0; }
.ev-meta { display:flex; flex-direction:column; gap:.3rem; font-size:.82rem; color:var(--ink-2); margin-top:.2rem; }
.ev-meta span { display:flex; gap:.5rem; align-items:baseline; }
.ev-meta i { width:14px; color:var(--blue); text-align:center; flex-shrink:0; }
.ev-seats { margin-top:auto; padding-top:.7rem; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; font-size:.8rem; font-weight:700; }
.ev-seats .ok { color:#0f8a5f; }
.ev-seats .low { color:#b45309; }
.ev-seats .full, .ev-seats .past { color:var(--muted); }
.ev-seats .go { color:var(--blue); }
.ev-empty { text-align:center; padding:3rem 1.5rem; border:2px dashed var(--border); border-radius:18px; color:var(--muted); }
.ev-empty i { font-size:2rem; color:var(--blue); opacity:.6; margin-bottom:.6rem; }
@media (prefers-reduced-motion: reduce) { .ev-card { transition:none; } .ev-card:hover { transform:none; } }
@media (max-width:640px) { .ev-wrap { padding:0 1rem; } }
</style>
@endpush
@endonce
