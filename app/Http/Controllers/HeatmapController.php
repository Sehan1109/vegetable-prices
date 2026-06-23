<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PriceRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HeatmapController extends Controller
{
    /**
     * Map backend market_id values → frontend district IDs.
     * One district can aggregate multiple markets.
     */
    private array $marketToDistrict = [
        'narahenpita'   => 'colombo',
        'pettah'        => 'colombo',
        'peliyagoda'    => 'colombo',
        'meegoda'       => 'colombo',
        'negombo'       => 'colombo',
        'dambulla'      => 'dambulla',
        'norochchole'   => 'dambulla',      // Norochcholai is North Central, close to Dambulla trade zone
        'thambuththegama' => 'anuradhapura',
        'kandy'         => 'kandy',
        'nuwara-eliya'  => 'nuwara_eliya',
        'keppetipola'   => 'nuwara_eliya',  // Keppetipola is Uva highlands, grouped with Nuwara Eliya
        'bandarawela'   => 'badulla',
    ];

    /**
     * Static geographic/agrarian metadata per frontend district.
     * Prices are injected from the database.
     */
    private array $districtMeta = [
        'jaffna'        => ['name' => 'Jaffna',               'localNameSi' => 'යාපනය',         'localNameTa' => 'யாழ்ப்பாணம்',    'province' => 'Northern Province',      'role' => 'Production',    'cx' => 120, 'cy' => 110, 'r' => 20, 'primaryCrops' => ['Red Onion', 'Green Chillies', 'Ladies Fingers']],
        'anuradhapura'  => ['name' => 'Anuradhapura',         'localNameSi' => 'අනුරාධපුරය',    'localNameTa' => 'அனுராதபுரம்',     'province' => 'North Central Province', 'role' => 'Production',    'cx' => 160, 'cy' => 300, 'r' => 24, 'primaryCrops' => ['Pumpkin', 'Ladies Fingers', 'Manioc']],
        'dambulla'      => ['name' => 'Dambulla Hub',         'localNameSi' => 'දඹුල්ල',        'localNameTa' => 'தம்புள்ளை',       'province' => 'Central Province',       'role' => 'Distribution', 'cx' => 190, 'cy' => 420, 'r' => 28, 'primaryCrops' => ['Tomato', 'Pumpkin', 'Capsicum']],
        'puttalam'      => ['name' => 'Puttalam',             'localNameSi' => 'පුත්තලම',       'localNameTa' => 'புத்தளம்',         'province' => 'North Western Province', 'role' => 'Production',    'cx' => 90,  'cy' => 400, 'r' => 22, 'primaryCrops' => ['Red Onion', 'Brinjal']],
        'trincomalee'   => ['name' => 'Trincomalee',          'localNameSi' => 'ත්‍රිකුණාමලය', 'localNameTa' => 'திருகோணமலை',      'province' => 'Eastern Province',       'role' => 'Distribution', 'cx' => 260, 'cy' => 280, 'r' => 20, 'primaryCrops' => ['Brinjal', 'Tomato']],
        'kandy'         => ['name' => 'Kandy',                'localNameSi' => 'මහනුවර',        'localNameTa' => 'கண்டி',            'province' => 'Central Province',       'role' => 'Distribution', 'cx' => 190, 'cy' => 500, 'r' => 22, 'primaryCrops' => ['Leeks', 'Cabbage', 'Beans']],
        'nuwara_eliya'  => ['name' => 'Nuwara Eliya',         'localNameSi' => 'නුවරඑළිය',      'localNameTa' => 'நுவரெலியா',        'province' => 'Central Province',       'role' => 'Production',    'cx' => 180, 'cy' => 580, 'r' => 26, 'primaryCrops' => ['Carrot', 'Leeks', 'Beetroot', 'Potato']],
        'badulla'       => ['name' => 'Badulla (Welimada)',   'localNameSi' => 'බදුල්ල',        'localNameTa' => 'பதுளை',            'province' => 'Uva Province',           'role' => 'Production',    'cx' => 260, 'cy' => 560, 'r' => 24, 'primaryCrops' => ['Potato', 'Carrot', 'Cabbage']],
        'colombo'       => ['name' => 'Colombo (Pettah)',     'localNameSi' => 'කොළඹ',          'localNameTa' => 'கொழும்பு',         'province' => 'Western Province',       'role' => 'Consumption',   'cx' => 70,  'cy' => 590, 'r' => 30, 'primaryCrops' => ['Urban Premium Retail', 'Imported Goods']],
        'galle'         => ['name' => 'Galle',                'localNameSi' => 'ගාල්ල',         'localNameTa' => 'காலி',             'province' => 'Southern Province',      'role' => 'Consumption',   'cx' => 100, 'cy' => 720, 'r' => 22, 'primaryCrops' => ['Low Country Sinks', 'Retail']],
        'hambantota'    => ['name' => 'Hambantota',           'localNameSi' => 'හම්බන්තොට',     'localNameTa' => 'அம்பாந்தோட்டை', 'province' => 'Southern Province',      'role' => 'Production',    'cx' => 230, 'cy' => 720, 'r' => 22, 'primaryCrops' => ['Pumpkin', 'Brinjal']],
        'moneragala'    => ['name' => 'Moneragala',           'localNameSi' => 'මොණරාගල',       'localNameTa' => 'மொனராகலை',         'province' => 'Uva Province',           'role' => 'Production',    'cx' => 280, 'cy' => 650, 'r' => 24, 'primaryCrops' => ['Pumpkin', 'Ladies Fingers']],
    ];

    /**
     * Helper: get the latest date available in the DB.
     */
    private function getLatestDate(): ?string
    {
        return PriceRecord::whereNotNull('price')
            ->max('date');
    }

    /**
     * Helper: aggregate price data per district.
     * Returns an array keyed by district_id with avg, max crop, min crop, prev_week_avg.
     */
    private function aggregateByDistrict(string $latestDate): array
    {
        $prevWeekDate = Carbon::parse($latestDate)->subDays(7)->toDateString();

        // --- TODAY: avg price, max/min crop per market ---
        $todayRows = PriceRecord::select('market_id', 'vegetable_id', DB::raw('AVG(price) as avg_price'))
            ->whereDate('date', $latestDate)
            ->whereNotNull('price')
            ->groupBy('market_id', 'vegetable_id')
            ->get();

        // --- PREV WEEK: avg price per market ---
        $prevRows = PriceRecord::select('market_id', DB::raw('AVG(price) as avg_price'))
            ->whereDate('date', $prevWeekDate)
            ->whereNotNull('price')
            ->groupBy('market_id')
            ->pluck('avg_price', 'market_id');

        // Roll market rows up into districts
        $districtBuckets = [];   // [districtId => [prices, crops, prevPrices]]

        foreach ($todayRows as $row) {
            $districtId = $this->marketToDistrict[$row->market_id] ?? null;
            if (!$districtId) continue;

            if (!isset($districtBuckets[$districtId])) {
                $districtBuckets[$districtId] = [
                    'prices'     => [],
                    'crops'      => [],
                    'prevPrices' => [],
                ];
            }
            $districtBuckets[$districtId]['prices'][] = $row->avg_price;
            $districtBuckets[$districtId]['crops'][]  = [
                'name'  => $this->formatVegetableName($row->vegetable_id),
                'price' => round($row->avg_price),
            ];
        }

        // Attach previous week prices
        foreach ($prevRows as $marketId => $avgPrice) {
            $districtId = $this->marketToDistrict[$marketId] ?? null;
            if (!$districtId) continue;
            if (!isset($districtBuckets[$districtId])) continue;
            $districtBuckets[$districtId]['prevPrices'][] = $avgPrice;
        }

        // Build final district stats
        $districts = [];
        foreach ($districtBuckets as $districtId => $bucket) {
            $meta = $this->districtMeta[$districtId] ?? null;
            if (!$meta || empty($bucket['prices'])) continue;

            $avgToday = round(array_sum($bucket['prices']) / count($bucket['prices']));
            $avgPrev  = empty($bucket['prevPrices'])
                ? $avgToday  // No change if no prev data
                : round(array_sum($bucket['prevPrices']) / count($bucket['prevPrices']));

            $trendPercent = $avgPrev > 0
                ? round((($avgToday - $avgPrev) / $avgPrev) * 100, 1)
                : 0;

            $sortedCrops = collect($bucket['crops'])->sortByDesc('price')->values();

            $districts[$districtId] = array_merge($meta, [
                'id'                          => $districtId,
                'computedAveragePrice'        => $avgToday,
                'computedAveragePricePrevWeek' => $avgPrev,
                'trendPercent'                => $trendPercent,
                'highestCropName'             => $sortedCrops->first()['name'] ?? 'N/A',
                'highestCropPrice'            => $sortedCrops->first()['price'] ?? 0,
                'lowestCropName'              => $sortedCrops->last()['name'] ?? 'N/A',
                'lowestCropPrice'             => $sortedCrops->last()['price'] ?? 0,
            ]);
        }

        // Inject districts that have NO database data (no market mapping) using meta only
        // so the map still renders their pins — they'll show "N/A" data gracefully.
        foreach ($this->districtMeta as $districtId => $meta) {
            if (!isset($districts[$districtId])) {
                $districts[$districtId] = array_merge($meta, [
                    'id'                          => $districtId,
                    'computedAveragePrice'        => null,
                    'computedAveragePricePrevWeek' => null,
                    'trendPercent'                => 0,
                    'highestCropName'             => 'N/A',
                    'highestCropPrice'            => 0,
                    'lowestCropName'              => 'N/A',
                    'lowestCropPrice'             => 0,
                ]);
            }
        }

        return $districts;
    }

    /**
     * GET /api/heatmap/districts
     * Returns price aggregates for every district.
     */
    public function districts()
    {
        $cacheKey = 'heatmap_districts_v2';

        $result = Cache::remember($cacheKey, now()->addHour(), function () {
            $latestDate = $this->getLatestDate();
            if (!$latestDate) {
                return ['districts' => [], 'date' => null];
            }

            $districts = $this->aggregateByDistrict($latestDate);

            return [
                'districts' => array_values($districts),
                'date'      => $latestDate,
            ];
        });

        return response()->json($result);
    }

    /**
     * GET /api/heatmap/summary
     * Returns national-level statistics: avg, most expensive, cheapest.
     */
    public function summary()
    {
        $cacheKey = 'heatmap_summary_v2';

        $result = Cache::remember($cacheKey, now()->addHour(), function () {
            $latestDate = $this->getLatestDate();
            if (!$latestDate) {
                return [
                    'nationalAverage'   => 0,
                    'expensiveDistrict' => 'N/A',
                    'expensivePrice'    => 0,
                    'cheapestDistrict'  => 'N/A',
                    'cheapestPrice'     => 0,
                    'distribution'      => ['lowCount' => 0, 'midCount' => 0, 'highCount' => 0],
                    'date'              => null,
                ];
            }

            $districts = $this->aggregateByDistrict($latestDate);

            // Only count districts with real data
            $withData = array_filter($districts, fn($d) => $d['computedAveragePrice'] !== null);

            if (empty($withData)) {
                return ['nationalAverage' => 0, 'date' => $latestDate];
            }

            $prices      = array_column($withData, 'computedAveragePrice');
            $nationalAvg = round(array_sum($prices) / count($prices));
            $min         = min($prices);
            $max         = max($prices);
            $range       = $max - $min;

            $lowThreshold    = $min + $range * 0.25;
            $mediumThreshold = $min + $range * 0.55;
            $highThreshold   = $min + $range * 0.80;

            $low = $mid = $high = 0;
            $expDistrict = $chpDistrict = null;

            foreach ($withData as $d) {
                $p = $d['computedAveragePrice'];
                if (!$expDistrict || $p > $expDistrict['computedAveragePrice']) $expDistrict = $d;
                if (!$chpDistrict || $p < $chpDistrict['computedAveragePrice']) $chpDistrict = $d;

                if ($p <= $lowThreshold)    $low++;
                elseif ($p <= $mediumThreshold) $mid++;
                else $high++;
            }

            return [
                'nationalAverage'   => $nationalAvg,
                'expensiveDistrict' => $expDistrict['name'] ?? 'N/A',
                'expensivePrice'    => $expDistrict['computedAveragePrice'] ?? 0,
                'cheapestDistrict'  => $chpDistrict['name'] ?? 'N/A',
                'cheapestPrice'     => $chpDistrict['computedAveragePrice'] ?? 0,
                'distribution'      => ['lowCount' => $low, 'midCount' => $mid, 'highCount' => $high],
                'date'              => $latestDate,
            ];
        });

        return response()->json($result);
    }

    /**
     * GET /api/heatmap/comparison?districtA=colombo&districtB=nuwara_eliya
     * Returns side-by-side data for two districts.
     */
    public function comparison(Request $request)
    {
        $idA = $request->query('districtA', 'colombo');
        $idB = $request->query('districtB', 'nuwara_eliya');

        $cacheKey = "heatmap_comparison_v2_{$idA}_{$idB}";

        $result = Cache::remember($cacheKey, now()->addHour(), function () use ($idA, $idB) {
            $latestDate = $this->getLatestDate();
            if (!$latestDate) {
                return ['districtA' => null, 'districtB' => null];
            }

            $all = $this->aggregateByDistrict($latestDate);

            return [
                'districtA' => $all[$idA] ?? null,
                'districtB' => $all[$idB] ?? null,
                'date'      => $latestDate,
            ];
        });

        return response()->json($result);
    }

    /**
     * Convert vegetable_id slug to a human-readable name.
     * e.g. "green-beans" → "Green Beans"
     */
    private function formatVegetableName(string $slug): string
    {
        // Special-case overrides for better display names
        $overrides = [
            'big-onion-imported'       => 'Big Onion (Imp.)',
            'red-onion-imported'       => 'Red Onion (Imp.)',
            'red-onion-local'          => 'Red Onion (Local)',
            'potato-imported'          => 'Potato (Imp.)',
            'potato-local'             => 'Potato (Local)',
            'dry-chilli-imported'      => 'Dry Chilli (Imp.)',
            'fish-balaya'              => 'Fish (Balaya)',
            'fish-hurulla'             => 'Fish (Hurulla)',
            'fish-kelawalla'           => 'Fish (Kelawalla)',
            'fish-linna'               => 'Fish (Linna)',
            'fish-salaya'              => 'Fish (Salaya)',
            'fish-thalapath'           => 'Fish (Thalapath)',
            'fish-katta-imported'      => 'Fish (Katta Imp.)',
            'fish-paraw'               => 'Fish (Paraw)',
            'fish-sprat-imported'      => 'Fish (Sprat Imp.)',
            'rice-kekulu-red'          => 'Rice (Kekulu Red)',
            'rice-kekulu-white'        => 'Rice (Kekulu White)',
            'rice-nadu'                => 'Rice (Nadu)',
            'rice-samba'               => 'Rice (Samba)',
            'rice-ponni-samba-imported'=> 'Rice (Ponni Samba Imp.)',
            'apple-imported'           => 'Apple (Imp.)',
            'orange-imported'          => 'Orange (Imp.)',
            'coconut-oil'              => 'Coconut Oil',
            'red-lentils'              => 'Red Lentils',
            'ash-plantain'             => 'Ash Plantain',
            'long-beans'               => 'Long Beans',
            'green-beans'              => 'Green Beans',
            'green-chilli'             => 'Green Chilli',
            'snake-gourd'              => 'Snake Gourd',
            'bitter-gourd'             => 'Bitter Gourd',
            'ridge-gourd'              => 'Ridge Gourd',
            'sweet-potato'             => 'Sweet Potato',
            'ladies-fingers'           => 'Ladies Fingers',
        ];

        return $overrides[$slug] ?? ucwords(str_replace('-', ' ', $slug));
    }
}
