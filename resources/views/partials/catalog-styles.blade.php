{{-- Catalog layout shared by the course and article listings, in the look of the home and search pages: cream hero with grid lines, #0056D2 blue, bordered filter boxes. --}}
@once
@push('styles')
<style>
.ct-wrap { max-width:1280px; margin:0 auto; padding:0 1.5rem; }
.ct-hero { background:#F5F5F4; border-bottom:1px solid #E2E8F0; padding:2rem 0 0; position:relative; overflow:hidden; }
.ct-hero::before { content:''; position:absolute; inset:0; background-image:linear-gradient(rgba(0,86,210,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(0,86,210,.03) 1px,transparent 1px); background-size:48px 48px; pointer-events:none; }
.ct-hero-inner { position:relative; z-index:1; display:grid; grid-template-columns:1fr auto; gap:2rem; align-items:end; }
.ct-hero-left { padding-bottom:1.75rem; }
.ct-crumb { font-size:.78rem; color:#6B7280; margin-bottom:.6rem; }
.ct-crumb a { color:#6B7280; text-decoration:none; }
.ct-crumb a:hover { color:#0056D2; }
.ct-hero h1 { font-family:'Sora',sans-serif; font-size:clamp(1.4rem,3vw,2rem); font-weight:700; color:#0F1114; letter-spacing:-.02em; margin-bottom:.25rem; }
.ct-hero h1 span { color:#0056D2; }
.ct-hero p { font-size:.85rem; color:#6B7280; max-width:620px; }
.ct-hero-img { width:180px; height:120px; object-fit:contain; object-position:bottom; display:block; opacity:.9; }

.ct-bar { background:#fff; border-bottom:1px solid #E2E8F0; padding:.875rem 0; box-shadow:0 1px 8px rgba(0,0,0,.06); }
.ct-search { display:flex; align-items:center; gap:.75rem; }
.ct-search-wrap { flex:1; display:flex; align-items:center; background:#F8FAFF; border:1.5px solid #CBD5E1; border-radius:10px; overflow:hidden; transition:border-color .2s, box-shadow .2s; }
.ct-search-wrap:focus-within { border-color:#0056D2; box-shadow:0 0 0 3px rgba(0,86,210,.1); }
.ct-search-wrap i { color:#94A3B8; margin:0 .75rem; }
.ct-search-wrap input { flex:1; min-width:0; border:0; background:transparent; outline:0; padding:.75rem .5rem .75rem 0; font-size:.9rem; color:#0F1114; }
.ct-search button { background:#0056D2; color:#fff; border:0; padding:.75rem 1.5rem; border-radius:8px; font-weight:600; font-size:.875rem; cursor:pointer; white-space:nowrap; }
.ct-search button:hover { background:#0048B0; }

.ct-body { background:#fff; padding:1.5rem 0 4rem; }
.ct-layout { display:grid; grid-template-columns:240px 1fr; gap:2rem; align-items:start; }
.ct-side { position:sticky; top:88px; }
.ct-box { border:1.5px solid #E2E8F0; border-radius:12px; overflow:hidden; background:#fff; margin-bottom:1rem; }
.ct-box-head { padding:.875rem 1rem; border-bottom:1px solid #F1F5F9; font-family:'Sora',sans-serif; font-size:.82rem; font-weight:700; color:#0F1114; display:flex; justify-content:space-between; align-items:center; }
.ct-box-head a { font-size:.72rem; font-weight:600; color:#0056D2; text-decoration:none; }
.ct-box-body { padding:.6rem .75rem; display:flex; flex-direction:column; gap:.2rem; }
.ct-chip { display:flex; align-items:center; gap:.5rem; padding:.45rem .6rem; border-radius:8px; font-size:.8rem; font-weight:500; color:#374151; text-decoration:none; transition:background .15s; }
.ct-chip i { width:16px; text-align:center; font-size:.75rem; color:#94A3B8; }
.ct-chip:hover { background:#F1F5FF; color:#0056D2; }
.ct-chip.on { background:#EEF4FF; color:#0056D2; font-weight:600; }
.ct-chip.on i { color:#0056D2; }
.ct-chip em { margin-left:auto; font-style:normal; font-size:.7rem; color:#9CA3AF; }

.ct-head { display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap; margin-bottom:1rem; padding-bottom:.75rem; border-bottom:1px solid #F1F5F9; }
.ct-head h2 { font-family:'Sora',sans-serif; font-size:.95rem; font-weight:700; color:#0F1114; }
.ct-head-right { display:flex; align-items:center; gap:.5rem; }
.ct-sort { border:1.5px solid #E2E8F0; background:#fff; border-radius:8px; padding:.45rem .7rem; font-size:.8rem; color:#0F1114; }
.ct-filter-btn { display:none; border:1.5px solid #E2E8F0; background:#fff; border-radius:8px; padding:.45rem .8rem; font-size:.8rem; font-weight:600; color:#0F1114; }
.ct-tags { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1rem; }
.ct-tag { display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .75rem; background:#EEF4FF; color:#0056D2; font-size:.73rem; font-weight:600; border-radius:999px; border:1px solid rgba(0,86,210,.2); text-decoration:none; }
.ct-tag:hover { color:#c00; }
/* Category gate on the course catalog */
.cg-all { font-size:.85rem; font-weight:600; color:#0056D2; text-decoration:none; }
.cg-all:hover { text-decoration:underline; }
.cg-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(min(100%, 260px), 1fr)); gap:1rem; }
.cg-card { display:flex; flex-direction:column; gap:.5rem; padding:1.4rem; background:#fff; border:1.5px solid #E2E8F0; border-radius:16px; text-decoration:none; color:#0F1114; transition:border-color .2s, box-shadow .2s, transform .2s; }
.cg-card:hover { border-color:#0056D2; box-shadow:0 10px 28px rgba(0,86,210,.1); transform:translateY(-2px); }
.cg-card:focus-visible { outline:3px solid rgba(0,86,210,.35); outline-offset:2px; }
.cg-icon { width:48px; height:48px; border-radius:12px; display:grid; place-items:center; background:#EEF4FF; color:#0056D2; font-size:1.2rem; margin-bottom:.35rem; }
.cg-name { font-family:'Sora',sans-serif; font-weight:700; font-size:1.05rem; }
.cg-desc { font-size:.85rem; color:#6B7280; line-height:1.55; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.cg-meta { margin-top:auto; padding-top:.6rem; display:flex; justify-content:space-between; align-items:center; gap:.5rem; font-size:.8rem; font-weight:600; color:#0056D2; }
.cg-card:hover .cg-meta i { transform:translateX(3px); }
.cg-meta i { transition:transform .2s; }
@media (prefers-reduced-motion: reduce) { .cg-card, .cg-meta i { transition:none; } .cg-card:hover { transform:none; } }
.ct-empty { text-align:center; padding:3.5rem 1.5rem; }
.ct-empty i { font-size:2.5rem; color:#CBD5E1; }
.ct-empty h3 { font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:700; color:#0F1114; margin:1rem 0 .4rem; }
.ct-empty p { font-size:.85rem; color:#6B7280; }
.ct-empty a { color:#0056D2; font-weight:600; text-decoration:none; }

@media (max-width: 900px) {
    .ct-layout { grid-template-columns:1fr; }
    .ct-side { position:static; display:none; }
    .ct-side.open { display:block; }
    .ct-filter-btn { display:inline-flex; gap:.4rem; align-items:center; }
}
@media (max-width: 640px) {
    .ct-hero-img { display:none; }
    .ct-search button { padding:.75rem 1rem; }
}
</style>
@endpush
@endonce
