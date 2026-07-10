@extends('layouts.app')
@section('title', config('app.name') . ' — Platform Belajar Online Terpercaya')

@push('styles')
<style>
/* ═══════════════════════════════════════════
   RAY ACADEMY — HOME PAGE (Hi-Tech Redesign)
   Palette: #0056D2 blue · #0F1114 dark · #F5F5F4 cream
   ═══════════════════════════════════════════ */

/* Scroll Reveal */
.rv{opacity:0;transform:translateY(28px);transition:opacity .6s ease,transform .6s ease}
.rv.in{opacity:1;transform:translateY(0)}
.rv-d1{transition-delay:.08s}.rv-d2{transition-delay:.16s}.rv-d3{transition-delay:.24s}.rv-d4{transition-delay:.32s}

/* Wrapper */
.wrap{max-width:1280px;margin:0 auto;padding:0 1.5rem}
@media(max-width:640px){.wrap{padding:0 1rem}}

/* Shared section tag */
.sec-tag{
    display:inline-flex;align-items:center;gap:.4rem;
    font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
    color:#0056D2;background:#EEF4FF;border:1px solid rgba(0,86,210,.15);
    padding:.3rem .85rem;border-radius:999px;margin-bottom:.85rem;
}
.sec-tag-white{
    display:inline-flex;align-items:center;gap:.4rem;
    font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
    color:rgba(255,255,255,.85);background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);
    padding:.3rem .85rem;border-radius:999px;margin-bottom:.85rem;
}

/* ═══ 1. HERO ═══ */
.hero-ra{
    background:#F5F5F4;
    position:relative;
    overflow:hidden;
    min-height:600px;
    padding:4rem 0 2rem;
}
/* subtle grid lines */
.hero-ra::before{
    content:'';position:absolute;inset:0;
    background-image:
        linear-gradient(rgba(0,86,210,.04) 1px,transparent 1px),
        linear-gradient(90deg,rgba(0,86,210,.04) 1px,transparent 1px);
    background-size:48px 48px;
    pointer-events:none;
}
/* inner: left is normal flow, right is relative for photo */
.hero-ra-inner{
    max-width:1280px;margin:0 auto;padding:0 1.5rem;
    display:block;
    position:relative;z-index:1;
    min-height:520px;
}
.hero-ra-left{
    padding-bottom:3.5rem;
    padding-top:1.5rem;
    display:flex;
    flex-direction:column;
    justify-content:center;
    max-width:52%;
}


