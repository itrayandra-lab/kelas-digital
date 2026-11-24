{{-- Section layout shared by the school and experts pages. --}}
@push('styles')
<style>
.sc-sec{padding:clamp(2.5rem,5vw,4rem) 0;}
.sc-sec.alt{background:#f6f9fe;}
.sc-wrap{max-width:1280px;margin:0 auto;padding:0 1.5rem;}
@media (max-width:640px){ .sc-wrap{padding:0 1.25rem;} }
.sc-head{display:flex;align-items:flex-end;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.25rem;}
.sc-eyebrow{font-size:.78rem;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#1f6fd1;margin:0 0 .35rem;}
.sc-title{font-family:'Cinzel',Georgia,serif;font-weight:600;text-transform:uppercase;font-size:clamp(1.45rem,2.6vw,2.1rem);line-height:1.15;color:#0b1f4d;margin:0;}
.sc-title span{color:#1f6fd1;}
.sc-title.keep-case{text-transform:none;}
.sc-link{font-size:.9rem;font-weight:700;color:#0b2a63;text-decoration:none;white-space:nowrap;}
.sc-link:hover{color:#1f6fd1;}
.sc-program + .sc-program{margin-top:2.25rem;}
.sc-program h3{display:flex;align-items:center;gap:.6rem;font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:700;color:#0b1f4d;margin:0 0 .9rem;}
.sc-program h3 i{width:34px;height:34px;border-radius:10px;display:grid;place-items:center;background:#e6effb;color:#1f6fd1;font-size:.85rem;}
.sc-program h3 small{font-family:'DM Sans',sans-serif;font-size:.82rem;font-weight:600;color:#5b6b8a;}
.sc-empty{display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap;padding:1.5rem;background:#fff;border:1px dashed #c9d9f0;border-radius:20px;}
.sc-empty i{font-size:1.6rem;color:#1f6fd1;}
.sc-empty div{flex:1;min-width:220px;}
.sc-empty h3{font-family:'Sora',sans-serif;font-size:1.05rem;font-weight:700;color:#0b1f4d;margin:0 0 .3rem;}
.sc-empty p{font-size:.92rem;line-height:1.5;color:#33415c;margin:0;}
.sc-actions{display:flex;flex-wrap:wrap;gap:.75rem;}
.sc-families{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1.25rem;}
@media (max-width:900px){ .sc-families{grid-template-columns:1fr;} }
</style>
@endpush
