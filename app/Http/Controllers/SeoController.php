<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Event;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Files for search engines and AI assistants: robots.txt, sitemaps, llms.txt, ai.txt, RSS and OpenSearch.
 * Same approach as the other sites in the group, filled with Ray Academy's own content.
 */
class SeoController extends Controller
{
    private const PRIVATE_PATHS = ['/admin/', '/dashboard', '/profile', '/payment', '/login', '/register', '/tiket/', '/cek-tiket', '/course/*/learn', '/events/*/daftar', '/events/*/berhasil'];

    // Indexable hosts always advertise https://primary_host; other hosts describe themselves.
    private function siteUrl(): string
    {
        return $this->indexable() ? 'https://'.config('seo.primary_host') : request()->getSchemeAndHttpHost();
    }

    private function indexable(): bool
    {
        return in_array(request()->getHost(), config('seo.indexable_hosts'), true);
    }

    private function xml(string $body, int $maxAge = 3600)
    {
        return response($body, 200, ['Content-Type' => 'application/xml; charset=utf-8', 'Cache-Control' => "public, max-age={$maxAge}"]);
    }

    private function text(string $body, int $maxAge = 3600)
    {
        return response($body, 200, ['Content-Type' => 'text/plain; charset=utf-8', 'Cache-Control' => "public, max-age={$maxAge}"]);
    }

