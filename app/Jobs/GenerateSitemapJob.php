<?php

namespace App\Jobs;

use App\Services\SitemapGeneratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateSitemapJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SitemapGeneratorService $service): void
    {
        try {
            \Illuminate\Support\Facades\Log::info("GenerateSitemapJob started.");
            $service->generate();
            \Illuminate\Support\Facades\Log::info("GenerateSitemapJob completed successfully.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("GenerateSitemapJob failed: " . $e->getMessage());
            throw $e; // Re-throw to ensure the job is marked as failed in Laravel
        }
    }
}
