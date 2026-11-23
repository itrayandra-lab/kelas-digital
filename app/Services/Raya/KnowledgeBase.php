<?php

namespace App\Services\Raya;

use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Cache;

/**
 * What RAYA can look things up in: the site's plain text (llms-full.txt: classes, events, articles, FAQ)
 * plus the Learning Families and experts from config/homepage.php. Retrieval is plain BM25 over small
 * chunks, so no vector database or extra service is needed (same approach as Lunaray's AILUNA).
 */
class KnowledgeBase
{
    private const CHUNK_CHARS = 900;

    private const STOPWORDS = [
        'yang', 'dan', 'di', 'ke', 'dari', 'untuk', 'dengan', 'pada', 'adalah', 'ini', 'itu', 'atau', 'juga', 'dalam',
        'akan', 'bisa', 'dapat', 'tidak', 'ada', 'saya', 'anda', 'kami', 'kita', 'apa', 'bagaimana', 'berapa', 'sebuah',
        'serta', 'agar', 'oleh', 'karena', 'sudah', 'masih', 'lebih', 'mau', 'ingin', 'pengen', 'belajar', 'kelas',
        'the', 'and', 'for', 'with', 'that', 'this', 'are', 'you', 'your', 'how', 'what', 'can', 'rayacademy',
    ];

    // Everyday words that should also find the wording the site uses.
    private const SYNONYMS = [
        '/harga|biaya|bayar|gratis|murah|price/' => ['harga', 'gratis', 'pembayaran', 'midtrans'],
        '/sertifikat|certificate/' => ['sertifikat', 'penyelesaian'],
        '/\bai\b|artificial|chatgpt|kecerdasan/' => ['amaizing', 'artificial', 'intelligence', 'technology'],
        '/marketing|pemasaran|branding|brand|jualan|konten|sosmed|social/' => ['dobetter', 'marketing', 'branding', 'digital'],
        '/bisnis|usaha|wirausaha|leader|pimpin|manajemen|komunikasi/' => ['levelup', 'bisnis', 'leadership', 'kewirausahaan', 'komunikasi'],
        '/kosmetik|skincare|formulasi|cosmetic|kecantikan/' => ['kosmetik', 'formulasi', 'sekolah'],
        '/perusahaan|corporate|tim|karyawan|company/' => ['corporate', 'training', 'perusahaan', 'pelatihan'],
        '/event|workshop|acara|offline|seminar/' => ['event', 'workshop', 'sesi', 'offline'],
        '/pemula|dasar|baru mulai|beginner/' => ['pemula', 'dasar', 'beginner'],
    ];

    /** @return array<int, array{text:string,title:string,url:string,score:float}> */
    public function search(string $query, int $limit = 6): array
    {
        $index = $this->index();
        $terms = $this->queryTerms($query);
        if (! $terms || ! $index['chunks']) {
            return [];
        }

        $n = count($index['chunks']);
        $avg = $index['avgLen'] ?: 1;
        $scored = [];
        foreach ($index['chunks'] as $i => $chunk) {
            $score = 0.0;
            foreach ($terms as $term => $weight) {
                if (! $tf = $chunk['tf'][$term] ?? 0) {
                    continue;
                }
                $df = $index['df'][$term] ?? 1;
                $idf = log(1 + ($n - $df + 0.5) / ($df + 0.5));
                $score += $weight * $idf * (($tf * 2.4) / ($tf + 1.4 * (0.25 + 0.75 * $chunk['len'] / $avg)));
            }
            if ($score > 0) {
                $scored[$i] = $score;
            }
        }
        if (! $scored) {
            return [];
        }

        arsort($scored);
        $top = reset($scored);
        $picked = [];
        foreach ($scored as $i => $score) {
            if ($score < $top * 0.35 || count($picked) >= $limit) {
                break;
            }
            $c = $index['chunks'][$i];
            $picked[] = ['text' => $c['text'], 'title' => $c['title'], 'url' => $c['url'], 'score' => $score / $top];
        }

        return $picked;
    }

