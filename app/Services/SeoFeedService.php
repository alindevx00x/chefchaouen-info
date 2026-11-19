<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Place;
use App\Models\PlaceCategory;
use App\Models\PublicPage;
use App\Models\Setting;
use App\Models\TouristRoute;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SeoFeedService
{
    public static function flushCaches(): void
    {
        Cache::put('seo_feeds_version', Str::random(8), 86400);
    }

    protected function resolveBaseUrl(?string $requestBase = null): string
    {
        $configured = trim((string) Setting::getValue('seo_canonical_base', ''));
        if ($configured !== '' && filter_var($configured, FILTER_VALIDATE_URL)) {
            return rtrim($configured, '/');
        }

        if ($requestBase) {
            return rtrim($requestBase, '/');
        }

        return rtrim((string) config('app.url', 'https://chefchaouen.info'), '/');
    }

    public function buildSitemapXml(?string $requestBase = null): string
    {
        $base = $this->resolveBaseUrl($requestBase);
        $version = Cache::get('seo_feeds_version', '1');
        $cacheKey = 'sitemap_xml_' . $version . '_' . md5($base);

        return Cache::remember($cacheKey, 3600, function () use ($base) {
            $urls = [];
            $today = now()->toAtomString();

            $staticPaths = [
                ['path' => '/', 'changefreq' => 'daily', 'priority' => '1.0', 'lastmod' => $today],
                ['path' => '/places', 'changefreq' => 'daily', 'priority' => '0.9', 'lastmod' => $today],
                ['path' => '/routes', 'changefreq' => 'weekly', 'priority' => '0.9', 'lastmod' => $today],
                ['path' => '/map', 'changefreq' => 'weekly', 'priority' => '0.9', 'lastmod' => $today],
                ['path' => '/gallery', 'changefreq' => 'weekly', 'priority' => '0.7', 'lastmod' => $today],
                ['path' => '/blog', 'changefreq' => 'weekly', 'priority' => '0.8', 'lastmod' => $today],
                ['path' => '/faq', 'changefreq' => 'monthly', 'priority' => '0.7', 'lastmod' => $today],
                ['path' => '/contact', 'changefreq' => 'monthly', 'priority' => '0.6', 'lastmod' => $today],
                ['path' => '/suggest', 'changefreq' => 'monthly', 'priority' => '0.6', 'lastmod' => $today],
            ];

            foreach ($staticPaths as $entry) {
                $urls[] = [
                    'loc' => $base . ($entry['path'] === '/' ? '/' : $entry['path']),
                    'lastmod' => $entry['lastmod'],
                    'changefreq' => $entry['changefreq'],
                    'priority' => $entry['priority'],
                ];
            }

            PlaceCategory::where('is_active', true)->orderBy('sort_order')->get()->each(function ($cat) use (&$urls, $base) {
                $urls[] = [
                    'loc' => $base . '/places?category=' . urlencode($cat->slug),
                    'lastmod' => optional($cat->updated_at)->toAtomString() ?: now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            });

            Place::where('status', 'published')->orderByDesc('updated_at')->get()->each(function ($place) use (&$urls, $base) {
                $urls[] = [
                    'loc' => $base . '/places/' . $place->slug,
                    'lastmod' => optional($place->updated_at)->toAtomString() ?: now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => $place->is_featured ? '0.9' : '0.8',
                ];
            });

            TouristRoute::where('status', 'published')->orderByDesc('updated_at')->get()->each(function ($route) use (&$urls, $base) {
                $urls[] = [
                    'loc' => $base . '/routes/' . $route->slug,
                    'lastmod' => optional($route->updated_at)->toAtomString() ?: now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => $route->is_featured ? '0.9' : '0.8',
                ];
            });

            PublicPage::where('is_published', true)->orderBy('sort_order')->get()->each(function ($page) use (&$urls, $base) {
                $urls[] = [
                    'loc' => $base . '/page/' . $page->slug,
                    'lastmod' => optional($page->updated_at)->toAtomString() ?: now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ];
            });

            BlogPost::where('is_published', true)->orderByDesc('published_at')->get()->each(function ($post) use (&$urls, $base) {
                $urls[] = [
                    'loc' => $base . '/blog/' . $post->slug,
                    'lastmod' => optional($post->updated_at ?? $post->published_at)->toAtomString() ?: now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ];
            });

            $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

            foreach ($urls as $item) {
                $xml .= "  <url>\n";
                $xml .= '    <loc>' . htmlspecialchars($item['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
                $xml .= '    <lastmod>' . htmlspecialchars($item['lastmod'], ENT_XML1, 'UTF-8') . "</lastmod>\n";
                $xml .= '    <changefreq>' . htmlspecialchars($item['changefreq'], ENT_XML1, 'UTF-8') . "</changefreq>\n";
                $xml .= '    <priority>' . htmlspecialchars($item['priority'], ENT_XML1, 'UTF-8') . "</priority>\n";
                $xml .= "  </url>\n";
            }

            $xml .= '</urlset>';

            return $xml;
        });
    }

    public function buildLlmsTxt(?string $requestBase = null): string
    {
        $base = $this->resolveBaseUrl($requestBase);
        $version = Cache::get('seo_feeds_version', '1');
        $cacheKey = 'llms_txt_' . $version . '_' . md5($base);

        return Cache::remember($cacheKey, 3600, function () use ($base) {
            $siteName = Setting::getValue('site_name', 'Chefchaouen.info');
            $tagline = Setting::getValue('seo_meta_description', 'Curated travel directory, interactive map, Rif Mountain hiking routes, and AI travel guide for Chefchaouen, Morocco.');

            $lines = [
                "# {$siteName} | Deeper Than Blue",
                '',
                "> {$tagline}",
                '',
                '## Geographic Context (GEO)',
                '- Destination: Chefchaouen (The Blue Pearl), Rif Mountains, Northern Morocco',
                '- Coordinates: 35.1688 N, -5.2636 W (Altitude ~600m)',
                '- Region Code: MA-01 (Tanger-Tetouan-Al Hoceima)',
                "- Interactive Map: {$base}/map",
                "- Sitemap: {$base}/sitemap.xml",
                '',
                '## Key Travel Guides & Pages',
            ];

            PublicPage::where('is_published', true)->orderBy('sort_order')->get()->each(function ($page) use (&$lines, $base) {
                $summary = Str::limit(trim((string) ($page->subtitle ?: strip_tags((string) $page->content))), 140);
                $lines[] = "- [{$page->title}]({$base}/page/{$page->slug}): {$summary}";
            });

            $lines[] = '';
            $lines[] = '## Suggested Tourist Routes (Inside Medina & Rif Mountains)';
            TouristRoute::where('status', 'published')->orderByDesc('is_featured')->get()->each(function ($route) use (&$lines, $base) {
                $lines[] = "- [{$route->title}]({$base}/routes/{$route->slug}) ({$route->route_type_label}, {$route->difficulty}, {$route->estimated_walking_time}): {$route->short_description}";
            });

            $lines[] = '';
            $lines[] = '## Directory Categories';
            PlaceCategory::where('is_active', true)->orderBy('sort_order')->get()->each(function ($cat) use (&$lines, $base) {
                $lines[] = "- [{$cat->name}]({$base}/places?category={$cat->slug}): {$cat->subtitle}";
            });

            $lines[] = '';
            $lines[] = '## Top Featured Places';
            Place::with('category')->where('status', 'published')->orderByDesc('is_featured')->orderByDesc('rating_avg')->take(25)->get()->each(function ($place) use (&$lines, $base) {
                $cat = $place->category->name ?? 'Place';
                $lines[] = "- [{$place->title}]({$base}/places/{$place->slug}) ({$cat}, {$place->neighborhood}, {$place->price_range}): {$place->short_description}";
            });

            $lines[] = '';
            $lines[] = '## Travel Journal Articles';
            BlogPost::where('is_published', true)->latest('published_at')->take(15)->get()->each(function ($post) use (&$lines, $base) {
                $lines[] = "- [{$post->title}]({$base}/blog/{$post->slug}): {$post->excerpt}";
            });

            $lines[] = '';
            $lines[] = '## Common Visitor FAQs';
            Faq::where('is_active', true)->orderBy('sort_order')->take(12)->get()->each(function ($faq) use (&$lines) {
                $lines[] = "- **{$faq->question}**: {$faq->answer}";
            });

            return implode("\n", $lines) . "\n";
        });
    }
}
