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
    protected $description = 'Scrape Daily Food Commodity Prices from the CBSL (primary) and HARTI (fallback)';

    private const PDF_BASE_URL = 'https://www.cbsl.gov.lk/sites/default/files/cbslweb_documents/statistics/pricerpt/price_report_%s_e.pdf';
    private const HARTI_INDEX_URL = 'https://www.harti.gov.lk/daily-price.php';

    private const MARKET_COLUMNS = [
        0 => ['id' => 'pettah',       'yesterday' => 0, 'today' => 1],
        1 => ['id' => 'dambulla',     'yesterday' => 2, 'today' => 3],
        2 => ['id' => 'narahenpita',  'yesterday' => 4, 'today' => 5],
        3 => ['id' => 'peliyagoda',   'yesterday' => 6, 'today' => 7],
        4 => ['id' => 'negombo',      'yesterday' => 8, 'today' => 9],
    ];

    private const HARTI_MARKET_COLUMNS = [
        0 => 'peliyagoda',
        1 => 'kandy',
        2 => 'dambulla',
        3 => 'meegoda',
        4 => 'norochchole',
        5 => 'thambuththegama',
        6 => 'keppetipola',
        7 => 'nuwara-eliya',
        8 => 'bandarawela',
        9 => 'veyangoda',
    ];

    private ?string $hartiIndexHtml = null;

    public function handle(): int
    {
        $targetDate = $this->option('date')
            ? Carbon::parse($this->option('date'), 'Asia/Colombo')
            : Carbon::now('Asia/Colombo');

        $today = $targetDate->toDateString();

        if (!$this->option('force') && !$this->option('date')) {
            if (Cache::get('last_auto_update_date') === $today) {
                $this->info("Already scraped today ({$today}). Use --force to re-run.");
                return Command::SUCCESS;
            }
        }

        $this->info('═══════════════════════════════════════════════════');
        $this->info('  Daily Price Report — PDF Extraction Pipeline');
        $this->info('═══════════════════════════════════════════════════');

        for ($i = 0; $i <= 7; $i++) {
            $currentDate = $targetDate->copy()->subDays($i);
            $this->info("\n[CHECKING DATE: {$currentDate->toDateString()}]");

            // --- PRIMARY PIPELINE (HARTI) ---
            $this->info('[PRIMARY] Attempting HARTI scrape...');
            [$hartiUrl, $hartiDate] = $this->findHartiPdfUrl($currentDate);

            if ($hartiUrl) {
                $this->line("   → PDF URL: {$hartiUrl}");
                $this->line("   → Report date: {$hartiDate}");

                $this->info('[HARTI 2/4] Downloading PDF...');
                $pdfContent = $this->downloadPdf($hartiUrl);

                if ($pdfContent) {
                    $this->info('[HARTI 3/4] Parsing price data from PDF table...');
                    try {
                        $rows = $this->parseHartiPdf($pdfContent, $hartiDate);

                        if (!empty($rows)) {
                            $this->line('   → Parsed ' . count($rows) . ' price rows.');

                            $this->info('[HARTI 4/4] Saving to database...');
                            $saved = $this->saveToDatabase($rows);
                            $this->line("   → {$saved} records saved/updated.");

                            Cache::put('last_auto_update_date', $hartiDate);
                            Cache::put('pipeline_info', [
                                'pipelineHealth'     => 'healthy',
                                'lastError'          => null,
                                'lastErrorTime'      => null,
                                'lastAutoUpdateDate' => $hartiDate,
                                'source'             => 'harti'
                            ]);
                            Cache::forever('scraped_pdf_url', $hartiUrl);
                            Cache::forever('scraped_pdf_date', $hartiDate);
                            $this->addLog("[HARTI] Pipeline complete: {$saved} records for {$hartiDate}.", 'success');
                            $this->info("✔ Done (HARTI) — {$saved} price records saved for {$hartiDate}.");
                            return Command::SUCCESS;
                        } else {
                            $this->warn('[HARTI] Parsed 0 rows — PDF table structure may have changed. Proceeding to fallback.');
                        }
                    } catch (\Throwable $e) {
                        $this->warn("HARTI Pipeline error: " . $e->getMessage() . ". Proceeding to fallback.");
                        Log::error('HARTI Pipeline Exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                    }
                } else {
                    $this->warn('[HARTI] Failed to download PDF. Proceeding to fallback.');
                }
            } else {
                $this->warn('[HARTI] PDF not found for ' . $currentDate->toDateString() . '. Proceeding to fallback.');
            }

            // --- FALLBACK PIPELINE (CBSL) ---
            if ($currentDate->isWeekend()) {
                $this->warn('[CBSL] Skipping CBSL scrape (Weekend).');
                continue;
            }

            $this->info('[FALLBACK] Attempting CBSL scrape...');
            [$cbslUrl, $cbslDate] = $this->findCbslPdfUrl($currentDate);

            if ($cbslUrl) {
                $this->line("   → PDF URL: {$cbslUrl}");
                $this->line("   → Report date: {$cbslDate}");

                $this->info('[CBSL 2/4] Downloading PDF...');
                $pdfContent = $this->downloadPdf($cbslUrl);

                if ($pdfContent) {
                    $this->info('[CBSL 3/4] Parsing price data from PDF table...');
                    $rows = $this->parseCbslPdf($pdfContent, $cbslDate);

                    if (!empty($rows)) {
                        $this->info('[CBSL 4/4] Saving to database...');
                        $saved = $this->saveToDatabase($rows);
                        $this->line("   → {$saved} records saved/updated.");

                        Cache::put('last_auto_update_date', $cbslDate);
                        Cache::put('pipeline_info', [
                            'pipelineHealth'     => 'healthy',
                            'lastError'          => null,
                            'lastErrorTime'      => null,
                            'lastAutoUpdateDate' => $cbslDate,
                            'source'             => 'cbsl'
                        ]);
                        Cache::forever('scraped_pdf_url', $cbslUrl);
                        Cache::forever('scraped_pdf_date', $cbslDate);
                        $this->addLog("[CBSL] Pipeline complete: {$saved} records for {$cbslDate}.", 'success');
                        $this->info("✔ Done (CBSL fallback) — {$saved} price records saved for {$cbslDate}.");
                        return Command::SUCCESS;
                    } else {
                        $this->warn('[CBSL] Parsed 0 rows from CBSL PDF — structure may have changed.');
                    }
                } else {
                    $this->warn('[CBSL] Failed to download PDF from CBSL.');
                }
            } else {
                $this->warn('[CBSL] PDF not found for ' . $currentDate->toDateString() . '.');
            }
        }

        $msg = 'Could not locate a price report PDF on either HARTI or CBSL for the last 7 days.';
        $this->error($msg);
        $this->addLog($msg, 'error');
        $this->markPipelineError($msg);
        return Command::FAILURE;
    }

    private function findCbslPdfUrl(Carbon $date): array
    {
        $dateStr = $date->format('Ymd');
        $url = sprintf(self::PDF_BASE_URL, $dateStr);
        $this->line("   → Trying CBSL: {$url}");

        try {
            $response = Http::timeout(20)->withHeaders(['User-Agent' => 'LankaPriceBot/1.0'])->head($url);
            if ($response->successful() || $response->status() === 302) return [$url, $date->toDateString()];
            $response = Http::timeout(20)->withHeaders(['User-Agent' => 'LankaPriceBot/1.0', 'Range' => 'bytes=0-1023'])->get($url);
            if ($response->successful() || $response->status() === 206) return [$url, $date->toDateString()];
        } catch (\Throwable $e) {}

        return [null, null];
    }

    private function findHartiPdfUrl(Carbon $date): array
    {
        $this->line("   → Checking HARTI index page...");
        try {
            if ($this->hartiIndexHtml === null) {
                $response = Http::timeout(20)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get(self::HARTI_INDEX_URL);
                if (!$response->successful()) return [null, null];
                $this->hartiIndexHtml = $response->body();
            }

            $html = $this->hartiIndexHtml;
            preg_match_all('/href=["\']([^"\']+\.pdf)["\']/i', $html, $matches);
            $links = $matches[1] ?? [];
            
            // HARTI uses "2026.06.19" in the filename
            $targetDateStr = $date->format('Y.m.d');
            $targetDateDash = $date->toDateString(); // "2026-06-19" just in case
            
            foreach ($links as $link) {
                if (str_contains($link, $targetDateStr) || str_contains($link, $targetDateDash)) {
                    $fullUrl = str_starts_with($link, 'http') ? $link : "https://www.harti.gov.lk/" . ltrim($link, '/');
                    return [$fullUrl, $date->toDateString()];
                }
            }
        } catch (\Throwable $e) {
            $this->warn("HARTI index check failed: " . $e->getMessage());
        }
        return [null, null];
    }

    private function downloadPdf(string $url): ?string
    {
        try {
            $response = Http::timeout(60)->withHeaders(['User-Agent' => 'Mozilla/5.0'])->get($url);
            if (!$response->successful()) return null;
            $body = $response->body();
            if (strlen($body) < 5000) return null;
            return $body;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function parseCbslPdf(string $pdfContent, string $date): array
    {
        $parser = new Parser();
        $pdf    = $parser->parseContent($pdfContent);
        $text   = $pdf->getText();
        $rows   = [];

        $tableStart = strpos($text, 'Wholesale and Retail');
        if ($tableStart === false) return [];
        $tableText = substr($text, $tableStart);
        $lines = explode("\n", $tableText);
        $dataLines = array_slice($lines, 2);

        foreach ($dataLines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            if (str_starts_with($line, 'n.a.') || preg_match('/^\d{1,2}\s+\w+,\s+\d{4}$/', $line) || preg_match('/^[A-Z\s]+$/', $line) || str_contains($line, 'Marandagahamula')) break;

            $line = preg_replace('/\t+/', "\t", $line);
            if (!preg_match('/^(.+?)\s+Rs\.\/(kg|Nut|Ltr|Each|litre)\s+(.+)$/i', $line, $m)) continue;

            $rawName  = trim($m[1]);
            $priceStr = trim($m[3]);
            $vegId = VegetableNormalizer::normalize($rawName);
            if (!$vegId) continue;

            $parts = preg_split('/\s+/', trim($priceStr));
            $prices = [];
            foreach ($parts as $part) {
                if (preg_match('/^n\.?a\.?$/i', $part)) { $prices[] = null; continue; }
                $num = str_replace(',', '', $part);
                $prices[] = is_numeric($num) ? (float) $num : null;
            }

            foreach (self::MARKET_COLUMNS as $marketDef) {
                $marketId  = $marketDef['id'];
                $priceYest  = $prices[$marketDef['yesterday']] ?? null;
                $priceToday = $prices[$marketDef['today']] ?? null;
                if ($priceToday === null && $priceYest === null) continue;

                $change = null;
                $trend  = 'none';
                if ($priceToday !== null && $priceYest !== null && $priceYest > 0) {
                    $change = round((($priceToday - $priceYest) / $priceYest) * 100, 2);
                    $trend  = $this->trendFromChange($change);
                }
                $rows["{$date}|{$marketId}|{$vegId}"] = [
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

    private function parseHartiPdf(string $pdfContent, string $date): array
    {
        $parser = new Parser();
        $pdf    = $parser->parseContent($pdfContent);
        $text   = $pdf->getText();

        $rows            = [];
        $lines           = explode("\n", $text);
        $inDataSection   = false;
        $skippedHeaders  = ['Low country Vegetable', 'Low Country Vegetable', 'Up Country Vegetable', 'Variety'];
        $stopPatterns    = ['/^\s{30,}$/', '/^\d{4}\.\d{2}\.\d{2}/'];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (!$inDataSection) {
                if (str_contains($trimmed, 'Up Country') || str_contains($trimmed, 'Variety')) $inDataSection = true;
                continue;
            }

            foreach ($stopPatterns as $pattern) {
                if (preg_match($pattern, $line)) goto finishParsing;
            }

            if (str_starts_with($trimmed, 'Banana') || str_starts_with($trimmed, 'Other Fruits') || str_starts_with($trimmed, 'Pineapple') || str_starts_with($trimmed, 'Hector') || str_starts_with($trimmed, 'Data Management')) break;

            if (empty($trimmed)) continue;
            if (in_array($trimmed, $skippedHeaders, true) || preg_match('/^(Up Country|Low [Cc]ountry)\s+Vegetable/i', $trimmed)) continue;

            $normalised = str_replace("\t", " ", $trimmed);
            $name       = null;
            $priceStr   = null;

            if (preg_match('/\d{2,3}\s*-\s*\d{2,3}/', $normalised, $m, PREG_OFFSET_CAPTURE)) {
                $rangeStart = $m[0][1];
                $before     = substr($normalised, 0, $rangeStart);
                $name       = preg_replace('/(\s+-\s*)+$/', '', $before);
                $name       = trim($name);
                $priceStr   = trim(substr($normalised, strlen($name)));
            } elseif (preg_match('/^(.+?)\s+([-\s]+)$/', $normalised, $allNullMatch)) {
                $name     = trim($allNullMatch[1]);
                $priceStr = trim($allNullMatch[2]);
            } else {
                continue; 
            }

            if (empty($name) || empty($priceStr)) continue;

            $vegId = VegetableNormalizer::normalize($name);
            if (!$vegId) continue;

            $str    = str_replace("\t", " ", trim($priceStr));
            $tokens = [];
            $pos    = 0;
            $len    = strlen($str);

            while ($pos < $len && count($tokens) < 10) {
                while ($pos < $len && $str[$pos] === ' ') $pos++;
                if ($pos >= $len) break;

                if (ctype_digit($str[$pos])) {
                    $numStart = $pos;
                    while ($pos < $len && ctype_digit($str[$pos])) $pos++;
                    $firstNum = substr($str, $numStart, $pos - $numStart);
                    $firstLen = strlen($firstNum);

                    if ($pos < $len && $str[$pos] === '-') {
                        $pos++;
                        $savedPos   = $pos;
                        $validRange = false;

                        foreach ([$firstLen, $firstLen + 1] as $targetLen) {
                            $pos        = $savedPos;
                            $digitCount = 0;

                            while ($pos < $len && ctype_digit($str[$pos]) && $digitCount < $targetLen) {
                                $pos++;
                                $digitCount++;
                            }

                            if ($digitCount >= 2) {
                                $secondNum = substr($str, $savedPos, $digitCount);
                                $lo = (int) $firstNum;
                                $hi = (int) $secondNum;

                                if ($hi >= $lo) {
                                    $tokens[]   = round(($lo + $hi) / 2, 2);
                                    $validRange = true;
                                    break;
                                }
                            }
                        }
                        if (!$validRange) $pos = $savedPos;
                    }
                } elseif ($str[$pos] === '-') {
                    $tokens[] = null;
                    $pos++;
                } else {
                    $pos++;
                }
            }

            $yesterday = Carbon::parse($date, 'Asia/Colombo')->subDay()->toDateString();

            foreach (self::HARTI_MARKET_COLUMNS as $colIdx => $marketId) {
                $price = $tokens[$colIdx] ?? null;
                if ($price === null) continue;

                $priceYest = PriceRecord::where(['date' => $yesterday, 'market_id' => $marketId, 'vegetable_id' => $vegId])->value('price');
                $change = null;
                $trend  = 'none';
                if ($priceYest !== null && $priceYest > 0) {
                    $change = round((($price - $priceYest) / $priceYest) * 100, 2);
                    $trend  = $this->trendFromChange($change);
                }

                $rows["{$date}|{$marketId}|{$vegId}"] = [
                    'date'            => $date,
                    'market_id'       => $marketId,
                    'vegetable_id'    => $vegId,
                    'price'           => $price,
                    'price_yesterday' => $priceYest,
                    'change_percent'  => $change,
                    'trend'           => $trend,
                ];
            }
        }

        finishParsing:
        return array_values($rows);
    }

    private function trendFromChange(?float $change): string
    {
        if ($change === null) return 'none';
        if ($change > 0)  return 'up';
        if ($change < 0)  return 'down';
        return 'flat';
    }

    private function saveToDatabase(array $rows): int
    {
        $count = 0;
        foreach ($rows as $row) {
            PriceRecord::updateOrCreate(
                ['date' => $row['date'], 'market_id' => $row['market_id'], 'vegetable_id' => $row['vegetable_id']],
                ['price' => $row['price'], 'price_yesterday' => $row['price_yesterday'], 'change_percent' => $row['change_percent'], 'trend' => $row['trend']]
            );
            $count++;
        }
        return $count;
    }

    private function addLog(string $message, string $type = 'info'): void
    {
        $logs = Cache::get('pipeline_logs', []);
        array_unshift($logs, ['timestamp' => now()->toIso8601String(), 'message' => $message, 'type' => $type]);
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
