<x-admin-layout>
    @slot('header')HARTI Scraper Console @endslot

    <div class="space-y-6">

        {{-- Control Panel --}}
        <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-2 h-2 rounded-full {{ isset($pipelineInfo['pipelineHealth']) && $pipelineInfo['pipelineHealth'] === 'healthy' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]' : 'bg-gray-500' }}"></div>
                    <h2 class="text-lg font-bold text-white">Scraper Control Panel</h2>
                </div>
                <span class="text-xs text-gray-500">Last successful scrape: <span class="text-gray-300">{{ $lastScrapeDate ?? 'Never' }}</span></span>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Run Scraper --}}
                <div class="bg-[#0a101d] border border-gray-800 rounded-lg p-5">
                    <h3 class="font-semibold text-white mb-1">Run Scraper Now</h3>
                    <p class="text-xs text-gray-500 mb-4">Manually trigger the HARTI/CBSL scraping pipeline with force flag.</p>
                    <form action="{{ route('admin.scraper.run') }}" method="POST">
                        @csrf
                        <input type="hidden" name="force" value="1">
                        <button type="submit" class="flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-md transition-colors">
                            <i data-lucide="play" class="w-4 h-4 mr-2"></i> Force Run Scraper
                        </button>
                    </form>
                </div>

                {{-- Scrape Specific Date --}}
                <div class="bg-[#0a101d] border border-gray-800 rounded-lg p-5">
                    <h3 class="font-semibold text-white mb-1">Scrape Specific Date</h3>
                    <p class="text-xs text-gray-500 mb-4">Fetch price data for a particular date (useful for backfilling missed days).</p>
                    <form action="{{ route('admin.scraper.run') }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" class="border border-gray-700 rounded-md py-2 px-3 bg-[#111827] text-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <button type="submit" class="px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm font-medium rounded-md border border-gray-700 transition-colors">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>

                {{-- Download PDF --}}
                @if($pdfUrl)
                <div class="bg-[#0a101d] border border-gray-800 rounded-lg p-5">
                    <h3 class="font-semibold text-white mb-1">Download Last PDF</h3>
                    <p class="text-xs text-gray-500 mb-4">Download the raw PDF that was last processed ({{ $pdfDate ?? 'unknown date' }}).</p>
                    <a href="{{ $pdfUrl }}" target="_blank" class="flex items-center px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm font-medium rounded-md border border-gray-700 transition-colors w-fit">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i> Download PDF
                    </a>
                </div>
                @endif

                {{-- Backfill --}}
                <div class="bg-[#0a101d] border border-gray-800 rounded-lg p-5">
                    <h3 class="font-semibold text-white mb-1">Historical Backfill</h3>
                    <p class="text-xs text-gray-500 mb-4">Queue a backfill job to retrieve data for recent days not yet scraped.</p>
                    <form action="{{ route('admin.scraper.backfill') }}" method="POST" class="flex gap-2 items-center">
                        @csrf
                        <select name="days" class="border border-gray-700 rounded-md py-2 px-3 bg-[#111827] text-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="7">Last 7 days</option>
                            <option value="14">Last 14 days</option>
                            <option value="30">Last 30 days</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-500 text-white text-sm font-medium rounded-md border border-yellow-700 transition-colors">
                            <i data-lucide="history" class="w-4 h-4 inline mr-1"></i> Backfill
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Scraper Output --}}
        @if(session('scrape_output'))
        <div class="bg-[#0a101d] border border-gray-800 shadow-2xl rounded-lg overflow-hidden">
            <div class="px-4 py-2 bg-[#111827] border-b border-gray-800 flex items-center justify-between">
                <div class="flex space-x-2">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                </div>
                <span class="text-xs text-gray-500 font-mono">Terminal — Scraper Output</span>
            </div>
            <div class="p-6 overflow-x-auto">
                <pre class="text-sm text-emerald-400 font-mono whitespace-pre-wrap leading-relaxed">{{ session('scrape_output') }}</pre>
            </div>
        </div>
        @endif

        {{-- Recent Logs --}}
        <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-lg font-bold text-white">Pipeline Log History</h2>
            </div>
            <div class="divide-y divide-gray-800">
                @forelse($pipelineLogs as $log)
                    <div class="px-6 py-3 flex items-start space-x-4 hover:bg-[#0a101d]">
                        <div class="shrink-0 mt-0.5">
                            @if($log['type'] === 'success')
                                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                            @elseif($log['type'] === 'error')
                                <i data-lucide="x-circle" class="w-4 h-4 text-red-500"></i>
                            @else
                                <i data-lucide="info" class="w-4 h-4 text-blue-400"></i>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-300">{{ $log['message'] }}</p>
                        </div>
                        <span class="text-[10px] text-gray-600 shrink-0">{{ \Carbon\Carbon::parse($log['timestamp'])->format('d M Y H:i') }}</span>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-gray-500">
                        <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-3 text-gray-600"></i>
                        <p>No pipeline logs yet. Run the scraper to see activity here.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-admin-layout>
