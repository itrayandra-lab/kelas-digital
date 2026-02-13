<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleSeederNew extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Membangun Personal Branding yang Autentik di Era Digital Modern',
                'author' => 'Ria R. Christiana SE, MBA',
                'excerpt' => 'Personal branding bukan tentang menciptakan persona palsu, tapi tentang mengkomunikasikan nilai autentik Anda dengan cara yang tepat.',
                'content' => 'Di era digital, personal branding menjadi kunci untuk membedakan diri di tengah keramaian. Namun, banyak yang salah kaprah dengan menciptakan persona yang tidak autentik. Personal branding sejati dimulai dari pemahaman mendalam tentang nilai, keahlian, dan visi Anda. Identifikasi kekuatan unik yang Anda miliki dan bagaimana hal itu dapat memberikan nilai bagi orang lain. Konsistensi adalah kunci - dari cara Anda berkomunikasikan di media sosial hingga bagaimana Anda berinteraksi secara langsung. Bangun narasi yang kuat tentang perjalanan Anda, bukan hanya pencapaian. Gunakan platform digital dengan bijak untuk memperkuat pesan Anda, namun jangan lupakan pentingnya koneksi manusiawi yang genuine.',
                'thumbnail' => 'https://picsum.photos/seed/branding1/800/600',
                'categories' => ['personal-branding'],
                'tags' => ['branding', 'digital'],
                'hero_slider_order' => 1,
                'is_recommended' => true,
            ],
            [
                'title' => 'Strategi Marketing Berbasis Data untuk Meningkatkan Konversi Penjualan Anda',
                'author' => 'Wendra Wilendra M.MT',
                'excerpt' => 'Data adalah aset berharga dalam marketing modern. Pelajari cara menganalisis dan menggunakan data untuk strategi yang lebih efektif.',
                'content' => 'Marketing berbasis data mengubah cara bisnis memahami pelanggan mereka. Mulailah dengan mengumpulkan data yang relevan dari berbagai touchpoint - website, media sosial, email, dan transaksi. Gunakan tools analytics untuk mengidentifikasi pola perilaku konsumen. Segmentasi audiens berdasarkan demografi, perilaku, dan preferensi memungkinkan personalisasi yang lebih baik. A/B testing adalah kunci untuk mengoptimalkan setiap elemen kampanye. Jangan hanya fokus pada vanity metrics seperti likes atau followers, tapi ukur metrik yang benar-benar berdampak pada bisnis seperti conversion rate dan customer lifetime value. Ingat, data tanpa interpretasi yang tepat hanya angka.',
                'thumbnail' => 'https://picsum.photos/seed/marketing2/800/600',
                'categories' => ['digital-marketing', 'business-strategy'],
                'tags' => ['marketing', 'strategy'],
                'hero_slider_order' => 2,
                'is_recommended' => true,
            ],
            [
                'title' => 'Psikologi Komunikasi Efektif dalam Membangun Tim yang Solid dan Produktif',
                'author' => 'Sukmayanti Ranadireksa, M.Psi',
                'excerpt' => 'Komunikasi yang efektif adalah fondasi tim yang kuat. Pahami psikologi di balik interaksi untuk membangun kolaborasi yang lebih baik.',
                'content' => 'Komunikasi bukan sekadar menyampaikan pesan, tapi memastikan pesan dipahami dengan benar. Dalam konteks tim, pemahaman psikologi komunikasi sangat krusial. Setiap individu memiliki gaya komunikasi yang berbeda - ada yang langsung, ada yang lebih halus. Kenali tipe kepribadian anggota tim Anda untuk menyesuaikan pendekatan. Active listening adalah skill yang sering diabaikan namun sangat powerful. Berikan ruang aman bagi tim untuk menyampaikan pendapat tanpa takut dihakimi. Feedback yang konstruktif harus spesifik, tepat waktu, dan fokus pada perilaku bukan pribadi. Konflik adalah hal normal, yang penting adalah bagaimana mengelolanya dengan mature.',
                'thumbnail' => 'https://picsum.photos/seed/psychology3/800/600',
                'categories' => ['leadership-management'],
                'tags' => ['communication', 'team-building'],
                'hero_slider_order' => 3,
            ],
            [
                'title' => 'Mengoptimalkan Produktivitas dengan Manajemen Energi Bukan Sekadar Waktu Kerja',
                'author' => 'Ria R. Christiana SE, MBA',
                'excerpt' => 'Waktu kita sama, 24 jam. Tapi energi kita berbeda. Pelajari cara mengelola energi untuk produktivitas maksimal.',
                'content' => 'Banyak orang fokus pada time management, padahal yang lebih penting adalah energy management. Kita semua punya 24 jam yang sama, tapi tidak semua jam memiliki kualitas energi yang sama. Identifikasi kapan Anda berada di peak performance - pagi, siang, atau malam. Alokasikan tugas-tugas penting yang membutuhkan fokus tinggi di waktu tersebut. Pahami bahwa energi bukan hanya fisik, tapi juga mental, emosional, dan spiritual. Istirahat bukan pemborosan waktu, tapi investasi untuk energi yang lebih baik. Teknik Pomodoro dapat membantu menjaga fokus dengan interval istirahat teratur. Hindari multitasking yang menguras energi mental.',
                'thumbnail' => 'https://picsum.photos/seed/productivity4/800/600',
                'categories' => ['productivity-growth'],
                'tags' => ['productivity', 'growth'],
                'is_recommended' => true,
            ],
            [
                'title' => 'Memahami Algoritma Media Sosial untuk Strategi Konten yang Lebih Efektif',
                'author' => 'Wendra Wilendra M.MT',
                'excerpt' => 'Algoritma media sosial terus berubah. Pahami cara kerjanya untuk membuat konten yang lebih engaging dan menjangkau audiens lebih luas.',
                'content' => 'Algoritma media sosial dirancang untuk menampilkan konten yang paling relevan bagi setiap pengguna. Memahami prinsip dasarnya membantu Anda membuat strategi konten yang lebih efektif. Engagement adalah raja - likes, comments, shares, dan saves adalah sinyal kuat bahwa konten Anda valuable. Konsistensi posting penting, tapi kualitas tetap lebih utama dari kuantitas. Timing juga berperan - posting saat audiens Anda paling aktif. Video dan carousel post cenderung mendapat engagement lebih tinggi. Gunakan hashtag strategis yang relevan, bukan yang paling populer. Interaksi dua arah sangat penting - balas komentar dan DM dengan genuine.',
                'thumbnail' => 'https://picsum.photos/seed/algorithm5/800/600',
                'categories' => ['digital-marketing', 'content-creation'],
                'tags' => ['social-media', 'content'],
                'hero_slider_order' => 4,
            ],
            [
                'title' => 'Membangun Brand Identity yang Kuat dan Konsisten di Semua Platform',
                'author' => 'Ria R. Christiana SE, MBA',
                'excerpt' => 'Brand identity yang kuat membuat bisnis Anda mudah dikenali dan diingat. Pelajari elemen-elemen penting dalam membangun identitas brand.',
                'content' => 'Brand identity adalah wajah bisnis Anda di mata konsumen. Ini bukan hanya logo, tapi keseluruhan pengalaman visual dan emosional yang Anda ciptakan. Mulai dengan mendefinisikan brand personality - apakah brand Anda profesional, playful, atau sophisticated? Pilih palet warna yang konsisten dan memiliki makna. Typography juga berperan penting dalam komunikasi brand. Tone of voice harus konsisten di semua platform - dari website, media sosial, hingga customer service. Brand guidelines adalah dokumen penting yang memastikan konsistensi. Visual consistency membangun recognition dan trust. Namun, konsistensi bukan berarti kaku - brand Anda harus bisa beradaptasi dengan konteks yang berbeda.',
                'thumbnail' => 'https://picsum.photos/seed/brandidentity6/800/600',
                'categories' => ['personal-branding'],
                'tags' => ['branding', 'strategy'],
                'is_recommended' => true,
            ],
            [
                'title' => 'Mengajarkan Anak Literasi Digital dan Keamanan Online di Era Modern',
                'author' => 'dr. Frecillia Regina, Sp.A',
                'excerpt' => 'Anak-anak tumbuh di era digital. Sebagai orang tua, penting untuk mengajarkan literasi digital dan keamanan online sejak dini.',
                'content' => 'Literasi digital bukan hanya tentang cara menggunakan gadget, tapi memahami bagaimana teknologi bekerja dan dampaknya. Ajarkan anak untuk berpikir kritis terhadap informasi online - tidak semua yang ada di internet adalah benar. Privacy adalah konsep penting yang harus dipahami sejak dini. Jelaskan mengapa tidak boleh membagikan informasi pribadi seperti alamat, nomor telepon, atau foto sekolah. Diskusikan tentang jejak digital dan bagaimana apa yang mereka posting bisa bertahan selamanya. Cyberbullying adalah ancaman nyata - ciptakan komunikasi terbuka agar anak merasa aman bercerita. Screen time management penting untuk kesehatan fisik dan mental.',
                'thumbnail' => 'https://picsum.photos/seed/digitalliteracy7/800/600',
                'categories' => ['technology-ai'],
                'tags' => ['digital', 'innovation'],
                'hero_slider_order' => 5,
            ],
            [
                'title' => 'Strategi Competitor Analysis yang Efektif untuk Keunggulan Kompetitif Bisnis',
                'author' => 'Wendra Wilendra M.MT',
                'excerpt' => 'Memahami kompetitor adalah kunci strategi bisnis yang efektif. Pelajari cara melakukan analisis kompetitor yang mendalam dan actionable.',
                'content' => 'Competitor analysis bukan tentang meniru, tapi memahami landscape industri untuk menemukan positioning unik Anda. Mulai dengan mengidentifikasi kompetitor langsung dan tidak langsung. Analisis produk atau layanan mereka - apa kelebihan dan kelemahannya? Pelajari strategi marketing mereka di berbagai channel. Perhatikan bagaimana mereka berkomunikasi dengan audiens dan value proposition yang mereka tawarkan. Gunakan tools seperti SEMrush atau SimilarWeb untuk analisis digital presence. Monitor media sosial mereka untuk memahami engagement dan sentiment. Analisis pricing strategy dan bagaimana mereka memposisikan diri di market. Customer reviews adalah goldmine untuk memahami pain points yang belum terpecahkan.',
                'thumbnail' => 'https://picsum.photos/seed/competitor8/800/600',
                'categories' => ['business-strategy'],
                'tags' => ['business', 'strategy'],
                'is_recommended' => true,
            ],
            [
                'title' => 'Mengelola Stres dan Burnout di Tempat Kerja dengan Pendekatan Psikologis',
                'author' => 'Sukmayanti Ranadireksa, M.Psi',
                'excerpt' => 'Burnout adalah masalah serius di dunia kerja modern. Kenali tanda-tandanya dan pelajari strategi mengelola stres secara efektif.',
                'content' => 'Burnout bukan sekadar kelelahan biasa, tapi kondisi exhaustion fisik, mental, dan emosional yang kronis. Tanda-tandanya meliputi cynicism terhadap pekerjaan, penurunan produktivitas, dan detachment emosional. Pencegahan lebih baik dari pengobatan - kenali early warning signs. Set boundaries yang jelas antara pekerjaan dan kehidupan pribadi. Learn to say no pada komitmen yang berlebihan. Praktikkan self-care yang konsisten - tidur cukup, olahraga, dan nutrisi seimbang. Mindfulness dan meditasi terbukti efektif mengurangi stres. Bangun support system yang kuat, baik di tempat kerja maupun di luar. Jangan ragu mencari bantuan profesional jika diperlukan.',
                'thumbnail' => 'https://picsum.photos/seed/burnout9/800/600',
                'categories' => ['productivity-growth', 'leadership-management'],
                'tags' => ['productivity', 'leadership'],
            ],
            [
                'title' => 'Memanfaatkan AI untuk Meningkatkan Efisiensi Bisnis dan Inovasi Produk',
                'author' => 'Wendra Wilendra M.MT',
                'excerpt' => 'AI bukan lagi masa depan, tapi kenyataan saat ini. Pelajari cara memanfaatkan AI untuk transformasi bisnis yang lebih efisien.',
                'content' => 'Artificial Intelligence mengubah cara bisnis beroperasi di berbagai industri. Mulai dari customer service dengan chatbot hingga predictive analytics untuk forecasting. AI dapat mengotomatisasi tugas-tugas repetitif, membebaskan tim untuk fokus pada pekerjaan yang lebih strategis. Dalam marketing, AI membantu personalisasi konten dan targeting yang lebih akurat. Machine learning dapat menganalisis data pelanggan untuk insight yang lebih dalam. Namun, implementasi AI harus strategis - identifikasi area mana yang paling benefit dari automation. Investasi dalam AI membutuhkan infrastruktur data yang baik. Training tim untuk bekerja dengan AI tools juga krusial. Ethical consideration penting - pastikan penggunaan AI transparan dan tidak bias.',
                'thumbnail' => 'https://picsum.photos/seed/ai10/800/600',
                'categories' => ['technology-ai'],
                'tags' => ['ai', 'innovation'],
            ],
        ];

        foreach ($articles as $data) {
            $article = \App\Models\Article::updateOrCreate(
                ['title' => $data['title']],
                [
                    'title' => $data['title'],
                    'author' => $data['author'],
                    'excerpt' => $data['excerpt'],
                    'content_format' => 'wordpress',
                    'content' => '<p>' . $data['content'] . '</p>',
                    'thumbnail' => $data['thumbnail'],
                    'post_type' => 'post',
                    'status' => 'published',
                    'published_at' => now()->subDays(rand(1, 30)),
                    'hero_slider_order' => $data['hero_slider_order'] ?? null,
                    'is_recommended' => $data['is_recommended'] ?? false,
                    'views_count' => rand(100, 5000),
                ]
            );

            $categoryIds = \App\Models\ArticleCategory::whereIn('slug', $data['categories'])->pluck('id');
            $tagIds = \App\Models\Tag::whereIn('slug', $data['tags'])->pluck('id');

            $article->categories()->sync($categoryIds);
            $article->tags()->sync($tagIds);
        }
    }
}
