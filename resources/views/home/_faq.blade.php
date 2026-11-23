{{-- FAQ accordion from config/faq.php. All closed on load, one open at a time; the first six show,
     "Lihat Semua FAQ" reveals the rest in place (there is no separate FAQ page). --}}
@php $faqs = config('faq'); $faqShown = 6; @endphp
@push('styles')
<style>
.fq{background:#eef5fd;}
.fq-grid{display:grid;grid-template-columns:minmax(0,.95fr) minmax(0,1.3fr);gap:clamp(2rem,5vw,4.5rem);align-items:center;}
.fq-intro .hs-eyebrow{padding-bottom:1rem;margin-bottom:1.4rem;position:relative;}
.fq-intro .hs-eyebrow::after{content:'';position:absolute;left:0;bottom:0;width:72px;height:2px;background:#8fb6e6;}
.fq-intro .hs-title{font-size:clamp(1.8rem,3vw,2.9rem);}
.fq-intro .hs-title span{display:block;}
.fq-intro h3{font-family:'Sora',sans-serif;font-size:clamp(1.05rem,1.6vw,1.3rem);font-weight:700;line-height:1.35;color:#0b1f4d;margin:2rem 0 .7rem;}
.fq-intro > p:last-child{color:#5a7194;font-size:.95rem;line-height:1.7;margin:0;max-width:420px;}
.fq-panel{border-radius:22px;padding:clamp(1rem,2vw,1.4rem);background:rgba(255,255,255,.6);border:1px solid #cfe0f3;box-shadow:0 18px 44px rgba(31,77,140,.08);}
.fq-list{display:flex;flex-direction:column;gap:.65rem;}
.fq-item{border-radius:14px;border:1px solid #d4e3f5;background:rgba(255,255,255,.85);transition:background-color .2s ease;}
.fq-item.open{background:#eaf2fc;}
.fq-item > h3{margin:0;}
.fq-q{width:100%;display:flex;align-items:center;gap:1rem;padding:1rem 1.1rem;background:none;border:0;cursor:pointer;text-align:left;
    font-family:'Sora',sans-serif;font-size:.95rem;font-weight:600;line-height:1.4;color:#0b1f4d;border-radius:14px;}
.fq-q:focus-visible{outline:3px solid #7cb7ff;outline-offset:2px;}
.fq-num{flex-shrink:0;width:3.6rem;font-family:Georgia,'Times New Roman',serif;font-size:1.5rem;font-weight:400;color:#1f5fc0;padding-right:1rem;border-right:1px solid #b9cfea;}
.fq-text{flex:1;}
.fq-sign{flex-shrink:0;position:relative;width:16px;height:16px;color:#1f5fc0;}
.fq-sign::before,.fq-sign::after{content:'';position:absolute;left:0;top:7px;width:16px;height:2px;border-radius:2px;background:currentColor;transition:transform .25s ease;}
.fq-sign::after{transform:rotate(90deg);}
.fq-item.open .fq-sign::after{transform:rotate(0);}
.fq-a p{margin:0;padding:0 1.25rem 1.15rem 5.7rem;font-size:.9rem;line-height:1.7;color:#33415c;}
.fq-foot{margin-top:1.3rem;}
.fq-foot > p{font-family:'Sora',sans-serif;font-weight:700;color:#0b1f4d;margin:0 0 .75rem;}
.fq-foot div{display:flex;flex-wrap:wrap;gap:.75rem;}
.fq-foot .hb-outline{color:#1f5fc0;border-color:#1f5fc0;background:#fff;}
.fq-foot .hb-outline:hover{background:#1f5fc0;color:#fff;}
@media (max-width:960px){ .fq-grid{grid-template-columns:1fr;} }
@media (max-width:560px){
    .fq-num{font-size:1.2rem;padding-right:.7rem;width:2.8rem;}
    .fq-q{gap:.7rem;font-size:.9rem;}
    .fq-a p{padding-left:1.1rem;}
    .fq-foot .hb{width:100%;}
}
</style>
@endpush
<section class="hs fq" id="faq" aria-labelledby="fq-title" x-data="{ open: null, all: false }">
    <div class="hs-bg"><img src="{{ asset('img/home/bg-soft-m.webp') }}" srcset="{{ asset('img/home/bg-soft-m.webp') }} 900w, {{ asset('img/home/bg-soft.webp') }} 1672w" sizes="100vw" alt="" loading="lazy" decoding="async"></div>
    <div class="wrap fq-grid">
        <div class="fq-intro rv">
            <p class="hs-eyebrow">Frequently asked questions</p>
            <h2 class="hs-title" id="fq-title">Got questions? <span>Let’s find answers.</span></h2>
            <h3>Pertanyaan Umum Seputar Pembelajaran di RayAcademy</h3>
            <p>Temukan informasi penting sebelum memilih kelas dan memulai perjalanan belajar Anda di RayAcademy.</p>
        </div>
        <div class="fq-panel rv">
            <div class="fq-list">
                @foreach($faqs as $faq)
                    <div class="fq-item" :class="open === {{ $loop->index }} && 'open'" @if($loop->index >= $faqShown) x-show="all" x-collapse x-cloak @endif>
                        <h3>
                            <button type="button" class="fq-q" id="fq-q-{{ $loop->index }}" aria-controls="fq-a-{{ $loop->index }}"
                                    :aria-expanded="open === {{ $loop->index }}" @click="open = open === {{ $loop->index }} ? null : {{ $loop->index }}">
                                <span class="fq-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="fq-text">{{ $faq['q'] }}</span>
                                <span class="fq-sign" aria-hidden="true"></span>
                            </button>
                        </h3>
                        <div class="fq-a" id="fq-a-{{ $loop->index }}" role="region" aria-labelledby="fq-q-{{ $loop->index }}" x-show="open === {{ $loop->index }}" x-collapse x-cloak>
                            <p>{{ $faq['a'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="fq-foot">
                <p>Masih memiliki pertanyaan?</p>
                <div>
                    @if(count($faqs) > $faqShown)
                        <button type="button" class="hb hb-navy" @click="all = !all; if (!all && open >= {{ $faqShown }}) open = null" :aria-expanded="all">
                            <span x-text="all ? 'Tampilkan Lebih Sedikit' : 'Lihat Semua FAQ'">Lihat Semua FAQ</span> <i class="fas fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    @endif
                    <a href="{{ $links['raya'] }}" class="hb hb-outline">Tanya RAYA <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>
