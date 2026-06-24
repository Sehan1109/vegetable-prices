<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PriceDashboardController;
use App\Http\Controllers\SeoPageController;
use Illuminate\Support\Facades\Route;

// Main frontend view
Route::get('/', [PriceDashboardController::class, 'index'])->name('home');
Route::get('/prices', [PriceDashboardController::class, 'index'])->name('prices');
Route::get('/trends', [PriceDashboardController::class, 'index'])->name('trends');
Route::get('/heatmap', [PriceDashboardController::class, 'index'])->name('heatmap');
Route::get('/about', [PriceDashboardController::class, 'index'])->name('about');
Route::get('/pipeline', [PriceDashboardController::class, 'index'])->name('pipeline');

// SEO static pages route (must be before specific APIs but after defined static ones)
Route::get('/{slug}', [SeoPageController::class, 'show'])
    ->where('slug', '^[0-9]{4}-[0-9]{2}-[0-9]{2}-.*-price-.*$')
    ->name('seo.page.show');

// Price API Endpoints
Route::get('/api/prices/today', [PriceDashboardController::class, 'getPrices']);
Route::get('/api/prices/history', [PriceDashboardController::class, 'getHistory']);

// Heatmap API Endpoints
Route::get('/api/heatmap/districts', [\App\Http\Controllers\HeatmapController::class, 'districts']);
Route::get('/api/heatmap/summary', [\App\Http\Controllers\HeatmapController::class, 'summary']);
Route::get('/api/heatmap/comparison', [\App\Http\Controllers\HeatmapController::class, 'comparison']);

// Admin Dashboard
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::post('/admin/scrape', [\App\Http\Controllers\ScraperController::class, 'triggerScrape'])->name('admin.scrape');
});

require __DIR__.'/auth.php';
