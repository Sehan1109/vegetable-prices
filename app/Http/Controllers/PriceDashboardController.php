<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PriceRecord;
use App\Models\SeoPage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use DB;

class PriceDashboardController extends Controller
{
    private $serverStartTimeKey = 'server_start_time';

    public function __construct()
    {
        // Server එක start වුණු වෙලාව track කරන්න (Uptime සදහා)
        if (!Cache::has($this->serverStartTimeKey)) {
            Cache::forever($this->serverStartTimeKey, now()->timestamp);
        }
    }

    /**
     * Return the main Blade view for the dashboard
     */
    public function index(Request $request)
    {
        $lang = $request->query('lang', 'en');
        $todayPrices = PriceRecord::whereDate('date', Carbon::today())->get();
        
        $path = $request->path();
        $activeTab = 'home';
        if ($path == 'prices') $activeTab = 'rates';
        elseif ($path == 'trends') $activeTab = 'trends';
        elseif ($path == 'heatmap') $activeTab = 'heatmap';
        elseif ($path == 'about') $activeTab = 'about';
        elseif ($path == 'pipeline') $activeTab = 'pipeline';

        $latestSeoPages = SeoPage::with(['market', 'vegetable'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', [
            'lang' => $lang,
            'todayPrices' => $todayPrices,
            'initialTab' => $activeTab,
            'latestSeoPages' => $latestSeoPages
        ]);
    }

    /**
     * API: Get daily prices for a market
     * Node.js වල /api/prices/today එකට අදාල PHP එක
     */
    public function getPrices(Request $request)
    {
        $marketId = $request->query('marketId', 'peliyagoda');
        
        // Latest ම තියෙන දිනය හොයාගන්නවා
        $latestRecord = PriceRecord::where('market_id', $marketId)
            ->orderBy('date', 'desc')
            ->first();

        if ($latestRecord) {
            $date = $latestRecord->date;
            
            // ඒ දිනයට අදාල ඔක්කොම records ගන්නවා
            $records = PriceRecord::where('date', $date)
                ->where('market_id', $marketId)
                ->get();

            // Node.js වල වගේම client බලාපොරොත්තු වන Hashmap format එකට හදනවා
            $pricesMap = [];
            foreach ($records as $r) {
                $pricesMap[$r->vegetable_id] = [
                    'price' => $r->price,
                    'priceYesterday' => $r->price_yesterday,
                    'changePercent' => $r->change_percent,
                    'priceYearAgo' => $r->price_year_ago ?? 0, // Node.js එකේ තිබූ අගයන්
                    'changePercentYear' => $r->change_percent_year ?? 0
                ];
            }

            return response()->json([
                'date' => $date,
                'marketId' => $marketId,
                'prices' => $pricesMap,
                'scrapedPdfUrl' => Cache::get('scraped_pdf_url'),
                'scrapedPdfDate' => Cache::get('scraped_pdf_date')
            ]);
        }

        return response()->json([
            'date' => 'unknown',
            'marketId' => $marketId,
            'prices' => (object)[],
            'scrapedPdfUrl' => Cache::get('scraped_pdf_url'),
            'scrapedPdfDate' => Cache::get('scraped_pdf_date')
        ]);
    }

    /**
     * API: Get historical prices for trends charts
     * Node.js වල /api/prices/history එකට අදාල PHP එක
     */
    public function getHistory(Request $request)
    {
        $vegetableId = $request->query('vegetableId', 'carrot');
        $marketId = $request->query('marketId', 'peliyagoda');
        $days = (int) $request->query('days', 30);

        $records = PriceRecord::where('market_id', $marketId)
            ->where('vegetable_id', $vegetableId)
            ->orderBy('date', 'desc')
            ->take($days)
            ->get();

        // Node.js format එකට .reverse() කරලා සකස් කිරීම
        $history = $records->map(function($r) {
            return [
                'date' => $r->date,
                'price' => $r->price
            ];
        })->reverse()->values();

        return response()->json([
            'vegetableId' => $vegetableId,
            'marketId' => $marketId,
            'days' => $days,
            'history' => $history
        ]);
    }

