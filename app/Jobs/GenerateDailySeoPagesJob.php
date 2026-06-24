<?php

namespace App\Jobs;

use App\Models\PriceRecord;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateDailySeoPagesJob implements ShouldQueue
{
    use Queueable;

    public $date;

    /**
     * Create a new job instance.
     */
    public function __construct($date = null)
    {
        $this->date = $date ? Carbon::parse($date)->toDateString() : Carbon::today()->toDateString();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Chunk through price records of the specific date
        PriceRecord::whereDate('date', $this->date)->chunkById(500, function ($records) {
            foreach ($records as $record) {
                GenerateSingleSeoPageJob::dispatch($record->id);
            }
        });
    }
}
