<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Membangun Personal Branding yang Autentik di Era Digital Modern',
                'author' => 'Dr. Sarah Wijaya',
                'excerpt' => 'Temukan langkah-langkah skincare routine yang efektif untuk mendapatkan kulit glowing dan sehat secara alami.',
                'content' => 'Kulit glowing adalah impian setiap orang. Untuk mencapainya, Anda perlu memahami jenis kulit dan kebutuhan spesifiknya. Mulailah dengan double cleansing untuk membersihkan makeup dan kotoran. Gunakan toner untuk menyeimbangkan pH kulit, diikuti dengan serum yang mengandung vitamin C atau niacinamide. Jangan lupa moisturizer untuk menjaga kelembaban kulit. Terakhir, aplikasikan sunscreen setiap pagi untuk melindungi dari sinar UV. Konsistensi adalah kunci utama dalam skincare routine. Lakukan rutin ini pagi dan malam untuk hasil maksimal.',
                'thumbnail' => 'https://picsum.photos/seed/skincare1/800/600',
                'categories' => ['skincare-tips'],
                'tags' => ['skincare', 'tips'],
                'hero_slider_order' => 1,
                'is_recommended' => true,
            ],
            [
                'title' => 'Mengenal Kandungan Retinol dan Manfaatnya untuk Anti Aging',
                'author' => 'Dr. Amanda Chen',
                'excerpt' => 'Retinol menjadi bahan aktif populer dalam produk anti-aging. Pelajari cara penggunaan yang benar dan manfaatnya.',
                'content' => 'Retinol adalah turunan vitamin A yang terbukti efektif mengatasi tanda-tanda penuaan. Bahan ini bekerja dengan meningkatkan produksi kolagen dan mempercepat regenerasi sel kulit. Retinol dapat mengurangi garis halus, kerutan, dan hiperpigmentasi. Namun, penggunaan retinol harus dilakukan secara bertahap. Mulai dengan konsentrasi rendah 0.25% dan gunakan 2-3 kali seminggu. Tingkatkan frekuensi secara perlahan sesuai toleransi kulit. Selalu gunakan sunscreen di pagi hari karena retinol membuat kulit lebih sensitif terhadap sinar matahari. Hindari penggunaan bersamaan dengan AHA/BHA atau vitamin C.',
                'thumbnail' => 'https://picsum.photos/seed/retinol2/800/600',
                'categories' => ['product-review', 'skincare-tips'],
                'tags' => ['anti-aging', 'review'],
                'hero_slider_order' => 2,
                'is_recommended' => true,
            ],
            [
                'title' => 'Cara Memilih Sunscreen yang Tepat untuk Jenis Kulit Anda',
                'author' => 'Dr. Michael Tan',
                'excerpt' => 'Sunscreen adalah produk wajib dalam skincare. Ketahui cara memilih sunscreen yang sesuai dengan jenis kulit Anda.',
                'content' => 'Sunscreen melindungi kulit dari kerusakan akibat sinar UV yang dapat menyebabkan penuaan dini dan kanker kulit. Pilih sunscreen dengan SPF minimal 30 dan broad spectrum untuk perlindungan UVA dan UVB. Untuk kulit berminyak, pilih formula gel atau water-based yang ringan dan tidak menyumbat pori. Kulit kering cocok dengan sunscreen berbasis krim yang melembabkan. Kulit sensitif sebaiknya memilih physical sunscreen dengan zinc oxide atau titanium dioxide. Aplikasikan sunscreen 15 menit sebelum keluar rumah dan reapply setiap 2-3 jam. Jangan lupa area leher, telinga, dan punggung tangan.',
                'thumbnail' => 'https://picsum.photos/seed/sunscreen3/800/600',
                'categories' => ['skincare-tips'],
                'tags' => ['sunscreen', 'tips'],
                'hero_slider_order' => 3,
            ],
            [
                'title' => 'Manfaat Vitamin C Serum untuk Mencerahkan Kulit Kusam',
                'author' => 'Dr. Lisa Park',
                'excerpt' => 'Vitamin C serum adalah produk andalan untuk mencerahkan kulit. Simak manfaat dan cara penggunaannya yang benar.',
                'content' => 'Vitamin C adalah antioksidan kuat yang melindungi kulit dari radikal bebas dan polusi. Serum vitamin C dapat mencerahkan kulit kusam, memudarkan dark spot, dan meratakan warna kulit. Bahan ini juga merangsang produksi kolagen untuk kulit lebih kencang. Pilih vitamin C dalam bentuk L-ascorbic acid dengan konsentrasi 10-20% untuk hasil optimal. Simpan di tempat sejuk dan gelap karena vitamin C mudah teroksidasi. Gunakan serum vitamin C di pagi hari sebelum sunscreen untuk perlindungan ekstra. Kombinasikan dengan vitamin E dan ferulic acid untuk meningkatkan efektivitas.',
                'thumbnail' => 'https://picsum.photos/seed/vitaminc4/800/600',
                'categories' => ['product-review'],
                'tags' => ['vitamin-c', 'brightening'],
                'is_recommended' => true,
            ],
            [
                'title' => 'Panduan Lengkap Mengatasi Jerawat dengan Bahan Alami dan Medis',
                'author' => 'Dr. Kevin Wijaya',
                'excerpt' => 'Jerawat adalah masalah kulit yang umum. Pelajari cara mengatasi jerawat dengan pendekatan alami dan medis yang efektif.',
                'content' => 'Jerawat terjadi ketika pori-pori tersumbat oleh minyak, sel kulit mati, dan bakteri. Untuk mengatasi jerawat, mulai dengan membersihkan wajah dua kali sehari menggunakan gentle cleanser. Gunakan bahan aktif seperti salicylic acid untuk eksfoliasi dan benzoyl peroxide untuk membunuh bakteri. Tea tree oil adalah alternatif alami yang efektif. Niacinamide membantu mengontrol produksi sebum dan mengurangi peradangan. Hindari memencet jerawat karena dapat menyebabkan bekas luka. Jaga pola makan dengan mengurangi gula dan dairy. Konsultasikan dengan dermatolog jika jerawat tidak membaik setelah 6-8 minggu perawatan mandiri.',
                'thumbnail' => 'https://picsum.photos/seed/acne5/800/600',
                'categories' => ['skincare-tips', 'wellness'],
                'tags' => ['acne', 'treatment'],
                'hero_slider_order' => 4,
            ],
            [
                'title' => 'Eksfoliasi Wajah: Perbedaan AHA, BHA, dan PHA yang Perlu Diketahui',
                'author' => 'Dr. Rachel Kim',
                'excerpt' => 'Eksfoliasi kimia dengan AHA, BHA, dan PHA memiliki fungsi berbeda. Kenali perbedaannya untuk hasil optimal.',
                'content' => 'Eksfoliasi kimia mengangkat sel kulit mati untuk kulit lebih cerah dan halus. AHA (Alpha Hydroxy Acid) seperti glycolic acid dan lactic acid bekerja di permukaan kulit, cocok untuk kulit kering dan kusam. BHA (Beta Hydroxy Acid) atau salicylic acid larut dalam minyak, dapat menembus pori untuk mengatasi jerawat dan komedo. PHA (Polyhydroxy Acid) adalah eksfolian paling gentle, cocok untuk kulit sensitif. Mulai dengan konsentrasi rendah dan gunakan 2-3 kali seminggu. Jangan kombinasikan dengan retinol di waktu yang sama. Selalu gunakan sunscreen karena eksfoliasi membuat kulit lebih sensitif terhadap sinar matahari.',
                'thumbnail' => 'https://picsum.photos/seed/exfoliate6/800/600',
                'categories' => ['skincare-tips'],
                'tags' => ['exfoliation', 'chemical-exfoliant'],
                'is_recommended' => true,
            ],
            [
                'title' => 'Rutinitas Perawatan Kulit Malam untuk Regenerasi Optimal Saat Tidur',
                'author' => 'Dr. Jennifer Lee',
                'excerpt' => 'Malam hari adalah waktu terbaik untuk regenerasi kulit. Optimalkan dengan rutinitas perawatan yang tepat.',
                'content' => 'Kulit melakukan regenerasi maksimal saat tidur, sehingga perawatan malam sangat penting. Mulai dengan double cleansing untuk membersihkan makeup dan kotoran. Gunakan toner untuk menyiapkan kulit menerima produk selanjutnya. Aplikasikan treatment serum seperti retinol atau peptide untuk anti-aging. Gunakan eye cream untuk area mata yang sensitif. Akhiri dengan night cream atau sleeping mask yang kaya nutrisi. Produk malam biasanya lebih kental dan nutrisi karena tidak perlu khawatir dengan makeup atau sunscreen. Tidur cukup 7-8 jam untuk hasil optimal. Ganti sarung bantal secara rutin untuk menjaga kebersihan.',
                'thumbnail' => 'https://picsum.photos/seed/nightcare7/800/600',
                'categories' => ['skincare-tips', 'wellness'],
                'tags' => ['night-routine', 'skincare'],
                'hero_slider_order' => 5,
            ],
            [
                'title' => 'Mengenal Hyaluronic Acid: Bahan Ajaib untuk Kulit Lembab dan Kenyal',
                'author' => 'Dr. Daniel Wong',
                'excerpt' => 'Hyaluronic acid adalah humektan super yang dapat menahan air hingga 1000 kali beratnya. Pelajari manfaatnya.',
                'content' => 'Hyaluronic acid (HA) adalah molekul yang secara alami ada di kulit kita. Seiring usia, produksi HA menurun sehingga kulit menjadi kering dan keriput. Produk skincare dengan HA dapat menghidrasi kulit secara intensif, membuat kulit lebih plump dan kenyal. HA bekerja dengan menarik dan menahan air di lapisan kulit. Gunakan HA serum pada kulit yang sedikit lembab untuk hasil maksimal. Pilih produk dengan berbagai ukuran molekul HA untuk penetrasi optimal. HA cocok untuk semua jenis kulit, termasuk kulit berminyak dan sensitif. Kombinasikan dengan moisturizer untuk mengunci kelembaban. Gunakan pagi dan malam untuk kulit terhidrasi sepanjang hari.',
                'thumbnail' => 'https://picsum.photos/seed/hyaluronic8/800/600',
                'categories' => ['product-review'],
                'tags' => ['hydration', 'ingredients'],
                'is_recommended' => true,
            ],
            [
                'title' => 'Tips Merawat Kulit Sensitif agar Tetap Sehat dan Nyaman',
                'author' => 'Dr. Emily Chen',
                'excerpt' => 'Kulit sensitif memerlukan perawatan khusus. Ikuti tips ini untuk menjaga kulit sensitif tetap sehat tanpa iritasi.',
                'content' => 'Kulit sensitif mudah mengalami kemerahan, gatal, dan iritasi. Pilih produk dengan formula minimal dan bebas fragrance. Hindari bahan iritan seperti alkohol, sulfate, dan essential oil. Lakukan patch test sebelum menggunakan produk baru. Gunakan gentle cleanser yang tidak mengandung sabun. Pilih moisturizer dengan ceramide untuk memperkuat skin barrier. Gunakan physical sunscreen dengan zinc oxide yang lebih gentle. Hindari air panas saat mencuci muka. Perkenalkan produk baru satu per satu dengan jarak 2 minggu. Jika terjadi reaksi, hentikan penggunaan dan konsultasi dengan dermatolog. Jaga pola hidup sehat dengan cukup tidur dan kelola stres.',
                'thumbnail' => 'https://picsum.photos/seed/sensitive9/800/600',
                'categories' => ['skincare-tips', 'wellness'],
                'tags' => ['sensitive-skin', 'tips'],
            ],
            [
                'title' => 'Pentingnya Skin Barrier dan Cara Memperbaiki Skin Barrier yang Rusak',
                'author' => 'Dr. Sophie Martinez',
                'excerpt' => 'Skin barrier adalah pertahanan pertama kulit. Ketahui tanda-tanda skin barrier rusak dan cara memperbaikinya.',
                'content' => 'Skin barrier adalah lapisan terluar kulit yang melindungi dari polusi, bakteri, dan kehilangan air. Tanda skin barrier rusak meliputi kulit kering, kemerahan, gatal, dan mudah iritasi. Penyebabnya bisa dari over-exfoliation, produk harsh, atau faktor lingkungan. Untuk memperbaiki, sederhanakan rutinitas skincare. Fokus pada produk dengan ceramide, cholesterol, dan fatty acid yang menyusun skin barrier. Hindari eksfoliasi dan bahan aktif kuat sementara waktu. Gunakan gentle cleanser dan moisturizer yang melembabkan. Aplikasikan occlusive seperti petrolatum di malam hari untuk mengunci kelembaban. Proses perbaikan membutuhkan waktu 2-4 minggu. Bersabar dan konsisten adalah kunci keberhasilan.',
                'thumbnail' => 'https://picsum.photos/seed/barrier10/800/600',
                'categories' => ['skincare-tips'],
                'tags' => ['skin-barrier', 'repair'],
            ],
        ];

        foreach ($articles as $data) {
            $article = Article::updateOrCreate(
                ['title' => $data['title']],
                [
                    'title' => $data['title'],
                    'author' => $data['author'],
                    'excerpt' => $data['excerpt'],
                    'content_format' => 'html',
                    'content' => '<p>'.$data['content'].'</p>',
                    'thumbnail' => $data['thumbnail'],
                    'post_type' => 'post',
                    'status' => 'published',
                    'published_at' => now()->subDays(rand(1, 30)),
                    'hero_slider_order' => $data['hero_slider_order'] ?? null,
                    'is_recommended' => $data['is_recommended'] ?? false,
                    'views_count' => rand(100, 5000),
                ]
            );

            $categoryIds = ArticleCategory::whereIn('slug', $data['categories'])->pluck('id');
            $tagIds = Tag::whereIn('slug', $data['tags'])->pluck('id');

            $article->categories()->sync($categoryIds);
            $article->tags()->sync($tagIds);
        }
    }
}
