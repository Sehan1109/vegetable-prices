@extends('layouts.app')

@section('title', 'HARTI - Sri Lanka Daily Vegetable Prices')

@section('content')
<div x-data="Dashboard()" 
     @theme-changed.window="console.log('Dashboard received theme-changed event. New darkMode:', $event.detail); $nextTick(() => { if(window.lucide) { lucide.createIcons(); } })"
     class="w-full relative min-h-screen bg-white dark:bg-slate-950 flex flex-col font-sans text-slate-900 dark:text-slate-100">
    
    <nav class="sticky top-0 z-50 transition-all duration-300 w-full" 
         :class="scrolled ? 'bg-white/90 backdrop-blur-xl border-b border-slate-200 shadow-lg dark:bg-slate-900/90 dark:border-slate-800' : 'bg-transparent'"
         x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                
                <div class="flex items-center gap-3 cursor-pointer select-none group" @click="handleLogoClick()">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-emerald-500 rounded-xl flex items-center justify-center transform group-hover:rotate-12 transition-all duration-300 shadow-md shadow-emerald-500/20 shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-slate-900 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-base sm:text-lg font-black tracking-tight text-slate-900 dark:text-white transition-colors leading-none">Lanka Produce Prices</span>
                        <span class="text-[9px] uppercase tracking-widest text-emerald-400 font-mono font-bold leading-none mt-0.5">Ceylon Markets</span>
                    </div>
                </div>

                <div class="hidden lg:flex items-center justify-center gap-1 flex-1 mx-8">
                    <template x-for="item in navItems" :key="item.id">
                        <button 
                            @click="activeTab = item.id" 
                            class="group relative px-4 py-2 text-xs font-mono font-bold tracking-wide uppercase transition-all duration-300 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100/50 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-900/50"
                            :class="activeTab === item.id ? 'text-emerald-600 bg-slate-100 dark:text-emerald-400 dark:bg-slate-900' : ''"
                        >
                            <span class="relative z-10 flex items-center gap-2" x-text="item.label"></span>
                        </button>
                    </template>
                </div>

                <div class="flex items-center gap-2 sm:gap-4">
                    <button
                        @click="$store.theme.toggle()"
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center
                            text-slate-600 hover:text-slate-900
                            dark:text-slate-400 dark:hover:text-white"
                    >
                        <svg x-show="$store.theme.darkMode"
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">
                            <circle cx="12" cy="12" r="5"/>
                            <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/>
                        </svg>

                        <svg x-show="!$store.theme.darkMode"
                            xmlns="http://www.w3.org/2000/svg"
                            width="20"
                            height="20"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">
                            <path d="M12 3a9 9 0 1 0 9 9A7 7 0 0 1 12 3z"/>
                        </svg>
                    </button>

                    <div class="hidden sm:flex items-center gap-1 border border-emerald-500/30 rounded-md p-0.5 bg-emerald-950/30">
                        <button @click="setLang('en')" :class="lang === 'en' ? 'bg-emerald-500 text-white' : 'text-emerald-400 hover:bg-emerald-500/20'" class="text-xs font-mono font-bold px-2.5 py-1 rounded-sm transition">EN</button>
                        <button @click="setLang('si')" :class="lang === 'si' ? 'bg-emerald-500 text-white' : 'text-emerald-400 hover:bg-emerald-500/20'" class="text-xs font-mono font-bold px-2.5 py-1 rounded-sm transition">SI</button>
                        <button @click="setLang('ta')" :class="lang === 'ta' ? 'bg-emerald-500 text-white' : 'text-emerald-400 hover:bg-emerald-500/20'" class="text-xs font-mono font-bold px-2.5 py-1 rounded-sm transition">TA</button>
                    </div>

                    <!-- Mobile hamburger -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="lg:hidden w-9 h-9 rounded-lg flex items-center justify-center text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors"
                        aria-label="Toggle menu">
                        <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Drawer -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 shadow-xl"
             @click.away="mobileMenuOpen = false">
            <div class="max-w-7xl mx-auto px-4 py-4 space-y-1">
                <template x-for="item in navItems" :key="item.id">
                    <button
                        @click="activeTab = item.id; mobileMenuOpen = false"
                        class="w-full text-left px-4 py-3 rounded-xl text-sm font-mono font-bold tracking-wide uppercase transition-all duration-200 text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800"
                        :class="activeTab === item.id ? 'text-emerald-600 bg-emerald-50 dark:text-emerald-400 dark:bg-emerald-950/40' : ''"
                        x-text="item.label">
                    </button>
                </template>
                <div class="pt-4 pb-2 border-t border-slate-200 dark:border-slate-800 flex items-center gap-3 px-4">
                    <span class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400 uppercase">Language</span>
                    <div class="flex items-center gap-1 border border-emerald-500/30 rounded-md p-0.5 bg-emerald-950/30">
                        <button @click="setLang('en')" :class="lang === 'en' ? 'bg-emerald-500 text-white' : 'text-emerald-400 hover:bg-emerald-500/20'" class="text-xs font-mono font-bold px-3 py-1 rounded-sm transition">EN</button>
                        <button @click="setLang('si')" :class="lang === 'si' ? 'bg-emerald-500 text-white' : 'text-emerald-400 hover:bg-emerald-500/20'" class="text-xs font-mono font-bold px-3 py-1 rounded-sm transition">SI</button>
                        <button @click="setLang('ta')" :class="lang === 'ta' ? 'bg-emerald-500 text-white' : 'text-emerald-400 hover:bg-emerald-500/20'" class="text-xs font-mono font-bold px-3 py-1 rounded-sm transition">TA</button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="w-full relative z-10 flex-1">
        
        <div x-show="activeTab === 'home'" x-transition:enter="transition ease-out duration-300" class="space-y-12 sm:space-y-24" style="{{ ($initialTab ?? 'home') !== 'home' ? 'display: none;' : '' }}">
            
            <!-- Premium Hero Section -->
            <section class="relative w-full overflow-hidden bg-white dark:bg-slate-950 transition-colors duration-500">
                <!-- Background Decorative Glows -->
                <div class="absolute top-0 right-0 -translate-y-12 translate-x-12 w-[400px] sm:w-[600px] h-[400px] sm:h-[600px] bg-emerald-50 dark:bg-emerald-900/20 rounded-full blur-3xl opacity-50"></div>
                <div class="absolute bottom-0 left-0 translate-y-12 -translate-x-12 w-[300px] sm:w-[400px] h-[300px] sm:h-[400px] bg-slate-50 dark:bg-slate-800/30 rounded-full blur-3xl opacity-50"></div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-20">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                        
                        <!-- Left Column: Content -->
                        <div class="space-y-8 relative z-10">
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/5 border border-emerald-500/10 backdrop-blur-sm shadow-sm transition-all hover:bg-emerald-500/10">
                                <i data-lucide="leaf" class="w-3.5 h-3.5 text-emerald-500"></i>
                                <span class="text-emerald-600 font-mono font-bold tracking-widest text-[10px] uppercase">Sourced from HARTI & CBSL</span>
                            </div>

                            <div class="space-y-4">
                                <h1 class="text-4xl lg:text-6xl font-black tracking-tight leading-[1.1]">
                                    <span class="text-slate-900 dark:text-white block">Daily Vegetable</span>
                                    <span class="text-[#10B981] block">Prices Across SL</span>
                                </h1>
                                <p class="text-slate-500 dark:text-slate-400 text-base md:text-lg leading-relaxed max-w-lg font-medium">
                                    Track fresh market prices, trends, and historical data updated daily. Compare rates between Peliyagoda, Dambulla, and other major national economic centers.
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-4 items-center">
                                <button @click="activeTab = 'rates'" class="group relative px-6 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-2xl font-bold text-sm shadow-xl shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-95 flex items-center gap-2">
                                    <span>View Today's Prices</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:translate-x-1"></i>
                                </button>
                                <button @click="activeTab = 'trends'" class="px-6 py-3.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 rounded-2xl font-bold text-sm transition-all hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600">
                                    Explore Trends
                                </button>
                            </div>

                            <!-- Verification Card -->
                            <div class="p-4 rounded-3xl bg-emerald-500/5 border border-emerald-500/10 backdrop-blur-md flex items-center justify-between group cursor-default transition-all hover:bg-emerald-500/10 max-w-lg">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-white" x-text="dataSource">HARTI Official Data Verified</h4>
                                        <p class="text-xs text-emerald-600/70 font-mono mt-0.5">Extracted: <span x-text="pdfDate"></span></p>
                                    </div>
                                </div>
                                <a :href="pdfUrl" x-show="pdfUrl" target="_blank" class="p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-emerald-100 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 transition-all hover:bg-emerald-500 dark:hover:bg-emerald-600 hover:text-white shadow-sm">
                                    <i data-lucide="file-text" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Right Column: Visual Dashboard -->
                        <div class="relative flex justify-center items-center overflow-hidden sm:overflow-visible">
                            <!-- Floating Background Icons (hidden on very small screens to prevent overflow) -->
                            <div class="hidden sm:block absolute -top-12 -left-12 animate-[bounce_4s_ease-in-out_infinite] delay-700">
                                <x-vegetable-illustration id="carrot" size="80" class="opacity-80 drop-shadow-xl" />
                            </div>
                            <div class="hidden sm:block absolute top-1/2 -right-8 animate-[bounce_5s_ease-in-out_infinite]">
                                <x-vegetable-illustration id="tomato" size="70" class="opacity-70 drop-shadow-xl" />
                            </div>
                            <div class="hidden sm:block absolute -bottom-10 left-10 animate-[bounce_6s_ease-in-out_infinite] delay-1000">
                                <x-vegetable-illustration id="leeks" size="90" class="opacity-60 drop-shadow-xl" />
                            </div>
                            <div class="hidden sm:block absolute -bottom-4 right-1/4 animate-[bounce_3.5s_ease-in-out_infinite] delay-300">
                                <x-vegetable-illustration id="brinjal" size="60" class="opacity-70 drop-shadow-xl" />
                            </div>

                            <!-- Main Dashboard Card -->
                            <div class="w-full max-w-sm bg-white dark:bg-slate-800 rounded-[2.5rem] p-2 shadow-[0_32px_64px_-12px_rgba(0,0,0,0.12)] dark:shadow-[0_32px_64px_-12px_rgba(0,0,0,0.5)] border border-slate-100 dark:border-slate-700 relative z-20 transition-all hover:scale-[1.01]">
                                <div class="bg-slate-50 dark:bg-slate-900 rounded-[2rem] p-5 space-y-4">
                                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                            <span class="text-[10px] font-mono font-black uppercase text-slate-400 dark:text-slate-500 tracking-tighter">Live Market Feed</span>
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 font-mono" x-text="selectedMarket === 'dambulla' ? 'Dambulla Econ Center' : (selectedMarket === 'peliyagoda' ? 'Peliyagoda Market' : selectedMarket.charAt(0).toUpperCase() + selectedMarket.slice(1))">Peliyagoda Market</span>
                                    </div>

                                    <div class="space-y-2">
                                        <!-- Row 1: Carrot -->
                                        <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-all hover:shadow-md">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center">
                                                    <x-vegetable-illustration id="carrot" size="24" />
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100">Carrot</p>
                                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-mono uppercase">Wholesale Grade</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-black text-slate-900 dark:text-white font-mono" x-text="prices.carrot ? 'Rs. ' + (prices.carrot.price_average ?? prices.carrot.price) : '—'">—</p>
                                                <span class="text-[9px] font-bold"
                                                    :class="prices.carrot?.changePercent >= 0 ? 'text-emerald-500' : 'text-rose-500'"
                                                    x-text="prices.carrot ? (prices.carrot.changePercent > 0 ? '+' : '') + prices.carrot.changePercent + '%' : ''">—</span>
                                            </div>
                                        </div>

                                        <!-- Row 2: Tomato -->
                                        <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-all hover:shadow-md">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center">
                                                    <x-vegetable-illustration id="tomato" size="24" />
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100">Tomato</p>
                                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-mono uppercase">LKR Spot Index</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-black text-slate-900 dark:text-white font-mono" x-text="prices.tomato ? 'Rs. ' + (prices.tomato.price_average ?? prices.tomato.price) : '—'">—</p>
                                                <span class="text-[9px] font-bold"
                                                    :class="prices.tomato?.changePercent >= 0 ? 'text-emerald-500' : 'text-rose-500'"
                                                    x-text="prices.tomato ? (prices.tomato.changePercent > 0 ? '+' : '') + prices.tomato.changePercent + '%' : ''">—</span>
                                            </div>
                                        </div>

                                        <!-- Row 3: Green Beans -->
                                        <div class="flex items-center justify-between p-3 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm transition-all hover:shadow-md">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-green-50 dark:bg-green-900/30 flex items-center justify-center">
                                                    <x-vegetable-illustration id="beans" size="24" />
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-100">Green Beans</p>
                                                    <p class="text-[9px] text-slate-400 dark:text-slate-500 font-mono uppercase">Fresh Produce</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-black text-slate-900 dark:text-white font-mono" x-text="prices['green-beans'] ? 'Rs. ' + (prices['green-beans'].price_average ?? prices['green-beans'].price) : '—'">—</p>
                                                <span class="text-[9px] font-bold"
                                                    :class="prices['green-beans']?.changePercent >= 0 ? 'text-emerald-500' : 'text-rose-500'"
                                                    x-text="prices['green-beans'] ? (prices['green-beans'].changePercent > 0 ? '+' : '') + prices['green-beans'].changePercent + '%' : ''">—</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-2">
                                        <div class="bg-emerald-600 rounded-2xl p-4 flex items-center justify-between shadow-lg shadow-emerald-600/20">
                                            <div>
                                                <p class="text-[9px] font-bold text-emerald-100 uppercase tracking-widest">Market Avg</p>
                                                <p class="text-lg font-black text-white font-mono mt-0.5" x-text="heroNationalAvg ? 'Rs. ' + heroNationalAvg : '—'">—</p>
                                            </div>
                                            <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white">
                                                <i data-lucide="trending-up" class="w-4 h-4"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Featured Cards Overview -->
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="space-y-2 mb-6">
                    <h2 class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-widest">Clear Portal Guidelines</h2>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">Understanding Ceylon Food Pricing</p>
                    <p class="text-slate-600 dark:text-slate-400 text-sm max-w-2xl">Our purpose is to empower standard householders, vendors, and farmers with direct, simplified market values to bypass trading intermediaries.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div @click="activeTab = 'rates'" class="bg-white border border-slate-200 hover:border-emerald-500/30 p-6 rounded-2xl transition cursor-pointer group dark:bg-slate-900/40 dark:border-slate-800/80">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 group-hover:bg-emerald-500 group-hover:text-slate-950 transition mb-4 font-mono font-bold text-xs dark:bg-slate-800 dark:text-slate-400">1</div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-2">Check Daily Rates</h4>
                        <p class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed mb-4">Sri Lankan vegetable prices fluctuate daily based on rainfall and diesel transport tariffs. Always check Peliyagoda vs Dambulla levels before bulk purchase.</p>
                        <span class="text-xs font-mono font-bold text-emerald-400 flex items-center gap-1 group-hover:underline">Go to Rates Table <i data-lucide="arrow-right" class="w-3 h-3"></i></span>
                    </div>

                    <div @click="activeTab = 'rates'" class="bg-white border border-slate-200 hover:border-emerald-500/30 p-6 rounded-2xl transition cursor-pointer group dark:bg-slate-900/40 dark:border-slate-800/80">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 group-hover:bg-emerald-500 group-hover:text-slate-950 transition mb-4 font-mono font-bold text-xs dark:bg-slate-800 dark:text-slate-400">2</div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-2">Compare Hubs</h4>
                        <p class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed mb-4">Expand our Compare Hub Prices switch to compare prices side-by-side across all major trade hubs in real-time.</p>
                        <span class="text-xs font-mono font-bold text-emerald-400 flex items-center gap-1 group-hover:underline">Start Comparison <i data-lucide="arrow-right" class="w-3 h-3"></i></span>
                    </div>

                    <div @click="activeTab = 'heatmap'" class="bg-white border border-slate-200 hover:border-emerald-500/30 p-6 rounded-2xl transition cursor-pointer group dark:bg-slate-900/40 dark:border-slate-800/80">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 group-hover:bg-emerald-500 group-hover:text-slate-950 transition mb-4 font-mono font-bold text-xs dark:bg-slate-800 dark:text-slate-400">3</div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-2">Geo Distributions</h4>
                        <p class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed mb-4">Highlands provide up-country cold crops (Leeks, Potatoes), while low-country dry zones produce Pumpkin & Okra, shipping outward.</p>
                        <span class="text-xs font-mono font-bold text-emerald-400 flex items-center gap-1 group-hover:underline">View Distribution Map <i data-lucide="arrow-right" class="w-3 h-3"></i></span>
                    </div>

                    <div @click="activeTab = 'trends'" class="bg-white border border-slate-200 hover:border-emerald-500/30 p-6 rounded-2xl transition cursor-pointer group dark:bg-slate-900/40 dark:border-slate-800/80">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600 group-hover:bg-emerald-500 group-hover:text-slate-950 transition mb-4 font-mono font-bold text-xs dark:bg-slate-800 dark:text-slate-400">4</div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-2">Examine History</h4>
                        <p class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed mb-4">Analyze seasonal 7, 30, and 90-day graphical charts to buy when pricing curves hit historical lows.</p>
                        <span class="text-xs font-mono font-bold text-emerald-400 flex items-center gap-1 group-hover:underline">Browse History <i data-lucide="arrow-right" class="w-3 h-3"></i></span>
                    </div>
                </div>
            </section>

            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <h3 class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-widest">National Trade Hub Handbook</h3>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">Wholesale vs Retail Centers</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white border border-slate-200 dark:bg-slate-900 dark:border-slate-800 rounded-2xl p-6 relative">
                        <span class="text-[9px] font-mono font-bold uppercase text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded absolute top-6 right-6">BENCHMARK: Highest Price Peak</span>
                        <p class="text-xs font-mono text-slate-500 dark:text-slate-500">Colombo Terminal</p>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Peliyagoda Manning Market</h4>
                        <p class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed">Representing the primary high-volume retail and final distribution node. Sinks heavy cargo from all over Sri Lanka. Premium price point due to transport margins.</p>
                    </div>

                    <div class="bg-white border border-slate-200 dark:bg-slate-900 dark:border-slate-800 rounded-2xl p-6 relative">
                        <span class="text-[9px] font-mono font-bold uppercase text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded absolute top-6 right-6">BENCHMARK: Core Trade Base Rates</span>
                        <p class="text-xs font-mono text-slate-500 dark:text-slate-500">National Intersect</p>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Dambulla Dedicated Economic Center</h4>
                        <p class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed">Sits at the geographic center of major low-country harvesting regions. Governs wholesale pricing thresholds for crop trades across Ceylon. Defining base pricing.</p>
                    </div>

                    <div class="bg-white border border-slate-200 dark:bg-slate-900 dark:border-slate-800 rounded-2xl p-6 relative">
                        <span class="text-[9px] font-mono font-bold uppercase text-cyan-400 bg-cyan-400/10 px-2 py-0.5 rounded absolute top-6 right-6">BENCHMARK: Lowest Cold Slope Rates</span>
                        <p class="text-xs font-mono text-slate-500 dark:text-slate-500">Source Highlands</p>
                        <h4 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Keppetipola & Nuwara Eliya</h4>
                        <p class="text-slate-600 dark:text-slate-400 text-xs leading-relaxed">The birthplace of premium mountain vegetables. Potatoes, Carrots, Leeks, and Cabbage are dispatched here directly from farms, bypassing intermediary wholesalers.</p>
                    </div>
                </div>
            </section>

            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <p class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-widest">Market Favorites</p>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Popular Local Vegetables</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-xs mt-1">Click any vegetable card to automatically plot its daily price variations on the Trends chart below.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <template x-for="vegId in popularVegetables" :key="vegId">
                        <div x-show="prices[vegId]" 
                             @click="viewTrendFor(vegId); setTimeout(() => document.getElementById('laravelTrendChart').scrollIntoView({behavior: 'smooth', block: 'center'}), 100);"
                             class="group relative bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border border-slate-200/60 dark:border-slate-800/60 rounded-[24px] p-5 cursor-pointer transition-all duration-500 hover:-translate-y-1.5 hover:shadow-[0_20px_40px_-15px_rgba(16,185,129,0.15)] hover:border-emerald-500/30 dark:hover:border-emerald-500/30 overflow-hidden">
                            
                            <!-- Subtle Background Glow on Hover -->
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/0 via-transparent to-emerald-50/50 dark:to-emerald-900/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                            
                            <!-- Header: Icon & Action -->
                            <div class="flex items-start justify-between mb-4 relative z-10">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-[16px] bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-2xl shadow-inner border border-slate-100 dark:border-slate-700 transition-colors group-hover:bg-white dark:group-hover:bg-slate-700" x-text="vegetableMeta[vegId]?.icon"></div>
                                    <h5 class="text-base font-bold text-slate-900 dark:text-white tracking-tight" x-text="vegetableMeta[vegId]?.name"></h5>
                                </div>
                                <button class="w-8 h-8 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-400 group-hover:text-emerald-500 group-hover:border-emerald-200 dark:group-hover:border-emerald-800 group-hover:bg-emerald-50 dark:group-hover:bg-emerald-900/30 transition-all duration-300 shadow-sm">
                                    <i data-lucide="plus" class="w-4 h-4 transition-transform group-hover:rotate-90"></i>
                                </button>
                            </div>

                            <!-- Price -->
                            <div class="mb-4 relative z-10">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-sm font-mono text-slate-400 dark:text-slate-500 font-bold">Rs.</span>
                                    <span class="text-3xl font-black text-slate-900 dark:text-white font-mono tracking-tighter" x-text="prices[vegId]?.price"></span>
                                </div>
                            </div>

                            <!-- Metrics -->
                            <div class="flex items-center gap-3 relative z-10">
                                <!-- Daily Change -->
                                <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border backdrop-blur-sm transition-colors"
                                     :class="prices[vegId]?.changePercent >= 0 ? 'bg-emerald-50/50 border-emerald-100 text-emerald-600 dark:bg-emerald-500/10 dark:border-emerald-500/20 dark:text-emerald-400' : 'bg-rose-50/50 border-rose-100 text-rose-600 dark:bg-rose-500/10 dark:border-rose-500/20 dark:text-rose-400'">
                                    <i :data-lucide="prices[vegId]?.changePercent >= 0 ? 'trending-up' : 'trending-down'" class="w-3.5 h-3.5"></i>
                                    <span class="text-xs font-bold font-mono" x-text="(prices[vegId]?.changePercent > 0 ? '+' : '') + prices[vegId]?.changePercent + '%'"></span>
                                    <span class="text-[9px] font-semibold uppercase tracking-wider opacity-60 ml-0.5">1D</span>
                                </div>
                                
                                <!-- Yearly Change -->
                                <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl border backdrop-blur-sm transition-colors"
                                     :class="prices[vegId]?.changePercentYear >= 0 ? 'bg-slate-50 border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-600 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-300'">
                                    <span class="text-xs font-bold font-mono" x-text="(prices[vegId]?.changePercentYear > 0 ? '+' : '') + (prices[vegId]?.changePercentYear || 0) + '%'"></span>
                                    <span class="text-[9px] font-semibold uppercase tracking-wider opacity-60 ml-0.5">1Y</span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </section>

            <!-- Latest SEO Pages
            @if(isset($latestSeoPages) && count($latestSeoPages) > 0)
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
                <div class="mb-6 flex justify-between items-end">
                    <div>
                        <p class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-widest">Market Intelligence</p>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Latest Detailed Reports</h3>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($latestSeoPages as $seoPage)
                    <a href="{{ url($seoPage->slug) }}" class="group block p-5 bg-white border border-slate-200 rounded-2xl hover:border-emerald-500 hover:shadow-lg transition dark:bg-slate-900/50 dark:border-slate-800">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-1 rounded dark:bg-slate-800 dark:text-slate-400">{{ \Carbon\Carbon::parse($seoPage->date)->format('M d, Y') }}</span>
                            <i data-lucide="external-link" class="w-4 h-4 text-slate-400 group-hover:text-emerald-500 transition"></i>
                        </div>
                        <h4 class="font-bold text-slate-900 dark:text-white mb-1 group-hover:text-emerald-600 transition">{{ $seoPage->title }}</h4>
                        <p class="text-sm text-slate-500 line-clamp-2">{{ $seoPage->meta_description }}</p>
                    </a>
                    @endforeach
                </div>
            </section>
            @endif -->

        </div>

        <div x-show="activeTab === 'rates'" x-transition style="{{ ($initialTab ?? 'home') !== 'rates' ? 'display: none;' : '' }}">
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
                <div class="flex flex-col gap-4 mb-6">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Live Market Rates Today</h2>
                        <p class="text-xs text-slate-600 dark:text-slate-400 mt-1 font-mono">Real-time data feed from national open indices.</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" x-model="searchQuery" placeholder="Filter commodities..." class="bg-white border border-slate-200 text-sm rounded-xl px-4 py-2 focus:outline-none focus:ring-1 focus:ring-emerald-500 text-slate-900 dark:bg-slate-900 dark:border-slate-800 dark:text-white w-full sm:w-52">
                        <select x-model="selectedMarket" @change="fetchTodayPrices()" class="bg-white border border-slate-200 text-xs rounded-xl p-2.5 text-slate-900 focus:outline-none dark:bg-slate-900 dark:border-slate-800 dark:text-white w-full sm:w-auto">
                            <option value="peliyagoda">Peliyagoda Manning Market</option>
                            <option value="dambulla">Dambulla Economic Centre</option>
                            <option value="kandy">Kandy Market</option>
                            <option value="meegoda">Meegoda Economic Centre</option>
                            <option value="norochchole">Norochchole Economic Centre</option>
                            <option value="thambuththegama">Thambuththegama Economic Centre</option>
                            <option value="keppetipola">Keppetipola Economic Centre</option>
                            <option value="nuwara-eliya">Nuwara-Eliya Market</option>
                            <option value="bandarawela">Bandarawela Market</option>
                            <option value="veyangoda">Veyangoda Economic Centre</option>
                        </select>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 shadow-xl rounded-2xl overflow-hidden dark:bg-slate-900 dark:border-slate-800">
                    <div class="overflow-x-auto">
                    <table class="w-full min-w-[480px] text-left border-collapse text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-950 font-mono text-xs text-slate-600 dark:text-slate-400 uppercase border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th class="p-3 sm:p-4">Vegetable</th>
                                <th class="p-3 sm:p-4">Current Price</th>
                                <th class="p-3 sm:p-4 hidden sm:table-cell">Yesterday's Price</th>
                                <th class="p-3 sm:p-4">Daily Change</th>
                            </tr>
                        </thead>
                        <tbody class="text-slate-700 dark:text-slate-300">
                            <template x-for="(marketData, vegId) in filteredPrices" :key="vegId">
                                <tr @click="if(marketData.slug) { window.location.href = '/' + marketData.slug }" 
            class="border-b border-slate-200 transition-all duration-300 dark:border-slate-800 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:shadow-sm hover:-translate-y-px">
            <td class="p-3 sm:p-4 font-semibold capitalize text-slate-900 dark:text-white" x-text="marketData.vegetable || vegId"></td>
            <td class="p-3 sm:p-4 font-mono font-bold text-emerald-400" x-text="'Rs. ' + (marketData.price_average || marketData.price)"></td>
            <td class="p-3 sm:p-4 font-mono text-slate-500 hidden sm:table-cell" x-text="'Rs. ' + marketData.priceYesterday"></td>
            <td class="p-3 sm:p-4 font-mono font-bold" :class="marketData.changePercent >= 0 ? 'text-emerald-500' : 'text-rose-500'" x-text="(marketData.changePercent >= 0 ? '+' : '') + marketData.changePercent + '%'"></td>
        </tr>
                            </template>
                        </tbody>
                    </table>
                    </div>
                </div>
            </section>
        </div>

        <div x-show="activeTab === 'trends'" x-transition style="{{ ($initialTab ?? 'home') !== 'trends' ? 'display: none;' : '' }}">
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Market Trends Analytics</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-1" x-text="'Active Chart Vector: ' + trendVeg.toUpperCase()"></p>
                    </div>
                    <select x-model="trendVeg" @change="fetchTrendHistory()" class="bg-white border border-slate-200 dark:bg-slate-900 dark:border-slate-800 text-sm rounded-xl p-2 text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-emerald-500 w-full sm:w-auto">
                        <template x-for="vegId in Object.keys(prices)" :key="vegId">
                            <option :value="vegId" x-text="(prices[vegId]?.vegetable || vegId).split(/[-_]/).map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')"></option>
                        </template>
                    </select>
                </div>
                
                <div class="bg-white border border-slate-200 dark:bg-slate-900 dark:border-slate-800 rounded-3xl p-6 shadow-xl transition-colors duration-500">
                    <div class="h-80 w-full relative">
                        <canvas id="laravelTrendChart"></canvas>
                    </div>
                </div>
            </section>
        </div>

        <div x-show="activeTab === 'heatmap'" x-transition style="{{ ($initialTab ?? 'home') !== 'heatmap' ? 'display: none;' : '' }}">
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
                <div class="max-w-md mx-auto bg-white border border-slate-200 dark:bg-slate-900 dark:border-slate-800 p-8 rounded-3xl transition-colors duration-500 shadow-xl">
                    <i data-lucide="map" class="w-12 h-12 text-emerald-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Geographical Distribution Map</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-xs mt-2 leading-relaxed">Visual production yields mapping across major agricultural production hubs (Highland vs Low Country Zones) is synchronizing.</p>
                </div>
            </section>
        </div>

        <div x-show="activeTab === 'about'" x-transition style="{{ ($initialTab ?? 'home') !== 'about' ? 'display: none;' : '' }}">
            <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-16 space-y-10 sm:space-y-16">

                {{-- Hero Banner --}}
                <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-800 p-6 sm:p-10 shadow-2xl shadow-emerald-900/30">
                    <div class="absolute top-0 right-0 w-80 h-80 bg-white/5 rounded-full blur-3xl -translate-y-20 translate-x-20"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-black/10 rounded-full blur-3xl translate-y-20 -translate-x-16"></div>
                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 border border-white/20 mb-6">
                            <i data-lucide="leaf" class="w-3.5 h-3.5 text-emerald-200"></i>
                            <span class="text-emerald-100 font-mono font-bold tracking-widest text-[10px] uppercase">Open Market Data Initiative · Sri Lanka</span>
                        </div>
                        <h2 class="text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight mb-4">
                            Lanka Produce Prices<br>
                            <span class="text-emerald-200">Ceylon Markets</span>
                        </h2>
                        <p class="text-emerald-100/80 text-sm leading-relaxed max-w-2xl">
                            A free, open-access platform that publishes daily wholesale vegetable price indices across Sri Lanka's major trade hubs — empowering farmers, vendors, and households to make informed buying decisions without relying on intermediaries.
                        </p>
                    </div>
                </div>

                {{-- Mission & Purpose --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                                <i data-lucide="target" class="w-5 h-5"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Our Mission</h3>
                        </div>
                        <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                            Sri Lanka's vegetable supply chain suffers from severe information asymmetry — farmers sell low, consumers buy high, and middlemen capture the margin. This platform eliminates that gap by publishing raw wholesale rates the moment they are released by official government bodies, giving everyone equal access to market intelligence.
                        </p>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-teal-500/10 flex items-center justify-center text-teal-500">
                                <i data-lucide="users" class="w-5 h-5"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Who Is This For?</h3>
                        </div>
                        <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                            <li class="flex items-start gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0"></i><span><strong class="text-slate-800 dark:text-slate-200">Households</strong> — compare retail vs wholesale rates before your weekly market visit.</span></li>
                            <li class="flex items-start gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0"></i><span><strong class="text-slate-800 dark:text-slate-200">Vendors & Retailers</strong> — benchmark your buying cost against national trade hubs.</span></li>
                            <li class="flex items-start gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0"></i><span><strong class="text-slate-800 dark:text-slate-200">Farmers</strong> — track demand trends and price peaks to plan harvest dispatch timing.</span></li>
                            <li class="flex items-start gap-2"><i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0"></i><span><strong class="text-slate-800 dark:text-slate-200">Researchers & Policy Makers</strong> — access historical datasets for economic analysis.</span></li>
                        </ul>
                    </div>
                </div>

                {{-- Data Sources --}}
                <div>
                    <div class="mb-6">
                        <p class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-widest mb-1">Verified Data Pipeline</p>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Where Does the Data Come From?</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500">
                                    <i data-lucide="file-text" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">HARTI — Hector Kobbekaduwa Agrarian Research and Training Institute</h4>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                HARTI publishes official daily market price bulletins as PDF documents covering all major economic centres including Peliyagoda, Dambulla, Kandy, Meegoda, and Nuwara-Eliya. Our automated pipeline fetches, parses, and indexes these PDFs every day as soon as they are released.
                            </p>
                            <a href="https://www.harti.gov.lk" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-amber-500 hover:underline">
                                Visit HARTI Website <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                        </div>
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-500">
                                    <i data-lucide="landmark" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-sm">CBSL — Central Bank of Sri Lanka</h4>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                The Central Bank of Sri Lanka publishes supplementary consumer price and food inflation indices. These are cross-referenced to validate HARTI market data and provide broader macroeconomic context for observed price movements.
                            </p>
                            <a href="https://www.cbsl.gov.lk" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-blue-500 hover:underline">
                                Visit CBSL Website <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Markets Covered --}}
                <div>
                    <div class="mb-6">
                        <p class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-widest mb-1">Geographic Coverage</p>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Markets Covered</h3>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                        @foreach([
                            ['Peliyagoda Manning Market', 'Western Province', 'amber'],
                            ['Dambulla Economic Centre', 'North Central Province', 'emerald'],
                            ['Kandy Market', 'Central Province', 'violet'],
                            ['Meegoda Economic Centre', 'Western Province', 'cyan'],
                            ['Norochchole Economic Centre', 'North Western Province', 'rose'],
                            ['Thambuththegama', 'North Central Province', 'orange'],
                            ['Keppetipola', 'Uva Province', 'teal'],
                            ['Nuwara-Eliya Market', 'Central Highlands', 'sky'],
                            ['Bandarawela Market', 'Uva Province', 'indigo'],
                            ['Veyangoda Economic Centre', 'Gampaha District', 'lime'],
                        ] as [$market, $region, $color])
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-3 flex flex-col gap-1">
                            <span class="text-[10px] font-mono font-bold text-{{ $color }}-400 uppercase">{{ $region }}</span>
                            <span class="text-xs font-semibold text-slate-800 dark:text-slate-200 leading-snug">{{ $market }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Key Features --}}
                <div>
                    <div class="mb-6">
                        <p class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-widest mb-1">Platform Capabilities</p>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Key Features</h3>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach([
                            ['trending-up', 'emerald', 'Daily Price Feed', 'Fresh wholesale prices updated every day from official HARTI PDF bulletins — no manual entry, fully automated.'],
                            ['bar-chart-2', 'violet', 'Trend Analysis', 'Interactive 7, 30, and 90-day historical price charts for every vegetable, highlighting seasonal cycles and price peaks.'],
                            ['git-compare', 'amber', 'Market Comparison', 'Compare prices across Peliyagoda, Dambulla, Kandy, and all other major hubs side-by-side in real time.'],
                            ['shield-check', 'teal', 'Verified Sources', 'All data originates from official government publications — HARTI & CBSL — with direct links to source PDFs.'],
                            ['map-pin', 'rose', 'Geo Distribution', 'Understanding which regions supply which vegetables, from up-country cold crops to low-country dry zone produce.'],
                            ['moon', 'blue', 'Dark Mode & Trilingual UI', 'Full dark mode support. Interface available in English, Sinhala (සිංහල), and Tamil (தமிழ்).'],
                        ] as [$icon, $color, $title, $desc])
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 space-y-2.5">
                            <div class="w-9 h-9 rounded-xl bg-{{ $color }}-500/10 flex items-center justify-center text-{{ $color }}-500">
                                <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                            </div>
                            <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $title }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ $desc }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- How the Pipeline Works --}}
                <div>
                    <div class="mb-6">
                        <p class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-widest mb-1">Under the Hood</p>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">How the Data Pipeline Works</h3>
                    </div>
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden">
                        @foreach([
                            ['1', 'Download', 'file-down', 'emerald', 'An automated scraper checks the HARTI and CBSL websites daily for new PDF bulletins. When a new file is detected, it is downloaded immediately.'],
                            ['2', 'Parse & Extract', 'cpu', 'amber', 'The PDF is processed using an intelligent text extraction engine that identifies vegetable names, market locations, and price columns — normalising variations in naming and format.'],
                            ['3', 'Normalise & Store', 'database', 'violet', 'Extracted records are matched against a canonical vegetable index (handling Sinhala, Tamil, and English names) and stored in the database with full audit timestamps.'],
                            ['4', 'Serve via API', 'zap', 'teal', 'Clean REST API endpoints expose today\'s prices, historical trends, and per-market breakdowns. The dashboard consumes these endpoints in real time via Alpine.js.'],
                        ] as [$step, $label, $icon, $color, $desc])
                        <div class="flex items-start gap-5 p-5 @if(!$loop->last) border-b border-slate-100 dark:border-slate-800 @endif">
                            <div class="flex-shrink-0 w-9 h-9 rounded-xl bg-{{ $color }}-500/10 flex items-center justify-center text-{{ $color }}-500">
                                <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-mono font-bold text-{{ $color }}-400 bg-{{ $color }}-400/10 px-1.5 py-0.5 rounded uppercase">Step {{ $step }}</span>
                                    <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $label }}</span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ $desc }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Quick Links --}}
                <div class="bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white mb-1">Ready to explore the data?</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Start with today's live market prices or dive into historical trends.</p>
                    </div>
                    <div class="flex flex-wrap gap-3 shrink-0">
                        <button @click="activeTab = 'rates'"
                            class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-lg shadow-emerald-500/20">
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i> View Live Prices
                        </button>
                        <button @click="activeTab = 'trends'"
                            class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-xl transition hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                            <i data-lucide="trending-up" class="w-3.5 h-3.5"></i> Explore Trends
                        </button>
                    </div>
                </div>

            </section>
        </div>

        <div x-show="activeTab === 'pipeline'" x-transition style="{{ ($initialTab ?? 'home') !== 'pipeline' ? 'display: none;' : '' }}">
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-slate-900">
                <div class="bg-white border border-slate-200 shadow-sm rounded-3xl p-6 max-w-xl mx-auto">
                    <h2 class="text-lg font-bold mb-4 flex items-center gap-2 text-slate-800"><i data-lucide="shield" class="text-emerald-600"></i> HARTI Scraper Control Engine</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-500 mb-6 font-mono">Trigger manual updates directly bypassing background schedulers.</p>
                    
                    <div class="space-y-4">
                        <button @click="triggerScraperPipeline()" :disabled="scraping" class="w-full bg-slate-900 text-white font-mono p-3 rounded-xl hover:bg-slate-800 disabled:opacity-50 transition">
                            <span x-text="scraping ? 'Processing Scraping Job...' : 'Force Trigger CBSL PDF Pipeline Scrape'"></span>
                        </button>
                    </div>
                </div>
            </section>
        </div>

    </main>

    <footer class="w-full border-t border-slate-200 bg-slate-50 dark:border-t dark:border-slate-900 dark:bg-slate-950 mt-auto py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center gap-4 text-xs font-mono text-slate-500 text-center md:flex-row md:justify-between md:text-left">
            <div class="text-slate-700 dark:text-slate-300">
                <span class="text-slate-900 dark:text-slate-300 font-bold">Lanka Vegetable Prices</span><br class="sm:hidden"> <span class="hidden sm:inline">|</span> Empowering Sri Lankan households with pristine market rate visibility.
            </div>
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1">
                <span class="hover:text-emerald-400 cursor-pointer transition-colors" @click="activeTab = 'pipeline'">Admin Portal</span>
                <span class="hidden sm:inline">|</span>
                <span>© 2026 Ceylon Agriculture Hub</span>
                <span class="hidden sm:inline">|</span>
                <span class="text-emerald-400 cursor-pointer hover:text-emerald-300 transition-colors" @click="activeTab = 'rates'">Live Rates</span>
            </div>
        </div>
    </footer>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('Dashboard', () => ({
            activeTab: '{{ $initialTab ?? "home" }}',
            scrolled: false,
            logoClickCount: 0,
            selectedMarket: 'peliyagoda',
            searchQuery: '',
            prices: {},
            loading: false,
            scraping: false,
            trendVeg: 'carrot',
            pdfDate: '',
            pdfUrl: '',
            dataSource: 'HARTI Official Data Verified',
            chartInstance: null,

            navItems: [
                { id: 'home', label: 'Home Feed' },
                { id: 'rates', label: 'Market Prices' },
                { id: 'trends', label: 'Market Trends' },
                { id: 'heatmap', label: 'Heatmap View' },
                { id: 'about', label: 'About App' }
            ],

            vegetableMeta: {
                tomato: { name: 'Tomato', icon: '🍅' },
                carrot: { name: 'Carrot', icon: '🥕' },
                cabbage: { name: 'Cabbage', icon: '🥬' },
                pumpkin: { name: 'Pumpkin', icon: '🎃' }
            },
            popularVegetables: ['tomato', 'carrot', 'cabbage', 'pumpkin'],

            getVegLocalName(id) {
                const names = { carrot: { si: 'කැරට්', en: 'Carrot' }, tomato: { si: 'තක්කාලි', en: 'Tomato' } };
                return (names[id] && names[id][this.lang]) ? names[id][this.lang] : id.charAt(0).toUpperCase() + id.slice(1);
            },

            init() {
                window.addEventListener('scroll', () => {
                    this.scrolled = window.scrollY > 30;
                });

                this.fetchTodayPrices();

                // If landing directly on trends tab, load the chart immediately
                if (this.activeTab === 'trends') {
                    this.$nextTick(() => this.fetchTrendHistory());
                }

                this.$watch('activeTab', (value) => {
                    setTimeout(() => { if(window.lucide) { lucide.createIcons(); } }, 60);
                    if (value === 'trends') {
                        this.fetchTrendHistory();
                    }
                    
                    const paths = {
                        'home': '/',
                        'rates': '/prices',
                        'trends': '/trends',
                        'heatmap': '/heatmap',
                        'about': '/about',
                        'pipeline': '/pipeline'
                    };
                    if (paths[value] && window.location.pathname !== paths[value]) {
                        window.history.pushState({tab: value}, '', paths[value]);
                    }
                });

                window.addEventListener('popstate', (event) => {
                    if (event.state && event.state.tab) {
                        this.activeTab = event.state.tab;
                    } else {
                        const path = window.location.pathname;
                        if (path === '/prices') this.activeTab = 'rates';
                        else if (path === '/trends') this.activeTab = 'trends';
                        else if (path === '/heatmap') this.activeTab = 'heatmap';
                        else if (path === '/about') this.activeTab = 'about';
                        else if (path === '/pipeline') this.activeTab = 'pipeline';
                        else this.activeTab = 'home';
                    }
                });

                // Refresh chart colors when theme changes
                window.addEventListener('theme-changed', () => {
                    if (this.activeTab === 'trends') {
                        this.$nextTick(() => this.fetchTrendHistory());
                    }
                });
            },

            get filteredPrices() {
    if (!this.prices || typeof this.prices !== 'object') {
        return {};
    }

    if (!this.searchQuery?.trim()) {
        return this.prices;
    }

    let query = this.searchQuery.toLowerCase();

    return Object.fromEntries(
        Object.entries(this.prices).filter(([key]) =>
            key.toLowerCase().includes(query)
        )
    );
},

            get heroNationalAvg() {
    const entries = Object.values(this.prices || {});
    if (!entries.length) return null;
    const sum = entries.reduce((acc, v) => acc + parseFloat(v.price_average ?? v.price ?? 0), 0);
    return (sum / entries.length).toFixed(2);
},

            fetchTodayPrices() {
                this.loading = true;
                fetch(`/api/prices/today?marketId=${this.selectedMarket}`)
                    .then(res => {
                        if (!res.ok) throw new Error('Network response was not ok');
                        return res.json();
                    })
                    .then(data => {
                        this.prices = data?.prices || {};
                        this.pdfDate = data?.scrapedPdfDate || '';
                        this.pdfUrl = data?.scrapedPdfUrl || '';
                        this.dataSource = (this.pdfUrl && this.pdfUrl.toLowerCase().includes('cbsl')) ? 'CBSL Official Data Verified' : 'HARTI Official Data Verified';
                        this.loading = false;
                        // Default trendVeg to first available vegetable if current selection not in prices
                        const vegKeys = Object.keys(this.prices);
                        if (vegKeys.length && !this.prices[this.trendVeg]) {
                            this.trendVeg = vegKeys[0];
                        }
                        this.$nextTick(() => { if(window.lucide) { lucide.createIcons(); } });
                    })
                    .catch(err => {
                        console.error("Error fetching prices", err);
                        this.prices = {};
                        this.loading = false;
                    });
            },

            viewTrendFor(vegId) {
                this.trendVeg = vegId;
                this.activeTab = 'trends';
            },

            fetchTrendHistory() {
                this.$nextTick(() => {
                    fetch(`/api/prices/history?vegetableId=${this.trendVeg}&marketId=${this.selectedMarket}&days=30`)
                        .then(res => {
                            if (!res.ok) throw new Error('Network response was not ok');
                            return res.json();
                        })
                        .then(data => {
                            const canvas = document.getElementById('laravelTrendChart');
                            if (!canvas) return;
                            
                            const ctx = canvas.getContext('2d');
                            const history = Array.isArray(data?.history) ? data.history : [];

                            if (this.chartInstance) {
                                this.chartInstance.destroy();
                                this.chartInstance = null;
                            }
                            
                            if (history.length === 0) return;

                            const labels = history.map(h => h.date);
                            const datasetData = history.map(h => h.price);

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
                                        x: { grid: { display: false }, ticks: { color: Alpine.store('theme').darkMode ? '#94a3b8' : '#64748b', font: { size: 10 } } },
                                        y: { grid: { color: Alpine.store('theme').darkMode ? '#1e293b' : '#e2e8f0' }, ticks: { color: Alpine.store('theme').darkMode ? '#64748b' : '#475569', font: { size: 10 } } }
                                    }
                                }
                            });
                        })
                        .catch(err => {
                            console.error("Error fetching trend history:", err);
                            if (this.chartInstance) {
                                this.chartInstance.destroy();
                                this.chartInstance = null;
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