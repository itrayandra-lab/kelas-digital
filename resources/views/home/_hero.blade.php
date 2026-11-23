{{-- Homepage hero: one slide per RayAcademy program. Content and colors come from config/homepage.php. --}}
@php $firstImage = 'img/hero/hero-'.(config('homepage.hero.0.image') ?? 1); @endphp
@push('styles')
{{-- The first slide is the largest thing on screen; fetch it before anything else, in the size the screen needs. --}}
<link rel="preload" as="image" imagesrcset="{{ asset($firstImage.'-m.webp') }} 1000w, {{ asset($firstImage.'.webp') }} 1920w" imagesizes="100vw" fetchpriority="high">
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600&display=swap" rel="stylesheet">
<style>
.rh { position:relative; background:#071c3f; }
.rh .splide__track { overflow:hidden; }
.rh .splide__slide > .rh-slide { height:100%; }
.rh-slide { position:relative; display:flex; align-items:center; min-height:clamp(560px, 50vw, 740px); overflow:hidden; background:linear-gradient(135deg,#061634 0%,#0b2f66 60%,#123f86 100%); }
.rh-slide.light { background:linear-gradient(135deg,#f6f9fd 0%,#e7eef8 100%); }
.rh-bg { position:absolute; inset:0; }
.rh-bg img { width:100%; height:100%; object-fit:cover; object-position:center; display:block; }
.rh .splide__spinner { display:none; }
/* Text laid over a blank panel inside a photo. The box is cropped exactly like the picture (cover, centred),
   so the panel text stays on the panel at any slide size. Wide screens only: on phones the panel is too small. */
.rh-photo-box { display:none; }
@media (min-width: 900px) {
    .rh-bg { container-type:size; }
    .rh-photo-box { display:block; position:absolute; pointer-events:none; container-type:inline-size;
        width:max(100cqw, 177.78cqh); aspect-ratio:16 / 9;
        left:calc((100cqw - max(100cqw, 177.78cqh)) / 2); top:calc((100cqh - max(100cqw, 177.78cqh) * .5625) / 2); }
    .rh-panel { position:absolute; display:flex; flex-direction:column; justify-content:center; gap:.35cqw; padding-left:.8cqw; color:#fff; font-family:'DM Sans',sans-serif; }
    .rh-panel b { font-size:.95cqw; font-weight:700; letter-spacing:.16em; text-transform:uppercase; color:#f5b544; }
    .rh-panel span { font-size:1.28cqw; font-weight:600; line-height:1.3; }
    .rh-panel i { font-style:normal; color:#38c8f5; margin:0 .35em; }
}
/* Keeps the text side readable whatever is in the photo. */
.rh-shade { position:absolute; inset:0; pointer-events:none; }
.rh-slide.dark.left .rh-shade { background:linear-gradient(90deg,rgba(5,20,48,.86) 0%,rgba(5,20,48,.62) 38%,rgba(5,20,48,0) 62%); }
.rh-slide.dark.right .rh-shade { background:linear-gradient(270deg,rgba(5,20,48,.86) 0%,rgba(5,20,48,.62) 38%,rgba(5,20,48,0) 62%); }
.rh-slide.light.left .rh-shade { background:linear-gradient(90deg,rgba(246,249,253,.94) 0%,rgba(246,249,253,.75) 36%,rgba(246,249,253,0) 58%); }
.rh-inner { position:relative; z-index:2; width:100%; max-width:1280px; margin:0 auto; padding:3.5rem 2rem 5.5rem; display:flex; }
.rh-slide.right .rh-inner { justify-content:flex-end; }
.rh-text { max-width:690px; color:#fff; }
.rh-slide.light .rh-text { color:#14294f; }
.rh-bar { display:block; width:58px; height:6px; border-radius:999px; background:var(--bar); margin-bottom:1.25rem; }
.rh-label { font-family:'DM Sans',sans-serif; font-weight:600; font-size:clamp(.72rem,1.1vw,.9rem); letter-spacing:.2em; text-transform:none; margin:0 0 1rem; opacity:.95; }
.rh-slide.light .rh-label { color:#1d3461; }
.rh-title { font-family:'Cinzel',Georgia,serif; font-weight:600; font-size:clamp(1.65rem,2.75vw,2.9rem); line-height:1.08; letter-spacing:.01em; margin:0; }
.rh-title span { display:block; }
.rh-title span + span { color:var(--accent); }
.rh-slide.slant .rh-title span + span { font-style:italic; }
.rh-slide.light .rh-title { color:#0f2a5c; }
.rh-desc { font-family:'DM Sans',sans-serif; font-size:clamp(.92rem,1.15vw,1.05rem); line-height:1.65; margin:1.25rem 0 0; max-width:540px; opacity:.94; }
.rh-slide.light .rh-desc { color:#24375a; opacity:1; }

.rh-cta { display:inline-flex; align-items:center; gap:.7rem; margin-top:1.8rem; min-height:54px; padding:.85rem 1.9rem; border-radius:999px; font-family:'DM Sans',sans-serif; font-weight:700; font-size:clamp(.95rem,1.2vw,1.1rem); text-decoration:none; transition:transform .2s, box-shadow .2s, filter .2s; }
.rh-cta:hover { transform:translateY(-2px); filter:brightness(1.05); }
.rh-cta:focus-visible { outline:3px solid #fff; outline-offset:3px; }
.rh-cta.white { background:linear-gradient(180deg,#ffffff,#dfe9fb); color:#0f2a5c; box-shadow:0 8px 28px rgba(56,200,245,.35); }
.rh-cta.navy { background:#0f2a5c; color:#fff; border-radius:12px; box-shadow:0 10px 26px rgba(15,42,92,.28); }
.rh-cta.rose { background:linear-gradient(180deg,#f8d3c7,#eaa797); color:#0f2a5c; box-shadow:0 8px 26px rgba(234,167,151,.4); }
.rh-cta.orange { background:linear-gradient(180deg,#fb923c,#ea580c); color:#fff; box-shadow:0 8px 26px rgba(234,88,12,.45); }
.rh-cta.green { background:linear-gradient(180deg,#7fa04a,#4f6e2a); color:#fff; box-shadow:0 8px 26px rgba(79,110,42,.45); }
.rh-cta.navy:focus-visible, .rh-slide.light .rh-cta:focus-visible { outline-color:#0f2a5c; }

/* "A smarter way to learn": floating cards over the photo and the four-step strip */
.rh-cards { position:absolute; inset:0; z-index:2; pointer-events:none; max-width:1280px; margin:0 auto; }
.rh-card { position:absolute; display:flex; align-items:center; gap:.6rem; padding:.7rem 1rem; border-radius:16px; background:rgba(255,255,255,.62); -webkit-backdrop-filter:blur(12px); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,.85); box-shadow:0 14px 36px rgba(15,42,92,.16); font-family:'DM Sans',sans-serif; font-weight:700; font-size:.95rem; color:#14294f; animation:rh-bob 6s ease-in-out infinite; }
.rh-card i { width:34px; height:34px; border-radius:10px; background:#fff3e6; color:#c98a4b; display:flex; align-items:center; justify-content:center; }
.rh-card:nth-child(1) { left:51%; top:22%; }
.rh-card:nth-child(2) { left:46%; top:46%; animation-delay:-3s; }
@keyframes rh-bob { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-8px); } }
.rh-steps { display:grid; grid-template-columns:repeat(4,1fr); margin-top:1.6rem; max-width:460px; border-radius:18px; background:rgba(255,255,255,.62); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,.9); box-shadow:0 10px 30px rgba(15,42,92,.08); }
.rh-steps div { display:flex; flex-direction:column; align-items:center; gap:.45rem; padding:.9rem .4rem; font-family:'DM Sans',sans-serif; font-size:.78rem; font-weight:700; letter-spacing:.04em; color:#14294f; }
.rh-steps div + div { border-left:1px solid rgba(20,41,79,.12); }
.rh-steps i { font-size:1.35rem; color:#c98a4b; }

/* "From knowledge to expertise": learning journey card */
.rh-journey { margin-top:1.6rem; max-width:520px; padding:1rem 1.2rem; border-radius:18px; background:rgba(255,255,255,.7); -webkit-backdrop-filter:blur(10px); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,.95); box-shadow:0 12px 30px rgba(15,42,92,.1); }
.rh-journey small { display:block; font-family:'DM Sans',sans-serif; font-weight:700; font-size:.72rem; letter-spacing:.18em; color:#c98a4b; margin-bottom:.7rem; }
.rh-journey ol { list-style:none; margin:0; padding:0; display:flex; align-items:center; flex-wrap:wrap; gap:.4rem; }
.rh-journey li { display:flex; align-items:center; gap:.4rem; font-family:'DM Sans',sans-serif; font-weight:700; font-size:.9rem; color:#14294f; }
.rh-journey li b { width:24px; height:24px; border-radius:50%; background:#0f2a5c; color:#fff; font-size:.72rem; display:flex; align-items:center; justify-content:center; }
.rh-journey li + li::before { content:'\2192'; color:#4a86b8; font-weight:700; margin-right:.15rem; }

/* Slider controls */
.rh .splide__pagination { bottom:1.4rem; gap:.4rem; z-index:3; }
.rh .splide__pagination__page { width:10px; height:10px; margin:0; background:rgba(255,255,255,.6); box-shadow:0 0 0 1px rgba(15,42,92,.35); opacity:1; transition:width .25s, background .25s; }
.rh .splide__pagination__page.is-active { width:30px; border-radius:999px; background:#fff; transform:none; }
.rh .splide__arrow { width:40px; height:40px; background:rgba(255,255,255,.18); -webkit-backdrop-filter:blur(8px); backdrop-filter:blur(8px); border:1px solid rgba(255,255,255,.35); opacity:1; z-index:3; }
.rh .splide__arrow svg { fill:#fff; width:1.1em; height:1.1em; }
.rh .splide__arrow:hover:not(:disabled) { background:rgba(255,255,255,.32); }
/* Arrows sit beside the dots at the bottom, so they never cover the text. */
.rh .splide__arrow { top:auto; bottom:calc(1.4rem - 15px); transform:none; }
.rh .splide__arrow--prev { left:auto; right:calc(50% + 82px); } .rh .splide__arrow--next { left:calc(50% + 82px); right:auto; }
.rh .splide__arrow:focus-visible, .rh .splide__pagination__page:focus-visible { outline:3px solid #38c8f5; outline-offset:2px; }

@media (prefers-reduced-motion: reduce) { .rh-card { animation:none; } .rh-cta { transition:none; } }

/* Phones and small tablets: photo on top, text below on a plain background. */
@media (max-width: 899px) {
    .rh-slide { flex-direction:column; align-items:stretch; min-height:0; height:100%; }
    .rh-bg { position:relative; inset:auto; aspect-ratio:16/10; }
    .rh-bg img { object-position:var(--focus, center); }
    .rh-shade, .rh-cards, .rh .splide__arrow { display:none; }
    .rh-inner { padding:1.6rem 1.25rem 3.6rem; }
    .rh-slide.right .rh-inner { justify-content:flex-start; }
    .rh-text { max-width:none; }
    .rh-bar { width:44px; height:5px; margin-bottom:.9rem; }
    .rh-cta { width:100%; justify-content:center; text-align:center; }
    .rh-steps { max-width:none; }
    /* Blur is costly on phones and adds nothing on the plain background used here. */
    .rh-steps, .rh-journey { -webkit-backdrop-filter:none; backdrop-filter:none; background:#fff; }
    .rh .splide__pagination { bottom:1.1rem; }
}
</style>
@endpush

<section class="rh splide" id="hero-slider" aria-label="Program RayAcademy">
    <div class="splide__track">
        <ul class="splide__list">
            @foreach(config('homepage.hero') as $i => $slide)
                @php
                    $img = $slide['image'] && is_file(public_path("img/hero/hero-{$slide['image']}.webp")) ? "img/hero/hero-{$slide['image']}" : null;
                    $classes = implode(' ', [$slide['theme'], $slide['align'], $slide['italic'] ? 'slant' : '']);
                @endphp
                <li class="splide__slide">
                    <div class="rh-slide {{ $classes }}" style="--accent:{{ $slide['accent'] }};--bar:{{ $slide['bar'] }};--focus:{{ $slide['focus'] ?? 'center' }};">
                        @if($img)
                            {{-- hero-N-m.webp (1000px) for phones, hero-N.webp (1920px) for wide screens; the browser picks.
                                 Only the first slide loads up front; the slider fetches the others just before they show. --}}
                            @php $srcset = asset($img.'-m.webp').' 1000w, '.asset($img.'.webp').' 1920w'; @endphp
                            <div class="rh-bg">
                                @if($i === 0)
                                    <img src="{{ asset($img.'-m.webp') }}" srcset="{{ $srcset }}" sizes="100vw" alt="" width="1920" height="1080" fetchpriority="high">
                                @else
                                    <img data-splide-lazy="{{ asset($img.'-m.webp') }}" data-splide-lazy-srcset="{{ $srcset }}" sizes="100vw" alt="" width="1920" height="1080">
                                @endif
                                @if(! empty($slide['panel']))
                                    @php [$px, $py, $pw, $ph] = $slide['panel']['box']; @endphp
                                    <div class="rh-photo-box" aria-hidden="true">
                                        <div class="rh-panel" style="left:{{ $px }}%;top:{{ $py }}%;width:{{ $pw }}%;height:{{ $ph }}%;">
                                            <b>{{ $slide['panel']['title'] }}</b>
                                            <span>{!! collect($slide['panel']['items'])->map(fn ($item) => e($item))->implode('<i>·</i>') !!}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="rh-shade"></div>
                        @endif

                        @if(! empty($slide['cards']))
                            <div class="rh-cards" aria-hidden="true">
                                @foreach($slide['cards'] as $card)
                                    <div class="rh-card"><i class="fas {{ $card['icon'] }}"></i>{{ $card['label'] }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div class="rh-inner">
                            <div class="rh-text">
                                <span class="rh-bar" aria-hidden="true"></span>
                                <p class="rh-label">{{ $slide['label'] }}</p>
                                <{{ $i === 0 ? 'h1' : 'h2' }} class="rh-title">
                                    @foreach($slide['lines'] as $line)<span>{{ $line }}</span>@endforeach
                                </{{ $i === 0 ? 'h1' : 'h2' }}>
                                <p class="rh-desc">{{ $slide['text'] }}</p>
                                <a href="{{ str_starts_with($slide['href'], '#') ? $slide['href'] : url($slide['href']) }}" class="rh-cta {{ $slide['button'] }}">
                                    {{ $slide['cta'] }}@if(! empty($slide['arrow']))<i class="fas fa-arrow-right" aria-hidden="true"></i>@endif
                                </a>

                                @if(! empty($slide['steps']))
                                    <div class="rh-steps">
                                        @foreach($slide['steps'] as $step)
                                            <div><i class="fas {{ $step['icon'] }}" aria-hidden="true"></i>{{ $step['label'] }}</div>
                                        @endforeach
                                    </div>
                                @endif

                                @if(! empty($slide['journey']))
                                    <div class="rh-journey">
                                        <small>YOUR LEARNING JOURNEY</small>
                                        <ol>
                                            @foreach($slide['journey'] as $level)<li><b>{{ $loop->iteration }}</b>{{ $level }}</li>@endforeach
                                        </ol>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</section>
