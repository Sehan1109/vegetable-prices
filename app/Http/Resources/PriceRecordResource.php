<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\SeoUrlService;
use Illuminate\Support\Str;

class PriceRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $seoService = app(SeoUrlService::class);
        
        $vegetableName = $this->vegetable->name ?? Str::ucfirst($this->vegetable_id);
        $marketName = $this->market->name ?? Str::ucfirst($this->market_id);
        
        $slug = $seoService->generateSlug($this->date, $this->vegetable_id, $this->market_id);

        return [
            // Required by the prompt
            'vegetable' => $vegetableName,
            'price_average' => $this->price,
            'market' => $marketName,
            'date' => $this->date,
            'slug' => $slug,
            
            // Required for frontend backward compatibility
            'price' => $this->price,
            'priceYesterday' => $this->price_yesterday,
            'changePercent' => $this->change_percent,
            'priceYearAgo' => $this->price_year_ago ?? 0,
            'changePercentYear' => $this->change_percent_year ?? 0
        ];
    }
}
