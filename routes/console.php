<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('harti:scrape')
        ->timezone('Asia/Colombo')
        ->dailyAt('13:00');