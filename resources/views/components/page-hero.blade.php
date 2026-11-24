{{-- Page header in the homepage look (light background, pill, Cinzel title) for the course, event, article and search pages.
     Slots: crumbs, title (wrap the highlighted words in <span>), the default slot is the lead text, `after` sits below it (search form). --}}
@props(['pill' => null])
@once
@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&display=swap" rel="stylesheet">
<style>
.ph{position:relative;overflow:hidden;background:#eef5fd;padding:clamp(2.25rem,5vw,3.75rem) 0 clamp(2rem,4vw,3rem);}
.ph-bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;pointer-events:none;}
.ph-in{position:relative;max-width:1280px;margin:0 auto;padding:0 1.5rem;}
.ph-crumb{font-size:.78rem;color:#5b6b8a;margin-bottom:1rem;}
.ph-crumb a{color:#5b6b8a;text-decoration:none;}
.ph-crumb a:hover{color:#1f6fd1;}
.ph-pill{display:inline-block;font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#0b1f4d;background:#dceaf9;border-radius:999px;padding:.4rem 1rem;margin-bottom:.9rem;}
.ph h1{font-family:'Cinzel',Georgia,serif;font-weight:600;text-transform:uppercase;line-height:1.1;letter-spacing:.01em;color:#0b1f4d;
    font-size:clamp(1.7rem,3.6vw,2.8rem);margin:0 0 .75rem;max-width:900px;text-wrap:balance;overflow-wrap:anywhere;}
.ph h1 span{color:#1f6fd1;}
.ph-lead{font-size:clamp(.9rem,1.2vw,1rem);line-height:1.5;color:#33415c;max-width:680px;margin:0;}
.ph-after{margin-top:1.5rem;max-width:860px;}
/* Pill search box used under the header */
.ph-search{display:flex;align-items:center;gap:.5rem;background:#fff;border:1px solid #d6e3f5;border-radius:999px;padding:.35rem .35rem .35rem 1.1rem;
    box-shadow:0 12px 30px rgba(31,77,140,.1);transition:border-color .2s,box-shadow .2s;}
.ph-search:focus-within{border-color:#1f6fd1;box-shadow:0 0 0 3px rgba(31,111,209,.15),0 12px 30px rgba(31,77,140,.1);}
.ph-search > i, .ph-search > svg{color:#7d8fb0;flex-shrink:0;width:16px;height:16px;}
.ph-search input{flex:1;min-width:0;border:0;background:transparent;outline:0;padding:.6rem .25rem;font-size:.92rem;color:#0b1f4d;}
.ph-search button{display:inline-flex;align-items:center;gap:.45rem;min-height:44px;padding:0 1.4rem;border:0;border-radius:999px;background:#0b2a63;color:#fff;
    font-family:'Sora',sans-serif;font-weight:600;font-size:.88rem;cursor:pointer;white-space:nowrap;transition:background-color .2s;}
.ph-search button:hover{background:#123a82;}
.ph-search button:focus-visible{outline:3px solid #7cb7ff;outline-offset:2px;}
@media (max-width:640px){ .ph-in{padding:0 1.25rem;} .ph-search button{padding:0 1rem;} .ph-search button .ph-btn-text{display:none;} }
</style>
@endpush
@endonce
<section class="ph">
    <img class="ph-bg" src="{{ asset('img/home/bg-light-m.webp') }}" srcset="{{ asset('img/home/bg-light-m.webp') }} 900w, {{ asset('img/home/bg-light.webp') }} 1672w" sizes="100vw" alt="" decoding="async" fetchpriority="high">
    <div class="ph-in">
        @isset($crumbs)<nav class="ph-crumb" aria-label="Breadcrumb">{{ $crumbs }}</nav>@endisset
        @if($pill)<span class="ph-pill">{{ $pill }}</span>@endif
        <h1>{{ $title }}</h1>
        @if($slot->isNotEmpty())<p class="ph-lead">{{ $slot }}</p>@endif
        @isset($after)<div class="ph-after">{{ $after }}</div>@endisset
    </div>
</section>
