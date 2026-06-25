<x-admin-layout>
    @slot('header')
        Dashboard Overview
    @endslot

    <div class="space-y-6">

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Today's Imports</span>
                    <i data-lucide="download" class="w-4 h-4 text-emerald-500"></i>
                </div>
                <div class="text-3xl font-bold text-white">{{ number_format($todayImports) }}</div>
                <p class="text-[10px] text-gray-500 mt-1">Price records added today</p>
            </div>
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Today's SEO Pages</span>
                    <i data-lucide="file-text" class="w-4 h-4 text-emerald-500"></i>
                </div>
                <div class="text-3xl font-bold text-white">{{ number_format($todaySeoPages) }}</div>
                <p class="text-[10px] text-gray-500 mt-1">Pages generated today</p>
            </div>
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Markets</span>
                    <i data-lucide="map-pin" class="w-4 h-4 text-emerald-500"></i>
                </div>
                <div class="text-3xl font-bold text-white">{{ number_format($totalMarkets) }}</div>
                <p class="text-[10px] text-gray-500 mt-1">Active monitored markets</p>
            </div>
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Vegetables</span>
                    <i data-lucide="leaf" class="w-4 h-4 text-emerald-500"></i>
                </div>
                <div class="text-3xl font-bold text-white">{{ number_format($totalVegetables) }}</div>
                <p class="text-[10px] text-gray-500 mt-1">Tracked crop varieties</p>
            </div>
        </div>

        {{-- Secondary Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-4">
                <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">Total Price Records</div>
                <div class="text-lg font-bold text-white">{{ number_format($totalPriceRecords) }}</div>
            </div>
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-4">
                <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">Total SEO Pages</div>
                <div class="text-lg font-bold text-white">{{ number_format($totalSeoPages) }}</div>
            </div>
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-4">
                <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">Pending Jobs</div>
                <div class="text-lg font-bold text-white {{ $pendingJobs > 0 ? 'text-yellow-400' : '' }}">{{ $pendingJobs }}</div>
            </div>
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-4">
                <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">Failed Jobs</div>
                <div class="text-lg font-bold {{ $failedJobs > 0 ? 'text-red-400' : 'text-white' }}">{{ $failedJobs }}</div>
            </div>
        </div>

        {{-- System Status + Recent Pipeline --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-lg font-bold text-white">System Telemetry</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @php
                        $tiles = [
                            ['icon' => 'server',    'label' => 'System Uptime',  'value' => 'Operational', 'ok' => true],
                            ['icon' => 'database',  'label' => 'Database Status', 'value' => ucfirst($dbStatus), 'ok' => $dbStatus === 'connected'],
                            ['icon' => 'layers',    'label' => 'Queue Worker',    'value' => $pendingJobs > 0 ? $pendingJobs . ' pending' : 'Idle', 'ok' => $pendingJobs === 0],
                            ['icon' => 'zap',       'label' => 'Cache',           'value' => 'Active',     'ok' => true],
                            ['icon' => 'clock',     'label' => 'Last Scrape',     'value' => $lastScrapeDate ?? 'Never', 'ok' => !empty($lastScrapeDate)],
                            ['icon' => 'hard-drive','label' => 'Storage Used',    'value' => $storageUsed, 'ok' => true],
                        ];
                    @endphp
                    @foreach($tiles as $tile)
                        <div class="bg-[#0a101d] rounded-lg p-4 border border-gray-800 flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-emerald-500/10 text-emerald-500 rounded-md">
                                    <i data-lucide="{{ $tile['icon'] }}" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 font-bold uppercase">{{ $tile['label'] }}</div>
                                    <div class="text-sm text-gray-300">{{ $tile['value'] }}</div>
                                </div>
                            </div>
                            <div class="w-2 h-2 rounded-full {{ $tile['ok'] ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]' : 'bg-red-500' }}"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent Pipeline Logs --}}
            <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-lg font-bold text-white">Latest Pipeline Runs</h2>
                </div>
                <div class="flex-1 divide-y divide-gray-800 overflow-y-auto max-h-80">
                    @forelse($pipelineLogs as $log)
                        <div class="p-4 flex items-start space-x-3 hover:bg-[#0a101d] transition-colors">
                            <div class="mt-0.5 shrink-0">
                                @if($log['type'] === 'success')
                                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                                @elseif($log['type'] === 'error')
                                    <i data-lucide="x-circle" class="w-4 h-4 text-red-500"></i>
                                @else
                                    <i data-lucide="info" class="w-4 h-4 text-blue-400"></i>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-gray-300 truncate">{{ $log['message'] }}</p>
                                <p class="text-[10px] text-gray-500 mt-1">{{ \Carbon\Carbon::parse($log['timestamp'])->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500 text-sm">
                            <i data-lucide="activity" class="w-6 h-6 mx-auto mb-2 text-gray-600"></i>
                            No pipeline runs yet.
                        </div>
                    @endforelse
                </div>
                <div class="px-6 py-3 border-t border-gray-800 bg-[#0a101d] shrink-0">
                    <a href="{{ route('admin.scraper') }}" class="text-xs font-medium text-emerald-500 hover:text-emerald-400">Open Scraper Console &rarr;</a>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>