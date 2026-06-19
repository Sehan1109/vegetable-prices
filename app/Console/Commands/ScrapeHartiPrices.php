<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\PriceRecord;
use App\Helpers\VegetableNormalizer;
use Carbon\Carbon;
use Smalot\PdfParser\Parser;

class ScrapeHartiPrices extends Command
{
    protected $signature = 'harti:scrape {--force : Force re-scrape even if already done today} {--date= : Specific date to scrape (YYYY-MM-DD)}';
    protected $description = 'Scrape Daily Food Commodity Prices from the CBSL Daily Price Report PDF';

    /**
     * CBSL PDF URL pattern:
     * https://www.cbsl.gov.lk/sites/default/files/cbslweb_documents/statistics/pricerpt/price_report_YYYYMMDD_e.pdf
     */
    private const PDF_BASE_URL = 'https://www.cbsl.gov.lk/sites/default/files/cbslweb_documents/statistics/pricerpt/price_report_%s_e.pdf';

    /**
     * The market columns in the PDF table (left to right):
     * Columns 1-2: Pettah (wholesale Yesterday / Today)
     * Columns 3-4: Dambulla (Yesterday / Today)
     * Columns 5-6: Narahenpita (Yesterday / Today)  [retail]
     * Columns 7-8: Peliyagoda (Yesterday / Today)   [wholesale fish market]
     * Columns 9-10: Negombo (Yesterday / Today)
     */
    private const MARKET_COLUMNS = [
        0 => ['id' => 'pettah',       'yesterday' => 0, 'today' => 1],
        1 => ['id' => 'dambulla',     'yesterday' => 2, 'today' => 3],
        2 => ['id' => 'narahenpita',  'yesterday' => 4, 'today' => 5],
        3 => ['id' => 'peliyagoda',   'yesterday' => 6, 'today' => 7],
        4 => ['id' => 'negombo',      'yesterday' => 8, 'today' => 9],
    ];

    public function handle(): int
    {
        $targetDate = $this->option('date')
            ? Carbon::parse($this->option('date'), 'Asia/Colombo')
            : Carbon::now('Asia/Colombo');

        $today = $targetDate->toDateString();

        // ── Guard: skip if already ran today ───────────────────────────
        if (!$this->option('force') && !$this->option('date')) {
            if (Cache::get('last_auto_update_date') === $today) {
                $this->info("Already scraped today ({$today}). Use --force to re-run.");
                return Command::SUCCESS;
            }
        }

        $this->info('═══════════════════════════════════════════════════');
        $this->info('  CBSL Daily Price Report — PDF Extraction Pipeline');
        $this->info('═══════════════════════════════════════════════════');

        try {
            // Step 1 ── Find the PDF for today (try today + up to 3 previous working days)
            $this->info('[1/4] Finding today\'s PDF report...');
            [$pdfUrl, $pdfDate] = $this->findPdfUrl($targetDate);

            if (!$pdfUrl) {
                $msg = 'Could not locate today\'s price report PDF on CBSL. It may not be published yet.';
                $this->warn($msg);
                $this->addLog($msg, 'warning');
                $this->markPipelineError($msg);
                return Command::FAILURE;
            }

            $this->line("   → PDF URL: {$pdfUrl}");
            $this->line("   → Report date: {$pdfDate}");
            $this->addLog("Found PDF: {$pdfUrl}", 'info');
            Cache::put('scraped_pdf_url', $pdfUrl);
            Cache::put('scraped_pdf_date', $pdfDate);

            // Step 2 ── Download the PDF
            $this->info('[2/4] Downloading PDF...');
            $pdfContent = $this->downloadPdf($pdfUrl);

            if (!$pdfContent) {
                $msg = 'Failed to download PDF from CBSL.';
                $this->error($msg);
                $this->addLog($msg, 'error');
                $this->markPipelineError($msg);
                return Command::FAILURE;
            }

            $this->line('   → Downloaded ' . number_format(strlen($pdfContent)) . ' bytes.');

            // Step 3 ── Parse price rows from the PDF
            $this->info('[3/4] Parsing price data from PDF table...');
            $rows = $this->parsePdf($pdfContent, $pdfDate);

            if (empty($rows)) {
                $this->warn('Parsed 0 rows — PDF table structure may have changed.');
                $this->addLog('Warning: 0 rows parsed — PDF format may have changed.', 'warning');
            } else {
                $this->line('   → Parsed ' . count($rows) . ' price rows.');
            }

            // Step 4 ── Save to database
            $this->info('[4/4] Saving to database...');
            $saved = $this->saveToDatabase($rows);
            $this->line("   → {$saved} records saved/updated.");

            // ── Update cache ─────────────────────────────────────────────
            Cache::put('last_auto_update_date', $pdfDate);
            Cache::put('pipeline_info', [
                'pipelineHealth'     => 'healthy',
                'lastError'          => null,
                'lastErrorTime'      => null,
                'lastAutoUpdateDate' => $pdfDate,
            ]);
            $this->addLog("Pipeline complete: {$saved} records for {$pdfDate}.", 'success');
            $this->info("✔ Done — {$saved} price records saved for {$pdfDate}.");
            Log::info("CBSL Pipeline: {$saved} records for {$pdfDate}.");

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $msg = $e->getMessage();
            $this->error("Pipeline error: {$msg}");
            Log::error("CBSL Pipeline Exception", ['error' => $msg, 'trace' => $e->getTraceAsString()]);
            $this->addLog("Exception: {$msg}", 'error');
            $this->markPipelineError($msg);
            return Command::FAILURE;
        }
    }

