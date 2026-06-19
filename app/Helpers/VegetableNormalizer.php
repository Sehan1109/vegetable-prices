<?php

namespace App\Helpers;

class VegetableNormalizer
{
    /**
     * Map of lowercase CBSL PDF name patterns → vegetable_id slugs.
     * Keys are substrings that may appear in the raw PDF text.
     * Longer/more-specific keys are matched with priority (longest match wins).
     */
    private static array $map = [
        // Onions & Alliums
        'big onion (imp)'      => 'big-onion-imported',
        'big onion (local)'    => 'big-onion-local',
        'big onion'            => 'big-onion',
        'red onion (imp)'      => 'red-onion-imported',
        'red onion (lmp)'      => 'red-onion-imported',   // typo in PDF
        'red onion (local)'    => 'red-onion-local',
        'red onion'            => 'red-onion',
        'small onion'          => 'small-onion',
        'imported onion'       => 'red-onion-imported',

        // Leafy / Greens
        'leeks'                => 'leeks',
        'gotukola'             => 'gotukola',
        'mukunuwenna'          => 'mukunuwenna',
        'thampala'             => 'thampala',
        'kohila'               => 'kohila',
        'kathurumurunga'       => 'kathurumurunga',
        'spinach'              => 'spinach',
        'cabbage'              => 'cabbage',

        // Gourds & Squash
        'bitter gourd'         => 'bitter-gourd',
        'snake gourd'          => 'snake-gourd',
        'ridge gourd'          => 'ridge-gourd',
        'ash plantain'         => 'ash-plantain',
        'pumpkin'              => 'pumpkin',
        'luffa'                => 'ridge-gourd',

        // Beans
        'green beans'          => 'green-beans',
        'wing beans'           => 'wing-beans',
        'cowpeas'              => 'cowpea',
        'cowpea'               => 'cowpea',
        'long beans'           => 'long-beans',
        'beans'                => 'green-beans',          // catch-all after specific matches

        // Root Vegetables
        'carrot'               => 'carrot',
        'beet root'            => 'beetroot',
        'beetroot'             => 'beetroot',
        'radish'               => 'radish',
        'manioc'               => 'manioc',
        'sweet potato'         => 'sweet-potato',
        'potato (local)'       => 'potato-local',
        'potato (imp)'         => 'potato-imported',
        'potato'               => 'potato',

        // Fruity Veg
        'brinjal'              => 'brinjal',
        'tomato'               => 'tomato',
        'capsicum'             => 'capsicum',
        'green chilli'         => 'green-chilli',
        'dried chilli (imp)'   => 'dry-chilli-imported',
        'dry chilli'           => 'dry-chilli',
        'chilli'               => 'chilli',
        'okra'                 => 'okra',
        'ladies fingers'       => 'okra',

        // Spices
        'ginger'               => 'ginger',
        'garlic'               => 'garlic',
        'turmeric'             => 'turmeric',
        'goraka'               => 'goraka',
        'pandan'               => 'pandan',
        'curry leaves'         => 'curry-leaves',
        'lemon grass'          => 'lemon-grass',

        // Fruits (used as veg or commonly traded)
        'banana (sour)'        => 'banana',
        'banana'               => 'banana',
        'plantain'             => 'plantain',
        'jak'                  => 'jak',
        'bread fruit'          => 'bread-fruit',
        'coconut oil'          => 'coconut-oil',
        'coconut (avg.)'       => 'coconut',
        'coconut'              => 'coconut',
        'lime'                 => 'lime',
        'lemon'                => 'lemon',
        'papaw'                => 'papaya',
        'papaya'               => 'papaya',
        'pineapple'            => 'pineapple',
        'apple (imp)'          => 'apple-imported',
        'orange (imp)'         => 'orange-imported',

        // Pulses & Cereals
        'green gram'           => 'green-gram',
        'black gram'           => 'black-gram',
        'red dhal'             => 'red-lentils',
        'lentils'              => 'lentils',
        'red lentils'          => 'red-lentils',
        'chickpea'             => 'chickpea',

        // Rice varieties
        'samba'                => 'rice-samba',
        'ponni samba (imp)'    => 'rice-ponni-samba-imported',
        'kekulu (white) (imp)' => 'rice-kekulu-white-imported',
        'kekulu (red)'         => 'rice-kekulu-red',
        'kekulu (white)'       => 'rice-kekulu-white',
        'nadu (imp)'           => 'rice-nadu-imported',
        'nadu'                 => 'rice-nadu',

        // Other grocery
        'sugar (white)'        => 'sugar',
        'egg (white)'          => 'egg',

        // Fish
        'kelawalla'            => 'fish-kelawalla',
        'thalapath'            => 'fish-thalapath',
        'balaya'               => 'fish-balaya',
        'paraw'                => 'fish-paraw',
        'salaya'               => 'fish-salaya',
        'hurulla'              => 'fish-hurulla',
        'linna'                => 'fish-linna',
        'katta (imp)'          => 'fish-katta-imported',
        'sprat (imp)'          => 'fish-sprat-imported',
    ];

    /**
     * Map of CBSL market name patterns → market_id slugs.
     */
    private static array $marketMap = [
        'pettah'        => 'pettah',
        'manning'       => 'manning',
        'dambulla'      => 'dambulla',
        'narahenpita'   => 'narahenpita',
        'peliyagoda'    => 'peliyagoda',
        'negombo'       => 'negombo',
        'kurunegala'    => 'kurunegala',
        'kandy'         => 'kandy',
        'nuwara eliya'  => 'nuwara-eliya',
        'matale'        => 'matale',
        'galle'         => 'galle',
        'hambantota'    => 'hambantota',
        'anuradhapura'  => 'anuradhapura',
        'jaffna'        => 'jaffna',
        'trincomalee'   => 'trincomalee',
        'badulla'       => 'badulla',
        'ratnapura'     => 'ratnapura',
        'kalutara'      => 'kalutara',
        'colombo'       => 'pettah',    // CBSL sometimes writes Colombo
    ];

    /**
     * Normalise a raw vegetable/commodity name from PDF to a slug ID.
     * Returns null if no match found.
     */
    public static function normalize(string $rawName): ?string
    {
        $lower = strtolower(trim($rawName));
        $lower = preg_replace('/\s+/', ' ', $lower); // collapse multiple spaces

        // Try exact match first
        if (isset(self::$map[$lower])) {
            return self::$map[$lower];
        }

        // Try longest-key substring match (most-specific wins)
        $best    = null;
        $bestLen = 0;
        foreach (self::$map as $pattern => $slug) {
            if (str_contains($lower, $pattern) && strlen($pattern) > $bestLen) {
                $best    = $slug;
                $bestLen = strlen($pattern);
            }
        }

        return $best;
    }

    /**
     * Normalise a raw market name from PDF to a slug ID.
     * Returns a slugified fallback if no match found.
     */
    public static function normalizeMarket(string $rawMarket): string
    {
        $lower = strtolower(trim($rawMarket));

        foreach (self::$marketMap as $pattern => $slug) {
            if (str_contains($lower, $pattern)) {
                return $slug;
            }
        }

        // Fallback: slugify the raw name
        return strtolower(preg_replace('/[^a-z0-9]+/i', '-', trim($rawMarket)));
    }

    /**
     * Return all known vegetable slugs (useful for seeding / testing).
     */
    public static function allSlugs(): array
    {
        return array_unique(array_values(self::$map));
    }
}
