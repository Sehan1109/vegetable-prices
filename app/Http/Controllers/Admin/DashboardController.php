<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Models\Vegetable;
use App\Models\PriceRecord;
use App\Models\SeoPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // Real DB stats
        $todayImports  = PriceRecord::whereDate('created_at', $today)->count();
        $todaySeoPages = SeoPage::whereDate('created_at', $today)->count();
        $totalMarkets  = Market::count();
        $totalVegetables = Vegetable::count();
        $totalPriceRecords = PriceRecord::count();
        $totalSeoPages = SeoPage::count();

        // Cache/Pipeline info
        $pipelineInfo = Cache::get('pipeline_info', []);
        $pipelineLogs = Cache::get('pipeline_logs', []);
        $lastScrapeDate = Cache::get('last_auto_update_date', 'Never');

        // Queue stats
        $pendingJobs = DB::table('jobs')->count();
        $failedJobs  = DB::table('failed_jobs')->count();

        // DB status check
        $dbStatus = 'connected';
        try { DB::connection()->getPdo(); } catch (\Exception $e) { $dbStatus = 'error'; }

        // Storage usage
        $storagePath = storage_path('app');
        $storageUsed = $this->dirSize($storagePath);

        return view('admin.dashboard', compact(
            'todayImports', 'todaySeoPages', 'totalMarkets', 'totalVegetables',
            'totalPriceRecords', 'totalSeoPages',
            'pipelineInfo', 'pipelineLogs', 'lastScrapeDate',
            'pendingJobs', 'failedJobs', 'dbStatus', 'storageUsed'
        ));
    }

    private function dirSize(string $path): string
    {
        $size = 0;
        try {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
                $size += $file->getSize();
            }
        } catch (\Exception $e) {}
        return $size < 1048576 ? round($size / 1024, 1) . ' KB' : round($size / 1048576, 1) . ' MB';
    }

    public function scraper()
    {
        $pipelineLogs = Cache::get('pipeline_logs', []);
        $lastScrapeDate = Cache::get('last_auto_update_date', 'Never');
        $pipelineInfo = Cache::get('pipeline_info', []);
        $pdfUrl = Cache::get('scraped_pdf_url');
        $pdfDate = Cache::get('scraped_pdf_date');
        return view('admin.scraper', compact('pipelineLogs', 'lastScrapeDate', 'pipelineInfo', 'pdfUrl', 'pdfDate'));
    }

    public function runScraper(Request $request)
    {
        try {
            $options = ['--force' => true];
            if ($request->filled('date')) {
                $options['--date'] = $request->date;
            }
            $exitCode = Artisan::call('harti:scrape', $options);
            $output   = Artisan::output();
            if ($exitCode === 0) {
                return back()->with('success', 'Scraper completed successfully!')->with('scrape_output', $output);
            }
            return back()->with('error', 'Scraper encountered issues. Check logs.')->with('scrape_output', $output);
        } catch (\Exception $e) {
            Log::error('Admin scraper error: ' . $e->getMessage());
            return back()->with('error', 'Unexpected error: ' . $e->getMessage());
        }
    }

    public function backfill(Request $request)
    {
        try {
            $days = $request->input('days', 7);
            Artisan::queue('harti:backfill', ['--days' => $days]);
            return back()->with('success', "Backfill job dispatched for $days days.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function seo(Request $request)
    {
        $query = SeoPage::query();

        if ($request->filled('search')) {
            $query->where('title', 'ilike', '%' . $request->search . '%')
                  ->orWhere('slug', 'ilike', '%' . $request->search . '%');
        }
        if ($request->filled('market')) {
            $query->where('market_id', $request->market);
        }
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $pages     = $query->latest()->paginate(20)->withQueryString();
        $markets   = Market::orderBy('name')->get();
        $totalPages = SeoPage::count();

        return view('admin.seo', compact('pages', 'markets', 'totalPages'));
    }

    public function generateSeo(Request $request)
    {
        try {
            $mode = $request->input('mode', 'missing');
            if ($mode === 'all') {
                $exitCode = Artisan::call('seo:generate-daily', ['--force' => true]);
            } else {
                $exitCode = Artisan::call('seo:generate-daily');
            }
            $output = Artisan::output();
            return back()->with('success', "SEO generation completed. " . $output);
        } catch (\Exception $e) {
            Log::error('SEO generation error: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function deleteSeoPage(SeoPage $seoPage)
    {
        $seoPage->delete();
        return back()->with('success', 'SEO page deleted.');
    }

    public function sitemap()
    {
        $sitemapPath = public_path('sitemap.xml');
        $exists      = file_exists($sitemapPath);
        $lastModified = $exists ? Carbon::createFromTimestamp(filemtime($sitemapPath))->diffForHumans() : null;
        $urlCount    = 0;

        if ($exists) {
            try {
                $content = file_get_contents($sitemapPath);
                $urlCount = substr_count($content, '<url>');
            } catch (\Exception $e) {}
        }

        return view('admin.sitemap', compact('exists', 'lastModified', 'urlCount'));
    }

    public function generateSitemap()
    {
        try {
            $exitCode = Artisan::call('sitemap:generate');
            $output   = Artisan::output();
            if ($exitCode === 0) {
                return back()->with('success', 'Sitemap generated successfully!');
            }
            return back()->with('error', 'Sitemap generation failed. ' . $output);
        } catch (\Exception $e) {
            Log::error('Sitemap gen error: ' . $e->getMessage());
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadSitemap()
    {
        $sitemapPath = public_path('sitemap.xml');
        if (!file_exists($sitemapPath)) {
            return back()->with('error', 'Sitemap does not exist. Generate it first.');
        }
        return response()->download($sitemapPath);
    }

    public function database()
    {
        $stats = [
            'markets'      => Market::count(),
            'vegetables'   => Vegetable::count(),
            'price_records' => PriceRecord::count(),
            'seo_pages'    => SeoPage::count(),
        ];

        $latestDate = PriceRecord::max('date');
        $dbSize     = 'N/A';
        try {
            $result = DB::select("SELECT pg_size_pretty(pg_database_size(current_database())) AS size");
            $dbSize = $result[0]->size ?? 'N/A';
        } catch (\Exception $e) {}

        return view('admin.database', compact('stats', 'latestDate', 'dbSize'));
    }

    public function queue()
    {
        $pendingJobs = DB::table('jobs')->orderBy('created_at', 'desc')->take(20)->get();
        $failedJobs  = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->take(20)->get();
        $pendingCount = DB::table('jobs')->count();
        $failedCount  = DB::table('failed_jobs')->count();

        return view('admin.queue', compact('pendingJobs', 'failedJobs', 'pendingCount', 'failedCount'));
    }

    public function retryFailedJob(Request $request)
    {
        $uuid = $request->input('uuid');
        try {
            Artisan::call('queue:retry', ['id' => [$uuid]]);
            return back()->with('success', 'Job queued for retry.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function deleteFailedJob(Request $request)
    {
        $uuid = $request->input('uuid');
        try {
            DB::table('failed_jobs')->where('uuid', $uuid)->delete();
            return back()->with('success', 'Failed job deleted.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function clearFailedJobs()
    {
        try {
            Artisan::call('queue:flush');
            return back()->with('success', 'All failed jobs cleared.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function scheduler()
    {
        $schedule = app(\Illuminate\Console\Scheduling\Schedule::class);
        $events   = collect($schedule->events())->map(function ($event) {
            try {
                $cron = new \Cron\CronExpression($event->expression);
                $nextRun = $cron->getNextRunDate()->format('Y-m-d H:i:s');
            } catch (\Throwable $e) {
                $nextRun = 'N/A';
            }
            return [
                'command'     => $event->command ?? $event->description ?? 'Closure',
                'expression'  => $event->expression,
                'description' => $event->description,
                'nextRun'     => $nextRun,
            ];
        });

        return view('admin.scheduler', compact('events'));
    }

    public function runScheduler()
    {
        try {
            Artisan::call('schedule:run');
            $output = Artisan::output();
            return back()->with('success', 'Scheduler run triggered. ' . $output);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cache()
    {
        return view('admin.cache');
    }

    public function clearCache(Request $request)
    {
        $type = $request->input('type', 'all');
        $commands = [
            'all'    => ['cache:clear', 'view:clear', 'route:clear', 'config:clear'],
            'cache'  => ['cache:clear'],
            'route'  => ['route:clear'],
            'view'   => ['view:clear'],
            'config' => ['config:clear'],
        ];
        $toRun = $commands[$type] ?? $commands['all'];
        foreach ($toRun as $cmd) { Artisan::call($cmd); }
        return back()->with('success', ucfirst($type) . ' cache cleared successfully.');
    }

    public function optimize(Request $request)
    {
        $mode = $request->input('mode', 'optimize');
        try {
            if ($mode === 'clear') {
                Artisan::call('optimize:clear');
                return back()->with('success', 'Optimization cache cleared.');
            }
            Artisan::call('optimize');
            return back()->with('success', 'Application optimized successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function logs(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $lines   = [];
        $filter  = $request->input('filter', '');

        if (file_exists($logFile)) {
            $allLines = array_reverse(file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
            foreach ($allLines as $line) {
                if ($filter && !str_contains(strtolower($line), strtolower($filter))) continue;
                $lines[] = $line;
                if (count($lines) >= 200) break;
            }
        }

        return view('admin.logs', compact('lines', 'filter'));
    }

    public function downloadLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return back()->with('error', 'Log file not found.');
        }
        return response()->download($logFile);
    }

    public function analytics()
    {
        // Daily imports (last 30 days)
        $dailyImports = PriceRecord::selectRaw("date::text as label, COUNT(*) as total")
            ->where('date', '>=', Carbon::now()->subDays(30)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Daily SEO pages (last 30 days)
        $dailySeo = SeoPage::selectRaw("DATE(created_at) as label, COUNT(*) as total")
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('label')
            ->get();

        // Top vegetables by price records
        $topVegetables = PriceRecord::selectRaw('vegetable_id, COUNT(*) as total')
            ->groupBy('vegetable_id')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();

        // Top markets by price records
        $topMarkets = PriceRecord::selectRaw('market_id, COUNT(*) as total')
            ->groupBy('market_id')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();

        return view('admin.analytics', compact('dailyImports', 'dailySeo', 'topVegetables', 'topMarkets'));
    }

    public function users()
    {
        $users = \App\Models\User::latest()->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
