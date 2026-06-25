<?php

namespace App\Http\Controllers;

use App\Models\SeoPage;
use App\Models\PriceRecord;
use App\Services\SeoPageGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SeoPageController extends Controller
{
    public function show(string $slug, SeoPageGeneratorService $generator)
    {
        // Cache the SEO page ID for 30 days to avoid repeated DB lookups.
        // We cache the ID, not the rendered HTML, to avoid stale view compilation issues.
        $cacheKey = 'seo_page_id_' . md5($slug);

        $pageId = Cache::remember($cacheKey, now()->addDays(30), function () use ($slug, $generator) {
            $page = SeoPage::where('slug', $slug)->first();

            if (!$page) {
                Log::info("SEO Page not found in DB for slug: [{$slug}]. Attempting dynamic generation.");

                if (!preg_match('/^([0-9]{4}-[0-9]{2}-[0-9]{2})-(.+)-price-(.+)$/', $slug, $matches)) {
                    Log::warning("Slug [{$slug}] does not match expected pattern. Aborting.");
                    return null;
                }

                $date          = $matches[1];
                $vegetableSlug = $matches[2];
                $marketSlug    = $matches[3];

                $priceRecord = PriceRecord::where('date', $date)
                    ->where('vegetable_id', $vegetableSlug)
                    ->where('market_id', $marketSlug)
                    ->first();

                if (!$priceRecord) {
                    Log::warning("No PriceRecord found in DB for slug: [{$slug}]. Date={$date}, Veg={$vegetableSlug}, Market={$marketSlug}");
                    return null;
                }

                Log::info("Found PriceRecord (id={$priceRecord->id}). Generating SEO Page for [{$slug}].");
                $page = $generator->generateForPriceRecord($priceRecord);
            }

            return $page?->id;
        });

        if (!$pageId) {
            Log::error("Could not resolve SEO page for slug: [{$slug}]");
            abort(404);
        }

        // Load the full page with relationships fresh each time (Laravel caches compiled views separately)
        $page = SeoPage::with(['market', 'vegetable', 'priceRecord'])->findOrFail($pageId);

        // Historical trend (last 10 days for this vegetable and market)
        $historicalPrices = PriceRecord::where('market_id', $page->market_id)
            ->where('vegetable_id', $page->vegetable_id)
            ->where('date', '<=', $page->date)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        // Related markets (markets selling this vegetable on this date)
        $relatedMarkets = SeoPage::with('market')
            ->where('vegetable_id', $page->vegetable_id)
            ->where('date', $page->date)
            ->where('id', '!=', $page->id)
            ->limit(5)
            ->get();

        // Related vegetables (vegetables in this market on this date)
        $relatedVegetables = SeoPage::with('vegetable')
            ->where('market_id', $page->market_id)
            ->where('date', $page->date)
            ->where('id', '!=', $page->id)
            ->limit(5)
            ->get();

        return view('seo-page', compact('page', 'historicalPrices', 'relatedMarkets', 'relatedVegetables'));
    }
}
