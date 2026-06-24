<?php

namespace App\Jobs;

use App\Models\PriceRecord;
use App\Services\SeoPageGeneratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateSingleSeoPageJob implements ShouldQueue
{
    use Queueable;

    public $priceRecordId;

    /**
     * Create a new job instance.
     */
    public function __construct($priceRecordId)
    {
        $this->priceRecordId = $priceRecordId;
    }

    /**
     * Execute the job.
     */
    public function handle(SeoPageGeneratorService $service): void
    {
        $record = PriceRecord::find($this->priceRecordId);
        if ($record) {
            $service->generateForPriceRecord($record);
        }
    }
}