    private function index(): array
    {
        // llms-full.txt is itself cached for an hour; the index follows it.
        return Cache::remember('raya:kb:'.request()->getSchemeAndHttpHost(), 1800, fn () => $this->build());
    }

    private function build(): array
    {
        $chunks = $this->programChunks();
        try {
            $chunks = array_merge($chunks, $this->siteChunks(app(SeoController::class)->llmsFullText()));
        } catch (\Throwable $e) {
            report($e);
        }

        $df = [];
        $totalLen = 0;
        foreach ($chunks as &$chunk) {
            $tokens = $this->tokenize($chunk['title'].' '.$chunk['text']);
            $chunk['tf'] = array_count_values($tokens);
            $chunk['len'] = max(1, count($tokens));
            $totalLen += $chunk['len'];
            foreach (array_keys($chunk['tf']) as $term) {
                $df[$term] = ($df[$term] ?? 0) + 1;
            }
        }
        unset($chunk);

        return ['chunks' => $chunks, 'df' => $df, 'avgLen' => $chunks ? $totalLen / count($chunks) : 1];
    }

    private function programChunks(): array
    {
        $out = [];
        foreach (collect(config('homepage.families'))->where('active', true) as $f) {
            $out[] = ['title' => $f['name'], 'url' => $f['href'], 'text' => "[Learning Family: {$f['name']}]\n{$f['description']}\nHalaman kelasnya: {$f['href']}"];
        }
        $out[] = [
            'title' => 'Para pakar RayAcademy', 'url' => '/#instruktur',
            'text' => "[Pakar dan pengajar RayAcademy]\nRayAcademy belajar bersama akademisi, peneliti, pakar, dan praktisi: "
                .collect(config('homepage.experts'))->flatten()->implode('; ').'.',
        ];

        return $out;
    }

    /** Splits llms-full.txt at its "===== / KIND: title / URL: ... / =====" headers. */
    private function siteChunks(string $text): array
    {
        $parts = preg_split('/^=====\n(.*?)\n=====\n/ms', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $out = [];
        for ($i = 1; $i + 1 < count($parts); $i += 2) {
            [$header, $body] = [$parts[$i], $parts[$i + 1]];
            if ($header === 'FAQ') {
                foreach (preg_split('/\n\n+/', trim($body)) as $qa) {
                    $out[] = ['title' => 'FAQ', 'url' => '/#faq', 'text' => "[FAQ]\n".trim($qa)];
                }

                continue;
            }
            if (! preg_match('/^(KELAS|EVENT|ARTIKEL): (.+)\nURL: (\S+)$/', $header, $m)) {
                continue;
            }
            [, $kind, $title, $url] = $m;
            $path = parse_url($url, PHP_URL_PATH) ?: '/';
            $label = ['KELAS' => 'Kelas', 'EVENT' => 'Event', 'ARTIKEL' => 'Artikel'][$kind];
            foreach (mb_str_split(trim($body), self::CHUNK_CHARS) as $piece) {
                $out[] = ['title' => $title, 'url' => $path, 'text' => "[{$label}: {$title} | {$path}]\n{$piece}"];
            }
        }

        return $out;
    }

    private function tokenize(string $text): array
    {
        preg_match_all('/[\p{L}\p{N}]+/u', mb_strtolower($text), $m);

        return array_values(array_filter($m[0], fn ($t) => mb_strlen($t) >= 3 && ! in_array($t, self::STOPWORDS, true)));
    }

    /** @return array<string, float> term => weight */
    private function queryTerms(string $query): array
    {
        $terms = array_fill_keys($this->tokenize($query), 1.0);
        $lower = mb_strtolower($query);
        foreach (self::SYNONYMS as $pattern => $extra) {
            if (preg_match($pattern, $lower)) {
                foreach ($extra as $t) {
                    $terms[$t] = max($terms[$t] ?? 0, 0.6);
                }
            }
        }

        return $terms;
    }
}