    // ══════════════════════════════════════════════════════════════════
    //  Step 1 — Find the PDF URL
    //  Try today, then fall back up to 3 prior working days
    // ══════════════════════════════════════════════════════════════════

    private function findPdfUrl(Carbon $date): array
    {
        // Try today and the previous 3 days (in case PDF isn't published yet today)
        for ($i = 0; $i <= 3; $i++) {
            $d = $date->copy()->subDays($i);

            // Skip weekends (CBSL doesn't publish on Sat/Sun)
            if ($d->isWeekend()) {
                $this->line("   → Skipping weekend: {$d->toDateString()}");
                continue;
            }

            $dateStr = $d->format('Ymd');   // e.g. 20260619
            $url = sprintf(self::PDF_BASE_URL, $dateStr);
            $this->line("   → Trying: {$url}");

            try {
                $response = Http::timeout(20)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; LankaPriceBot/1.0)'])
                    ->head($url);

                if ($response->successful() || $response->status() === 302) {
                    return [$url, $d->toDateString()];
                }

                // Also try a GET with small range to confirm file exists
                $response = Http::timeout(20)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (compatible; LankaPriceBot/1.0)',
                        'Range'      => 'bytes=0-1023',
                    ])
                    ->get($url);

                if ($response->successful() || $response->status() === 206) {
                    return [$url, $d->toDateString()];
                }
            } catch (\Throwable $e) {
                $this->line("   → Error checking {$url}: " . $e->getMessage());
            }
        }

        return [null, null];
    }

    // ══════════════════════════════════════════════════════════════════
    //  Step 2 — Download the PDF bytes
    // ══════════════════════════════════════════════════════════════════

    private function downloadPdf(string $url): ?string
    {
        try {
            $response = Http::timeout(60)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; LankaPriceBot/1.0)'])
                ->get($url);

            if (!$response->successful()) {
                $this->warn("HTTP {$response->status()} downloading PDF.");
                return null;
            }

            $body = $response->body();
            if (strlen($body) < 5000) {
                $this->warn("PDF too small (" . strlen($body) . " bytes), likely invalid.");
                return null;
            }

            return $body;
        } catch (\Throwable $e) {
            $this->warn("Exception downloading PDF: " . $e->getMessage());
            return null;
        }
    }

    // ══════════════════════════════════════════════════════════════════
    //  Step 3 — Parse PDF text into price rows
    //
    //  The CBSL PDF table has this column structure:
    //
    //    Item | Unit | Pettah(Yest) | Pettah(Today) |
    //         Dambulla(Yest) | Dambulla(Today) |
    //         Narahenpita(Yest) | Narahenpita(Today) |
    //         Peliyagoda(Yest) | Peliyagoda(Today) |
    //         Negombo(Yest) | Negombo(Today)
    //
    //  pdfparser extracts text row-by-row, tab-separated.
    // ══════════════════════════════════════════════════════════════════

    private function parsePdf(string $pdfContent, string $date): array
    {
        $parser = new Parser();
        $pdf    = $parser->parseContent($pdfContent);
        $text   = $pdf->getText();

        $rows = [];

        // Find the table section — starts at "Wholesale and Retail Prices"
        $tableStart = strpos($text, 'Wholesale and Retail');
        if ($tableStart === false) {
            $this->warn('   → Could not find table section in PDF.');
            return [];
        }

        $tableText = substr($text, $tableStart);

        // Split into lines
        $lines = explode("\n", $tableText);

        // Skip the header rows (first 2 lines: title + "Yesterday Today Yesterday Today...")
        $dataLines = array_slice($lines, 2);

        foreach ($dataLines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Stop at footer notes
            if (str_starts_with($line, 'n.a. - Price is not reported')
                || str_starts_with($line, 'Price increased')
                || str_starts_with($line, 'Price decreased')
                || preg_match('/^\d{1,2}\s+\w+,\s+\d{4}$/', $line)   // date line like "19 June, 2026"
                || preg_match('/^[A-Z\s]+$/', $line)                   // section headers like "V E G E T A B L E S"
                || str_contains($line, 'Marandagahamula')
            ) {
                break;
            }

            // Each data line looks like:
            // "Beans\tRs./kg 450.00           350.00       450.00          315.00       ..."
            // OR
            // "Snake gourd Rs./kg 250.00           250.00       ..."
            // The item name may or may not be tab-separated from unit

            // Normalize tabs to spaces for consistent splitting
            $line = preg_replace('/\t+/', "\t", $line);

            // Extract item name (everything before Rs./ or Rs. )
            if (!preg_match('/^(.+?)\s+Rs\.\/(kg|Nut|Ltr|Each|litre)\s+(.+)$/i', $line, $m)) {
                continue;
            }

            $rawName  = trim($m[1]);
            // $unit  = $m[2]; // not currently used
            $priceStr = trim($m[3]);

            // Normalize vegetable name
            $vegId = VegetableNormalizer::normalize($rawName);
            if (!$vegId) {
                // Log unmatched names in debug mode
                $this->line("   [skip] Unmatched: '{$rawName}'");
                continue;
            }

            // Parse the 10 price values (5 markets × 2 columns: Yesterday, Today)
            $prices = $this->parsePriceRow($priceStr);

            // Map each market column to a database row
            foreach (self::MARKET_COLUMNS as $marketDef) {
                $marketId  = $marketDef['id'];
                $yestIdx   = $marketDef['yesterday'];
                $todayIdx  = $marketDef['today'];

                $priceYest  = $prices[$yestIdx] ?? null;
                $priceToday = $prices[$todayIdx] ?? null;

                // Only save if we have at least the today price
                if ($priceToday === null && $priceYest === null) continue;

                $change = null;
                $trend  = 'none';
                if ($priceToday !== null && $priceYest !== null && $priceYest > 0) {
                    $change = round((($priceToday - $priceYest) / $priceYest) * 100, 2);
                    $trend  = $this->trendFromChange($change);
                }

                $key = "{$date}|{$marketId}|{$vegId}";
                $rows[$key] = [
                    'date'            => $date,
                    'market_id'       => $marketId,
                    'vegetable_id'    => $vegId,
                    'price'           => $priceToday,
                    'price_yesterday' => $priceYest,
                    'change_percent'  => $change,
                    'trend'           => $trend,
                ];
            }
        }

        return array_values($rows);
    }

    /**
     * Parse a string of whitespace-separated price values like:
     *   "450.00           350.00       450.00          315.00       500.00  ..."
     *   Values may be "n.a." (not available).
     * Returns array of floats|nulls indexed 0..9.
     */
    private function parsePriceRow(string $priceStr): array
    {
        // Split on whitespace, collapsing multiple spaces
        $parts = preg_split('/\s+/', trim($priceStr));

        $values = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) continue;

            // Handle "n.a." and variants
            if (preg_match('/^n\.?a\.?$/i', $part)) {
                $values[] = null;
                continue;
            }

            // Handle numbers with commas like "1,000.00"
            $num = str_replace(',', '', $part);
            if (is_numeric($num)) {
                $values[] = (float) $num;
            } else {
                // Unknown token — treat as null
                $values[] = null;
            }
        }

        return $values;
    }

    private function trendFromChange(?float $change): string
    {
        if ($change === null) return 'none';
        if ($change > 0)  return 'up';
        if ($change < 0)  return 'down';
        return 'flat';
    }

    // ══════════════════════════════════════════════════════════════════
    //  Step 4 — Upsert to database
    // ══════════════════════════════════════════════════════════════════

    private function saveToDatabase(array $rows): int
    {
        $count = 0;
        foreach ($rows as $row) {
            PriceRecord::updateOrCreate(
                [
                    'date'         => $row['date'],
                    'market_id'    => $row['market_id'],
                    'vegetable_id' => $row['vegetable_id'],
                ],
                [
                    'price'           => $row['price'],
                    'price_yesterday' => $row['price_yesterday'],
                    'change_percent'  => $row['change_percent'],
                    'trend'           => $row['trend'],
                ]
            );
            $count++;
        }
        return $count;
    }

    // ══════════════════════════════════════════════════════════════════
    //  Cache helpers
    // ══════════════════════════════════════════════════════════════════

    private function addLog(string $message, string $type = 'info'): void
    {
        $logs = Cache::get('pipeline_logs', []);
        array_unshift($logs, [
            'timestamp' => now()->toIso8601String(),
            'message'   => $message,
            'type'      => $type,
        ]);
        Cache::put('pipeline_logs', array_slice($logs, 0, 100));
    }

    private function markPipelineError(string $message): void
    {
        Cache::put('pipeline_info', [
            'pipelineHealth'     => 'error',
            'lastError'          => $message,
            'lastErrorTime'      => now()->toIso8601String(),
            'lastAutoUpdateDate' => Cache::get('last_auto_update_date', ''),
        ]);
    }
}