    /**
     * API: Get current pipeline status and logs
     */
    public function getPipelineStatus()
    {
        $startTime = Cache::get($this->serverStartTimeKey, now()->timestamp);
        $uptimeSeconds = now()->timestamp - $startTime;

        $pipelineInfo = Cache::get('pipeline_info', [
            'pipelineHealth' => 'healthy',
            'lastError' => null,
            'lastErrorTime' => null,
            'lastAutoUpdateDate' => Cache::get('last_auto_update_date', '')
        ]);

        $pipelineInfo['uptimeSeconds'] = $uptimeSeconds;
        $logs = Cache::get('pipeline_logs', []);

        return response()->json([
            'status' => 'ok',
            'pipelineInfo' => $pipelineInfo,
            'logs' => array_slice($logs, 0, 50) // අන්තිම logs 50 පමණක් යවයි
        ]);
    }

    /**
     * API: Manually trigger automated pipeline crawling
     * Double trigger වැලැක්වීමට Cache Lock පාවිච්චි කර ඇත (Node.js isScrapingInProgress වෙනුවට)
     */
    public function triggerScrape()
    {
        $lock = Cache::lock('scraping_lock', 600); // විනාඩි 10 ක max lock එකක්

        if (!$lock->get()) {
            return response()->json(['error' => 'Scraping is already running.'], 409);
        }

        try {
            $this->addLog('Manual scraping trigger detected. Launching REAL CBSL PDF Price Extraction Pipeline.', 'info');
            
            // Background එකේ රන් වෙන්න Queue කරන්න පුළුවන් (Artisan::queue)
            // නැත්නම් කෙලින්ම run කරන්න පුළුවන්
            Artisan::call('harti:scrape'); 
            
            $this->addLog('CBSL PDF pricing parsing complete successfully.', 'success');

            // Pipeline Status එක Update කිරීම
            Cache::put('pipeline_info', [
                'pipelineHealth' => 'healthy',
                'lastError' => null,
                'lastErrorTime' => null,
                'lastAutoUpdateDate' => Carbon::now('Asia/Colombo')->toDateString()
            ]);

            $lock->release();

            return response()->json([
                'message' => 'Scraping initiated successfully.',
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->addLog('Unexpected pipeline error: ' . $e->getMessage(), 'error');
            
            Cache::put('pipeline_info', [
                'pipelineHealth' => 'error',
                'lastError' => $e->getMessage(),
                'lastErrorTime' => now()->toIso8601String(),
                'lastAutoUpdateDate' => Cache::get('last_auto_update_date', '')
            ]);

            $lock->release();
            return response()->json(['error' => 'Pipeline failed'], 500);
        }
    }

    /**
     * API: Reset database
     */
    public function resetPipeline()
    {
        try {
            // Price records table එක truncate (සිස්දු) කරයි
            Schema::disableForeignKeyConstraints();
            PriceRecord::truncate();
            Schema::enableForeignKeyConstraints();

            // Logs reset කිරීම
            Cache::forget('pipeline_logs');
            Cache::forget('pipeline_info');
            
            return response()->json([
                'message' => 'Database reset to default seed values successfully.',
                'status' => 'ok',
                'logCount' => 0
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'DB reset failure: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper to add logs to cache (Node.js addLog function එක වෙනුවට)
     */
    private function addLog($message, $type = 'info')
    {
        $logs = Cache::get('pipeline_logs', []);
        array_unshift($logs, [
            'timestamp' => now()->toIso8601String(),
            'message' => $message,
            'type' => $type
        ]);
        
        // උපරිම logs 100ක් තියාගන්නවා
        Cache::put('pipeline_logs', array_slice($logs, 0, 100));
    }
}