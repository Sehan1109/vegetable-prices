<x-app-layout>
    <div class="min-h-screen bg-[#070b14] text-gray-300 font-sans selection:bg-emerald-500 selection:text-white pb-12">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-6">
            <h1 class="text-3xl font-extrabold text-white tracking-wide">System Control Matrix</h1>
            <p class="text-sm text-gray-500 mt-2 max-w-3xl">
                Directly manipulate Ceylon data pipelines, trigger raw bulletin scythe routines, perform database resets, and inspect operational telemetry traces safely.
            </p>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
            <div class="bg-[#052e16] border border-emerald-500/50 text-emerald-400 px-4 py-3 rounded-md flex items-center space-x-3 shadow-[0_0_15px_rgba(16,185,129,0.1)]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-[#450a0a] border border-red-500/50 text-red-400 px-4 py-3 rounded-md flex items-center space-x-3 shadow-[0_0_15px_rgba(239,68,68,0.1)]">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            @endif

            <div class="bg-[#111827] border border-gray-800 rounded-xl overflow-hidden shadow-2xl">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                        
                        <div class="flex items-start space-x-4">
                            <div class="p-3 bg-[#0a101d] border border-gray-700 rounded-lg text-emerald-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                            </div>
                            <div>
                                <div class="flex items-center space-x-3 mb-1">
                                    <span class="text-xs font-bold text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded tracking-wider">ADMIN CONSOLE</span>
                                    <span class="text-xs text-gray-500 flex items-center"><span class="w-1.5 h-1.5 rounded-full bg-gray-500 mr-1.5"></span> Secure SSL</span>
                                </div>
                                <h2 class="text-2xl font-bold text-white tracking-tight">Admin Pipeline Monitor</h2>
                                <p class="text-sm text-gray-400 mt-1">HARTI price crawl traces & local database performance</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <form action="{{ route('admin.scrape') }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center space-x-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-md transition-colors shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    <span>FORCE HARTI SCAN</span>
                                </button>
                            </form>
                            <button class="p-2.5 bg-[#0a101d] border border-gray-700 hover:border-gray-500 text-gray-400 rounded-md transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            <button class="flex items-center space-x-2 px-4 py-2.5 bg-[#0a101d] border border-gray-700 hover:border-gray-500 text-gray-300 text-sm font-medium rounded-md transition-colors">
                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span>Lock Console</span>
                            </button>
                        </div>
                    </div>

                    <div class="mt-8 bg-[#0a1a15] border border-emerald-900/50 rounded-md p-3 flex items-center space-x-3">
                        <span class="relative flex h-3 w-3">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                        <span class="text-sm font-mono text-emerald-400">Scrape process initialized. Logging live steps below...</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">CRAWLER ENGINE</span>
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 rounded-full bg-gray-500"></div>
                        <span class="text-sm font-bold text-white tracking-wide">IDLE - LISTENING</span>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-2">Next Schedule: Daily 13:00 (1:00 PM) SLST</p>
                </div>
                <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">OCR SCANNERS</span>
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>
                        <span class="text-sm font-bold text-white tracking-wide">OCR RESOLVED</span>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-2">Parser Engine version 1.1-CJS</p>
                </div>
                <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">LOCAL DB NODE</span>
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>
                        <span class="text-sm font-bold text-white tracking-wide">SQLITE OPERATIONAL</span>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-2">Tracked Prices: 2,700 items</p>
                </div>
                <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">SWEEPER SCHEDULER</span>
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>
                        <span class="text-sm font-bold text-white tracking-wide">ONLINE - MANUAL</span>
                    </div>
                    <p class="text-[10px] text-gray-500 mt-2">System Uptime: 11m 11s</p>
                </div>
            </div>

            <div class="pt-4">
                <h3 class="flex items-center text-xs font-bold text-gray-400 tracking-widest mb-4">
                    <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    SYSTEM PERFORMANCE METRICS
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                        <div class="flex justify-between text-[10px] font-bold text-gray-400 mb-3">
                            <span class="uppercase">CPU UTILIZATION</span>
                            <span class="text-emerald-500">6.5%</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-1.5 mb-6">
                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: 6.5%"></div>
                        </div>
                        <div class="h-10 border-b border-emerald-500/30 flex items-end opacity-50">
                            <svg viewBox="0 0 100 20" class="w-full h-full stroke-emerald-500" fill="none" stroke-width="1"><polyline points="0,15 10,12 20,16 30,8 40,14 50,10 60,15 70,5 80,12 90,8 100,10"/></svg>
                        </div>
                        <p class="text-[10px] text-gray-600 mt-3">Trace: Cloud Run sandbox container vCores</p>
                    </div>

                    <div class="bg-[#111827] border border-gray-800 rounded-lg p-5 flex flex-col justify-between">
                        <div class="text-[10px] font-bold text-gray-400 uppercase mb-2">MEMORY FOOTPRINT</div>
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-2xl font-bold text-white">147.1</span>
                                <span class="text-xs text-gray-500 ml-1">MB</span>
                            </div>
                            <div class="w-12 h-12 rounded-full border-4 border-gray-800 border-t-emerald-500 flex items-center justify-center">
                                <span class="text-[10px] text-gray-400">29%</span>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-4">Limit constraint: <span class="text-white font-bold">512.0 MB maximum</span></p>
                    </div>

                    <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                        <div class="flex justify-between text-[10px] font-bold text-gray-400 mb-3">
                            <span class="uppercase">DATABASE LATENCY</span>
                            <span class="text-emerald-500">4.7 ms</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-1.5 mb-6">
                            <div class="bg-cyan-500 h-1.5 rounded-full" style="width: 20%"></div>
                        </div>
                         <div class="h-10 border-b border-cyan-500/30 flex items-end opacity-50">
                            <svg viewBox="0 0 100 20" class="w-full h-full stroke-cyan-500" fill="none" stroke-width="1"><polyline points="0,15 20,16 40,14 60,15 80,12 100,16"/></svg>
                        </div>
                        <p class="text-[10px] text-gray-600 mt-3">Trace: SQLite process roundtrip ping</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pb-8">
                <div class="bg-[#111827] border border-gray-800 rounded-lg p-4">
                    <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">CACHE QUERY HITS</div>
                    <div class="text-lg font-bold text-white">98.9%</div>
                </div>
                <div class="bg-[#111827] border border-gray-800 rounded-lg p-4">
                    <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">ACTIVE VIEWERS</div>
                    <div class="text-lg font-bold text-white flex items-center">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></span>
                        2 Viewer
                    </div>
                </div>
                <div class="bg-[#111827] border border-gray-800 rounded-lg p-4">
                    <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">SYNC EXECUTIONS</div>
                    <div class="text-lg font-bold text-white">1 Runs</div>
                </div>
                <div class="bg-[#111827] border border-gray-800 rounded-lg p-4">
                    <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">LAST SYNC TIME</div>
                    <div class="text-lg font-bold text-white">{{ date('m/d/Y, h:i:s A') }}</div>
                </div>
            </div>

            @if(session('scrape_output'))
            <div class="bg-[#0a101d] border border-gray-800 shadow-2xl rounded-lg overflow-hidden mt-6">
                <div class="px-4 py-2 bg-[#111827] border-b border-gray-800 flex items-center justify-between">
                    <div class="flex space-x-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                    <span class="text-xs text-gray-500 font-mono">Terminal - Scraper Logs</span>
                </div>
                <div class="p-6">
                    <pre class="text-sm text-emerald-400 font-mono whitespace-pre-wrap overflow-x-auto leading-relaxed">{{ session('scrape_output') }}</pre>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>