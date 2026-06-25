<?php

namespace App\Services;

use Illuminate\Support\Str;

class SeoUrlService
{
    /**
     * Generate the SEO slug dynamically.
     * Example: 2026-07-06-carrot-price-dambulla
     *
     * @param string $date
     * @param string $vegetableName
     * @param string $marketName
     * @return string
     */
    public function generateSlug(string $date, string $vegetableName, string $marketName): string
    {
        return sprintf(
            '%s-%s-price-%s',
            $date,
            Str::slug($vegetableName),
            Str::slug($marketName)
        );
    }
}
