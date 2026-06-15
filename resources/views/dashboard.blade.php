@extends('layouts.app')

@section('title', 'HARTI - Sri Lanka Daily Vegetable Prices')

@section('content')
<div x-data="Dashboard()" class="w-full relative min-h-screen bg-slate-950 flex flex-col font-sans text-slate-100 pb-[120px]">
    
    <nav class="sticky top-0 z-50 transition-all duration-300 w-full" 
         :class="scrolled ? 'bg-slate-900/90 backdrop-blur-xl border-b border-slate-800 shadow-lg' : 'bg-transparent'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <div class="flex items-center gap-3 cursor-pointer select-none group" @click="handleLogoClick()">
                    <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center transform group-hover:rotate-12 transition-all duration-300 shadow-md shadow-emerald-500/20">
                        <svg class="w-6 h-6 text-slate-950 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-black tracking-tight text-white transition-colors">CEYLON PULSE</span>
                        <span class="text-[9px] uppercase tracking-widest text-emerald-400 font-mono font-bold leading-none -mt-0.5">Agri Index</span>
                    </div>
                </div>

                <div class="hidden lg:flex items-center justify-center gap-1 flex-1 mx-8">
                    <template x-for="item in navItems" :key="item.id">
                        <button 
                            @click="activeTab = item.id" 
                            class="group relative px-4 py-2 text-xs font-mono font-bold tracking-wide uppercase transition-all duration-300 rounded-xl"
                            :class="activeTab === item.id ? 'text-emerald-400 bg-slate-900' : 'text-slate-400 hover:text-white hover:bg-slate-900/50'"
                        >
                            <span class="relative z-10 flex items-center gap-2" x-text="item.label"></span>
                        </button>
                    </template>
                </div>

                <div class="flex items-center gap-4">
                    <button @click="setLang('en')" class="text-xs font-mono font-bold px-2.5 py-1 rounded-md border transition border-emerald-500/30 text-emerald-400 bg-emerald-950/30">EN</button>
                </div>
            </div>
        </div>
    </nav>

    <main class="w-full relative z-10 flex-1">
        
        <div x-show="activeTab === 'home'" x-transition:enter="transition ease-out duration-300" class="space-y-12">
            
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12">
                <div class="bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800 rounded-[3rem] p-8 md:p-12 relative overflow-hidden shadow-2xl">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,#10b9810a,transparent_45%)]"></div>
                    
                    <div class="max-w-3xl space-y-6 relative z-10">
                        <span class="text-emerald-400 font-mono font-bold tracking-[0.2em] text-[10px] uppercase bg-emerald-950/50 w-max px-3 py-1.5 rounded-lg border border-emerald-500/20 shadow-inner">
                            Sourced from HARTI & CBSL
                        </span>
                        <h1 class="text-4xl sm:text-5xl md:text-6xl font-black text-white tracking-tight leading-none">
                            Daily Vegetable Prices<br/><span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-400">Across Sri Lanka</span> 
                        </h1>
                        <p class="text-slate-400 text-sm md:text-base leading-relaxed max-w-xl">
                            Track fresh market prices, trends, and historical data updated daily. Compare rates between Pettah, Dambulla, and other major national economic centers.
                        </p>
                        <div class="flex flex-wrap gap-4 pt-2">
                            <button @click="activeTab = 'rates'" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-mono font-bold px-6 py-3.5 rounded-xl shadow-lg shadow-emerald-500/10 transition transform active:scale-95">
                                View Today's Prices
                            </button>
                            <button @click="activeTab = 'trends'" class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-mono font-bold px-6 py-3.5 rounded-xl border border-slate-700 transition">
                                Explore Trends
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-6">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                        <div class="flex items-center gap-2 text-emerald-400">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span class="text-xs font-mono font-bold uppercase tracking-wider">CBSL Official Data Verified</span>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-500 text-xs font-mono">Extracted Report Date: </span>
                            <span class="text-slate-300 text-xs font-mono font-bold" x-text="pdfDate || '2026-06-11'"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-mono text-slate-400">Carrot</p>
                                <p class="text-[10px] text-slate-500">Dambulla Econ Center</p>
                                <p class="text-xl font-bold font-mono text-white mt-1">Rs. <span x-text="prices.carrot?.price || '320'"></span></p>
                            </div>
                            <span class="text-xs font-mono font-bold text-emerald-400 bg-emerald-950/40 px-2 py-1 rounded border border-emerald-500/10">+4.2%</span>
                        </div>
                        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-mono text-slate-400">Tomato</p>
                                <p class="text-[10px] text-slate-500">Dambulla Econ Center</p>
                                <p class="text-xl font-bold font-mono text-white mt-1">Rs. <span x-text="prices.tomato?.price || '240'"></span></p>
                            </div>
                            <span class="text-xs font-mono font-bold text-rose-400 bg-rose-950/40 px-2 py-1 rounded border border-rose-500/10">-2.1%</span>
                        </div>
                        <div class="bg-slate-900/60 border border-slate-800 rounded-2xl p-5 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-mono text-slate-400">Leeks</p>
                                <p class="text-[10px] text-slate-500">Dambulla Econ Center</p>
                                <p class="text-xl font-bold font-mono text-white mt-1">Rs. <span x-text="prices.leeks?.price || '460'"></span></p>
                            </div>
                            <span class="text-xs font-mono font-bold text-emerald-400 bg-emerald-950/40 px-2 py-1 rounded border border-emerald-500/10">+1.5%</span>
                        </div>
                    </div>
                </div>
            </section>

            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="space-y-2 mb-6">
                    <h2 class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-widest">Clear Portal Guidelines</h2>
                    <p class="text-2xl font-bold text-white">Understanding Ceylon Food Pricing</p>
                    <p class="text-slate-400 text-sm max-w-2xl">Our purpose is to empower standard householders, vendors, and farmers with direct, simplified market values to bypass trading intermediaries.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div @click="activeTab = 'rates'" class="bg-slate-900/40 border border-slate-800/80 hover:border-emerald-500/30 p-6 rounded-2xl transition cursor-pointer group">
                        <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-emerald-500 group-hover:text-slate-950 transition mb-4 font-mono font-bold text-xs">1</div>
                        <h4 class="font-bold text-white text-sm mb-2">Check Daily Rates</h4>
                        <p class="text-slate-400 text-xs leading-relaxed mb-4">Sri Lankan vegetable prices fluctuate daily based on rainfall and diesel transport tariffs. Always check Pettah vs Dambulla levels before bulk purchase.</p>
                        <span class="text-xs font-mono font-bold text-emerald-400 flex items-center gap-1 group-hover:underline">Go to Rates Table <i data-lucide="arrow-right" class="w-3 h-3"></i></span>
                    </div>

                    <div @click="activeTab = 'rates'" class="bg-slate-900/40 border border-slate-800/80 hover:border-emerald-500/30 p-6 rounded-2xl transition cursor-pointer group">
                        <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-emerald-500 group-hover:text-slate-950 transition mb-4 font-mono font-bold text-xs">2</div>
                        <h4 class="font-bold text-white text-sm mb-2">Compare Hubs</h4>
                        <p class="text-slate-400 text-xs leading-relaxed mb-4">Expand our Compare Hub Prices switch to compare prices side-by-side across all major trade hubs in real-time.</p>
                        <span class="text-xs font-mono font-bold text-emerald-400 flex items-center gap-1 group-hover:underline">Start Comparison <i data-lucide="arrow-right" class="w-3 h-3"></i></span>
                    </div>

                    <div @click="activeTab = 'heatmap'" class="bg-slate-900/40 border border-slate-800/80 hover:border-emerald-500/30 p-6 rounded-2xl transition cursor-pointer group">
                        <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-emerald-500 group-hover:text-slate-950 transition mb-4 font-mono font-bold text-xs">3</div>
                        <h4 class="font-bold text-white text-sm mb-2">Geo Distributions</h4>
                        <p class="text-slate-400 text-xs leading-relaxed mb-4">Highlands provide up-country cold crops (Leeks, Potatoes), while low-country dry zones produce Pumpkin & Okra, shipping outward.</p>
                        <span class="text-xs font-mono font-bold text-emerald-400 flex items-center gap-1 group-hover:underline">View Distribution Map <i data-lucide="arrow-right" class="w-3 h-3"></i></span>
                    </div>

                    <div @click="activeTab = 'trends'" class="bg-slate-900/40 border border-slate-800/80 hover:border-emerald-500/30 p-6 rounded-2xl transition cursor-pointer group">
                        <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 group-hover:bg-emerald-500 group-hover:text-slate-950 transition mb-4 font-mono font-bold text-xs">4</div>
                        <h4 class="font-bold text-white text-sm mb-2">Examine History</h4>
                        <p class="text-slate-400 text-xs leading-relaxed mb-4">Analyze seasonal 7, 30, and 90-day graphical charts to buy when pricing curves hit historical lows.</p>
                        <span class="text-xs font-mono font-bold text-emerald-400 flex items-center gap-1 group-hover:underline">Browse History <i data-lucide="arrow-right" class="w-3 h-3"></i></span>
                    </div>
                </div>
            </section>

            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h3 class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-widest">National Trade Hub Handbook</h3>
                    <p class="text-2xl font-bold text-white">Wholesale vs Retail Centers</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative">
                        <span class="text-[9px] font-mono font-bold uppercase text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded absolute top-6 right-6">BENCHMARK: Highest Price Peak</span>
                        <p class="text-xs font-mono text-slate-500">Colombo Terminal</p>
                        <h4 class="text-lg font-bold text-white mb-2">Pettah Manning Market</h4>
                        <p class="text-slate-400 text-xs leading-relaxed">Representing the primary high-volume retail and final distribution node. Sinks heavy cargo from all over Sri Lanka. Premium price point due to transport margins.</p>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative">
                        <span class="text-[9px] font-mono font-bold uppercase text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded absolute top-6 right-6">BENCHMARK: Core Trade Base Rates</span>
                        <p class="text-xs font-mono text-slate-500">National Intersect</p>
                        <h4 class="text-lg font-bold text-white mb-2">Dambulla Dedicated Economic Center</h4>
                        <p class="text-slate-400 text-xs leading-relaxed">Sits at the geographic center of major low-country harvesting regions. Governs wholesale pricing thresholds for crop trades across Ceylon. Defining base pricing.</p>
                    </div>

                    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative">
                        <span class="text-[9px] font-mono font-bold uppercase text-cyan-400 bg-cyan-400/10 px-2 py-0.5 rounded absolute top-6 right-6">BENCHMARK: Lowest Cold Slope Rates</span>
                        <p class="text-xs font-mono text-slate-500">Source Highlands</p>
                        <h4 class="text-lg font-bold text-white mb-2">Keppetipola & Nuwara Eliya</h4>
                        <p class="text-slate-400 text-xs leading-relaxed">The birthplace of premium mountain vegetables. Potatoes, Carrots, Leeks, and Cabbage are dispatched here directly from farms, bypassing intermediary wholesalers.</p>
                    </div>
                </div>
            </section>

            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <p class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-widest">Market Favorites</p>
                    <h3 class="text-xl font-bold text-white">Popular Local Vegetables</h3>
                    <p class="text-slate-400 text-xs mt-1">Click any vegetable card to automatically plot its daily price variations on the Trends chart below.</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <template x-for="(marketData, vegId) in prices" :key="vegId">
                        <div @click="viewTrendFor(vegId)" class="bg-slate-900 hover:bg-slate-800 border border-slate-800 hover:border-emerald-500/30 rounded-2xl p-4 transition cursor-pointer flex items-center justify-between">
                            <div>
                                <h5 class="text-sm font-bold text-white capitalize" x-text="vegId"></h5>
                                <p class="text-xs font-mono text-emerald-400 mt-1" x-text="'Rs. ' + marketData.price"></p>
                            </div>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-slate-600"></i>
                        </div>
                    </template>
                </div>
            </section>

        </div>

        <div x-show="activeTab === 'rates'" x-transition style="display: none;">
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-white">Live Market Rates Today</h2>
                        <p class="text-xs text-slate-400 mt-1 font-mono">Real-time data feed from national open indices.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <input type="text" x-model="searchQuery" placeholder="Filter commodities..." class="bg-slate-900 border border-slate-800 text-sm rounded-xl px-4 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-white w-52">
                        <select x-model="selectedMarket" @change="fetchTodayPrices()" class="bg-slate-900 border border-slate-800 text-xs rounded-xl p-2.5 text-white focus:outline-none">
                            <option value="pettah">Pettah Wholesale</option>
                            <option value="dambulla">Dambulla Economic Centre</option>
                            <option value="narahenpita">Narahenpita Economic Centre</option>
                        </select>
                    </div>
                </div>

                <div class="bg-slate-900 border border-slate-800 shadow-xl rounded-2xl overflow-hidden">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-slate-950 font-mono text-xs text-slate-400 uppercase border-b border-slate-800">
                            <tr>
                                <th class="p-4">Vegetable Commodity</th>
                                <th class="p-4">Current Price</th>
                                <th class="p-4">Yesterday's Price</th>
                                <th class="p-4">Daily Change</th>
                            </tr>
                        </thead>
                        <tbody class="text-slate-300">
                            <template x-for="(marketData, vegId) in filteredPrices" :key="vegId">
                                <tr class="border-b border-slate-800 hover:bg-slate-800/30 transition">
                                    <td class="p-4 font-semibold capitalize text-white" x-text="vegId"></td>
                                    <td class="p-4 font-mono font-bold text-emerald-400" x-text="'Rs. ' + marketData.price"></td>
                                    <td class="p-4 font-mono text-slate-500" x-text="'Rs. ' + marketData.priceYesterday"></td>
                                    <td class="p-4 font-mono font-bold" :class="marketData.changePercent >= 0 ? 'text-emerald-500' : 'text-rose-500'" x-text="(marketData.changePercent >= 0 ? '+' : '') + marketData.changePercent + '%'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div x-show="activeTab === 'trends'" x-transition style="display: none;">
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-white">Market Trends Analytics</h2>
                        <p class="text-xs text-slate-400 font-mono mt-1" x-text="'Active Chart Vector: ' + trendVeg.toUpperCase()"></p>
                    </div>
                    <select x-model="trendVeg" @change="fetchTrendHistory()" class="bg-slate-900 border border-slate-800 text-sm rounded-xl p-2 text-white">
                        <option value="carrot">Carrot</option>
                        <option value="tomato">Tomato</option>
                        <option value="leeks">Leeks</option>
                        <option value="beans">Beans</option>
                    </select>
                </div>
                
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-xl">
                    <div class="h-80 w-full relative">
                        <canvas id="laravelTrendChart"></canvas>
                    </div>
                </div>
            </section>
        </div>

        <div x-show="activeTab === 'heatmap'" x-transition style="display: none;">
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
                <div class="max-w-md mx-auto bg-slate-900 border border-slate-800 p-8 rounded-3xl">
                    <i data-lucide="map" class="w-12 h-12 text-emerald-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-bold text-white">Geographical Distribution Map</h3>
                    <p class="text-slate-400 text-xs mt-2 leading-relaxed">Visual production yields mapping across major agricultural production hubs (Highland vs Low Country Zones) is synchronizing.</p>
                </div>
            </section>
        </div>

        <div x-show="activeTab === 'about'" x-transition style="display: none;">
            <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-6">
                <h2 class="text-2xl font-bold text-white">About the Open Pricing Platform</h2>
                <p class="text-slate-400 text-sm leading-relaxed">This terminal tracks macro-economic food pricing indices scraped from direct agricultural central market gates. Verified metrics assist in avoiding price exploitation by middle-tier wholesale distribution pools.</p>
            </section>
        </div>

        <div x-show="activeTab === 'pipeline'" x-transition style="display: none;">
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-slate-900">
                <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-6 max-w-xl mx-auto">
                    <h2 class="text-lg font-bold mb-4 flex items-center gap-2 text-slate-800"><i data-lucide="shield" class="text-emerald-600"></i> HARTI Scraper Control Engine</h2>
                    <p class="text-xs text-slate-500 mb-6 font-mono">Trigger manual updates directly bypassing background schedulers.</p>
                    
                    <div class="space-y-4">
                        <button @click="triggerScraperPipeline()" :disabled="scraping" class="w-full bg-slate-900 text-white font-mono p-3 rounded-xl hover:bg-slate-800 disabled:opacity-50 transition">
                            <span x-text="scraping ? 'Processing Scraping Job...' : 'Force Trigger CBSL PDF Pipeline Scrape'"></span>
                        </button>
                    </div>
                </div>
            </section>
        </div>

    </main>

    <footer class="w-full border-t border-slate-900 bg-slate-950 mt-auto py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-mono text-slate-500">
            <div>
                <span class="text-slate-300 font-bold">Lanka Vegetable Prices</span> | Empowering Sri Lankan households with pristine market rate visibility.
            </div>
            <div class="flex gap-4">
                <span class="hover:text-emerald-400 cursor-pointer" @click="activeTab = 'pipeline'">Admin Portal</span>
                <span>|</span>
                <span>© 2026 Ceylon Agriculture Hub</span>
                <span>|</span>
                <span class="text-emerald-400 cursor-pointer" @click="activeTab = 'rates'">Live Rates</span>
            </div>
        </div>
    </footer>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('Dashboard', () => ({
            activeTab: 'home',
            scrolled: false,
            logoClickCount: 0,
            selectedMarket: 'pettah',
            searchQuery: '',
            prices: {},
            loading: false,
            scraping: false,
            trendVeg: 'carrot',
            pdfDate: '',
            chartInstance: null,

            navItems: [
                { id: 'home', label: 'Home Feed' },
                { id: 'rates', label: 'Market Prices' },
                { id: 'trends', label: 'Market Trends' },
                { id: 'heatmap', label: 'Heatmap View' },
                { id: 'about', label: 'About App' }
            ],

            init() {
                window.addEventListener('scroll', () => {
                    this.scrolled = window.scrollY > 30;
                });

                this.fetchTodayPrices();

                this.$watch('activeTab', (value) => {
                    setTimeout(() => { if(window.lucide) { lucide.createIcons(); } }, 60);
                    if (value === 'trends') {
                        this.fetchTrendHistory();
                    }
                });
            },

            get filteredPrices() {
                if (!this.searchQuery.trim()) return this.prices;
                let query = this.searchQuery.toLowerCase();
                return Object.fromEntries(
                    Object.entries(this.prices).filter(([key]) => key.toLowerCase().includes(query))
                );
            },

            fetchTodayPrices() {
                this.loading = true;
                fetch(`/api/prices/today?marketId=${this.selectedMarket}`)
                    .then(res => res.json())
                    .then(data => {
                        this.prices = data.prices || {};
                        this.pdfDate = data.scrapedPdfDate || '';
                        this.loading = false;
                        setTimeout(() => { if(window.lucide) { lucide.createIcons(); } }, 50);
                    })
                    .catch(err => {
                        console.error("Error fetching prices", err);
                        this.loading = false;
                    });
            },

            viewTrendFor(vegId) {
                this.trendVeg = vegId;
                this.activeTab = 'trends';
            },

            fetchTrendHistory() {
                fetch(`/api/prices/history?vegetableId=${this.trendVeg}&marketId=${this.selectedMarket}&days=30`)
                    .then(res => res.json())
                    .then(data => {
                        const canvas = document.getElementById('laravelTrendChart');
                        if (!canvas) return;
                        
                        const ctx = canvas.getContext('2d');
                        const labels = data.history.map(h => h.date);
                        const datasetData = data.history.map(h => h.price);

                        if (this.chartInstance) {
                            this.chartInstance.destroy();
                        }

                        this.chartInstance = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: `${this.trendVeg.toUpperCase()} Price (Rs.)`,
                                    data: datasetData,
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.05)',
                                    tension: 0.4,
                                    fill: true,
                                    borderWidth: 2,
                                    pointRadius: 2
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false }
                                },
                                scales: {
                                    x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 10 } } },
                                    y: { grid: { color: '#1e293b' }, ticks: { color: '#64748b', font: { size: 10 } } }
                                }
                            }
                        });
                    });
            },

            triggerScraperPipeline() {
                this.scraping = true;
                fetch('/api/pipeline/trigger', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message || 'Scraping Job Processed!');
                    this.scraping = false;
                    this.fetchTodayPrices();
                })
                .catch(() => {
                    this.scraping = false;
                });
            },

            handleLogoClick() {
                this.activeTab = 'home';
                this.logoClickCount++;
                if (this.logoClickCount >= 5) {
                    this.activeTab = 'pipeline';
                    this.logoClickCount = 0;
                }
            }
        }));
    });
</script>
@endsection