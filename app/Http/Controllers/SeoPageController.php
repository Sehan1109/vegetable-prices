<?php

namespace App\Http\Controllers;

use App\Models\SeoPage;
use App\Models\PriceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SeoPageController extends Controller
{
    public function show($slug)
    {
        // Cache the entire rendered page for 30 days
        // Since URL has the date and data won't change, we can cache it forever.
        $cacheKey = 'seo_page_' . md5($slug);
        
        $html = Cache::remember($cacheKey, now()->addDays(30), function () use ($slug) {
            $page = SeoPage::with(['market', 'vegetable', 'priceRecord'])->where('slug', $slug)->firstOrFail();
            
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

            return view('seo-page', compact('page', 'historicalPrices', 'relatedMarkets', 'relatedVegetables'))->render();
        });

        return response($html);
    }
}
