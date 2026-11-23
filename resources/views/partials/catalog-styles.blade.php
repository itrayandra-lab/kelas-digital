{{-- Catalog layout shared by the course and article listings, in the homepage palette (navy #0b1f4d, blue #1f6fd1). The page header is <x-page-hero>. --}}
@once
@push('styles')
<style>
.ct-wrap { max-width:1280px; margin:0 auto; padding:0 1.5rem; }
.ct-body { background:#f6f9fe; padding:1.5rem 0 4rem; }
.ct-layout { display:grid; grid-template-columns:240px 1fr; gap:2rem; align-items:start; }
.ct-side { position:sticky; top:88px; }
.ct-box { border:1px solid #e3ecf8; border-radius:18px; box-shadow:0 8px 24px rgba(31,77,140,.06); overflow:hidden; background:#fff; margin-bottom:1rem; }
.ct-box-head { padding:.875rem 1rem; border-bottom:1px solid #F1F5F9; font-family:'Sora',sans-serif; font-size:.82rem; font-weight:700; color:#0b1f4d; display:flex; justify-content:space-between; align-items:center; }
.ct-box-head a { font-size:.72rem; font-weight:600; color:#1f6fd1; text-decoration:none; }
.ct-box-body { padding:.6rem .75rem; display:flex; flex-direction:column; gap:.2rem; }
.ct-chip { display:flex; align-items:center; gap:.5rem; padding:.45rem .6rem; border-radius:8px; font-size:.8rem; font-weight:500; color:#374151; text-decoration:none; transition:background .15s; }
.ct-chip i { width:16px; text-align:center; font-size:.75rem; color:#94A3B8; }
.ct-chip:hover { background:#F1F5FF; color:#1f6fd1; }
.ct-chip.on { background:#EEF4FF; color:#1f6fd1; font-weight:600; }
.ct-chip.on i { color:#1f6fd1; }
.ct-chip em { margin-left:auto; font-style:normal; font-size:.7rem; color:#9CA3AF; }

.ct-head { display:flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap; margin-bottom:1rem; padding-bottom:.75rem; border-bottom:1px solid #F1F5F9; }
.ct-head h2 { font-family:'Sora',sans-serif; font-size:.95rem; font-weight:700; color:#0b1f4d; }
.ct-head-right { display:flex; align-items:center; gap:.5rem; }
.ct-sort { border:1.5px solid #E2E8F0; background:#fff; border-radius:8px; padding:.45rem .7rem; font-size:.8rem; color:#0b1f4d; }
.ct-filter-btn { display:none; border:1.5px solid #E2E8F0; background:#fff; border-radius:8px; padding:.45rem .8rem; font-size:.8rem; font-weight:600; color:#0b1f4d; }
.ct-tags { display:flex; flex-wrap:wrap; gap:.5rem; margin-bottom:1rem; }
.ct-tag { display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .75rem; background:#EEF4FF; color:#1f6fd1; font-size:.73rem; font-weight:600; border-radius:999px; border:1px solid rgba(31,111,209,.2); text-decoration:none; }
.ct-tag:hover { color:#c00; }
/* Category gate on the course catalog */
.cg-all { font-size:.85rem; font-weight:600; color:#1f6fd1; text-decoration:none; }
.cg-all:hover { text-decoration:underline; }
.cg-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(min(100%, 260px), 1fr)); gap:1rem; }
.cg-card { display:flex; flex-direction:column; gap:.5rem; padding:1.4rem; background:#fff; border:1px solid #e3ecf8; border-radius:20px; box-shadow:0 12px 30px rgba(31,77,140,.08); text-decoration:none; color:#0b1f4d; transition:border-color .2s, box-shadow .2s, transform .2s; }
.cg-card:hover { border-color:#1f6fd1; box-shadow:0 10px 28px rgba(31,111,209,.1); transform:translateY(-2px); }
.cg-card:focus-visible { outline:3px solid rgba(31,111,209,.35); outline-offset:2px; }
.cg-icon { width:48px; height:48px; border-radius:12px; display:grid; place-items:center; background:#EEF4FF; color:#1f6fd1; font-size:1.2rem; margin-bottom:.35rem; }
.cg-name { font-family:'Sora',sans-serif; font-weight:700; font-size:1.05rem; }
.cg-desc { font-size:.85rem; color:#6B7280; line-height:1.55; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
.cg-meta { margin-top:auto; padding-top:.6rem; display:flex; justify-content:space-between; align-items:center; gap:.5rem; font-size:.8rem; font-weight:600; color:#1f6fd1; }
.cg-card:hover .cg-meta i { transform:translateX(3px); }
.cg-meta i { transition:transform .2s; }
@media (prefers-reduced-motion: reduce) { .cg-card, .cg-meta i { transition:none; } .cg-card:hover { transform:none; } }
.ct-empty { text-align:center; padding:3.5rem 1.5rem; }
.ct-empty i { font-size:2.5rem; color:#CBD5E1; }
.ct-empty h3 { font-family:'Sora',sans-serif; font-size:1.1rem; font-weight:700; color:#0b1f4d; margin:1rem 0 .4rem; }
.ct-empty p { font-size:.85rem; color:#6B7280; }
.ct-empty a { color:#1f6fd1; font-weight:600; text-decoration:none; }

@media (max-width: 900px) {
    .ct-layout { grid-template-columns:1fr; }
    .ct-side { position:static; display:none; }
    .ct-side.open { display:block; }
    .ct-filter-btn { display:inline-flex; gap:.4rem; align-items:center; }
}
</style>
@endpush
@endonce
