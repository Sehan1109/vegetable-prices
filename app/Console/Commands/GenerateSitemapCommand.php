<?php

namespace App\Console\Commands;

use App\Services\SitemapGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the XML sitemap for the application.';

    /**
     * Execute the console command.
     */
    public function handle(SitemapGeneratorService $sitemapService)
    {
        $this->info('Starting sitemap generation...');

        try {
            $sitemapService->generate();
            $this->info('Sitemap generation completed successfully.');
        } catch (\Exception $e) {
            $this->error('ERROR: Unable to generate sitemap.');
            $this->error('Reason: ' . $e->getMessage());
            Log::error("Sitemap generation command failed: " . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
