<?php

namespace App\Services;

use App\Models\Market;
use App\Models\SeoPage;
use App\Models\Vegetable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SitemapGeneratorService
{
    protected $chunkSize = 40000; // Safe limit under 50,000

    public function generate()
    {
        Log::info("Starting sitemap generation...");

        try {
            $urls = $this->collectAllUrls();
            $total = count($urls);
            
            Log::info("Loaded {$total} total URLs for sitemap.");

            if ($total == 0) {
                Log::warning("No URLs found for sitemap generation.");
                return;
            }

            Log::info("Generating XML...");

            if ($total <= $this->chunkSize) {
                $this->generateSingleSitemap($urls);
            } else {
                $this->generateSitemapIndex($urls, $total);
            }

            $this->updateRobotsTxt();

            Log::info("Generation completed successfully.");
            
        } catch (\Exception $e) {
            Log::error("ERROR: Unable to write sitemap.xml. Reason: " . $e->getMessage());
            throw $e;
        }
    }

    protected function collectAllUrls(): array
    {
        $urls = [];
        $baseUrl = rtrim(config('app.url'), '/');

        // Static / Base Pages
        $staticPages = [
            '/',
            '/prices',
            '/trends',
            '/heatmap',
            '/about',
            '/pipeline'
        ];

        foreach ($staticPages as $page) {
            $urls[] = [
                'loc' => $baseUrl . $page,
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0'
            ];
        }

        // SEO Pages
        $seoPages = SeoPage::select('slug', 'updated_at')->get();
        foreach ($seoPages as $page) {
            $urls[] = [
                'loc' => $baseUrl . '/' . $page->slug,
                'lastmod' => $page->updated_at->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.8'
            ];
        }

        // Markets
        $markets = Market::select('slug', 'updated_at')->get();
        foreach ($markets as $market) {
            if ($market->slug) {
                $urls[] = [
                    'loc' => $baseUrl . '/markets/' . $market->slug,
                    'lastmod' => $market->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7'
                ];
            }
        }

        // Vegetables
        $vegetables = Vegetable::select('slug', 'updated_at')->get();
        foreach ($vegetables as $vegetable) {
            if ($vegetable->slug) {
                $urls[] = [
                    'loc' => $baseUrl . '/vegetables/' . $vegetable->slug,
                    'lastmod' => $vegetable->updated_at->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.7'
                ];
            }
        }

        return $urls;
    }

    protected function generateSingleSitemap(array $urls)
    {
        $xml = $this->buildSitemapXml($urls);
        $path = public_path('sitemap.xml');
        
        Log::info("Saving to {$path}");
        
        if (File::put($path, $xml) === false) {
            throw new \Exception("Permission denied or disk full while writing {$path}");
        }

        // Validate creation
        if (!File::exists($path)) {
            throw new \Exception("Validation failed: file {$path} does not exist after writing.");
        }
        
        // Remove index and chunked sitemaps if they existed previously
        if (File::exists(public_path('sitemap-index.xml'))) {
            File::delete(public_path('sitemap-index.xml'));
            $this->cleanupChunkedSitemaps();
        }
    }

    protected function generateSitemapIndex(array $urls, int $total)
    {
        $chunks = ceil($total / $this->chunkSize);
        $baseUrl = config('app.url');

        // Generate individual chunked sitemaps
        for ($i = 0; $i < $chunks; $i++) {
            $chunkUrls = array_slice($urls, $i * $this->chunkSize, $this->chunkSize);
            $xml = $this->buildSitemapXml($chunkUrls);
            $path = public_path("sitemap-".($i + 1).".xml");
            
            if (File::put($path, $xml) === false) {
                throw new \Exception("Permission denied while writing {$path}");
            }
            if (!File::exists($path)) {
                throw new \Exception("Validation failed: file {$path} does not exist after writing.");
            }
        }

        // Generate Index
        $indexXml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $indexXml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        for ($i = 0; $i < $chunks; $i++) {
            $indexXml .= '  <sitemap>' . PHP_EOL;
            $indexXml .= '    <loc>' . $baseUrl . '/sitemap-'.($i + 1).'.xml</loc>' . PHP_EOL;
            $indexXml .= '    <lastmod>' . now()->toAtomString() . '</lastmod>' . PHP_EOL;
            $indexXml .= '  </sitemap>' . PHP_EOL;
        }

        $indexXml .= '</sitemapindex>';
        
        $indexPath = public_path('sitemap-index.xml');
        Log::info("Saving index sitemap to {$indexPath}");
        
        if (File::put($indexPath, $indexXml) === false) {
            throw new \Exception("Permission denied while writing {$indexPath}");
        }
        if (!File::exists($indexPath)) {
            throw new \Exception("Validation failed: file {$indexPath} does not exist after writing.");
        }
        
        // Ensure main sitemap.xml doesn't exist if we are using index
        if (File::exists(public_path('sitemap.xml'))) {
            File::delete(public_path('sitemap.xml'));
        }
    }

    protected function buildSitemapXml(array $urls)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        foreach ($urls as $url) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8') . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . PHP_EOL;
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }
        
        $xml .= '</urlset>';
        return $xml;
    }

    protected function cleanupChunkedSitemaps()
    {
        $files = File::glob(public_path('sitemap-*.xml'));
        foreach ($files as $file) {
            // Keep sitemap-index if it's there
            if (basename($file) !== 'sitemap-index.xml') {
                File::delete($file);
            }
        }
    }

    protected function updateRobotsTxt()
    {
        $robotsPath = public_path('robots.txt');
        $sitemapUrl = config('app.url') . '/sitemap.xml';
        
        // If we chunked, use sitemap-index.xml
        if (File::exists(public_path('sitemap-index.xml'))) {
            $sitemapUrl = config('app.url') . '/sitemap-index.xml';
        }

        $sitemapLine = "Sitemap: {$sitemapUrl}";

        if (File::exists($robotsPath)) {
            $content = File::get($robotsPath);
            if (!str_contains($content, 'Sitemap:')) {
                File::append($robotsPath, PHP_EOL . $sitemapLine . PHP_EOL);
                Log::info("Added Sitemap directive to existing robots.txt");
            }
        } else {
            $content = "User-agent: *" . PHP_EOL;
            $content .= "Disallow: /admin/" . PHP_EOL . PHP_EOL;
            $content .= $sitemapLine . PHP_EOL;
            
            if (File::put($robotsPath, $content) === false) {
                Log::warning("Could not create robots.txt automatically.");
            } else {
                Log::info("Created robots.txt with Sitemap directive.");
            }
        }
    }
}
