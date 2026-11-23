{{-- Knowledge & Insights: up to five published articles. A recommended article leads (else the newest),
     the newest others fill the 2×2 grid. Picked in HomeController. --}}
@push('styles')
<style>
.ki{background:#e6f0fb;}
.ki .hs-bar{background:#f07a2a;}
.ki .hs-eyebrow{color:#0b1f4d;letter-spacing:.24em;}
.ki .hs-title{font-size:clamp(1.8rem,4vw,3.1rem);}
.ki .hs-title span{display:block;color:#9b1b30;}
.ki .hs-lead{max-width:860px;margin-inline:auto;}
.ki-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,2fr);gap:clamp(1rem,2vw,1.5rem);}
.ki-grid.solo{grid-template-columns:minmax(0,520px);justify-content:center;}
.ki-more{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:clamp(1rem,2vw,1.5rem);}
.ki-card{position:relative;display:flex;flex-direction:column;overflow:hidden;border-radius:20px;background:rgba(255,255,255,.9);border:1px solid #fff;
    box-shadow:0 16px 36px rgba(31,77,140,.12);text-decoration:none;transition:transform .25s ease,box-shadow .25s ease;}
.ki-card:hover{transform:translateY(-4px);box-shadow:0 22px 44px rgba(31,77,140,.18);}
.ki-card:focus-visible{outline:3px solid #7cb7ff;outline-offset:3px;}
.ki-thumb{position:relative;aspect-ratio:16/7;overflow:hidden;background:linear-gradient(135deg,#dbe9fa,#b9d3f3);display:flex;align-items:center;justify-content:center;color:#7fa3d6;font-size:1.8rem;}
.ki-thumb img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .35s ease;}
.ki-card:hover .ki-thumb img{transform:scale(1.04);}
.ki-lead .ki-thumb{aspect-ratio:auto;flex:1;min-height:260px;}
.ki-body{display:flex;align-items:flex-end;gap:.75rem;padding:1rem 1.2rem 1.15rem;}
.ki-body div{flex:1;min-width:0;border-left:3px solid #f07a2a;padding-left:.7rem;}
.ki-cat{display:block;font-family:'DM Sans',sans-serif;font-size:.7rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#1f5fc0;margin-bottom:.2rem;}
.ki-card h3{font-family:'Sora',sans-serif;font-size:1rem;font-weight:700;line-height:1.35;color:#0b1f4d;margin:0;
    display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
.ki-lead h3{font-size:clamp(1.15rem,1.8vw,1.45rem);-webkit-line-clamp:3;}
.ki-body > i{flex-shrink:0;color:#f07a2a;font-size:1.1rem;transition:transform .2s ease;}
.ki-card:hover .ki-body > i{transform:translateX(3px);}
.ki-empty{text-align:center;color:#4a5878;background:rgba(255,255,255,.75);border-radius:20px;padding:2.5rem 1.5rem;margin:0;}
.ki-cta{text-align:center;margin-top:clamp(1.75rem,3vw,2.5rem);}
.hb-maroon{background:#9b1b30;color:#fff;font-size:1rem;font-weight:700;padding:.85rem 2.4rem;box-shadow:0 10px 26px rgba(155,27,48,.28);}
.hb-maroon:hover{background:#b3223a;color:#fff;}
@media (max-width:960px){ .ki-grid{grid-template-columns:1fr;} .ki-lead .ki-thumb{aspect-ratio:16/9;min-height:0;flex:none;} }
@media (max-width:600px){ .ki-more{grid-template-columns:1fr;} }
</style>
@endpush
<section class="hs ki" aria-labelledby="ki-title">
    <div class="hs-bg"><img src="{{ asset('img/home/bg-skincare-m.webp') }}" srcset="{{ asset('img/home/bg-skincare-m.webp') }} 900w, {{ asset('img/home/bg-skincare.webp') }} 1669w" sizes="100vw" alt="" loading="lazy" decoding="async"></div>
    <div class="wrap">
        <header class="hs-center rv">
            <span class="hs-bar"></span>
            <p class="hs-eyebrow">Knowledge &amp; Insights</p>
            <h2 class="hs-title" id="ki-title">Understand the science. <span>Explore the industry.</span></h2>
            <p class="hs-lead">Jelajahi artikel, pembahasan ilmiah, dan analisis dari RayAcademy untuk memahami perkembangan industri kosmetik. Mulai dari sains dan formulasi, regulasi dan manufaktur, hingga perilaku konsumen, branding, marketing, dan penerapan AI dalam bisnis kosmetik.</p>
        </header>

        @if($leadArticle)
            <div class="ki-grid {{ $moreArticles->isEmpty() ? 'solo' : '' }}">
                @foreach($moreArticles->prepend($leadArticle) as $article)
                    @if($loop->index === 1)<div class="ki-more">@endif
                    <a href="{{ route('article.show', $article->slug) }}" class="ki-card rv {{ $loop->first ? 'ki-lead' : '' }}">
                        <div class="ki-thumb">
                            <i class="fas fa-flask" aria-hidden="true"></i>
                            @if($article->thumbnail_url)
                                <img src="{{ $article->thumbnail_url }}" alt="" loading="lazy" decoding="async" onerror="this.remove()">
                            @endif
                        </div>
                        <div class="ki-body">
                            <div>
                                @if($category = $article->categories->first())<span class="ki-cat">{{ $category->name }}</span>@endif
                                <h3>{{ $article->title }}</h3>
                            </div>
                            <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </div>
                    </a>
                    @if($loop->last && ! $loop->first)</div>@endif
                @endforeach
            </div>
        @else
            <p class="ki-empty">Artikel pertama sedang kami siapkan. Kunjungi lagi dalam waktu dekat.</p>
        @endif

        <div class="ki-cta"><a href="{{ route('article.index') }}" class="hb hb-maroon">Jelajahi Knowledge Center <i class="fas fa-arrow-right" aria-hidden="true"></i></a></div>
    </div>
</section>
