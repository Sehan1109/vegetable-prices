<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PriceDashboardController;

// Main dashboard view
Route::get('/', [PriceDashboardController::class, 'index'])->name('dashboard');

// Price API Endpoints
Route::get('/api/prices/today', [PriceDashboardController::class, 'getPrices']);
Route::get('/api/prices/history', [PriceDashboardController::class, 'getHistory']);

// Pipeline Management Endpoints
Route::get('/api/pipeline/status', [PriceDashboardController::class, 'getPipelineStatus']);
Route::post('/api/pipeline/trigger', [PriceDashboardController::class, 'triggerScrape']);
Route::post('/api/pipeline/reset', [PriceDashboardController::class, 'resetPipeline']);