    private static function plain(?string $html, int $limit = 0): string
    {
        $text = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $html))));

        return $limit ? Str::limit($text, $limit) : $text;
    }

    public function robots()
    {
        if (! $this->indexable()) {
            return $this->text("# Non-production host, not for indexing\nUser-agent: *\nDisallow: /\n");
        }

        $base = $this->siteUrl();
        $agents = [
            '# AI search and assistants',
            'GPTBot', 'ChatGPT-User', 'OAI-SearchBot', 'ClaudeBot', 'Claude-User', 'Claude-SearchBot', 'anthropic-ai',
            'PerplexityBot', 'Perplexity-User', 'Google-Extended', 'Applebot-Extended', 'Meta-ExternalAgent', 'Amazonbot',
            'CCBot', 'Bytespider', 'DeepSeekBot', 'YouBot', 'Diffbot',
            '# Search engines',
            'Googlebot', 'Googlebot-Image', 'Bingbot', 'DuckDuckBot', 'YandexBot', 'Slurp', 'Applebot',
            '# Social previews',
            'FacebookExternalHit', 'Twitterbot', 'LinkedInBot', 'WhatsApp', 'TelegramBot',
        ];

        $lines = ['# robots.txt - RayAcademy', '', 'User-agent: *', 'Allow: /'];
        foreach (self::PRIVATE_PATHS as $path) {
            $lines[] = "Disallow: {$path}";
        }
        // Named crawlers get their own group, so the private paths are repeated for each one.
        foreach ($agents as $agent) {
            $lines[] = '';
            if (str_starts_with($agent, '#')) {
                $lines[] = $agent;

                continue;
            }
            $lines[] = "User-agent: {$agent}";
            $lines[] = 'Allow: /';
            foreach (self::PRIVATE_PATHS as $path) {
                $lines[] = "Disallow: {$path}";
            }
        }
        array_push($lines, '', "Sitemap: {$base}/sitemap.xml", "# LLM-readable summary: {$base}/llms.txt", "# Full content for AI: {$base}/llms-full.txt", "# AI usage policy: {$base}/ai.txt");

        return $this->text(implode("\n", $lines)."\n", 86400);
    }

    public function sitemapIndex()
    {
        $base = $this->siteUrl();
        $now = now()->toAtomString();
        $body = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach (['pages', 'courses', 'events', 'articles'] as $name) {
            $body .= "  <sitemap><loc>{$base}/sitemap/{$name}.xml</loc><lastmod>{$now}</lastmod></sitemap>\n";
        }

        return $this->xml($body.'</sitemapindex>');
    }

    public function sitemap(string $name)
    {
        abort_unless(in_array($name, ['pages', 'courses', 'events', 'articles'], true), 404);

        $body = Cache::remember("sitemap:{$name}:".$this->siteUrl(), 3600, function () use ($name) {
            $base = $this->siteUrl();
            $urls = match ($name) {
                'pages' => collect(config('seo.pages'))->map(fn ($meta, $path) => ['loc' => $base.$path, 'freq' => $meta[0], 'priority' => $meta[1]])->values()
                    ->concat(CourseCategory::whereHas('courses')->orderBy('name')->get()->map(fn ($c) => ['loc' => "{$base}/courses?category={$c->slug}", 'lastmod' => $c->updated_at, 'freq' => 'weekly', 'priority' => '0.7']))
                    ->concat(ArticleCategory::orderBy('name')->get()->map(fn ($c) => ['loc' => "{$base}/articles/category/{$c->slug}", 'lastmod' => $c->updated_at, 'freq' => 'weekly', 'priority' => '0.6'])),
                'courses' => Course::latest('updated_at')->get()->map(fn ($c) => [
                    'loc' => "{$base}/course/{$c->slug}", 'lastmod' => $c->updated_at, 'freq' => 'weekly', 'priority' => '0.9', 'image' => $c->thumbnail_url, 'title' => $c->title,
                ]),
                'events' => Event::published()->latest('event_date')->get()->map(fn ($e) => [
                    'loc' => "{$base}/events/{$e->slug}", 'lastmod' => $e->updated_at, 'freq' => $e->isPast() ? 'yearly' : 'daily', 'priority' => $e->isPast() ? '0.4' : '0.9', 'image' => $e->cover_url, 'title' => $e->title,
                ]),
                'articles' => Article::published()->latest('published_at')->get()->map(fn ($a) => [
                    'loc' => "{$base}/article/{$a->slug}", 'lastmod' => $a->updated_at, 'freq' => 'monthly', 'priority' => '0.7', 'image' => $a->thumbnail_url, 'title' => $a->title,
                ]),
            };

            $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'."\n";
            foreach ($urls as $u) {
                $xml .= '  <url><loc>'.e($u['loc']).'</loc>';
                if (! empty($u['lastmod'])) {
                    $xml .= '<lastmod>'.$u['lastmod']->toAtomString().'</lastmod>';
                }
                $xml .= "<changefreq>{$u['freq']}</changefreq><priority>{$u['priority']}</priority>";
                if (! empty($u['image'])) {
                    $xml .= '<image:image><image:loc>'.e($u['image']).'</image:loc><image:title>'.e($u['title']).'</image:title></image:image>';
                }
                $xml .= "</url>\n";
            }

            return $xml.'</urlset>';
        });

        return $this->xml($body);
    }

    private function about(): string
    {
        return 'RayAcademy adalah ekosistem pendidikan dan pengembangan kompetensi dari Indonesia bagi individu, profesional, dan perusahaan. '
            .'Programnya: Sekolah Kosmetik Indonesia (sains kosmetik, formulasi, pengembangan produk, dan manufaktur), '
            .'doBetter Class (branding dan marketing), amAIzing Class (pembelajaran dan penerapan Artificial Intelligence), '
            .'serta LevelUp Class (entrepreneurship, leadership, dan pengembangan diri). '
            .'Kelas online terbagi per modul dengan pre-test, materi video dan bacaan, serta post-test. RayAcademy juga mengadakan event gratis, offline maupun online.';
    }

    private function contact(): string
    {
        return collect([Setting::get('contact_email'), Setting::get('contact_phone')])->filter()->implode(', ');
    }

    // llms.txt: a plain-language map of the site for AI assistants and AI search (https://llmstxt.org).
    public function llms()
    {
        $body = Cache::remember('llms:'.$this->siteUrl(), 1800, function () {
            $base = $this->siteUrl();
            $out = ['# RayAcademy', '', '> '.$this->about(), ''];
            if ($contact = $this->contact()) {
                $out[] = "Kontak: {$contact}.";
                $out[] = '';
            }
            array_push($out,
                '## Halaman utama',
                "- [Beranda]({$base}/): program RayAcademy, kelas pilihan, event, artikel, dan FAQ",
                "- [Katalog kelas]({$base}/courses): semua kelas, gratis dan berbayar, per kategori",
                "- [Event]({$base}/events): kelas praktik dan workshop gratis, offline maupun online",
                "- [Artikel]({$base}/articles): artikel dan wawasan dari instruktur",
                "- [Konten lengkap]({$base}/llms-full.txt): isi kelas, event, artikel, dan FAQ dalam teks biasa",
            );

            $courses = Course::with('category')->latest()->get();
            if ($courses->isNotEmpty()) {
                $out[] = '';
                $out[] = '## Kelas';
                foreach ($courses as $c) {
                    $meta = collect([$c->category?->name, $c->instructor ? "oleh {$c->instructor}" : null, $c->price > 0 ? 'Rp'.number_format($c->price, 0, ',', '.') : 'gratis'])->filter()->implode(', ');
                    $out[] = "- [{$c->title}]({$base}/course/{$c->slug}) ({$meta}): ".self::plain($c->description, 180);
                }
            }

            $events = Event::published()->upcoming()->get();
            if ($events->isNotEmpty()) {
                $out[] = '';
                $out[] = '## Event mendatang';
                foreach ($events as $e) {
                    $out[] = "- [{$e->title}]({$base}/events/{$e->slug}) ({$e->dateLabel()}, ".($e->isOnline() ? 'online' : $e->venue).', gratis): '.self::plain($e->plainDescription(), 160);
                }
            }

            $articles = Article::published()->with('categories')->latest('published_at')->take(50)->get();
            if ($articles->isNotEmpty()) {
                $out[] = '';
                $out[] = '## Artikel';
                foreach ($articles as $a) {
                    $cat = $a->categories->first()?->name;
                    $out[] = "- [{$a->title}]({$base}/article/{$a->slug})".($cat ? " ({$cat})" : '').($a->excerpt ? ': '.self::plain($a->excerpt, 180) : '');
                }
            }

            $out[] = '';
            $out[] = '## FAQ';
            foreach (config('faq') as $f) {
                $out[] = "- **{$f['q']}** {$f['a']}";
            }

            return implode("\n", $out)."\n";
        });

        return $this->text($body, 1800);
    }

    // llms-full.txt: full plain-text content so AI can answer without scraping HTML.
    public function llmsFull()
    {
        $body = Cache::remember('llms-full:'.$this->siteUrl(), 3600, function () {
            $base = $this->siteUrl();
            $out = ['# RayAcademy - Konten Lengkap', "# Sumber: {$base}", "# Ringkasan singkat: {$base}/llms.txt", '', $this->about()];
            if ($contact = $this->contact()) {
                $out[] = "Kontak: {$contact}.";
            }
            $block = function (string $kind, string $title, string $url) use (&$out) {
                array_push($out, '', '=====', "{$kind}: {$title}", "URL: {$url}", '=====');
            };

            foreach (Course::with(['category', 'sections.lessons'])->latest()->get() as $c) {
                $block('KELAS', $c->title, "{$base}/course/{$c->slug}");
                foreach (['Kategori' => $c->category?->name, 'Instruktur' => $c->instructor, 'Level' => $c->level, 'Harga' => $c->price > 0 ? 'Rp'.number_format($c->price, 0, ',', '.') : 'Gratis'] as $label => $value) {
                    if ($value) {
                        $out[] = "{$label}: {$value}";
                    }
                }
                $out[] = '';
                $out[] = self::plain($c->description);
                if ($c->sections->isNotEmpty()) {
                    $out[] = '';
                    $out[] = 'Modul:';
                    foreach ($c->sections as $s) {
                        $out[] = "- {$s->title}".($s->lessons->isNotEmpty() ? ' ('.$s->lessons->count().' materi)' : '');
                    }
                }
            }

            foreach (Event::published()->latest('event_date')->get() as $e) {
                $block('EVENT', $e->title, "{$base}/events/{$e->slug}");
                $out[] = "Tanggal: {$e->dateLabel()}";
                $out[] = 'Sesi: '.$e->sessions->map(fn ($s) => "{$s->label} {$s->timeLabel()} {$e->timezone}")->implode(', ');
                $out[] = 'Tempat: '.($e->isOnline() ? 'Online' : trim($e->venue.', '.$e->address, ', '));
                $out[] = 'Biaya: Gratis, daftar dengan akun RayAcademy';
                $out[] = '';
                $out[] = $e->plainDescription();
            }

            foreach (Article::published()->with('categories')->latest('published_at')->take(30)->get() as $a) {
                $block('ARTIKEL', $a->title, "{$base}/article/{$a->slug}");
                $out[] = 'Terbit: '.$a->published_at?->toDateString().($a->categories->isNotEmpty() ? ' | Kategori: '.$a->categories->pluck('name')->implode(', ') : '');
                $out[] = self::plain($a->processed_content);
            }

            array_push($out, '', '=====', 'FAQ', '=====');
            foreach (config('faq') as $f) {
                array_push($out, "T: {$f['q']}", "J: {$f['a']}", '');
            }

            return implode("\n", $out)."\n";
        });

        return $this->text($body);
    }

    // ai.txt: AI usage policy, mirrors robots.txt.
    public function aiTxt()
    {
        if (! $this->indexable()) {
            return $this->text("# Non-production host, not for AI use\nUser-Agent: *\nDisallow: /\n");
        }

        $base = $this->siteUrl();
        $lines = ["# ai.txt - RayAcademy ({$base})", '# Kebijakan untuk crawler AI, AI search, dan answer engine.', '', 'User-Agent: *', 'Allow: /'];
        foreach (self::PRIVATE_PATHS as $path) {
            $lines[] = "Disallow: {$path}";
        }
        array_push($lines, '',
            '# Konten publik boleh dipakai untuk pencarian, jawaban, dan grounding faktual dengan atribusi',
            '# ("Menurut RayAcademy") atau tautan ke halaman sumber. Materi kelas berbayar tidak untuk dilatih atau disalin.',
            '# Sumber yang disarankan:',
            "#   {$base}/llms.txt",
            "#   {$base}/llms-full.txt",
            "#   {$base}/sitemap.xml",
        );

        return $this->text(implode("\n", $lines)."\n", 86400);
    }

    // RSS of new articles, courses and events, read by feed readers and AI search to find fresh content.
    public function feed()
    {
        $body = Cache::remember('feed:'.$this->siteUrl(), 600, function () {
            $base = $this->siteUrl();
            $items = Article::published()->with('categories')->latest('published_at')->take(30)->get()->map(fn ($a) => [
                'title' => $a->title, 'link' => "{$base}/article/{$a->slug}", 'date' => $a->published_at, 'category' => $a->categories->first()?->name,
                'description' => $a->excerpt ? self::plain($a->excerpt, 300) : self::plain($a->processed_content, 300),
            ])->concat(Course::with('category')->latest()->take(20)->get()->map(fn ($c) => [
                'title' => "Kelas: {$c->title}", 'link' => "{$base}/course/{$c->slug}", 'date' => $c->created_at, 'category' => $c->category?->name, 'description' => self::plain($c->description, 300),
            ]))->concat(Event::published()->latest()->take(20)->get()->map(fn ($e) => [
                'title' => "Event: {$e->title} ({$e->dateLabel()})", 'link' => "{$base}/events/{$e->slug}", 'date' => $e->created_at, 'category' => 'Event', 'description' => self::plain($e->plainDescription(), 300),
            ]))->sortByDesc('date')->take(40);

            $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"><channel>'
                .'<title>RayAcademy</title><link>'.$base.'/</link><description>Artikel, kelas, dan event terbaru dari RayAcademy</description><language>id-ID</language>'
                .'<atom:link href="'.$base.'/feed.xml" rel="self" type="application/rss+xml"/>'."\n";
            foreach ($items as $i) {
                $xml .= '<item><title>'.e($i['title']).'</title><link>'.e($i['link']).'</link><guid isPermaLink="true">'.e($i['link']).'</guid>'
                    .($i['date'] ? '<pubDate>'.$i['date']->toRssString().'</pubDate>' : '')
                    .($i['category'] ? '<category>'.e($i['category']).'</category>' : '')
                    .'<description>'.e($i['description'])."</description></item>\n";
            }

            return $xml.'</channel></rss>';
        });

        return response($body, 200, ['Content-Type' => 'application/rss+xml; charset=utf-8', 'Cache-Control' => 'public, max-age=600']);
    }

    public function opensearch()
    {
        $base = $this->siteUrl();
        $body = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">'
            .'<ShortName>RayAcademy</ShortName><Description>Cari kelas dan artikel di RayAcademy</Description>'
            .'<InputEncoding>UTF-8</InputEncoding><Language>id-ID</Language>'
            .'<Image width="16" height="16" type="image/png">'.$base.'/favicon.png</Image>'
            .'<Url type="text/html" template="'.$base.'/search?q={searchTerms}"/></OpenSearchDescription>';

        return response($body, 200, ['Content-Type' => 'application/opensearchdescription+xml; charset=utf-8', 'Cache-Control' => 'public, max-age=86400']);
    }

    // IndexNow key file: search engines fetch it to confirm our pings come from this site.
    public function indexNowKey()
    {
        abort_unless($key = config('seo.indexnow_key'), 404);

        return $this->text($key, 86400);
    }
}
