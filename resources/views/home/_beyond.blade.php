{{-- Beyond the Classroom: text, the group photo, and three cards resting on the photo's lower edge. --}}
@php
    $steps = [
        ['icon' => 'fa-flask', 'label' => '01 – Practice What You Learn', 'title' => 'Belajar dengan Melakukan.',
         'text' => 'Perdalam pemahaman melalui latihan, simulasi, dan aktivitas praktik yang relevan dengan bidang pembelajaran.'],
        ['icon' => 'fa-layer-group', 'label' => '02 – Solve Real Challenges', 'title' => 'Berlatih Menghadapi Tantangan Nyata.',
         'text' => 'Gunakan studi kasus dan persoalan dunia kerja atau bisnis untuk melatih analisis, pemecahan masalah, serta pengambilan keputusan.'],
        ['icon' => 'fa-chart-line', 'label' => '03 – Apply It in Your World', 'title' => 'Membawa Pengetahuan ke Dunia Nyata.',
         'text' => 'Temukan cara menerapkan hasil pembelajaran dalam pekerjaan, pengembangan produk, pengelolaan bisnis, atau aktivitas profesional sehari-hari.'],
    ];
@endphp
@push('styles')
<style>
.bc{background:linear-gradient(180deg,#f3f8fe 0%,#e6f0fb 100%);}
.bc .hs-center{max-width:980px;margin-bottom:clamp(1rem,2vw,1.5rem);}
.bc .hs-title{font-size:clamp(1.8rem,3.8vw,3rem);}
.bc-sub{font-family:'Sora',sans-serif;font-size:clamp(1.05rem,1.7vw,1.35rem);font-weight:700;color:#0b1f4d;margin:0 0 .6rem;}
.bc-photo img{display:block;width:100%;height:auto;
    -webkit-mask-image:linear-gradient(to bottom,transparent 0,#000 14%,#000 72%,transparent 100%),linear-gradient(to right,transparent 0,#000 7%,#000 93%,transparent 100%);-webkit-mask-composite:source-in;
    mask-image:linear-gradient(to bottom,transparent 0,#000 14%,#000 72%,transparent 100%),linear-gradient(to right,transparent 0,#000 7%,#000 93%,transparent 100%);mask-composite:intersect;}
.bc-grid{position:relative;z-index:1;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:clamp(1rem,2vw,1.5rem);margin-top:clamp(-7rem,-8vw,-2rem);}
.bc-card{border-radius:22px;padding:1.4rem 1.4rem 1.5rem;background:rgba(255,255,255,.86);border:1px solid #fff;box-shadow:0 18px 40px rgba(31,77,140,.14);
    -webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);}
.bc-head{display:flex;align-items:center;gap:.75rem;margin-bottom:.8rem;}
.bc-head i{flex-shrink:0;width:2.6rem;height:2.6rem;border-radius:12px;background:#e6f0fc;color:#1f5fc0;display:flex;align-items:center;justify-content:center;font-size:1.05rem;}
.bc-head p{margin:0;font-family:'Sora',sans-serif;font-size:.8rem;font-weight:800;letter-spacing:.04em;text-transform:uppercase;line-height:1.3;color:#1f5fc0;
    border-left:3px solid #d9a441;padding-left:.6rem;}
.bc-card h3{font-family:'Sora',sans-serif;font-size:1.05rem;font-weight:700;color:#0b1f4d;margin:0 0 .45rem;}
.bc-card > p{font-size:.88rem;line-height:1.6;color:#4a5878;margin:0;}
.bc-close{display:flex;align-items:center;justify-content:center;gap:1.25rem;margin:clamp(2rem,4vw,3rem) 0 1.5rem;
    font-family:Georgia,'Times New Roman',serif;font-style:italic;font-weight:700;font-size:clamp(1.15rem,2.2vw,1.65rem);color:#0b1f4d;text-align:center;}
.bc-close::before,.bc-close::after{content:'';flex:0 0 clamp(2rem,6vw,5rem);height:2px;background:#d9a441;}
.bc-cta{text-align:center;}
@media (max-width:900px){
    .bc-grid{grid-template-columns:1fr;max-width:560px;margin-inline:auto;margin-top:.5rem;}
    .bc-photo img{aspect-ratio:4/3;object-fit:cover;object-position:center 60%;-webkit-mask-image:none;mask-image:none;border-radius:20px;}
    .bc-close::before,.bc-close::after{display:none;}
}
</style>
@endpush
<section class="hs bc" id="praktik" aria-labelledby="bc-title">
    <div class="wrap">
        <header class="hs-center rv">
            <span class="hs-bar"></span>
            <p class="hs-eyebrow">Beyond the classroom</p>
            <h2 class="hs-title" id="bc-title">Learning that goes <span>beyond knowing.</span></h2>
            <p class="bc-sub">Dari pengetahuan menuju penerapan nyata.</p>
            <p class="hs-lead">Di RayAcademy, pembelajaran tidak berhenti pada pemahaman materi. Melalui latihan, studi kasus, dan aktivitas berbasis praktik, peserta diajak menghubungkan pengetahuan dengan tantangan yang mereka hadapi dalam pekerjaan, profesi, maupun pengembangan usaha.</p>
        </header>
        <div class="bc-photo">
            <img src="{{ asset('img/home/beyond-m.webp') }}" srcset="{{ asset('img/home/beyond-m.webp') }} 900w, {{ asset('img/home/beyond.webp') }} 1670w" sizes="(min-width:1280px) 1280px, 100vw" width="1670" height="470"
                 alt="Peserta RayAcademy berdiskusi dan praktik di laboratorium kosmetik" loading="lazy" decoding="async">
        </div>
        <div class="bc-grid">
            @foreach($steps as $step)
                <article class="bc-card rv">
                    <div class="bc-head"><i class="fas {{ $step['icon'] }}" aria-hidden="true"></i><p>{{ $step['label'] }}</p></div>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['text'] }}</p>
                </article>
            @endforeach
        </div>
        <p class="bc-close rv">Because Knowledge Matters Most When You Can Use It.</p>
        <div class="bc-cta"><a href="{{ route('course.index') }}" class="hb hb-navy">Jelajahi Program Pembelajaran <i class="fas fa-arrow-right" aria-hidden="true"></i></a></div>
    </div>
</section>
