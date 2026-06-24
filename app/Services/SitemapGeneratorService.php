<?php

namespace App\Services;

use App\Models\SeoPage;
use Illuminate\Support\Facades\File;

class SitemapGeneratorService
{
    protected $chunkSize = 40000; // Safe limit under 50,000

    public function generate()
    {
        $total = SeoPage::count();

        if ($total == 0) {
            return;
        }

        if ($total <= $this->chunkSize) {
            $this->generateSingleSitemap();
        } else {
            $this->generateSitemapIndex($total);
        }
    }

    protected function generateSingleSitemap()
    {
        $pages = SeoPage::select('slug', 'updated_at')->get();
        $xml = $this->buildSitemapXml($pages);
        
        File::put(public_path('sitemap.xml'), $xml);
        
        // Remove index and chunked sitemaps if they existed previously
        if (File::exists(public_path('sitemap-index.xml'))) {
            File::delete(public_path('sitemap-index.xml'));
            $this->cleanupChunkedSitemaps();
        }
    }

    protected function generateSitemapIndex($total)
    {
        $chunks = ceil($total / $this->chunkSize);
        $baseUrl = config('app.url');

        // Generate individual chunked sitemaps
        for ($i = 0; $i < $chunks; $i++) {
            $pages = SeoPage::select('slug', 'updated_at')
                ->offset($i * $this->chunkSize)
                ->limit($this->chunkSize)
                ->get();
            
            $xml = $this->buildSitemapXml($pages);
            File::put(public_path("sitemap-".($i + 1).".xml"), $xml);
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
        
        File::put(public_path('sitemap-index.xml'), $indexXml);
        
        // Set main sitemap.xml to point to index or be replaced by it (Google supports sitemap-index as sitemap.xml too)
        // Here we just keep sitemap-index.xml as the main index.
    }

    protected function buildSitemapXml($pages)
    {
        $baseUrl = rtrim(config('app.url'), '/');
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        foreach ($pages as $page) {
            $xml .= '  <url>' . PHP_EOL;
            $xml .= '    <loc>' . $baseUrl . '/' . $page->slug . '</loc>' . PHP_EOL;
            $xml .= '    <lastmod>' . $page->updated_at->toAtomString() . '</lastmod>' . PHP_EOL;
            $xml .= '    <changefreq>daily</changefreq>' . PHP_EOL;
            $xml .= '    <priority>0.8</priority>' . PHP_EOL;
            $xml .= '  </url>' . PHP_EOL;
        }
        
        $xml .= '</urlset>';
        return $xml;
    }

    protected function cleanupChunkedSitemaps()
    {
        $files = File::glob(public_path('sitemap-*.xml'));
        foreach ($files as $file) {
            File::delete($file);
        }
    }
}
