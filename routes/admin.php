<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MarketController;
use App\Http\Controllers\Admin\VegetableController;
use App\Http\Controllers\Admin\PriceRecordController;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // ── 1. Dashboard ──────────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── 2. HARTI Scraper ──────────────────────────────────────────────────────
    Route::get('/scraper', [DashboardController::class, 'scraper'])->name('scraper');
    Route::post('/scraper/run', [DashboardController::class, 'runScraper'])->name('scraper.run');
    Route::post('/scraper/backfill', [DashboardController::class, 'backfill'])->name('scraper.backfill');

    // ── 3. SEO Management ─────────────────────────────────────────────────────
    Route::get('/seo', [DashboardController::class, 'seo'])->name('seo');
    Route::post('/seo/generate', [DashboardController::class, 'generateSeo'])->name('seo.generate');
    Route::delete('/seo/{seoPage}', [DashboardController::class, 'deleteSeoPage'])->name('seo.delete');

    // ── 4. Sitemap ────────────────────────────────────────────────────────────
    Route::get('/sitemap', [DashboardController::class, 'sitemap'])->name('sitemap');
    Route::post('/sitemap/generate', [DashboardController::class, 'generateSitemap'])->name('sitemap.generate');
    Route::get('/sitemap/download', [DashboardController::class, 'downloadSitemap'])->name('sitemap.download');

    // ── 5. Database State ─────────────────────────────────────────────────────
    Route::get('/database', [DashboardController::class, 'database'])->name('database');

    // ── 6. Price Records ──────────────────────────────────────────────────────
    Route::resource('prices', PriceRecordController::class);

    // ── 7. Markets ────────────────────────────────────────────────────────────
    Route::resource('markets', MarketController::class);

    // ── 8. Vegetables ─────────────────────────────────────────────────────────
    Route::resource('vegetables', VegetableController::class);

    // ── 9. Queue Manager ──────────────────────────────────────────────────────
    Route::get('/queue', [DashboardController::class, 'queue'])->name('queue');
    Route::post('/queue/retry', [DashboardController::class, 'retryFailedJob'])->name('queue.retry');
    Route::post('/queue/delete-failed', [DashboardController::class, 'deleteFailedJob'])->name('queue.delete-failed');
    Route::post('/queue/clear-failed', [DashboardController::class, 'clearFailedJobs'])->name('queue.clear-failed');

    // ── 10. Scheduler ─────────────────────────────────────────────────────────
    Route::get('/scheduler', [DashboardController::class, 'scheduler'])->name('scheduler');
    Route::post('/scheduler/run', [DashboardController::class, 'runScheduler'])->name('scheduler.run');

    // ── 11. Cache Manager ─────────────────────────────────────────────────────
    Route::get('/cache', [DashboardController::class, 'cache'])->name('cache');
    Route::post('/cache/clear', [DashboardController::class, 'clearCache'])->name('cache.clear');
    Route::post('/cache/optimize', [DashboardController::class, 'optimize'])->name('cache.optimize');

    // ── 12. System Logs ───────────────────────────────────────────────────────
    Route::get('/logs', [DashboardController::class, 'logs'])->name('logs');
    Route::get('/logs/download', [DashboardController::class, 'downloadLogs'])->name('logs.download');

    // ── 13. Analytics ─────────────────────────────────────────────────────────
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');

    // ── 14. Users ─────────────────────────────────────────────────────────────
    Route::get('/users', [DashboardController::class, 'users'])->name('users');

    // ── 15. Settings ──────────────────────────────────────────────────────────
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
});
