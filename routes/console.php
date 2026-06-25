<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('harti:scrape')
        ->timezone('Asia/Colombo')
        ->dailyAt('13:00');

// Fallback scheduled sitemap generation (runs every night)
Schedule::command('sitemap:generate')
        ->timezone('Asia/Colombo')
        ->dailyAt('02:00');