<!-- District Heatmap Dashboard Component -->
<div 
    x-data="CeylonHeatmap()" 
    x-init="initData()" 
    class="space-y-8 animate-fade-in animate-duration-300 relative" 
    id="crop-heatmap-dashboard-root"
>
    
    <!-- =========================================================================
         LOADING / ERROR STATES (Overlays)
         ========================================================================= -->
    <div x-show="serverLoading && !offlineFallback" class="absolute inset-0 z-50 bg-white border border-slate-100 rounded-3xl p-10 flex flex-col items-center justify-center min-h-[500px]" id="heatmap-loader">
        <div class="relative flex flex-col items-center justify-center space-y-6">
            <div class="w-16 h-16 border-4 border-slate-100 border-t-emerald-600 rounded-full animate-spin"></div>
            <div class="absolute top-4">
                <i data-lucide="compass" class="w-8 h-8 text-emerald-500 animate-pulse"></i>
            </div>
            <div class="text-center space-y-2">
                <h4 class="font-extrabold text-slate-900 tracking-tight text-lg">Synchronizing Geographical Database...</h4>
                <p class="text-slate-600 text-xs max-w-sm">
                    Retrieving live spot vegetable prices from Sri Lanka's HARTI central data exchange to compute localized multipliers. 
                </p>
            </div>
            <button 
                type="button"
                @click="offlineFallback = true; serverLoading = false;" 
                class="text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-100 font-mono text-[10px] font-bold py-1.5 px-3 rounded-lg transition-colors cursor-pointer"
            >
                USE OFFLINE DATA CACHE
            </button>
        </div>
    </div>

    <div x-show="serverError && !offlineFallback" class="absolute inset-0 z-50 bg-white border border-slate-100 rounded-3xl p-8 max-w-xl mx-auto flex flex-col items-center justify-center text-center space-y-6 shadow-3xs" id="heatmap-error">
        <div class="w-14 h-14 bg-rose-50 border border-rose-150 rounded-full flex items-center justify-center text-rose-600">
            <i data-lucide="alert-triangle" class="w-6 h-6 animate-bounce"></i>
        </div>
        <div class="space-y-2 text-center">
            <h4 class="font-extrabold text-slate-900 text-lg">Database Ingestion Failure</h4>
            <p class="text-slate-500 text-xs leading-relaxed max-w-md">
                The system was unable to establish a secure HTTP connection to the backend price scraper daemon. Toggle the offline backup cache to utilize offline default statistics.
            </p>
        </div>
        <div class="flex gap-3">
            <button
                type="button"
                @click="fetchData()"
                class="bg-slate-900 text-white rounded-xl py-2 px-4 text-xs font-bold font-sans hover:bg-slate-800 transition-colors flex items-center gap-1.5 cursor-pointer"
            >
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                Retry server connection
            </button>
            <button
                type="button"
                @click="offlineFallback = true; serverError = false;"
                class="bg-emerald-50 text-emerald-800 border border-emerald-100 rounded-xl py-2 px-4 text-xs font-bold font-sans hover:bg-emerald-100 transition-colors cursor-pointer"
            >
                Activate Offline Cache
            </button>
        </div>
    </div>

    <!-- Wrapping Content in x-show to hide when loading -->
    <div x-show="!serverLoading || offlineFallback" class="space-y-8" x-transition.opacity.duration.500ms>
        
        <!-- =========================================================================
             DASHBOARD STATISTICS CARDS OVERVIEW
             ========================================================================= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Card 1 -->
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-3xs hover:shadow-2xs transition-all relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-[3.5px] bg-emerald-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[10px] font-mono font-bold tracking-wider text-slate-400 uppercase block">National Avg (LKR/kg)</span>
                        <h3 class="text-3xl font-black font-mono text-slate-900 mt-1">Rs. <span x-text="nationalStats.nationalAverage"></span></h3>
                        <p class="text-[11px] text-slate-500 mt-1 leading-snug">Average price index calculated across all 12 trading districts.</p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                        <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-50 flex items-center justify-between text-[11px] font-mono text-slate-400">
                    <span>Historical Baseline:</span>
                    <span class="font-bold text-slate-600">Rs. 320/kg</span>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-3xs hover:shadow-2xs transition-all relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-[3.5px] bg-rose-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[10px] font-mono font-bold tracking-wider text-slate-400 uppercase block">Most Expensive Hub</span>
                        <h3 class="text-xl font-bold font-display text-rose-600 mt-1 truncate max-w-[155px]" x-text="nationalStats.expensiveDistrict"></h3>
                        <p class="text-2xl font-black font-mono text-slate-900 mt-0.5">Rs. <span x-text="nationalStats.expensivePrice"></span> <span class="text-xs text-slate-400 font-normal">/kg</span></p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center text-rose-600 shrink-0">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2.5 text-[11px] text-slate-500 leading-none">Suffers transit premiums & structural markups.</div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-3xs hover:shadow-2xs transition-all relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-[3.5px] bg-emerald-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[10px] font-mono font-bold tracking-wider text-slate-400 uppercase block">Cheapest Supply</span>
                        <h3 class="text-xl font-bold font-display text-emerald-600 mt-1 truncate max-w-[155px]" x-text="nationalStats.cheapestDistrict"></h3>
                        <p class="text-2xl font-black font-mono text-slate-900 mt-0.5">Rs. <span x-text="nationalStats.cheapestPrice"></span> <span class="text-xs text-slate-400 font-normal">/kg</span></p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                        <i data-lucide="trending-down" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2.5 text-[11px] text-slate-500 leading-none">Advantaged farmgate production yields.</div>
            </div>

            <!-- Card 4 -->
            <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-3xs hover:shadow-2xs transition-all relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-[3.5px] bg-indigo-500"></div>
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[10px] font-mono font-bold tracking-wider text-slate-450 uppercase block">Market Balance Status</span>
                        <h3 class="text-xl font-black font-mono text-slate-900 mt-1">Dispersion Levels</h3>
                        <div class="flex gap-2.5 mt-2 text-[10px] font-mono">
                            <span class="bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded font-bold" title="Low price districts">
                                <span x-text="nationalStats.distribution.lowCount"></span> Low
                            </span>
                            <span class="bg-yellow-50 text-yellow-700 px-1.5 py-0.5 rounded font-bold" title="Medium price districts">
                                <span x-text="nationalStats.distribution.midCount"></span> Mid
                            </span>
                            <span class="bg-orange-50 text-orange-700 px-1.5 py-0.5 rounded font-bold" title="High price districts">
                                <span x-text="nationalStats.distribution.highCount"></span> High
                            </span>
                        </div>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                        <i data-lucide="layers" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="mt-2 text-[10px] text-slate-400">
                    <template x-if="offlineFallback">
                        <span class="text-yellow-600 flex items-center gap-1"><i data-lucide="sparkles" class="w-3 h-3"></i> Offline Backup Mode active</span>
                    </template>
                    <template x-if="!offlineFallback">
                        <span class="text-emerald-600 flex items-center gap-1"><i data-lucide="zap" class="w-3 h-3"></i> Live Database pricing synced</span>
                    </template>
                </div>
            </div>
        </div>

        <!-- =========================================================================
             INTERACTIVE CEYLON MAP & ADVANCED DETAILS
             ========================================================================= -->
        <div class="bg-white border border-slate-100 rounded-3xl p-5 sm:p-8 shadow-xs">
            
            <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 mb-8 border-b border-slate-100 gap-4">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-800 rounded-full text-[10px] font-mono tracking-wider uppercase font-bold mb-3">
                        <i data-lucide="compass" class="w-3.5 h-3.5 text-emerald-600 animate-spin-slow"></i>
                        <span>Sri Lankan Agricultural Arbitrage Mapping</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 font-display tracking-tight">Ceylon Price Dispersion Projection</h2>
                    <p class="text-slate-500 text-xs sm:text-sm mt-1 max-w-xl">An interactive geospatial heatmap showcasing real LKR vegetable price distributions, agrarian roles, and high-altitude production centers.</p>
                </div>

                <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200 self-start md:self-center h-10 shadow-3xs animate-fade-in">
                    <button @click="displayMode = 'prices'" :class="displayMode === 'prices' ? 'bg-white text-slate-800 shadow-3xs font-semibold' : 'text-slate-550 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg text-xs font-bold font-sans transition-all flex items-center gap-1.5 cursor-pointer select-none">
                        <i data-lucide="layers" class="w-3.5 h-3.5 text-emerald-600"></i> Price Multipliers
                    </button>
                    <button @click="displayMode = 'supply-roles'" :class="displayMode === 'supply-roles' ? 'bg-white text-slate-800 shadow-3xs font-semibold' : 'text-slate-550 hover:text-slate-800'" class="px-3 py-1.5 rounded-lg text-xs font-bold font-sans transition-all flex items-center gap-1.5 cursor-pointer select-none">
                        <i data-lucide="activity" class="w-3.5 h-3.5 text-teal-600"></i> Supply Roles
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
                <!-- Left Panel -->
                <div class="lg:col-span-5 flex flex-col justify-between space-y-6">
                    <div class="space-y-6">
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <h3 class="font-bold text-slate-800 text-xs font-display mb-1.5 flex items-center gap-1.5">
                                <i data-lucide="info" class="w-3.5 h-3.5 text-indigo-500"></i> <span>How to Interoperate:</span>
                            </h3>
                            <p class="text-slate-500 text-xs leading-relaxed">
                                Hover above and across labeled pins on the projection map to generate instant tooltip popovers, or <strong>click directly</strong> on any pin node to capture that district's live commodity rates.
                            </p>
                        </div>

                        <div class="space-y-2.5">
                            <span class="text-[10px] font-mono font-bold text-slate-400 uppercase tracking-wider block">Filter Category Index Averages</span>
                            <div class="flex flex-wrap items-center gap-1 bg-slate-50 p-1 rounded-xl border border-slate-150 max-w-xl">
                                <template x-for="cat in categories" :key="cat.id">
                                    <button 
                                        @click="selectedCategory = cat.id"
                                        :class="selectedCategory === cat.id ? 'bg-white text-slate-900 shadow-2xs font-semibold' : 'text-slate-550 hover:text-slate-800'"
                                        class="px-3 py-1.5 rounded-lg text-[10.5px] font-bold transition-all cursor-pointer select-none"
                                        x-text="cat.label"
                                    ></button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Callout Panel -->
                    <div class="bg-emerald-600 text-white rounded-3xl p-6 border border-emerald-700 shadow-md relative overflow-hidden flex flex-col justify-between min-h-[280px]">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-500/20 to-transparent rounded-full -mr-10 -mt-10 pointer-events-none"></div>
                        
                        <div class="space-y-5 relative z-10">
                            <div class="flex justify-between items-center bg-emerald-700 py-1.5 px-3 rounded-xl border border-emerald-800 self-start">
                                <span class="text-[9px] font-mono tracking-widest text-emerald-400 font-bold uppercase">SELECTED DISTRICT UNIT</span>
                                <div class="flex items-center gap-1 text-[9px] text-slate-400 font-mono pl-3">
                                    <i data-lucide="calendar" class="w-3 h-3 text-slate-600"></i>
                                    <span x-text="formattedPayloadDate"></span>
                                </div>
                            </div>

                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="flex items-baseline gap-1.5">
                                        <h4 class="font-extrabold text-white font-display text-xl tracking-tight" x-text="selectedDistrict.name"></h4>
                                        <span class="text-emerald-400 text-xs font-bold font-mono">(<span x-text="selectedDistrict.id.toUpperCase()"></span>)</span>
                                    </div>
                                    <p class="text-[12px] text-slate-400 font-sans mt-0.5">
                                        <span x-text="selectedDistrict.localNameSi"></span> • <span x-text="selectedDistrict.localNameTa"></span>
                                    </p>
                                    <p class="text-[10px] text-slate-500 font-mono mt-1.5 uppercase tracking-wider" x-text="selectedDistrict.province"></p>
                                </div>
                                
                                <span :class="{
                                    'bg-emerald-950/80 text-emerald-400 border-emerald-900': selectedDistrict.role === 'Production',
                                    'bg-blue-950/80 text-blue-400 border-blue-900': selectedDistrict.role === 'Distribution',
                                    'bg-rose-950/80 text-rose-400 border-rose-900': selectedDistrict.role === 'Consumption'
                                }" class="px-2 py-1 rounded-md border text-[9.5px] font-mono font-bold flex items-center gap-1 uppercase" x-text="selectedDistrict.role === 'Production' ? 'HARVEST' : (selectedDistrict.role === 'Distribution' ? 'MIDDLE TRADE' : 'DEMAND SINK')"></span>
                            </div>

                            <div class="grid grid-cols-3 gap-3.5 pt-4 border-t border-slate-800">
                                <div>
                                    <span class="text-[9px] font-mono text-slate-450 uppercase block tracking-wider">Average Price:</span>
                                    <strong class="text-xl font-mono font-black text-white block mt-1">Rs. <span x-text="selectedDistrict.computedAveragePrice"></span></strong>
                                </div>
                                <div>
                                    <span class="text-[9px] font-mono text-slate-450 uppercase block tracking-wider">Week prior:</span>
                                    <strong class="text-slate-400 font-mono text-sm block mt-1.5">Rs. <span x-text="selectedDistrict.computedAveragePricePrevWeek"></span></strong>
                                </div>
                                <div>
                                    <span class="text-[9px] font-mono text-slate-450 uppercase block tracking-wider">Week Trend:</span>
                                    <span :class="selectedDistrict.trendPercent <= 0 ? 'text-emerald-400' : 'text-rose-400'" class="flex items-center gap-0.5 text-xs font-mono font-bold mt-1.5">
                                        <i :data-lucide="selectedDistrict.trendPercent <= 0 ? 'trending-down' : 'trending-up'" class="w-3.5 h-3.5"></i>
                                        <span x-text="Math.abs(selectedDistrict.trendPercent) + '%'"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-800/65">
                                <div class="bg-slate-850/50 p-2 rounded-lg border border-slate-800/50">
                                    <span class="text-[8px] font-mono text-rose-400 block font-bold uppercase tracking-wide">▲ Highest Commodity</span>
                                    <strong class="text-slate-200 block truncate text-[11px] mt-0.5" x-text="selectedDistrict.highestCropName"></strong>
                                    <span class="font-mono text-slate-400 text-[10.5px] mt-0.5 block">Rs. <span x-text="selectedDistrict.highestCropPrice"></span>/kg</span>
                                </div>
                                <div class="bg-slate-850/50 p-2 rounded-lg border border-slate-800/50">
                                    <span class="text-[8px] font-mono text-emerald-400 block font-bold uppercase tracking-wide">▼ Lowest Commodity</span>
                                    <strong class="text-slate-200 block truncate text-[11px] mt-0.5" x-text="selectedDistrict.lowestCropName"></strong>
                                    <span class="font-mono text-slate-400 text-[10.5px] mt-0.5 block">Rs. <span x-text="selectedDistrict.lowestCropPrice"></span>/kg</span>
                                </div>
                            </div>

                            <span class="text-[10px] text-slate-400 block bg-slate-900/60 p-2 rounded-lg border border-slate-800 leading-normal">
                                <strong class="text-slate-500 font-mono text-[8.5px] mr-1 uppercase">PRIMED HARVESTS:</strong>
                                <span x-text="selectedDistrict.primaryCrops.join(', ')"></span>
                            </span>

                            <button @click="showGlassModal = true" type="button" class="w-full py-2.5 px-4 mt-2 hover:bg-emerald-500 hover:text-slate-950 transition-all text-xs font-semibold bg-emerald-600/10 text-emerald-400 border border-emerald-500/20 rounded-xl cursor-pointer flex items-center justify-center gap-1.5 shadow-sm">
                                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> View Glassmorphism HUD Detail
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Side Map Canvas -->
                <div class="lg:col-span-7 flex flex-col items-center justify-center relative p-3 bg-slate-50 rounded-2.5xl border border-slate-100 min-h-[500px]">
                    
                    <!-- Tooltip Popup hover element -->
                    <div 
                        x-show="hoveredDistrict"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                        class="absolute top-4 right-4 bg-white text-slate-900 border border-slate-200 rounded-xl p-3 shadow-lg z-20 w-52 text-[11px] pointer-events-none"
                    >
                        <template x-if="hoveredDistrict">
                            <div>
                                <div class="flex justify-between items-start border-b border-slate-200 pb-1 mb-1.5">
                                    <div>
                                        <h5 class="font-bold text-[11.5px]" x-text="hoveredDistrict.name"></h5>
                                        <span class="text-[8.5px] text-slate-600 font-mono uppercase font-semibold" x-text="hoveredDistrict.province"></span>
                                    </div>
                                    <span class="bg-slate-800 text-emerald-400 text-[8px] font-mono px-1 rounded">Spot</span>
                                </div>
                                <div class="space-y-1 font-mono">
                                    <div class="flex justify-between">
                                        <span class="text-slate-600 text-[10px]">Avg LKR Rate:</span>
                                        <strong class="text-slate-900">Rs.<span x-text="hoveredDistrict.computedAveragePrice"></span>/kg</strong>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-600 text-[10px]">Highest crop:</span>
                                        <span class="text-rose-400 text-[9.5px] truncate max-w-[85px]" x-text="hoveredDistrict.highestCropName"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-slate-600 text-[10px]">Lowest crop:</span>
                                        <span class="text-emerald-400 text-[9.5px] truncate max-w-[85px]" x-text="hoveredDistrict.lowestCropName"></span>
                                    </div>
                                    <div class="flex justify-between border-t border-slate-800 pt-1 mt-1 text-[9px] text-slate-500">
                                        <span>Updated:</span>
                                        <span x-text="formattedPayloadDate"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="absolute bottom-6 left-6 flex items-center gap-2 select-none opacity-45 pointer-events-none">
                        <i data-lucide="compass" class="w-7 h-7 text-slate-400 animate-spin-slow" stroke-width="1"></i>
                        <div class="font-mono text-[9px] text-slate-450 leading-none">
                            <span class="block font-bold text-slate-600">CEYLON PROJECTION</span>
                            <span class="block mt-0.5 text-[8px]">07° 52' N, 80° 46' E</span>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="relative sm:absolute sm:top-4 sm:left-4 bg-white/95 backdrop-blur-md border border-slate-200/80 rounded-2xl p-3 text-[9.5px] font-mono text-slate-650 z-10 shadow-3xs w-full sm:w-auto mt-2 sm:mt-0 mb-4 sm:mb-0">
                        <template x-if="displayMode === 'prices'">
                            <div class="text-slate-600">
                                <span class="font-bold text-slate-800 uppercase tracking-wider block border-b border-slate-100 pb-1 mb-1.5">Map Color Calibration</span>
                                <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-full bg-emerald-500 shadow-3xs"></span><span>Green = Low (&lt; Rs. <span x-text="Math.round(bounds.lowThreshold)"></span>/kg)</span></div>
                                <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-full bg-yellow-400 shadow-3xs"></span><span>Yellow = Medium (<span x-text="Math.round(bounds.lowThreshold) + 1"></span> - <span x-text="Math.round(bounds.mediumThreshold)"></span>)</span></div>
                                <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-full bg-orange-400 shadow-3xs"></span><span>Orange = High (<span x-text="Math.round(bounds.mediumThreshold) + 1"></span> - <span x-text="Math.round(bounds.highThreshold)"></span>)</span></div>
                                <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-full bg-rose-500 shadow-3xs animate-pulse"></span><span>Red = Very High (&gt; Rs. <span x-text="Math.round(bounds.highThreshold)"></span>)</span></div>
                            </div>
                        </template>
                        <template x-if="displayMode === 'supply-roles'">
                            <div class="text-slate-600">
                                <span class="font-bold text-slate-800 uppercase tracking-wider block border-b border-slate-100 pb-1 mb-1.5">Agrarian Sector Legend</span>
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-emerald-600"></span><span>Green = Highland Yield Production</span></div>
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-blue-600"></span><span>Blue = Distribution Wholesale Centers</span></div>
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-sm bg-rose-600"></span><span>Red = Consumption Demand Sinks</span></div>
                            </div>
                        </template>
                    </div>

                    <!-- Map SVG Wrapper -->
                    <div class="w-full max-w-[280px] xs:max-w-[325px] sm:max-w-[420px] aspect-[450/793] relative select-none">
                        <svg viewBox="0 0 450 793" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full drop-shadow-lg">
                            <defs>
                                <pattern id="dot-mesh-light" x="0" y="0" width="16" height="16" patternUnits="userSpaceOnUse">
                                    <circle cx="2" cy="2" r="0.7" fill="#CBD5E1" opacity="0.4" />
                                </pattern>
                                <filter id="subtle-shadow" x="-5%" y="-5%" width="110%" height="110%">
                                    <feDropShadow dx="1" dy="2" stdDeviation="3" flood-color="#0F172A" flood-opacity="0.08" />
                                </filter>
                            </defs>
                            
                            <!-- Base grids & decorative lines -->
                            <path d="M 50 180 Q 150 190 200 180" stroke="#EEF2F6" stroke-width="1" opacity="0.8" stroke-dasharray="3 5"/>
                            <path d="M 320 120 Q 380 150 350 250" stroke="#EEF2F6" stroke-width="1" opacity="0.8" stroke-dasharray="3 5"/>
                            <path d="M 330 550 Q 370 630 300 710" stroke="#EEF2F6" stroke-width="1" opacity="0.8" stroke-dasharray="3 5"/>
                            <rect x="0" y="0" width="450" height="793" fill="url(#dot-mesh-light)" opacity="0.2" pointer-events="none" class="dark:hidden" />

                            <!-- Simulated simplified geographic outline of Sri Lanka -->
                            <g filter="url(#subtle-shadow)">
                                <path d="M 120 10 Q 180 30 200 100 Q 250 150 280 250 Q 320 300 350 400 Q 370 500 340 600 Q 300 750 250 780 Q 200 790 100 750 Q 50 650 30 500 Q 10 350 50 200 Q 80 100 120 10 Z" fill="#e2e8f0" fill-opacity="0.5" stroke="#cbd5e1" stroke-width="2" class="dark:fill-[#cbd5e1]" />
                            </g>

                            <!-- Data overlays (Nodes & Pins) -->
                            <template x-for="dist in calculatedMetrics" :key="dist.id">
                                <g class="pointer-events-auto cursor-pointer"
                                   @click="selectedDistrictId = dist.id; showGlassModal = true"
                                   @mouseenter="hoveredDistrictId = dist.id"
                                   @mouseleave="hoveredDistrictId = null"
                                >
                                    <!-- Base hover area to make hovering easier -->
                                    <circle :cx="dist.cx" :cy="dist.cy" r="28" fill="transparent" />

                                    <!-- Selected Pulse -->
                                    <template x-if="selectedDistrictId === dist.id">
                                        <circle :cx="dist.cx" :cy="dist.cy" r="14" fill="none" :stroke="displayMode === 'prices' ? '#0F172A' : '#3B82F6'" stroke-width="1.5" opacity="0.8" class="animate-pulse" />
                                    </template>

                                    <!-- Main node fill dynamically calculated -->
                                    <circle :cx="dist.cx" :cy="dist.cy" :r="dist.r"
                                            :fill="getFillColor(dist)" 
                                            :fill-opacity="(hoveredDistrictId === dist.id) ? 0.95 : (selectedDistrictId === dist.id ? 0.90 : 0.8)"
                                            :stroke="(hoveredDistrictId === dist.id) ? '#0F172A' : (selectedDistrictId === dist.id ? '#1E293B' : '#FFFFFF')"
                                            :stroke-width="(hoveredDistrictId === dist.id || selectedDistrictId === dist.id) ? 1.8 : 0.75"
                                            class="transition-all duration-300"
                                    />

                                    <!-- Inner center dot -->
                                    <circle :cx="dist.cx" :cy="dist.cy" r="4.5" fill="#FFFFFF"
                                            :stroke="(hoveredDistrictId === dist.id || selectedDistrictId === dist.id) ? '#0F172A' : '#64748B'" 
                                            stroke-width="2" class="transition-all duration-300 shadow-sm" />
                                    
                                    <circle :cx="dist.cx" :cy="dist.cy" r="1.8" 
                                            :fill="(hoveredDistrictId === dist.id || selectedDistrictId === dist.id) ? '#E11D48' : '#3B82F6'" />

                                    <!-- Label -->
                                    <text :x="dist.cx" :y="dist.cy + 13" text-anchor="middle" 
                                        :class="{
                                             'fill-slate-950 font-black text-[8.5px]': selectedDistrictId === dist.id, 
                                             'fill-slate-800 text-[8px]': hoveredDistrictId === dist.id, 
                                             'fill-slate-800 font-bold opacity-80 text-[7.2px]': selectedDistrictId !== dist.id && hoveredDistrictId !== dist.id
                                         }"
                                          class="font-sans select-none drop-shadow-md tracking-tight uppercase"
                                          style="letter-spacing: 0.02em; text-shadow: 0 1px 2px rgba(255,255,255,0.8)"
                                          x-text="dist.name.split(' ')[0]">
                                    </text>
                                </g>
                            </template>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- =========================================================================
             DISTRICTS COMPARISON SIDE-BY-SIDE ANALYTICS
             ========================================================================= -->
        <section class="bg-white border border-slate-100 rounded-3xl p-5 sm:p-8 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 mb-6 border-b border-slate-200 gap-3">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-indigo-50 text-indigo-800 rounded-full text-[10px] font-mono font-bold uppercase mb-2">
                        <i data-lucide="arrow-up-down" class="w-3.5 h-3.5 text-indigo-600"></i>
                        <span>Multi-District Valuation Contrast</span>
                    </div>
                    <h3 class="text-xl font-bold font-display text-slate-900">District Price Comparison Portal</h3>
                    <p class="text-slate-500 text-xs mt-0.5">Select any two administrative zones of Sri Lanka to run side-by-side spot analysis and premium delta evaluations.</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-1.5 bg-slate-50 p-1.5 rounded-xl border border-slate-150">
                        <label for="compare-a" class="text-[10px] font-mono text-slate-400 uppercase font-bold pl-1.5">HUB A:</label>
                        <select id="compare-a" x-model="compareDistrictIdA" class="bg-white border border-slate-200 text-xs font-semibold text-slate-800 py-1 px-2 rounded-lg cursor-pointer max-w-[140px] focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <template x-for="d in districtData" :key="'a-' + d.id">
                                <option :value="d.id" x-text="d.name"></option>
                            </template>
                        </select>
                    </div>

                    <div class="flex items-center gap-1.5 bg-slate-50 p-1.5 rounded-xl border border-slate-150">
                        <label for="compare-b" class="text-[10px] font-mono text-slate-400 uppercase font-bold pl-1.5">HUB B:</label>
                        <select id="compare-b" x-model="compareDistrictIdB" class="bg-white border border-slate-200 text-xs font-semibold text-slate-800 py-1 px-2 rounded-lg cursor-pointer max-w-[140px] focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <template x-for="d in districtData" :key="'b-' + d.id">
                                <option :value="d.id" x-text="d.name"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Empty State / Warning -->
            <div x-show="compareDistrictIdA === compareDistrictIdB" class="py-10 text-center text-slate-400 text-xs font-semibold border border-dashed border-slate-200 rounded-2xl bg-slate-50">
                Please select two different zones of Sri Lanka to compare and extract geographical arbitrage levels!
            </div>

            <!-- Active Compare Data -->
            <div x-show="compareDistrictIdA !== compareDistrictIdB" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-stretch">
                
                <!-- District A Card -->
                <div class="md:col-span-5 bg-gradient-to-b from-slate-50 to-white border border-slate-150 rounded-2xl p-5 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-[4px] bg-indigo-500"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[9px] font-mono bg-indigo-500 text-white px-2.5 py-0.5 rounded font-bold uppercase">District A</span>
                            <h4 class="text-lg font-bold font-display text-slate-900 mt-2" x-text="compareA.name"></h4>
                            <p class="text-xs text-slate-400 mt-0.5" x-text="compareA.province"></p>
                        </div>
                        <span class="text-[10.5px] font-mono text-slate-500 bg-slate-100 py-0.5 px-2 rounded-md font-bold" x-text="compareA.role"></span>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="bg-white border border-slate-100 p-4.5 rounded-xl shadow-3xs flex justify-between items-center">
                            <div>
                                <span class="text-[10px] font-mono text-slate-400 block uppercase font-bold">CALCULATED VALUATION</span>
                                <strong class="text-2xl font-mono text-slate-900 mt-1 block">Rs. <span x-text="compareA.computedAveragePrice"></span> /kg</strong>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] font-mono text-slate-400 uppercase font-semibold block">Week trend</span>
                                <span :class="compareA.trendPercent <= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-rose-500 bg-rose-50'" class="inline-flex items-center gap-0.5 font-mono text-xs font-bold leading-normal mt-1 px-1 py-0.5 rounded">
                                    <span x-text="compareA.trendPercent <= 0 ? '-' : '+'"></span><span x-text="Math.abs(compareA.trendPercent) + '%'"></span>
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="border border-slate-100 p-3 rounded-xl bg-white">
                                <span class="text-[9px] font-mono text-rose-500 font-bold block">▲ PEAK COMMODITY</span>
                                <strong class="text-slate-800 block truncate mt-0.5 font-bold" x-text="compareA.highestCropName"></strong>
                                <span class="text-slate-500 font-mono text-[10.5px]">Rs. <span x-text="compareA.highestCropPrice"></span> /kg</span>
                            </div>
                            <div class="border border-slate-100 p-3 rounded-xl bg-white">
                                <span class="text-[9px] font-mono text-emerald-500 font-bold block">▼ BASE COMMODITY</span>
                                <strong class="text-slate-800 block truncate mt-0.5 font-bold" x-text="compareA.lowestCropName"></strong>
                                <span class="text-slate-500 font-mono text-[10.5px]">Rs. <span x-text="compareA.lowestCropPrice"></span> /kg</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compare Center Verdict -->
                <div class="md:col-span-2 flex flex-col justify-center items-center text-center p-4 bg-slate-50 border border-slate-150 rounded-2xl">
                    <div class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs select-none shadow-md mb-2">VS</div>
                    <div class="space-y-4 w-full">
                        <div>
                            <span class="text-[9px] font-mono text-slate-400 block font-bold uppercase tracking-wider">GAP VALUE</span>
                            <strong class="text-sm font-black mt-1 font-mono text-slate-850 block">Rs. <span x-text="compareGapAbs"></span> /kg</strong>
                        </div>
                        <div class="p-3 rounded-xl bg-white border border-slate-150 shadow-3xs text-center">
                            <span class="text-[8px] font-mono text-slate-400 block font-bold uppercase">PRICE VERDICT</span>
                            <p class="text-xs font-bold text-slate-800 mt-1 leading-normal">
                                <strong x-text="compareGapIsACheaper ? compareA.name : compareB.name"></strong> is 
                                <span class="text-emerald-600 font-black"><span x-text="compareGapRatio"></span>% cheaper</span> for procurement.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- District B Card -->
                <div class="md:col-span-5 bg-gradient-to-b from-slate-50 to-white border border-slate-150 rounded-2xl p-5 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-[4px] bg-amber-500"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[9px] font-mono bg-amber-500 text-white px-2.5 py-0.5 rounded font-bold uppercase">District B</span>
                            <h4 class="text-lg font-bold font-display text-slate-900 mt-2" x-text="compareB.name"></h4>
                            <p class="text-xs text-slate-400 mt-0.5" x-text="compareB.province"></p>
                        </div>
                        <span class="text-[10.5px] font-mono text-slate-500 bg-slate-100 py-0.5 px-2 rounded-md font-bold" x-text="compareB.role"></span>
                    </div>

                    <div class="mt-6 space-y-4">
                        <div class="bg-white border border-slate-100 p-4.5 rounded-xl shadow-3xs flex justify-between items-center">
                            <div>
                                <span class="text-[10px] font-mono text-slate-400 block uppercase font-bold">CALCULATED VALUATION</span>
                                <strong class="text-2xl font-mono text-slate-900 mt-1 block">Rs. <span x-text="compareB.computedAveragePrice"></span> /kg</strong>
                            </div>
                            <div class="text-right">
                                <span class="text-[9px] font-mono text-slate-400 uppercase font-semibold block">Week trend</span>
                                <span :class="compareB.trendPercent <= 0 ? 'text-emerald-500 bg-emerald-50' : 'text-rose-500 bg-rose-50'" class="inline-flex items-center gap-0.5 font-mono text-xs font-bold leading-normal mt-1 px-1 py-0.5 rounded">
                                    <span x-text="compareB.trendPercent <= 0 ? '-' : '+'"></span><span x-text="Math.abs(compareB.trendPercent) + '%'"></span>
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="border border-slate-100 p-3 rounded-xl bg-white">
                                <span class="text-[9px] font-mono text-rose-500 font-bold block">▲ PEAK COMMODITY</span>
                                <strong class="text-slate-800 block truncate mt-0.5 font-bold" x-text="compareB.highestCropName"></strong>
                                <span class="text-slate-500 font-mono text-[10.5px]">Rs. <span x-text="compareB.highestCropPrice"></span> /kg</span>
                            </div>
                            <div class="border border-slate-100 p-3 rounded-xl bg-white">
                                <span class="text-[9px] font-mono text-emerald-500 font-bold block">▼ BASE COMMODITY</span>
                                <strong class="text-slate-800 block truncate mt-0.5 font-bold" x-text="compareB.lowestCropName"></strong>
                                <span class="text-slate-500 font-mono text-[10.5px]">Rs. <span x-text="compareB.lowestCropPrice"></span> /kg</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- =========================================================================
         GLASSMORPHIC MODAL
         ========================================================================= -->
    <div x-show="showGlassModal" style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div 
            x-show="showGlassModal"
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showGlassModal = false"
            class="absolute inset-0 bg-slate-950/40 backdrop-blur-md cursor-pointer"
        ></div>
        
        <!-- Modal Content -->
        <div 
            x-show="showGlassModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90 translate-y-8"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-90 translate-y-8"
            class="relative w-full max-w-xl backdrop-blur-xl bg-white/80 border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-[0_20px_50px_rgba(0,0,0,0.4)] overflow-hidden z-10 text-slate-900"
        >
            <!-- Glowing Orbs -->
            <div class="absolute -top-1/4 -right-1/4 w-72 h-72 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-1/4 -left-1/4 w-72 h-72 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <button @click="showGlassModal = false" type="button" class="absolute top-4 right-4 p-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 hover:border-slate-300 rounded-full transition-all cursor-pointer flex items-center justify-center text-slate-600 hover:text-slate-900">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>

            <!-- Title -->
            <div class="flex items-start gap-4 mb-6 relative z-10">
                <div :class="{
                    'text-emerald-400': selectedDistrict.role === 'Production',
                    'text-blue-400': selectedDistrict.role === 'Distribution',
                    'text-rose-450': selectedDistrict.role === 'Consumption'
                }" class="p-3.5 rounded-2xl bg-white/5 border border-white/10 text-xl font-bold flex items-center justify-center shadow-lg">
                    <i data-lucide="map-pin" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-2xl font-black tracking-tight font-display text-white" x-text="selectedDistrict.name"></h3>
                        <span class="text-[10px] font-mono font-bold tracking-widest bg-white/10 text-white/90 border border-white/5 px-2.5 py-0.5 rounded-full uppercase" x-text="selectedDistrict.role === 'Production' ? 'PRODUCER' : (selectedDistrict.role === 'Distribution' ? 'DISTRIBUTOR' : 'CONSUMER')"></span>
                    </div>
                    <p class="text-xs text-emerald-300 font-medium font-sans mt-0.5">
                        <span x-text="selectedDistrict.localNameSi"></span> • <span x-text="selectedDistrict.localNameTa"></span>
                    </p>
                    <p class="text-[9.5px] font-mono tracking-wider text-slate-400 uppercase mt-1" x-text="selectedDistrict.province"></p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 relative z-10 text-slate-900">
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 backdrop-blur-md relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400 block pb-1 border-b border-white/5">Average Price Index</span>
                        <strong class="text-3xl font-mono font-black text-slate-900 block mt-3">
                            Rs. <span x-text="selectedDistrict.computedAveragePrice"></span> <span class="text-xs text-slate-400 font-normal">/kg</span>
                        </strong>
                    </div>
                    <p class="text-[10px] text-slate-600 mt-2">Calculated from active central market trading nodes.</p>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 backdrop-blur-md relative overflow-hidden flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400 block pb-1 border-b border-white/5">Weekly Price Delta</span>
                            <strong :class="selectedDistrict.trendPercent <= 0 ? 'text-emerald-600' : 'text-rose-600'" class="text-2xl font-mono font-black block mt-3">
                                <span x-text="selectedDistrict.trendPercent <= 0 ? '-' : '+'"></span><span x-text="Math.abs(selectedDistrict.trendPercent) + '%'"></span>
                            </strong>
                        </div>
                        <div :class="selectedDistrict.trendPercent <= 0 ? 'text-emerald-600' : 'text-rose-600'" class="p-2 rounded-xl bg-slate-100 border border-slate-200">
                            <i :data-lucide="selectedDistrict.trendPercent <= 0 ? 'trending-down' : 'trending-up'" class="w-5 h-5"></i>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-600 mt-2">Delta relative to previous week's baseline (Rs. <span x-text="selectedDistrict.computedAveragePricePrevWeek"></span>).</p>
                </div>
            </div>

            <div class="space-y-3 mb-6 relative z-10 text-slate-900">
                <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400 block">Commodity Extremum Projections</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="bg-slate-50 border border-emerald-500/30 rounded-xl p-3.5 backdrop-blur-sm">
                        <div class="flex items-center gap-1.5 text-[9px] font-mono font-bold text-emerald-400 uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-ping"></span><span>▼ Least Expensive Crop</span>
                        </div>
                        <h5 class="font-bold text-[13px] text-slate-900 mt-1.5 truncate" x-text="selectedDistrict.lowestCropName"></h5>
                        <strong class="text-emerald-600 font-mono text-[12px] mt-0.5 block">Rs. <span x-text="selectedDistrict.lowestCropPrice"></span> /kg</strong>
                    </div>

                    <div class="bg-slate-50 border border-rose-500/30 rounded-xl p-3.5 backdrop-blur-sm">
                        <div class="flex items-center gap-1.5 text-[9px] font-mono font-bold text-rose-400 uppercase tracking-wide">
                            <span class="w-1.5 h-1.5 bg-rose-400 rounded-full"></span><span>▲ Most Expensive Crop</span>
                        </div>
                        <h5 class="font-bold text-[13px] text-slate-900 mt-1.5 truncate" x-text="selectedDistrict.highestCropName"></h5>
                        <strong class="text-rose-600 font-mono text-[12px] mt-0.5 block">Rs. <span x-text="selectedDistrict.highestCropPrice"></span> /kg</strong>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4.5 backdrop-blur-md relative z-10">
                <span class="text-[9px] font-mono font-bold uppercase tracking-wider text-slate-400 block mb-2.5">Core Agrarian Shipments & Yields</span>
                <div class="flex flex-wrap gap-2">
                    <template x-for="crop in selectedDistrict.primaryCrops" :key="crop">
                        <span class="px-2.5 py-1 text-xs font-semibold bg-slate-100 border border-slate-200 text-emerald-600 rounded-lg transition-all hover:bg-slate-200" x-text="crop"></span>
                    </template>
                </div>
            </div>

            <div class="mt-6 flex justify-end relative z-10">
                <button @click="showGlassModal = false" type="button" class="px-5 py-2.5 text-xs font-semibold rounded-xl bg-white text-slate-950 hover:bg-slate-100 transition-all cursor-pointer shadow-md text-center">
                    Dismiss HUD
                </button>
            </div>
        </div>
    </div>
