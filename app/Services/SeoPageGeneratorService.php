<?php

namespace App\Services;

use App\Models\PriceRecord;
use App\Models\SeoPage;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SeoPageGeneratorService
{
    /**
     * Generate an SEO Page record from a Price Record
     */
    public function generateForPriceRecord(PriceRecord $priceRecord): ?SeoPage
    {
        $marketSlug = $priceRecord->market_id;
        $vegetableSlug = $priceRecord->vegetable_id;

        // Auto-create Market if it doesn't exist
        $market = \App\Models\Market::firstOrCreate(
            ['slug' => $marketSlug],
            ['name' => Str::title(str_replace('-', ' ', $marketSlug))]
        );

        // Auto-create Vegetable if it doesn't exist
        $vegetable = \App\Models\Vegetable::firstOrCreate(
            ['slug' => $vegetableSlug],
            ['name' => Str::title(str_replace('-', ' ', $vegetableSlug))]
        );

        $date = Carbon::parse($priceRecord->date);
        
        // Example slug: /2026-07-06-beans-price-dambulla
        $slug = sprintf(
            '%s-%s-price-%s',
            $date->format('Y-m-d'),
            $vegetable->slug ?? Str::slug($vegetable->name),
            $market->slug ?? Str::slug($market->name)
        );

        // Title: Beans Price in Dambulla Today - 06 July 2026
        $title = sprintf(
            "%s Price in %s Today - %s",
            $vegetable->name,
            $market->name,
            $date->format('d F Y')
        );

        // Meta Description: Latest Beans Price in Dambulla Market on 06 July 2026...
        $metaDescription = sprintf(
            "Latest %s Price in %s Market on %s. Average price, minimum price, maximum price and historical trend data from official Sri Lankan market reports.",
            $vegetable->name,
            $market->name,
            $date->format('d F Y')
        );

        // Create or Update SEO Page
        return SeoPage::updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $title,
                'meta_description' => $metaDescription,
                'market_id' => $market->id,
                'vegetable_id' => $vegetable->id,
                'price_record_id' => $priceRecord->id,
                'date' => $priceRecord->date
            ]
        );
    }
}
