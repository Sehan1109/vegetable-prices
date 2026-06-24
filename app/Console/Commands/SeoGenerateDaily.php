<?php

namespace App\Console\Commands;

use App\Jobs\GenerateDailySeoPagesJob;
use App\Jobs\GenerateSitemapJob;
use Illuminate\Console\Command;

class SeoGenerateDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seo:generate-daily {date? : The date to generate pages for (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate SEO pages and sitemap for daily vegetable prices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->argument('date');
        $this->info("Dispatching Daily SEO Page Generation Job for " . ($date ?? 'today'));
        
        GenerateDailySeoPagesJob::dispatch($date);
        
        $this->info("Dispatching Sitemap Generation Job (delayed to allow pages to generate)");
        GenerateSitemapJob::dispatch()->delay(now()->addMinutes(10));
        
        $this->info("Done! The queue is processing the generation.");
    }
}