.hero-ra-badge{
    display:inline-flex;align-items:center;gap:.5rem;
    background:#fff;border:1px solid rgba(0,86,210,.18);
    border-radius:999px;padding:.35rem 1rem .35rem .5rem;
    margin-bottom:1.25rem;box-shadow:0 2px 12px rgba(0,86,210,.08);
}
.hero-ra-badge-dot{width:8px;height:8px;border-radius:50%;background:#22c55e;animation:pulse-g 2s infinite;}
@keyframes pulse-g{0%,100%{box-shadow:0 0 0 0 rgba(34,197,94,.4)}50%{box-shadow:0 0 0 6px rgba(34,197,94,0)}}
.hero-ra-badge span{font-size:.72rem;font-weight:600;color:#0F1114;}

.hero-ra-left h1{
    font-family:'Sora',sans-serif;
    font-size:clamp(2rem,4.5vw,3.2rem);
    font-weight:600;color:#0F1114;
    line-height:1.15;letter-spacing:-.03em;
    margin-bottom:1rem;
}
.hero-ra-left h1 strong{
    color:#0056D2;font-weight:600;
    background:linear-gradient(135deg,#0056D2,#3b82f6);
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
}
.hero-ra-left p{font-size:.95rem;color:#4B5563;line-height:1.7;margin-bottom:2rem;max-width:460px;}
.hero-btns-row{display:flex;gap:.85rem;flex-wrap:wrap;align-items:center;}

.btn-primary-ra{
    display:inline-flex;align-items:center;gap:.5rem;
    background:#0056D2;color:#fff;font-family:'Sora',sans-serif;
    font-weight:600;font-size:.875rem;padding:.8rem 1.75rem;
    border-radius:8px;text-decoration:none;border:none;
    transition:all .22s;box-shadow:0 4px 16px rgba(0,86,210,.35);
}
.btn-primary-ra:hover{background:#0048B0;transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,86,210,.4);color:#fff;}
.btn-ghost-ra{
    display:inline-flex;align-items:center;gap:.5rem;
    background:transparent;color:#0F1114;font-weight:600;font-size:.875rem;
    padding:.8rem 1.5rem;border-radius:8px;text-decoration:none;
    border:1.5px solid rgba(15,17,20,.15);transition:all .22s;
}
.btn-ghost-ra:hover{border-color:#0056D2;color:#0056D2;background:#EEF4FF;}

/* hero stats row */
.hero-stats-row{
    display:flex;gap:2rem;margin-top:2rem;flex-wrap:wrap;
    padding-top:2rem;border-top:1px solid rgba(15,17,20,.08);
}
.hero-stat-item{}
.hero-stat-num{font-family:'Sora',sans-serif;font-size:1.5rem;font-weight:700;color:#0F1114;line-height:1;}
.hero-stat-label{font-size:.72rem;color:#6B7280;margin-top:.2rem;}

/* hero right image */
.hero-ra-right{
    position:absolute;
    right:0;
    bottom:0;
    width:46%;
    display:flex;
    align-items:flex-end;
    justify-content:flex-end;
    z-index:2;
}
.hero-ra-img-wrap{
    position:relative;
    width:100%;
    display:flex;
    align-items:flex-end;
    justify-content:flex-end;
}
/* soft blob — warna nyambung ke #F5F5F4 hero bg */
.hero-ra-img-wrap::before{
    content:'';
    position:absolute;
    bottom:70px;
    right:16px;
    width:88%;
    height:80%;
    background:linear-gradient(145deg,#e8f0fb 0%,#d4e5f9 40%,#eaf4ff 100%);
    border-radius:36px;
    z-index:0;
    box-shadow:0 12px 56px rgba(0,86,210,.13),inset 0 1px 0 rgba(255,255,255,.7);
}
/* fade tepi foto nyatu ke background #F5F5F4 */
.hero-ra-img-wrap::after{
    content:'';
    position:absolute;
    inset:0;
    border-radius:28px;
    background:
        linear-gradient(to right,#F5F5F4 0%,transparent 18%),
        linear-gradient(to top,#F5F5F4 0%,transparent 14%);
    z-index:3;
    pointer-events:none;
}
.hero-ra-right img{
    display:block;
    width:100%;
    height:auto;
    object-fit:contain;
    object-position:bottom center;
    transform:translateY(-80px);
    border-radius:28px;
    position:relative;
    z-index:2;
    filter:drop-shadow(0 8px 32px rgba(0,86,210,.14)) drop-shadow(0 2px 8px rgba(0,0,0,.07));
}

/* ═══ 2. PROMO CARDS ═══ */
.promo-section{background:#fff;padding:2rem 0;position:relative;z-index:2;}
.promo-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;}
.promo-card{
    border-radius:20px;overflow:hidden;display:flex;align-items:stretch;
    min-height:160px;text-decoration:none;
    transition:transform .25s,box-shadow .25s;position:relative;
}
.promo-card:hover{transform:translateY(-4px);box-shadow:0 16px 40px rgba(0,0,0,.12);}
.promo-card-content{
    flex:1;padding:1.5rem;
    display:flex;flex-direction:column;justify-content:space-between;
    z-index:1;min-width:0;
}
.promo-logo-txt{font-family:'Sora',sans-serif;font-size:.62rem;font-weight:800;letter-spacing:.1em;color:#0F1114;margin-bottom:.6rem;}
.promo-title{font-family:'Sora',sans-serif;font-size:.88rem;font-weight:600;color:#0F1114;line-height:1.4;flex:1;}
.promo-link-txt{
    display:inline-flex;align-items:center;gap:.3rem;
    font-size:.75rem;font-weight:600;color:#0F1114;
    margin-top:.8rem;text-decoration:none;
}
.promo-link-txt:hover{opacity:.7;}
.promo-card-img{width:180px;flex-shrink:0;object-fit:cover;object-position:center;}

/* ═══ 3. FREE COURSE BANNER — FIXED ═══ */
.free-banner{background:#fff;padding:3.5rem 0;border-top:1px solid #F1F5F9;}
.free-banner-inner{
    display:grid;grid-template-columns:1fr 1fr;gap:4rem;
    align-items:center;
}
.free-banner-text h2{
    font-family:'Sora',sans-serif;
    font-size:clamp(1.6rem,3.2vw,2.2rem);
    font-weight:600;color:#0F1114;
    line-height:1.3;margin-bottom:1.75rem;
}
.btn-outline-blue{
    display:inline-flex;align-items:center;gap:.4rem;
    padding:.75rem 1.5rem;border:1.5px solid #0056D2;border-radius:8px;
    font-weight:600;font-size:.875rem;color:#0056D2;text-decoration:none;
    transition:all .2s;background:#fff;
}
.btn-outline-blue:hover{background:#0056D2;color:#fff;}

/* IMAGE FIXED — no crop */
.free-banner-img-wrap{
    position:relative;
    display:flex;align-items:center;justify-content:center;
    min-height:280px;
}
.free-banner-img-wrap img{
    width:100%;height:auto;
    max-height:360px;
    object-fit:contain;object-position:center;
    display:block;
    position:relative;z-index:1;
}
/* decorative background for image */
.free-banner-img-wrap::before{
    content:'';position:absolute;
    right:0;top:10%;bottom:10%;width:75%;
    background:linear-gradient(135deg,#EEF4FF 0%,#DBEAFE 100%);
    border-radius:20px;
}

/* ═══ 4. FEATURED COURSES ═══ */
.featured-section{background:#F8FAFF;padding:3rem 0;}
.featured-wrap{
    background:linear-gradient(89deg,#0056D2 0%,#1a6bdb 35%,#5B8BD8 75%,#7CA4E4 100%);
    border-radius:20px;padding:1.75rem;display:flex;gap:2rem;align-items:stretch;
    position:relative;overflow:hidden;
}
.featured-wrap::before{
    content:'';position:absolute;right:-40px;top:-40px;
    width:250px;height:250px;border-radius:50%;
    background:rgba(255,255,255,.05);pointer-events:none;
}
.featured-label{flex-shrink:0;width:200px;display:flex;flex-direction:column;justify-content:center;position:relative;z-index:1;}
.featured-label h3{font-family:'Sora',sans-serif;font-size:1.1rem;font-weight:600;color:#fff;margin-bottom:1.1rem;line-height:1.35;}
.btn-white-sm{
    display:inline-flex;align-items:center;gap:.35rem;
    background:#fff;color:#0056D2;font-weight:600;font-size:.78rem;
    padding:.5rem 1rem;border-radius:8px;text-decoration:none;
    transition:opacity .2s;width:fit-content;
}
.btn-white-sm:hover{opacity:.88;color:#0056D2;}
.featured-scroll{
    flex:1;display:flex;gap:.75rem;overflow-x:auto;
    padding-bottom:.5rem;scroll-snap-type:x mandatory;
    -webkit-overflow-scrolling:touch;position:relative;z-index:1;
}
.featured-scroll::-webkit-scrollbar{height:3px;}
.featured-scroll::-webkit-scrollbar-thumb{background:rgba(255,255,255,.35);border-radius:2px;}
.feat-card{
    flex:0 0 195px;background:#fff;border:1px solid #E2E8F0;
    border-radius:10px;overflow:hidden;scroll-snap-align:start;
    text-decoration:none;color:inherit;
    transition:transform .2s,box-shadow .2s;display:flex;flex-direction:column;
}
.feat-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.15);}
.feat-card-thumb{aspect-ratio:16/9;overflow:hidden;background:#dbeafe;position:relative;}
.feat-card-thumb img{width:100%;height:100%;object-fit:cover;transition:transform .3s;}
.feat-card:hover .feat-card-thumb img{transform:scale(1.05);}
.feat-card-body{padding:.75rem;flex:1;display:flex;flex-direction:column;}
.feat-card-cat{font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#0056D2;margin-bottom:.25rem;}
.feat-card-title{font-family:'Sora',sans-serif;font-size:.75rem;font-weight:600;color:#0F1114;line-height:1.35;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.feat-card-meta{font-size:.63rem;color:#6B7280;margin-top:.3rem;}
.feat-card-badge{font-size:.6rem;font-weight:700;color:#16a34a;margin-top:.25rem;}

/* ═══ 5. SEARCH ═══ */
.search-section{background:#fff;padding:3rem 0;text-align:center;border-top:1px solid #F1F5F9;}
.search-section h2{font-family:'Sora',sans-serif;font-size:clamp(1.4rem,3vw,2rem);font-weight:600;color:#0F1114;margin-bottom:1.5rem;}
.search-box-ra{
    display:flex;align-items:center;background:#fff;
    border:1.5px solid #CBD5E1;border-radius:10px;overflow:hidden;
    max-width:680px;margin:0 auto;
    box-shadow:0 4px 16px rgba(0,0,0,.06);
    transition:border-color .2s,box-shadow .2s;
}
.search-box-ra:focus-within{border-color:#0056D2;box-shadow:0 4px 16px rgba(0,86,210,.15);}
.search-box-ra input{flex:1;border:none;outline:none;padding:.9rem 1rem;font-family:'DM Sans',sans-serif;font-size:.9rem;color:#0F1114;background:transparent;}
.search-box-ra input::placeholder{color:#94A3B8;}
.search-box-ra button{background:#0056D2;border:none;padding:.9rem 1.1rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;}
.search-box-ra button:hover{background:#0048B0;}
.search-box-ra button svg{width:18px;height:18px;color:#fff;}
.chip-row{display:flex;gap:.5rem;flex-wrap:wrap;justify-content:center;margin-top:1rem;align-items:center;}
.chip-label{font-size:.72rem;font-weight:600;color:#374151;}
.search-chip{
    display:inline-flex;align-items:center;
    background:#F1F5FF;color:#0048B0;font-size:.73rem;font-weight:500;
    padding:.3rem .9rem;border-radius:999px;text-decoration:none;
    border:1px solid rgba(0,86,210,.12);transition:all .15s;
}
.search-chip:hover{background:#0056D2;color:#fff;border-color:#0056D2;}

/* ═══ 6. INSTRUCTORS (Belajar dari Ahli) ═══ */
.ins-section{
    padding:4rem 0;background:#0F1114;
    position:relative;overflow:hidden;
}
.ins-section::before{
    content:'';position:absolute;inset:0;
    background:radial-gradient(ellipse 60% 70% at 85% 50%,rgba(0,86,210,.18),transparent 65%);
    pointer-events:none;
}
.ins-header{text-align:center;margin-bottom:3rem;}
.ins-header h2{font-family:'Sora',sans-serif;font-size:clamp(1.8rem,3.5vw,2.5rem);font-weight:700;color:#fff;letter-spacing:-.02em;margin-top:.5rem;}
.ins-header p{color:rgba(255,255,255,.5);margin-top:.6rem;font-size:.9rem;max-width:400px;margin-left:auto;margin-right:auto;line-height:1.7;}

/* Staggered grid */
.ins-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:1.1rem;align-items:end;}
.ins-row2{grid-column:1/-1;display:flex;justify-content:center;margin-top:.5rem;}

.ins-card{
    border-radius:18px;overflow:hidden;position:relative;
    transition:transform .3s,box-shadow .3s;cursor:default;
    display:flex;flex-direction:column;
}
.ins-card:hover{transform:translateY(-8px);box-shadow:0 24px 56px rgba(0,0,0,.4);}
/* stagger heights */
.ins-card:nth-child(1){margin-top:2.5rem;}
.ins-card:nth-child(2){margin-top:0;}
.ins-card:nth-child(3){margin-top:2rem;}
.ins-card:nth-child(4){margin-top:0;}

.ins-card-inner{padding:1.4rem 1.4rem 0;position:relative;z-index:1;flex:1;}
.ins-card-logo{height:38px;object-fit:contain;margin-bottom:.8rem;display:block;}
.ins-card-name{font-family:'Sora',sans-serif;font-size:.88rem;font-weight:700;color:#fff;line-height:1.3;}
.ins-card-tag{font-size:.72rem;color:rgba(255,255,255,.5);margin-top:.2rem;}
.ins-card-btn{
    display:inline-block;margin-top:.9rem;padding:.45rem 1.1rem;
    background:rgba(255,255,255,.92);border-radius:999px;
    font-weight:700;font-size:.72rem;text-decoration:none;
    transition:opacity .2s,transform .15s;border:none;
}
.ins-card-btn:hover{opacity:.85;transform:translateX(3px);}
.ins-card-photo{display:block;height:185px;width:100%;object-fit:contain;object-position:bottom;margin-top:1rem;}
.ins-card-solo{width:255px;}
.ins-card-solo .ins-card-photo{height:185px;}

/* ═══ 7. ARTICLES — FIXED kategori lainnya ═══ */
.articles-section{background:#fff;padding:3rem 0;}
.articles-section h2{font-family:'Sora',sans-serif;font-size:1.05rem;font-weight:700;color:#0F1114;margin-bottom:1.25rem;}
.art-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;}
.art-col{background:#EEF4FF;border-radius:12px;padding:1.1rem;display:flex;flex-direction:column;}
.art-col-head{
    display:flex;align-items:center;gap:.35rem;
    font-family:'Sora',sans-serif;font-size:.82rem;font-weight:700;
    color:#0F1114;margin-bottom:1rem;text-decoration:none;
}
.art-col-head:hover{color:#0056D2;}
.art-col-head svg{width:13px;height:13px;flex-shrink:0;}

.art-item{
    background:#fff;border-radius:10px;padding:.65rem;
    display:flex;gap:.65rem;align-items:flex-start;
    margin-bottom:.6rem;text-decoration:none;color:inherit;
    transition:box-shadow .2s,transform .2s;
}
.art-item:last-child{margin-bottom:0;}
.art-item:hover{box-shadow:0 6px 20px rgba(0,0,0,.08);transform:translateX(3px);}
.art-thumb{width:54px;height:54px;border-radius:8px;overflow:hidden;flex-shrink:0;background:#dbeafe;}
.art-thumb img{width:100%;height:100%;object-fit:cover;}
.art-body{flex:1;min-width:0;}
.art-cat{font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#0056D2;}
.art-title{font-family:'Sora',sans-serif;font-size:.73rem;font-weight:600;color:#0F1114;line-height:1.35;margin-top:.15rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.art-date{font-size:.61rem;color:#9CA3AF;margin-top:.2rem;}

/* Placeholder saat artikel kosong */
.art-placeholder{
    background:#fff;border-radius:10px;padding:1.5rem;
    text-align:center;flex:1;display:flex;flex-direction:column;
    align-items:center;justify-content:center;gap:.75rem;
    border:1.5px dashed rgba(0,86,210,.2);
}
.art-placeholder-icon{
    width:40px;height:40px;border-radius:10px;
    background:#EEF4FF;display:flex;align-items:center;justify-content:center;
}
.art-placeholder p{font-size:.75rem;color:#9CA3AF;line-height:1.5;margin:0;}
.art-placeholder a{font-size:.72rem;font-weight:600;color:#0056D2;text-decoration:none;}
.art-placeholder a:hover{text-decoration:underline;}

/* ═══ 8. TESTIMONIALS ═══ */
.testi-section{background:#F8FAFF;padding:3rem 0;}
.testi-section h2{font-family:'Sora',sans-serif;font-size:clamp(1.4rem,3vw,1.75rem);font-weight:700;color:#0F1114;margin-bottom:1.5rem;}
.testi-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;}
.testi-card{
    background:#fff;border:1px solid #E2E8F0;border-radius:12px;
    padding:1.25rem;display:flex;flex-direction:column;gap:.75rem;
    transition:border-color .2s,box-shadow .2s;
}
.testi-card:hover{border-color:#BFDBFE;box-shadow:0 8px 24px rgba(0,86,210,.08);}
.testi-stars{display:flex;gap:2px;}
.testi-stars svg{width:14px;height:14px;color:#FBBF24;fill:#FBBF24;}
.testi-user{display:flex;align-items:center;gap:.75rem;}
.testi-avatar{
    width:44px;height:44px;border-radius:50%;flex-shrink:0;
    background:linear-gradient(135deg,#DBEAFE,#93C5FD);
    display:flex;align-items:center;justify-content:center;
    font-family:'Sora',sans-serif;font-weight:700;font-size:1rem;color:#0056D2;
}
.testi-name{font-family:'Sora',sans-serif;font-size:.85rem;font-weight:600;color:#0F1114;}
.testi-text{font-size:.8rem;color:#6B7280;line-height:1.65;flex:1;}

/* ═══ 9. FAQ ═══ */
.faq-section{background:#fff;padding:3rem 0;border-top:1px solid #F1F5F9;}
.faq-section h2{font-family:'Sora',sans-serif;font-size:clamp(1.4rem,3vw,1.75rem);font-weight:700;color:#0F1114;margin-bottom:1.5rem;}
.faq-box{border:1.5px solid #E2E8F0;border-radius:12px;overflow:hidden;}
.faq-item{border-bottom:1px solid #F1F5F9;}
.faq-item:last-child{border-bottom:none;}
.faq-q{
    display:flex;align-items:center;justify-content:space-between;
    padding:1rem 1.25rem;cursor:pointer;user-select:none;gap:.75rem;
    transition:background .15s;
}
.faq-q:hover{background:#F8FAFF;}
.faq-q h3{font-family:'DM Sans',sans-serif;font-size:.86rem;font-weight:600;color:#0F1114;margin:0;flex:1;}
.faq-chevron{
    width:24px;height:24px;border-radius:50%;background:#F1F5F9;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
    transition:transform .3s,background .2s;
}
.faq-item.open .faq-chevron{transform:rotate(180deg);background:#0056D2;}
.faq-chevron svg{width:13px;height:13px;color:#6B7280;}
.faq-item.open .faq-chevron svg{color:#fff;}
.faq-ans{max-height:0;overflow:hidden;transition:max-height .3s ease;}
.faq-item.open .faq-ans{max-height:400px;}
.faq-ans p{padding:.25rem 1.25rem 1.1rem;font-size:.82rem;color:#6B7280;line-height:1.7;margin:0;}

/* ═══ 10. PARTNERS ═══ */
.partners-section{background:#F8FAFF;padding:2.5rem 0;}
.partners-section h2{font-family:'Sora',sans-serif;font-size:clamp(1.1rem,2.5vw,1.4rem);font-weight:600;color:#0F1114;margin-bottom:1.5rem;}
.partners-logos{display:flex;align-items:center;gap:2rem;flex-wrap:wrap;}
.partners-logos img{height:56px;object-fit:contain;filter:grayscale(20%);opacity:.85;transition:all .2s;}
.partners-logos img:hover{filter:grayscale(0);opacity:1;}

/* ═══ 11. CTA BOTTOM ═══ */
.cta-section{background:#EEF4FF;padding:3.5rem 0;}
.cta-inner{display:flex;flex-direction:column;align-items:center;text-align:center;gap:1rem;}
.cta-logo-img{height:28px;object-fit:contain;}
.cta-inner p{font-size:.9rem;color:#374151;max-width:400px;margin:0;}
.cta-subtext{font-size:.75rem;color:#9CA3AF;margin:0!important;}

/* ═══ RESPONSIVE ═══ */
@media(max-width:900px){
    .testi-grid{grid-template-columns:repeat(2,1fr);}
    .art-grid{grid-template-columns:repeat(2,1fr);}
    .ins-grid{grid-template-columns:repeat(2,1fr);}
    .ins-card:nth-child(1),.ins-card:nth-child(2),.ins-card:nth-child(3),.ins-card:nth-child(4){margin-top:0;}
    .ins-row2{justify-content:flex-start;}
    .ins-card-solo{width:100%;}
}
@media(max-width:768px){
    .hero-ra-inner{grid-template-columns:1fr;min-height:auto;}
    .hero-ra-right{display:none;}
    .hero-ra-left{padding-bottom:3rem;max-width:100%;}
    .free-banner-inner{grid-template-columns:1fr;}
    .free-banner-img-wrap{min-height:220px;}
    .free-banner-img-wrap::before{width:100%;top:0;bottom:0;right:0;}
    .featured-wrap{flex-direction:column;}
    .featured-label{width:100%;}
    .promo-card-img{width:130px;}
}
@media(max-width:640px){
    html,body{overflow-x:hidden!important;max-width:100vw!important;}
    .promo-grid{grid-template-columns:1fr;}
    .promo-card-img{width:120px;}
    .testi-grid{grid-template-columns:1fr;}
    .art-grid{grid-template-columns:1fr;}
    .ins-grid{grid-template-columns:1fr;}
    .ins-row2{justify-content:center;}
    .feat-card{flex:0 0 175px;}
    .hero-stats-row{gap:1.25rem;}
    .hero-stat-num{font-size:1.25rem;}
}
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════
     1. HERO
═══════════════════════════════ --}}
<section class="hero-ra">
    <div class="hero-ra-inner">
        <div class="hero-ra-left rv" id="hero-left">
            <h1>Wujudkan karier impian Anda bersama <strong>{{ config('app.name', 'Ray Academy') }}</strong></h1>
            <p>Tingkatkan kompetensi dan Kuasai keterampilan yang dibutuhkan industri dengan dukungan praktisi dan ahli berpengalaman. Belajar fleksibel, kapan saja, di mana saja.</p>
            <div class="hero-btns-row">
                <a href="{{ route('course.index') }}" class="btn-primary-ra">
                    Mulai Gratis
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('course.index') }}" class="btn-ghost-ra">Jelajahi Kelas</a>
            </div>
            <div class="hero-stats-row">
                <div class="hero-stat-item">
                    <div class="hero-stat-num">{{ number_format($stats['total_students']) }}+</div>
                    <div class="hero-stat-label">Pelajar Aktif</div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-num">{{ number_format($stats['total_courses']) }}+</div>
                    <div class="hero-stat-label">Kursus Tersedia</div>
                </div>
                <div class="hero-stat-item">
                    <div class="hero-stat-num">Lifetime</div>
                    <div class="hero-stat-label">Akses Materi</div>
                </div>
            </div>
        </div>
        <div class="hero-ra-right" id="hero-right">
            <!-- decorative circles (like Figma) -->
            <svg style="position:absolute;bottom:0;right:0;width:55%;opacity:.12;z-index:0;pointer-events:none;" viewBox="0 0 320 320" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="200" cy="200" r="190" stroke="#0056D2" stroke-width="1.5"/>
                <circle cx="200" cy="200" r="145" stroke="#0056D2" stroke-width="1.5"/>
                <circle cx="200" cy="200" r="100" stroke="#0056D2" stroke-width="1.5"/>
                <circle cx="200" cy="200" r="55"  stroke="#0056D2" stroke-width="1.5"/>
            </svg>
            <div class="hero-ra-img-wrap">
                <img src="{{ asset('img/hero.png') }}" alt="Belajar di Ray Academy"
                     onerror="this.style.display='none'">
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     2. PROMO CARDS
═══════════════════════════════ --}}
<section class="promo-section">
    <div class="wrap">
        <div class="promo-grid">
            <a href="{{ route('course.index') }}" class="promo-card" style="background:#CCDDF6;">
                <div class="promo-card-content">
                    <div>
                        <div class="promo-logo-txt">RAYACADEMY</div>
                        <div class="promo-title">Buka akses ke 10.000+ kursus dengan berlangganan</div>
                    </div>
                    <span class="promo-link-txt">Berkembang bersama kami <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>
                <img src="{{ asset('img/content-course1.jpg') }}" alt="" class="promo-card-img" onerror="this.style.display='none'">
            </a>
            <a href="{{ route('course.index') }}" class="promo-card" style="background:#CEFFCC;">
                <div class="promo-card-content">
                    <div>
                        <div class="promo-logo-txt">RAYACADEMY</div>
                        <div class="promo-title">Memajukan bisnis Anda dan memberdayakan tim Anda</div>
                    </div>
                    <span class="promo-link-txt">Coba Rayacademy untuk Bisnis <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>
                <img src="{{ asset('img/content-course2.jpg') }}" alt="" class="promo-card-img" onerror="this.style.display='none'">
            </a>
            <a href="{{ route('course.index') }}" class="promo-card" style="background:#FFCDCD;">
                <div class="promo-card-content">
                    <div>
                        <div class="promo-logo-txt">RAYACADEMY</div>
                        <div class="promo-title">Buka akses ke 10.000+ kursus dengan berlangganan</div>
                    </div>
                    <span class="promo-link-txt">Berkembang bersama kami <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>
                <img src="{{ asset('img/content-course3.jpg') }}" alt="" class="promo-card-img" onerror="this.style.display='none'">
            </a>
            <a href="{{ route('course.index') }}" class="promo-card" style="background:#FFF9CC;">
                <div class="promo-card-content">
                    <div>
                        <div class="promo-logo-txt">RAYACADEMY</div>
                        <div class="promo-title">Memajukan bisnis Anda dan memberdayakan tim Anda</div>
                    </div>
                    <span class="promo-link-txt">Coba Rayacademy untuk Bisnis <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                </div>
                <img src="{{ asset('img/content-course4.jpg') }}" alt="" class="promo-card-img" onerror="this.style.display='none'">
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     3. FREE COURSE BANNER — FIXED
═══════════════════════════════ --}}
<section class="free-banner">
    <div class="wrap">
        <div class="free-banner-inner rv">
            <div class="free-banner-text">
                <h2>Bingung harus mulai dari mana? Mulailah dengan kursus gratis di Ray Academy hari ini. Akses materi berkualitas dan kembangkan keterampilan yang membuka lebih banyak peluang karier.</h2>
                <a href="{{ route('course.index') }}" class="btn-outline-blue">Mulai Kursus Gratis</a>
            </div>
            <div class="free-banner-img-wrap">
                <img src="{{ asset('img/content-ray.png') }}" alt="Mulai Belajar Gratis"
                     onerror="this.style.opacity='.3'">
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     4. FEATURED COURSES
═══════════════════════════════ --}}
<section class="featured-section">
    <div class="wrap">
        <div class="featured-wrap rv">
            <div class="featured-label">
                <h3>Rilis baru yang hangat</h3>
                <a href="{{ route('course.index') }}" class="btn-white-sm">
                    Jelajahi kursus
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="13" height="13"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="featured-scroll">
                @forelse($featuredCourses->take(7) as $course)
                <a href="{{ route('course.show', $course->slug) }}" class="feat-card">
                    <div class="feat-card-thumb">
                        @if($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}">
                        @else
                            <div style="width:100%;height:100%;background:linear-gradient(135deg,#DBEAFE,#EFF6FF);display:flex;align-items:center;justify-content:center;"><i class="fas fa-play-circle" style="font-size:1.5rem;color:#0056D2;opacity:.35;"></i></div>
                        @endif
                    </div>
                    <div class="feat-card-body">
                        @if($course->category)<div class="feat-card-cat">{{ $course->category->name }}</div>@endif
                        <div class="feat-card-title">{{ $course->title }}</div>
                        @if($course->instructor)<div class="feat-card-meta">{{ $course->instructor }}</div>@endif
                        <div class="feat-card-badge">{{ (!$course->price || $course->price == 0) ? 'Gratis' : 'Spesialisasi' }}</div>
                    </div>
                </a>
                @empty
                @for($i=0;$i<4;$i++)
                <div class="feat-card" style="pointer-events:none;">
                    <div class="feat-card-thumb" style="background:#E2E8F0;"></div>
                    <div class="feat-card-body"><div style="height:8px;background:#E2E8F0;border-radius:3px;width:45%;margin-bottom:.5rem;"></div><div style="height:10px;background:#E2E8F0;border-radius:3px;"></div></div>
                </div>
                @endfor
                @endforelse
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     5. SEARCH
═══════════════════════════════ --}}
<section class="search-section">
    <div class="wrap rv">
        <h2>Cari 10,000+ program pembelajaran</h2>
        <form action="{{ route('search') }}" method="GET">
            <div class="search-box-ra">
                <svg style="width:18px;height:18px;color:#94A3B8;margin-left:.85rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                <input type="search" name="q" placeholder="misalnya Pembelajaran Mesin" autocomplete="off">
                <button type="submit" aria-label="Cari">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/></svg>
                </button>
            </div>
        </form>
        <div class="chip-row">
            <span class="chip-label">Populer:</span>
            @if($courseCategories->isNotEmpty())
                @foreach($courseCategories->take(6) as $cat)
                <a href="{{ route('course.index') }}?category={{ $cat->slug }}" class="search-chip">{{ $cat->name }}</a>
                @endforeach
            @else
                <a href="{{ route('course.index') }}" class="search-chip">Bisnis</a>
                <a href="{{ route('course.index') }}" class="search-chip">Teknologi</a>
                <a href="{{ route('course.index') }}" class="search-chip">Kosmetik</a>
                <a href="{{ route('course.index') }}" class="search-chip">Kesehatan</a>
                <a href="{{ route('course.index') }}" class="search-chip">AI</a>
            @endif
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     6. INSTRUCTORS (Belajar dari Ahli)
═══════════════════════════════ --}}
<section class="ins-section" id="instruktur">
    <div class="wrap" style="position:relative;z-index:1;">
        <div class="ins-header rv">
            <span class="sec-tag-white">
                <i class="fas fa-chalkboard-teacher" style="font-size:.65rem;"></i>
                Pengajar dari Praktisi dan Ahli Industri
            </span>
            <h2>Belajar Langsung dari Para Ahli</h2>
            <p>Tingkatkan wawasan, pengalaman, dan kompetensi Anda bersama para profesional berpengalaman di bidangnya.</p>
        </div>

        <div class="ins-grid">
            @foreach([
                ['bg'=>'linear-gradient(135deg,#ff5733,#c0392b)','logo'=>'assets/logo-do better class.png','photo'=>'assets/s-ria.png','name'=>'Ria R. Christiana SE, MBA.','tag'=>'Business & Branding','link'=>route('course.index'),'c'=>'#c0392b'],
                ['bg'=>'linear-gradient(135deg,#7c3aed,#5b21b6)','logo'=>'assets/logo-psikologi bisnis.png','photo'=>'assets/s-sukmayanti.png','name'=>'Sukmayanti Ranadireksa, M.Psi.','tag'=>'Psikologi & Komunikasi','link'=>route('course.index'),'c'=>'#7c3aed'],
                ['bg'=>'linear-gradient(135deg,#db2777,#9d174d)','logo'=>'assets/logo-ski.png','photo'=>'assets/s-cahya.png','name'=>'Apt. Cahya Khairani K., M.Farm','tag'=>'Kosmetik & Kecantikan','link'=>route('course.index'),'c'=>'#be185d'],
                ['bg'=>'linear-gradient(135deg,#1d4ed8,#1e3a8a)','logo'=>'assets/logo-amaizing.png','photo'=>'assets/s-wendra.png','name'=>'Wendra Wilendra M.MT.','tag'=>'Teknologi & AI','link'=>route('course.index'),'c'=>'#1d4ed8'],
            ] as $idx => $ins)
            <div class="ins-card rv rv-d{{ min($idx+1,4) }}" style="background:{{ $ins['bg'] }};">
                <div class="ins-card-inner">
                    <img src="{{ asset($ins['logo']) }}" alt="{{ $ins['name'] }}" class="ins-card-logo" onerror="this.style.display='none'">
                    <h3 class="ins-card-name">{{ $ins['name'] }}</h3>
                    <p class="ins-card-tag">{{ $ins['tag'] }}</p>
                    <a href="{{ $ins['link'] }}" class="ins-card-btn" style="color:{{ $ins['c'] }};">
                        Mulai Belajar <i class="fas fa-arrow-right" style="font-size:.6rem;"></i>
                    </a>
                </div>
                <img src="{{ asset($ins['photo']) }}" alt="{{ $ins['name'] }}" class="ins-card-photo" onerror="this.style.display='none'">
            </div>
            @endforeach

            {{-- Row 2: 1 card centered --}}
            <div class="ins-row2">
                <div class="ins-card ins-card-solo rv" style="background:linear-gradient(135deg,#0891b2,#0e7490);">
                    <div class="ins-card-inner">
                        <img src="{{ asset('assets/logo-sobat-anak.png') }}" alt="dr. Frecillia Regina, Sp.A" class="ins-card-logo" onerror="this.style.display='none'">
                        <h3 class="ins-card-name">dr. Frecillia Regina, Sp.A</h3>
                        <p class="ins-card-tag">Kesehatan Anak</p>
                        <a href="{{ route('course.index') }}" class="ins-card-btn" style="color:#0891b2;">
                            Mulai Belajar <i class="fas fa-arrow-right" style="font-size:.6rem;"></i>
                        </a>
                    </div>
                    <img src="{{ asset('assets/s-fricil-1.png') }}" alt="dr. Frecillia Regina, Sp.A" class="ins-card-photo" onerror="this.style.display='none'">
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     7. ARTIKEL — FIXED kosong
═══════════════════════════════ --}}
<section class="articles-section">
    <div class="wrap">
        <h2>Artikel</h2>
        <div class="art-grid">
            @php
                /* Ambil semua artikel, bagi ke 3 kolom */
                $allArts  = $latestArticles ?? collect();
                $perCol   = max(1, (int) ceil($allArts->count() / 3));
                $chunks   = $allArts->chunk($perCol);

                $colDefs = [
                    ['label'=>'Tentang Kosmetik', 'route'=> route('article.index')],
                    ['label'=>'Tentang Bisnis',   'route'=> route('article.index')],
                    ['label'=>'Kategori Lainnya', 'route'=> route('article.index')],
                ];

                /* Konten fallback untuk kolom kosong */
                $fallbacks = [
                    ['title'=>'Tips Memilih Bahan Kosmetik yang Aman','cat'=>'KOSMETIK','date'=>'Apr'],
                    ['title'=>'Strategi Membangun Bisnis Digital di 2025','cat'=>'BISNIS','date'=>'Apr'],
                    ['title'=>'Cara Belajar Efektif dengan Metode Spaced Repetition','cat'=>'TIPS BELAJAR','date'=>'Apr'],
                ];
            @endphp

            @foreach($colDefs as $ci => $col)
            @php $colArts = $chunks->get($ci, collect()); @endphp
            <div class="art-col rv rv-d{{ $ci + 1 }}">
                <a href="{{ $col['route'] }}" class="art-col-head">
                    {{ $col['label'] }}
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>

                @if($colArts->isNotEmpty())
                    {{-- Real articles --}}
                    @foreach($colArts->take(3) as $art)
                    <a href="{{ route('article.show', $art->slug) }}" class="art-item">
                        <div class="art-thumb">
                            @if($art->thumbnail ?? $art->cover_image ?? null)
                                <img src="{{ asset('storage/' . ($art->thumbnail ?? $art->cover_image)) }}" alt="{{ $art->title }}">
                            @else
                                <div style="width:100%;height:100%;background:linear-gradient(135deg,#DBEAFE,#EFF6FF);display:flex;align-items:center;justify-content:center;"><i class="fas fa-file-alt" style="font-size:.9rem;color:#0056D2;opacity:.35;"></i></div>
                            @endif
                        </div>
                        <div class="art-body">
                            @if($art->categories->first())<div class="art-cat">{{ $art->categories->first()->name }}</div>@endif
                            <div class="art-title">{{ $art->title }}</div>
                            <div class="art-date">@if($art->published_at){{ $art->published_at->isoFormat('D MMM') }}@endif</div>
                        </div>
                    </a>
                    @endforeach
                @else
                    {{-- Placeholder — jangan kosong --}}
                    <div class="art-placeholder">
                        <div class="art-placeholder-icon">
                            <i class="fas fa-newspaper" style="font-size:1rem;color:#0056D2;opacity:.6;"></i>
                        </div>
                        <p>Konten artikel untuk kategori ini segera hadir. Yuk, jelajahi artikel lainnya!</p>
                        <a href="{{ route('article.index') }}">Lihat semua artikel &rarr;</a>
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     8. TESTIMONIALS
═══════════════════════════════ --}}
<section class="testi-section">
    <div class="wrap">
        <h2>Apa kata mereka</h2>
        <div class="testi-grid">
            @php
            $testis=[
                ['i'=>'A','n'=>'Abigail P.','t'=>'Saya memiliki pekerjaan penuh waktu dan 3 orang anak. Ray Academy memberi fleksibilitas yang saya butuhkan. Langganannya memotivasi saya untuk terus belajar.'],
                ['i'=>'S','n'=>'Shi Jie F.','t'=>'Platform yang sangat membantu! Dengan setiap kursus saya mendapat nilai lebih. Saya bisa mengakses hampir semua konten dengan satu berlangganan.'],
                ['i'=>'I','n'=>'Ines K.','t'=>'Saya sangat menghargai fleksibilitas yang ditawarkan. Bisa mencoba kursus apa saja tanpa biaya tambahan memotivasi saya belajar lebih banyak!'],
            ];
            @endphp
            @foreach($testis as $t)
            <div class="testi-card rv rv-d{{ $loop->index+1 }}">
                <div class="testi-stars">
                    @for($s=0;$s<5;$s++)<svg viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>@endfor
                </div>
                <p class="testi-text">"{{ $t['t'] }}"</p>
                <div class="testi-user">
                    <div class="testi-avatar">{{ $t['i'] }}</div>
                    <div class="testi-name">{{ $t['n'] }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     9. FAQ
═══════════════════════════════ --}}
<section class="faq-section">
    <div class="wrap">
        <h2>Pertanyaan yang sering diajukan</h2>
        <div class="faq-box rv">
            @php
            $faqs=[
                ['q'=>'Apakah Ray Academy menyediakan sertifikat resmi?','a'=>'Ya! Setiap kursus yang diselesaikan mendapatkan sertifikat resmi terverifikasi yang dapat dicantumkan di CV atau portofolio LinkedIn Anda.'],
                ['q'=>'Apa saja yang termasuk dalam paket berlangganan?','a'=>'Paket berlangganan mencakup akses tak terbatas ke semua kursus, video HD, forum diskusi, dan sertifikat resmi untuk setiap kursus yang diselesaikan.'],
                ['q'=>'Apakah saya akan menghemat dengan berlangganan?','a'=>'Sangat! Dengan berlangganan Anda mendapat akses ke semua kursus dengan harga jauh lebih hemat dibanding membeli satu per satu.'],
                ['q'=>'Berapa lama akses kursus yang saya beli?','a'=>'Anda mendapat akses LIFETIME untuk semua kursus yang dibeli. Tidak ada batasan waktu — belajar kapan saja sesuai kecepatan Anda sendiri.'],
                ['q'=>'Bisakah saya belajar dari smartphone?','a'=>'Tentu saja! Platform Ray Academy 100% mobile-friendly. Belajar dari smartphone, tablet, atau laptop dengan pengalaman optimal di semua perangkat.'],
                ['q'=>'Apakah ada garansi uang kembali?','a'=>'Ya, kami memberikan garansi uang kembali 30 hari untuk kursus premium jika Anda tidak puas dengan konten yang didapatkan.'],
            ];
            @endphp
            @foreach($faqs as $faq)
            <div class="faq-item" onclick="toggleFAQ(this)">
                <div class="faq-q">
                    <h3>{{ $faq['q'] }}</h3>
                    <div class="faq-chevron"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg></div>
                </div>
                <div class="faq-ans"><p>{{ $faq['a'] }}</p></div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     10. PARTNERS
═══════════════════════════════ --}}
<section class="partners-section">
    <div class="wrap rv">
        <h2>Bekerjasama dengan Universitas dan Lembaga Pelatihan</h2>
        <div class="partners-logos">
            <img src="{{ asset('img/logo-unpad.png') }}" alt="Universitas Padjadjaran" onerror="this.style.display='none'">
            <img src="{{ asset('img/logo-farmasi-unpad.jpg') }}" alt="Farmasi Unpad" onerror="this.style.display='none'">
            <img src="{{ asset('img/logo-labcos.png') }}" alt="Labcos" onerror="this.style.display='none'">
        </div>
    </div>
</section>

{{-- ═══════════════════════════════
     11. CTA BOTTOM
═══════════════════════════════ --}}
<section class="cta-section">
    <div class="wrap">
        <div class="cta-inner rv">
            <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" class="cta-logo-img" onerror="this.style.display='none'">
            <p>Wujudkan karier impian Anda bersama Ray Academy</p>
            @guest
            <a href="{{ route('register') }}" class="btn-primary-ra">Mulai 7 hari Masa Percobaan Gratis</a>
            <p class="cta-subtext">Daftar gratis &middot; Batalkan kapan saja</p>
            @else
            <a href="{{ route('dashboard') }}" class="btn-primary-ra">Dashboard Saya</a>
            @endguest
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    /* Scroll reveal */
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => { if(e.isIntersecting){e.target.classList.add('in');obs.unobserve(e.target);} });
    }, { threshold:0.06, rootMargin:'0px 0px -40px 0px' });
    document.querySelectorAll('.rv').forEach(el => obs.observe(el));

    /* Trigger hero immediately */
    setTimeout(() => {
        document.getElementById('hero-left')?.classList.add('in');
        setTimeout(() => document.getElementById('hero-right')?.classList.add('in'), 150);
    }, 50);

    /* FAQ */
    window.toggleFAQ = function(item) {
        const isOpen = item.classList.contains('open');
        document.querySelectorAll('.faq-item').forEach(f => f.classList.remove('open'));
        if(!isOpen) item.classList.add('open');
    };
});
</script>
@endpush