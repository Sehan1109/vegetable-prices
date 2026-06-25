<x-admin-layout>
    @slot('header')System Logs @endslot

    <div class="space-y-4">

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <form action="{{ route('admin.logs') }}" method="GET" class="flex gap-2 flex-1 max-w-xl">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-500"></i>
                    </div>
                    <input type="text" name="filter" value="{{ $filter }}" placeholder="Filter logs (e.g. ERROR, WARNING, exception name...)"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-700 rounded-md bg-[#0a101d] text-gray-300 placeholder-gray-500 focus:outline-none focus:border-emerald-500 sm:text-sm">
                </div>
                <button type="submit" class="px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm font-medium rounded-md border border-gray-700 transition-colors">Filter</button>
                @if($filter)
                    <a href="{{ route('admin.logs') }}" class="px-4 py-2 bg-[#450a0a] text-red-400 text-sm rounded-md border border-red-900 hover:bg-[#7f1d1d]">Clear</a>
                @endif
            </form>
            <a href="{{ route('admin.logs.download') }}" class="flex items-center px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm font-medium rounded-md border border-gray-700 transition-colors">
                <i data-lucide="download" class="w-4 h-4 mr-2"></i> Download Log
            </a>
        </div>

        {{-- Log Viewer --}}
        <div class="bg-[#0a101d] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="px-4 py-2 bg-[#111827] border-b border-gray-800 flex items-center justify-between">
                <div class="flex space-x-2">
                    <div class="w-3 h-3 rounded-full bg-red-500"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                </div>
                <span class="text-xs text-gray-500 font-mono">storage/logs/laravel.log {{ $filter ? '— filtered by: ' . $filter : '— last 200 lines' }}</span>
            </div>
            <div class="p-4 overflow-x-auto max-h-[70vh] overflow-y-auto" style="scrollbar-width: thin; scrollbar-color: #374151 transparent;">
                @if(empty($lines))
                    <p class="text-gray-500 text-sm">No log entries found{{ $filter ? ' matching filter' : '' }}.</p>
                @else
                    @foreach($lines as $line)
                        @php
                            $class = 'text-gray-400';
                            if (str_contains($line, '.ERROR') || str_contains($line, '.CRITICAL') || str_contains($line, 'Error') || str_contains($line, 'Exception')) {
                                $class = 'text-red-400';
                            } elseif (str_contains($line, '.WARNING')) {
                                $class = 'text-yellow-400';
                            } elseif (str_contains($line, '.INFO')) {
                                $class = 'text-emerald-400';
                            } elseif (str_contains($line, '.DEBUG')) {
                                $class = 'text-blue-400';
                            }
                        @endphp
                        <div class="font-mono text-xs {{ $class }} leading-5 hover:bg-gray-800/30 px-1 rounded">{{ $line }}</div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>
</x-admin-layout>
