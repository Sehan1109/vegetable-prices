<x-admin-layout>
    @slot('header')Database State @endslot

    <div class="space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach([
                ['Markets',       $stats['markets'],       'map-pin'],
                ['Vegetables',    $stats['vegetables'],    'leaf'],
                ['Price Records', $stats['price_records'], 'line-chart'],
                ['SEO Pages',     $stats['seo_pages'],     'file-text'],
            ] as [$label, $val, $icon])
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $label }}</span>
                    <i data-lucide="{{ $icon }}" class="w-4 h-4 text-emerald-500"></i>
                </div>
                <div class="text-3xl font-bold text-white">{{ number_format($val) }}</div>
            </div>
            @endforeach
        </div>

        {{-- DB Info --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">Database Size</div>
                <div class="text-2xl font-bold text-white mt-2">{{ $dbSize }}</div>
                <p class="text-xs text-gray-500 mt-1">PostgreSQL database size</p>
            </div>
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">Latest Data Date</div>
                <div class="text-2xl font-bold text-white mt-2">{{ $latestDate ?? 'No data yet' }}</div>
                <p class="text-xs text-gray-500 mt-1">Most recent price record date</p>
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-lg font-bold text-white">Manage Data</h2>
            </div>
            <div class="p-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="{{ route('admin.markets.index') }}" class="flex items-center px-4 py-3 bg-[#0a101d] hover:bg-[#1f2937] border border-gray-800 rounded-lg text-sm text-gray-300 hover:text-white transition-colors">
                    <i data-lucide="map-pin" class="w-4 h-4 mr-2 text-emerald-500"></i> Markets
                </a>
                <a href="{{ route('admin.vegetables.index') }}" class="flex items-center px-4 py-3 bg-[#0a101d] hover:bg-[#1f2937] border border-gray-800 rounded-lg text-sm text-gray-300 hover:text-white transition-colors">
                    <i data-lucide="leaf" class="w-4 h-4 mr-2 text-emerald-500"></i> Vegetables
                </a>
                <a href="{{ route('admin.prices.index') }}" class="flex items-center px-4 py-3 bg-[#0a101d] hover:bg-[#1f2937] border border-gray-800 rounded-lg text-sm text-gray-300 hover:text-white transition-colors">
                    <i data-lucide="line-chart" class="w-4 h-4 mr-2 text-emerald-500"></i> Prices
                </a>
                <a href="{{ route('admin.seo') }}" class="flex items-center px-4 py-3 bg-[#0a101d] hover:bg-[#1f2937] border border-gray-800 rounded-lg text-sm text-gray-300 hover:text-white transition-colors">
                    <i data-lucide="file-text" class="w-4 h-4 mr-2 text-emerald-500"></i> SEO Pages
                </a>
            </div>
        </div>

    </div>
</x-admin-layout>
