<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class BackfillPrices extends Command
{
    protected $signature = 'harti:backfill
                            {--days=14 : Number of past days to backfill}
                            {--from=  : Start date (YYYY-MM-DD), defaults to --days ago}
                            {--to=    : End date (YYYY-MM-DD), defaults to today}';

    protected $description = 'Backfill historical CBSL price data by scraping past PDF reports';

    public function handle(): int
    {
        $to   = $this->option('to')
            ? Carbon::parse($this->option('to'), 'Asia/Colombo')
            : Carbon::now('Asia/Colombo');

        $from = $this->option('from')
            ? Carbon::parse($this->option('from'), 'Asia/Colombo')
            : $to->copy()->subDays((int) $this->option('days') - 1);

        $this->info("Backfilling from {$from->toDateString()} to {$to->toDateString()}...");

        $total   = 0;
        $skipped = 0;
        $failed  = 0;

        $current = $from->copy();
        while ($current->lte($to)) {
            $dateStr = $current->toDateString();

            // Skip weekends
            if ($current->isWeekend()) {
                $this->line("  [skip] Weekend: {$dateStr}");
                $current->addDay();
                continue;
            }

            $this->line("  Scraping {$dateStr}...");

            $exitCode = $this->call('harti:scrape', [
                '--force' => true,
                '--date'  => $dateStr,
            ]);

            if ($exitCode === 0) {
                $total++;
            } else {
                $this->warn("  [failed] {$dateStr}");
                $failed++;
            }

            $current->addDay();

            // Brief pause to be polite to CBSL server
            usleep(500000); // 0.5 seconds
        }

        $this->info("Backfill complete: {$total} days scraped, {$failed} failed, {$skipped} skipped.");
        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