</div>

<script>

    // Make sure Lucide icons are re-rendered when Alpine finishes loading
    document.addEventListener('alpine:initialized', () => {
        if(window.lucide) { lucide.createIcons(); }
    });

    document.addEventListener('alpine:init', () => {
        Alpine.data('CeylonHeatmap', () => ({
            serverLoading: true,
            serverError: false,
            offlineFallback: false,

            // --- Live data fetched from the backend ---
            liveDistrictData: [],        // Array of district objects with real computed prices
            liveNationalStats: null,     // National summary object
            payloadDate: null,           // Date of the latest data

            selectedCategory: 'all',
            displayMode: 'prices',
            selectedDistrictId: 'nuwara_eliya',
            hoveredDistrictId: null,
            showGlassModal: false,

            compareDistrictIdA: 'nuwara_eliya',
            compareDistrictIdB: 'colombo',

            categories: [
                { id: 'all', label: 'All Crops' },
                { id: 'vegetables', label: 'Vegetables' },
                { id: 'other', label: 'Other Staples' }
            ],

            // ---------------------------------------------------------------
            // OFFLINE FALLBACK: only used when server is unreachable
            // ---------------------------------------------------------------
            offlineDistrictData: [
                { id: 'jaffna',       name: 'Jaffna',               localNameSi: 'යාපනය',         localNameTa: 'யாழ்ப்பாணம்',    province: 'Northern Province',      computedAveragePrice: 295, computedAveragePricePrevWeek: 290, trendPercent: 1.7,  highestCropName: 'Green Chilli', highestCropPrice: 520, lowestCropName: 'Pumpkin', lowestCropPrice: 90,  primaryCrops: ['Red Onion', 'Green Chillies', 'Ladies Fingers'], role: 'Production',    cx: 120, cy: 110, r: 20 },
                { id: 'anuradhapura', name: 'Anuradhapura',         localNameSi: 'අනුරාධපුරය',    localNameTa: 'அனுராதபுரம்',     province: 'North Central Province', computedAveragePrice: 248, computedAveragePricePrevWeek: 255, trendPercent: -2.7, highestCropName: 'Bitter Gourd', highestCropPrice: 490, lowestCropName: 'Pumpkin', lowestCropPrice: 102, primaryCrops: ['Pumpkin', 'Ladies Fingers', 'Manioc'],           role: 'Production',    cx: 160, cy: 300, r: 24 },
                { id: 'puttalam',     name: 'Puttalam',             localNameSi: 'පුත්තලම',       localNameTa: 'புத்தளம்',         province: 'North Western Province', computedAveragePrice: 305, computedAveragePricePrevWeek: 298, trendPercent: 2.3,  highestCropName: 'Red Onion',    highestCropPrice: 350, lowestCropName: 'Manioc',  lowestCropPrice: 85,  primaryCrops: ['Red Onion', 'Brinjal'],                          role: 'Production',    cx: 90,  cy: 400, r: 22 },
                { id: 'trincomalee',  name: 'Trincomalee',          localNameSi: 'ත්‍රිකුණාමලය', localNameTa: 'திருகோணமலை',      province: 'Eastern Province',       computedAveragePrice: 310, computedAveragePricePrevWeek: 305, trendPercent: 1.6,  highestCropName: 'Tomato',       highestCropPrice: 580, lowestCropName: 'Pumpkin', lowestCropPrice: 100, primaryCrops: ['Brinjal', 'Tomato'],                             role: 'Distribution', cx: 260, cy: 280, r: 20 },
                { id: 'dambulla',     name: 'Dambulla Hub',         localNameSi: 'දඹුල්ල',        localNameTa: 'தம்புள்ளை',       province: 'Central Province',       computedAveragePrice: 260, computedAveragePricePrevWeek: 270, trendPercent: -3.7, highestCropName: 'Tomato',       highestCropPrice: 578, lowestCropName: 'Pumpkin', lowestCropPrice: 102, primaryCrops: ['Tomato', 'Pumpkin', 'Capsicum'],                 role: 'Distribution', cx: 190, cy: 420, r: 28 },
                { id: 'kandy',        name: 'Kandy',                localNameSi: 'මහනුවර',        localNameTa: 'கண்டி',            province: 'Central Province',       computedAveragePrice: 330, computedAveragePricePrevWeek: 320, trendPercent: 3.1,  highestCropName: 'Green Beans',  highestCropPrice: 435, lowestCropName: 'Cabbage',  lowestCropPrice: 137, primaryCrops: ['Leeks', 'Cabbage', 'Beans'],                    role: 'Distribution', cx: 190, cy: 500, r: 22 },
                { id: 'nuwara_eliya', name: 'Nuwara Eliya',         localNameSi: 'නුවරඑළිය',      localNameTa: 'நுவரெலியா',        province: 'Central Province',       computedAveragePrice: 270, computedAveragePricePrevWeek: 265, trendPercent: 1.9,  highestCropName: 'Green Beans',  highestCropPrice: 430, lowestCropName: 'Radish',   lowestCropPrice: 107, primaryCrops: ['Carrot', 'Leeks', 'Beetroot', 'Potato'],        role: 'Production',    cx: 180, cy: 580, r: 26 },
                { id: 'badulla',      name: 'Badulla (Welimada)',   localNameSi: 'බදුල්ල',        localNameTa: 'பதுளை',            province: 'Uva Province',           computedAveragePrice: 275, computedAveragePricePrevWeek: 268, trendPercent: 2.6,  highestCropName: 'Bitter Gourd', highestCropPrice: 492, lowestCropName: 'Radish',   lowestCropPrice: 107, primaryCrops: ['Potato', 'Carrot', 'Cabbage'],                  role: 'Production',    cx: 260, cy: 560, r: 24 },
                { id: 'colombo',      name: 'Colombo (Pettah)',     localNameSi: 'කොළඹ',          localNameTa: 'கொழும்பு',         province: 'Western Province',       computedAveragePrice: 390, computedAveragePricePrevWeek: 375, trendPercent: 4.0,  highestCropName: 'Green Chilli', highestCropPrice: 535, lowestCropName: 'Pumpkin',  lowestCropPrice: 84,  primaryCrops: ['Urban Premium Retail', 'Imported Goods'],       role: 'Consumption',   cx: 70,  cy: 590, r: 30 },
                { id: 'galle',        name: 'Galle',                localNameSi: 'ගාල්ල',         localNameTa: 'காலி',             province: 'Southern Province',      computedAveragePrice: 365, computedAveragePricePrevWeek: 358, trendPercent: 1.9,  highestCropName: 'Green Chilli', highestCropPrice: 540, lowestCropName: 'Pumpkin',  lowestCropPrice: 88,  primaryCrops: ['Low Country Sinks', 'Retail'],                  role: 'Consumption',   cx: 100, cy: 720, r: 22 },
                { id: 'hambantota',   name: 'Hambantota',           localNameSi: 'හම්බන්තොට',     localNameTa: 'அம்பாந்தோட்டை',   province: 'Southern Province',      computedAveragePrice: 300, computedAveragePricePrevWeek: 295, trendPercent: 1.7,  highestCropName: 'Snake Gourd',  highestCropPrice: 225, lowestCropName: 'Pumpkin',  lowestCropPrice: 100, primaryCrops: ['Pumpkin', 'Brinjal'],                           role: 'Production',    cx: 230, cy: 720, r: 22 },
                { id: 'moneragala',   name: 'Moneragala',           localNameSi: 'මොණරාගල',       localNameTa: 'மொனராகலை',         province: 'Uva Province',           computedAveragePrice: 285, computedAveragePricePrevWeek: 280, trendPercent: 1.8,  highestCropName: 'Bitter Gourd', highestCropPrice: 492, lowestCropName: 'Pumpkin',  lowestCropPrice: 102, primaryCrops: ['Pumpkin', 'Ladies Fingers'],                    role: 'Production',    cx: 280, cy: 650, r: 24 },
            ],

            // ---------------------------------------------------------------
            // DATA INIT / FETCH
            // ---------------------------------------------------------------
            async initData() {
                this.serverLoading = true;
                this.serverError = false;
                await this.fetchData();
            },

            async fetchData() {
                this.serverLoading = true;
                this.serverError = false;

                try {
                    const [distRes, sumRes] = await Promise.all([
                        fetch('/api/heatmap/districts'),
                        fetch('/api/heatmap/summary'),
                    ]);

                    if (!distRes.ok || !sumRes.ok) throw new Error('API error');

                    const distJson = await distRes.json();
                    const sumJson  = await sumRes.json();

                    if (!distJson.districts || distJson.districts.length === 0) {
                        throw new Error('No district data returned');
                    }

                    // Build a lookup map keyed by district id
                    const liveMap = {};
                    distJson.districts.forEach(d => { liveMap[d.id] = d; });

                    // Merge live data into offline baseline (fills in map coords, names etc.)
                    this.liveDistrictData = this.offlineDistrictData.map(offline => {
                        const live = liveMap[offline.id];
                        if (live && live.computedAveragePrice !== null) {
                            return {
                                ...offline,             // keep cx, cy, r, localNames etc.
                                ...live,                // override with live prices
                            };
                        }
                        return offline;                 // no live data → keep offline fallback
                    });

                    this.liveNationalStats = sumJson;
                    this.payloadDate = distJson.date;
                    this.serverLoading = false;

                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });

                } catch (e) {
                    console.error('[CeylonHeatmap] Fetch failed:', e);
                    this.serverLoading = false;
                    this.serverError = true;

                    // Auto-activate offline fallback after error
                    // so the UI is still usable
                    this.offlineFallback = true;
                    this.serverError = false;

                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons();
                    });
                }
            },

            // ---------------------------------------------------------------
            // COMPUTED GETTERS — driven by live or offline data
            // ---------------------------------------------------------------

            /**
             * The active district list — always returns the full set of 12 districts.
             * Live data is used when available; offline data as fallback.
             */
            get districtData() {
                return (this.liveDistrictData.length > 0 && !this.offlineFallback)
                    ? this.liveDistrictData
                    : this.offlineDistrictData;
            },

            /**
             * calculatedMetrics now just returns districtData directly.
             * The backend already computed averages/trend; the frontend
             * only does a light pass to ensure the shape is consistent.
             */
            get calculatedMetrics() {
                return this.districtData.map(dist => ({
                    ...dist,
                    // Ensure numeric fields are present (guard against nulls from backend)
                    computedAveragePrice:         dist.computedAveragePrice         ?? 0,
                    computedAveragePricePrevWeek: dist.computedAveragePricePrevWeek ?? 0,
                    trendPercent:                 dist.trendPercent                 ?? 0,
                    highestCropName:              dist.highestCropName              ?? 'N/A',
                    highestCropPrice:             dist.highestCropPrice             ?? 0,
                    lowestCropName:               dist.lowestCropName               ?? 'N/A',
                    lowestCropPrice:              dist.lowestCropPrice              ?? 0,
                    primaryCrops:                 dist.primaryCrops                 ?? [],
                }));
            },

            get bounds() {
                const prices = this.calculatedMetrics
                    .map(d => d.computedAveragePrice)
                    .filter(p => p > 0);
                if (prices.length === 0) return { lowThreshold: 200, mediumThreshold: 350, highThreshold: 500 };
                const min   = Math.min(...prices);
                const max   = Math.max(...prices);
                const range = max - min || 1;
                return {
                    lowThreshold:    min + range * 0.25,
                    mediumThreshold: min + range * 0.55,
                    highThreshold:   min + range * 0.80,
                };
            },

            get nationalStats() {
                // Prefer the API-computed summary when live data is loaded
                if (this.liveNationalStats && !this.offlineFallback) {
                    return {
                        nationalAverage:   this.liveNationalStats.nationalAverage   ?? 0,
                        expensiveDistrict: this.liveNationalStats.expensiveDistrict ?? 'N/A',
                        expensivePrice:    this.liveNationalStats.expensivePrice    ?? 0,
                        cheapestDistrict:  this.liveNationalStats.cheapestDistrict  ?? 'N/A',
                        cheapestPrice:     this.liveNationalStats.cheapestPrice     ?? 0,
                        distribution:      this.liveNationalStats.distribution      ?? { lowCount: 0, midCount: 0, highCount: 0 },
                    };
                }

                // Offline / computed fallback
                let metrics = this.calculatedMetrics;
                if (!metrics.length) return { nationalAverage: 0, expensiveDistrict: '', cheapestDistrict: '', distribution: {} };

                let sum = 0, exp = metrics[0], chp = metrics[0];
                let low = 0, mid = 0, high = 0;
                let bnd = this.bounds;

                metrics.forEach(d => {
                    sum += d.computedAveragePrice;
                    if (d.computedAveragePrice > exp.computedAveragePrice) exp = d;
                    if (d.computedAveragePrice < chp.computedAveragePrice) chp = d;

                    if (d.computedAveragePrice <= bnd.lowThreshold)    low++;
                    else if (d.computedAveragePrice <= bnd.mediumThreshold) mid++;
                    else high++;
                });

                return {
                    nationalAverage:   Math.round(sum / metrics.length),
                    expensiveDistrict: exp.name,
                    expensivePrice:    exp.computedAveragePrice,
                    cheapestDistrict:  chp.name,
                    cheapestPrice:     chp.computedAveragePrice,
                    distribution:      { lowCount: low, midCount: mid, highCount: high },
                };
            },

            get formattedPayloadDate() {
                if (!this.payloadDate) return 'Offline Cache';
                try {
                    return new Date(this.payloadDate + 'T00:00:00').toLocaleDateString([], {
                        day: 'numeric', month: 'short', year: 'numeric'
                    });
                } catch (e) {
                    return this.payloadDate;
                }
            },

            // Accessors for currently active UI elements
            get selectedDistrict() {
                return this.calculatedMetrics.find(d => d.id === this.selectedDistrictId) || this.calculatedMetrics[0];
            },

            get hoveredDistrict() {
                if (!this.hoveredDistrictId) return null;
                return this.calculatedMetrics.find(d => d.id === this.hoveredDistrictId);
            },

            getFillColor(dist) {
                if (this.displayMode === 'prices') {
                    const p = dist.computedAveragePrice;
                    if (p <= 0)                            return '#94A3B8'; // slate (no data)
                    if (p <= this.bounds.lowThreshold)    return '#10B981'; // Emerald
                    if (p <= this.bounds.mediumThreshold) return '#FBBF24'; // Yellow
                    if (p <= this.bounds.highThreshold)   return '#FB923C'; // Orange
                    return '#F43F5E';                                         // Red
                } else {
                    if (dist.role === 'Production')    return '#059669';
                    if (dist.role === 'Distribution')  return '#2563EB';
                    return '#E11D48';
                }
            },

            // Comparison section
            get compareA() { return this.calculatedMetrics.find(d => d.id === this.compareDistrictIdA) || this.calculatedMetrics[0]; },
            get compareB() { return this.calculatedMetrics.find(d => d.id === this.compareDistrictIdB) || this.calculatedMetrics[1]; },

            get compareGapAbs()       { return Math.abs(this.compareA.computedAveragePrice - this.compareB.computedAveragePrice); },
            get compareGapRatio()     { return this.compareB.computedAveragePrice > 0 ? parseFloat(((this.compareGapAbs / this.compareB.computedAveragePrice) * 100).toFixed(1)) : 0; },
            get compareGapIsACheaper(){ return (this.compareA.computedAveragePrice - this.compareB.computedAveragePrice) < 0; },
        }));
    });

</script>