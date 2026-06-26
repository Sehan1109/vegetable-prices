<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ScraperController extends Controller
{
    /**
     * Trigger the scraper command manually.
     */
    public function triggerScrape(Request $request)
    {
        try {
            // Run the harti:scrape command synchronously with the force flag
            $exitCode = Artisan::call('harti:scrape', ['--force' => true]);
            
            // Get the output of the command
            $output = Artisan::output();
            
            if ($exitCode === 0) {
                return redirect()->route('admin.dashboard')->with('success', 'Scraper completed successfully!')->with('scrape_output', $output);
            } else {
                return redirect()->route('admin.dashboard')->with('error', 'Scraper failed. Check the logs for more details.')->with('scrape_output', $output);
            }
        } catch (\Exception $e) {
            Log::error('Manual Scrape Failed: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
}
