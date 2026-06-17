<!-- ================= BASKET ESTIMATE PANEL COMPONENT ================= -->
<div x-data="basketPanelComponent()" 
     @theme-changed.window="console.log('BasketPanel received theme-changed event. New darkMode:', $event.detail); $nextTick(() => { if(window.lucide) { lucide.createIcons(); } })"
     class="bg-white border border-slate-100 rounded-3xl p-6 md:p-8 shadow-sm transition-all duration-300 w-full max-w-2xl mx-auto text-slate-800">
    
    <!-- 1. PANEL HEADER -->
    <div class="flex items-center justify-between mb-6 border-b border-slate-50 pb-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 shadow-inner">
                <i data-lucide="shopping-bag" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-800 tracking-tight font-display text-base" 
                    x-text="translations[lang].basketOverview"></h3>
                <p class="text-xs text-slate-500 font-mono mt-0.5">
                    <span x-text="translations[lang].today"></span> 
                    <span class="text-slate-400">@</span> 
                    <span class="font-bold text-emerald-600" x-text="getMarketName(currentMarket.id)"></span>
                </p>
            </div>
        </div>
        
        <!-- Language Switcher & Quick Controls -->
        <div class="flex items-center gap-2">
            <!-- Theme Toggle -->
            <button @click="$store.theme.toggle()" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
                <i :data-lucide="$store.theme.darkMode ? 'sun' : 'moon'" class="w-4 h-4"></i>
            </button>

            <!-- Language Toggle Buttons -->
            <div class="flex bg-slate-100 dark:bg-slate-800 p-0.5 rounded-lg border border-slate-200/50 dark:border-slate-700/50 mr-1 text-[10px] font-mono font-bold">
                <button @click="lang = 'en'" :class="lang === 'en' ? 'bg-white text-slate-900 shadow-2xs dark:bg-slate-900 dark:text-white' : 'text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200'" class="px-1.5 py-0.5 rounded transition">EN</button>
                <button @click="lang = 'si'" :class="lang === 'si' ? 'bg-white text-slate-900 shadow-2xs dark:bg-slate-900 dark:text-white' : 'text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200'" class="px-1.5 py-0.5 rounded transition">සිං</button>
                <button @click="lang = 'ta'" :class="lang === 'ta' ? 'bg-white text-slate-900 shadow-2xs dark:bg-slate-900 dark:text-white' : 'text-slate-400 hover:text-slate-600 dark:text-slate-400 dark:hover:text-slate-200'" class="px-1.5 py-0.5 rounded transition">தமிழ்</button>
            </div>

            <button type="button" @click="addAll()"
                    class="text-[10px] text-emerald-700 hover:text-emerald-950 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-lg transition-all font-mono font-bold uppercase tracking-wider">
                <span x-text="translations[lang].basketAddAll"></span>
            </button>
            
            <template x-if="basket.length > 0">
                <button type="button" @click="clearBasket()"
                        class="text-xs text-slate-400 hover:text-rose-500 transition-colors flex items-center gap-1.5 font-mono">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> 
                    <span x-text="translations[lang].basketClearAll"></span>
                </button>
            </template>
        </div>
    </div>

    <!-- 2. EMPTY STATE -->
    <template x-if="basket.length === 0">
        <div class="space-y-6">
            <div class="py-10 text-center text-slate-400 flex flex-col items-center justify-center border border-dashed border-slate-200 rounded-2xl bg-slate-50/40">
                <div class="w-12 h-12 rounded-full border-2 border-dashed border-slate-200 flex items-center justify-center mb-3 text-slate-300 bg-white">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                </div>
                <p class="text-sm font-semibold text-slate-700 font-display" x-text="translations[lang].basketEmpty"></p>
                <p class="text-xs text-slate-400 mt-1 max-w-[240px] leading-relaxed font-sans" x-text="translations[lang].basketEmptyDesc"></p>
            </div>

            <!-- Value Pack Feature Box -->
            <div class="bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl p-4 flex flex-col justify-between">
                <div class="flex items-center justify-between text-[11px] font-mono uppercase text-slate-500 tracking-wider">
                    <span x-text="translations[lang].basketValuePack"></span>
                    <span class="text-emerald-600 font-bold">
                        <span x-text="vegetables.length"></span> <span x-text="lang === 'si' ? 'වර්ග • කිලෝ 1 බැගින්' : (lang === 'ta' ? 'உருப்படிகள் • தலா 1kg' : 'Items • 1kg each')"></span>
                    </span>
                </div>
                <div class="flex items-baseline justify-between mt-3 border-b border-slate-200/60 pb-3">
                    <span class="text-xs font-semibold text-slate-700 font-display" x-text="translations[lang].basketCombinedTotal"></span>
                    <span class="text-lg font-bold font-mono text-slate-900">
                        Rs. <span x-text="allProduceTotal.toLocaleString()"></span>
                    </span>
                </div>
                <p class="text-[10px] text-slate-400 dark:text-slate-400 mt-2.5 font-sans leading-relaxed" x-text="translations[lang].basketUnifiedCostDesc"></p>
                <button type="button" @click="addAll()"
                        class="mt-4 w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-mono font-bold text-xs uppercase tracking-wider rounded-xl transition flex items-center justify-center gap-1.5 shadow-xs">
                    <span x-text="translations[lang].basketAddEverythingBtn"></span>
                </button>
            </div>
        </div>
    </template>

    <!-- 3. ACTIVE BASKET CONTENT CONTENT HOLDER -->
    <template x-if="basket.length > 0">
        <div class="space-y-6">
            
            <!-- Items Rows Container -->
            <div class="space-y-3 max-h-[340px] overflow-y-auto pr-1">
                <template x-for="item in itemsWithPrices" :key="item.veg.id">
                    <div class="flex items-center justify-between gap-4 p-3 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100/80 dark:hover:bg-slate-700/80 rounded-xl transition-all border border-transparent hover:border-slate-100 dark:hover:border-slate-700">
                        
                        <!-- Commodity Metadata -->
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-xl shadow-2xs shrink-0 select-none border border-slate-100">
                                <span x-text="item.veg.emoji"></span>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800" x-text="getVegLocalName(item.veg)"></h4>
                                <p class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    Rs. <span x-text="item.unitPrice"></span> / kg
                                </p>
                            </div>
                        </div>

                        <!-- Quantity Selector and Action Cost Panel -->
                        <div class="flex items-center gap-4">
                            <!-- Dial Interval Selector -->
                            <div class="flex items-center border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 rounded-lg p-1 shadow-2xs h-8">
                                <button type="button" @click="decreaseQty(item.veg.id)"
                                        class="w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:bg-slate-50 font-bold text-sm transition">
                                    -
                                </button>
                                <span class="w-14 text-center text-xs font-mono font-bold text-slate-700" 
                                      x-text="item.quantity.toFixed(2) + ' kg'"></span>
                                <button type="button" @click="increaseQty(item.veg.id)"
                                        class="w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:bg-slate-50 font-bold text-sm transition">
                                    +
                                </button>
                            </div>

                            <!-- Cost Calculation Column -->
                            <div class="text-right w-24">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100 font-mono">
                                    Rs. <span x-text="Math.round(item.total).toLocaleString()"></span>
                                </p>
                                <button type="button" @click="removeItem(item.veg.id)"
                                        class="text-[10px] text-slate-400 hover:text-rose-500 transition-colors mt-0.5"
                                        x-text="lang === 'si' ? 'ඉවත් කරන්න' : (lang === 'ta' ? 'அகற்று' : 'Remove')">
                                </button>
                            </div>
                        </div>

                    </div>
                </template>
            </div>

            <!-- 4. ESTIMATED BALANCE TOTAL CARD -->
            <div class="bg-emerald-600 text-white rounded-2xl p-5 shadow-lg relative overflow-hidden dark:bg-slate-900 dark:text-white">
                <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-emerald-500/20 blur-xl pointer-events-none dark:bg-emerald-500/10"></div>
                <div class="absolute left-1/3 top-0 w-24 h-24 rounded-full bg-emerald-700/60 blur-lg pointer-events-none dark:bg-slate-800/60"></div>

                <div class="relative flex justify-between items-baseline mb-4">
                    <span class="text-xs font-mono tracking-wider uppercase text-emerald-100 dark:text-slate-400" x-text="translations[lang].basketEstimatedTotal"></span>
                    <div class="text-right">
                        <span class="text-3xl font-black tracking-tight text-white dark:text-emerald-400 font-mono">
                            Rs. <span x-text="Math.round(basketTotal).toLocaleString()"></span>
                        </span>
                        <p class="text-[10px] text-emerald-100 dark:text-slate-400 mt-1 font-mono">
                            <span x-text="lang === 'si' ? 'මුළු බර: ' : (lang === 'ta' ? 'மொத்த எடை: ' : 'For ')"></span>
                            <span class="font-bold text-white" x-text="totalWeight.toFixed(1)"></span>kg 
                            <span x-text="lang === 'si' ? ' එළවළු සඳහා' : (lang === 'ta' ? ' விளைபொருட்கள்' : ' of fresh produce')"></span>
                        </p>
                    </div>
                </div>

                <!-- Footer Benchmark Disclaimer Checkbox -->
                <div class="border-t border-emerald-700 pt-3 flex items-center gap-2.5 text-xs text-emerald-100 dark:border-slate-800 dark:text-slate-400">
                    <i data-lucide="scale" class="w-3.5 h-3.5 text-emerald-400 shrink-0"></i>
                    <span class="font-sans text-[11px] text-slate-300">
                        <template x-if="lang === 'si'">
                            <span>මෙම ගණනය කිරීම් වර්තමාන <strong class="text-emerald-400" x-text="getMarketType(currentMarket.type)"></strong> මිල පදනම් කර ගෙන ඇත.</span>
                        </template>
                        <template x-if="lang === 'ta'">
                            <span>கணக்கீடு தற்போதைய <strong class="text-emerald-400" x-text="getMarketType(currentMarket.type)"></strong> விலை குறியීடுகளின் அடிப்படையில் செய்யப்பட்டுள்ளது.</span>
                        </template>
                        <template x-if="lang === 'en'">
                            <span>Current calculation based on official <strong class="text-emerald-400" x-text="getMarketType(currentMarket.type)"></strong> price benchmarks.</span>
                        </template>
                    </span>
                </div>
            </div>

            <!-- 5. DYNAMIC MULTI-HUB ANALYTICS ANALYSIS -->
            <div class="space-y-3">
                <h4 class="text-xs font-mono font-bold text-slate-400 uppercase tracking-widest" 
                    x-text="translations[lang].basketComparisonTitle"></h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    <template x-for="comparison in marketComparisons" :key="comparison.marketId">
                        <div class="flex flex-col justify-between p-3 rounded-xl border text-xs transition-all bg-white dark:bg-slate-800 dark:border-slate-700"
                             :class="comparison.marketId === currentMarket.id ? 'border-emerald-500 bg-emerald-50/10 ring-1 ring-emerald-500/20 dark:bg-emerald-900/20' : 'border-slate-100 hover:border-slate-200 shadow-3xs dark:hover:border-slate-600'">
                            
                            <div class="mb-2">
                                <p class="font-bold text-slate-800" x-text="getMarketName(comparison.marketId)"></p>
                                <p class="text-[10px] text-slate-400 font-mono" x-text="getMarketType(comparison.marketType)"></p>
                            </div>

                            <div class="flex items-baseline justify-between mt-auto pt-2 border-t border-slate-50">
                                <span class="font-bold font-mono text-slate-800">
                                    Rs. <span x-text="Math.round(comparison.total).toLocaleString()"></span>
                                </span>
                                
                                <!-- Savings State Logic Matrix Tags -->
                                <div>
                                    <template x-if="comparison.marketId === currentMarket.id">
                                        <span class="text-[9px] text-emerald-600 bg-emerald-50 border border-emerald-200/50 px-1.5 py-0.5 rounded font-mono font-bold"
                                              x-text="lang === 'si' ? 'සක්‍රියයි' : (lang === 'ta' ? 'செயலில்' : 'Active')"></span>
                                    </template>
                                    <template x-if="comparison.marketId !== currentMarket.id && comparison.saving > 0">
                                        <span class="text-[9px] text-emerald-600 bg-emerald-50 border border-emerald-100 px-1.5 py-0.5 rounded font-mono font-bold flex items-center gap-0.5">
                                            <i data-lucide="trending-down" class="w-2.5 h-2.5"></i>
                                            <span x-text="lang === 'si' ? 'ඉතිරි: ' : (lang === 'ta' ? 'சேமிப்பு: ' : 'Save ')"></span>Rs.<span x-text="Math.round(comparison.saving)"></span>
                                        </span>
                                    </template>
                                    <template x-if="comparison.marketId !== currentMarket.id && comparison.saving <= 0">
                                        <span class="text-[9px] text-slate-400 font-mono font-medium" 
                                              x-text="'+' + Math.round(Math.abs(comparison.saving)) + ' LKR'"></span>
                                    </template>
                                </div>
                            </div>

                        </div>
                    </template>
                </div>
            </div>

        </div>
    </template>
</div>

<!-- ================= ALPINE COMPONENT INITIALIZATION CORE ENGINE ================= -->
<script>
function basketPanelComponent() {
    return {
        lang: 'en',
        currentMarket: { id: 'pettah', type: 'Retail' },
        
        // Active Basket: references master list IDs
        basket: [
            { vegetableId: '1', quantity: 1.00 }
        ],

        // Master Dataset including prices across 3 primary Sri Lankan Economic Hub Centers
        vegetables: [
            { id: '1', name: 'Carrot', localNameSi: 'කැරට්', localNameTa: 'கேரட்', emoji: '🥕', prices: { pettah: 180, dambulla: 150, narahenpita: 170 } },
            { id: '2', name: 'Beans', localNameSi: 'බෝංචි', localNameTa: 'பீன்ஸ்', emoji: '🫘', prices: { pettah: 240, dambulla: 195, narahenpita: 220 } },
            { id: '3', name: 'Tomato', localNameSi: 'තක්කාලි', localNameTa: 'தக்காளி', emoji: '🍅', prices: { pettah: 220, dambulla: 180, narahenpita: 240 } },
            { id: '4', name: 'Leeks', localNameSi: 'ලීක්ස්', localNameTa: 'லீக்ஸ்', emoji: '🥬', prices: { pettah: 300, dambulla: 260, narahenpita: 310 } }
        ],

        // Available Markets Framework Metadata
        markets: [
            { id: 'pettah', nameEn: 'Pettah Market', nameSi: 'පිටකොටුව මැනිං වෙළඳපොළ', nameTa: 'பெட்டா சந்தை', type: 'Retail' },
            { id: 'dambulla', nameEn: 'Dambulla Eco Center', nameSi: 'දඹුල්ල ආර්ථික මධ්‍යස්ථානය', nameTa: 'தம்புள்ளை பொருளாதார மையம்', type: 'Wholesale' },
            { id: 'narahenpita', nameEn: 'Narahenpita Center', nameSi: 'නාරාහේන්පිට ආර්ථික මධ්‍යස්ථානය', nameTa: 'நாரஹேன்பிட்டா மையம்', type: 'Retail' }
        ],

        // Unified International Translation Dictionaries
        translations: {
            en: {
                basketOverview: "Basket Estimate Mode",
                today: "Today at",
                basketAddAll: "Add All",
                basketClearAll: "Clear",
                basketEmpty: "Your basket is empty",
                basketEmptyDesc: "Add vegetables to compare prices across national markets.",
                basketValuePack: "Value Pack Strategy",
                basketCombinedTotal: "Combined Total Index",
                basketUnifiedCostDesc: "Aggregated price calculation tracking 1 kilogram index for each agricultural produce variation simultaneously.",
                basketAddEverythingBtn: "Add Everything to Basket",
                basketEstimatedTotal: "Estimated Market Total",
                basketComparisonTitle: "Cross-Market Comparison Analytics",
                dambullaName: "Dambulla", pettahName: "Pettah", narahenpitaName: "Narahenpita",
                typeWholesale: "Wholesale Center", typeRetail: "Retail Center", typeEconCenter: "Economic Center"
            },
            si: {
                basketOverview: "මිල ඇස්තමේන්තු කූඩය",
                today: "අද දින මිල -",
                basketAddAll: "සියල්ල එක් කරන්න",
                basketClearAll: "හිස් කරන්න",
                basketEmpty: "ඔබේ මිල ඇස්තමේන්තු කූඩය හිස්ය",
                basketEmptyDesc: "දේශීය වෙළඳපොළවල් අතර මිල සැසඳීමට එළවළු එකතු කරන්න.",
                basketValuePack: "පොදු වටිනාකම් ඇසුරුම",
                basketCombinedTotal: "ඒකාබද්ධ මුළු එකතුව",
                basketUnifiedCostDesc: "ලැයිස්තුවේ ඇති සියලුම එළවළු වර්ග වල කිලෝග්‍රෑම් 1 බැගින් වන මුළු එකතුව මෙහි දැක්වේ.",
                basketAddEverythingBtn: "සියලුම වර්ග කූඩයට එක් කරන්න",
                basketEstimatedTotal: "ඇස්තමේන්තුගත මුළු එකතුව",
                basketComparisonTitle: "වෙළඳපොළවල් අතර මිල සැසඳීම",
                dambullaName: "දඹුල්ල", pettahName: "පිටකොටුව", narahenpitaName: "නාරාහේන්පිට",
                typeWholesale: "තොග වෙළඳපොළ", typeRetail: "සිල්ලර වෙළඳපොළ", typeEconCenter: "ආර්ථික මධ්‍යස්ථානය"
            },
            ta: {
                basketOverview: "கூடை மதிப்பீட்டு முறை",
                today: "இன்று வெக்டரில்",
                basketAddAll: "அனைத்தையும் சேர்க்க",
                basketClearAll: "நீக்கு",
                basketEmpty: "உங்கள் கூடை காலியாக உள்ளது",
                basketEmptyDesc: "சந்தைகளில் விலைகளை ஒப்பிட காய்கறிகளைச் சேர்க்கவும்.",
                basketValuePack: "மதிப்பு பேக்",
                basketCombinedTotal: "கூட்டு மொத்த குறியீடு",
                basketUnifiedCostDesc: "ஒவ்வொரு விவசாய விளைபொருட்களின் மாறுபாட்டிற்கும் ஒரே நேரத்தில் 1 கிலோகிராம் குறியீட்டைக் கண்காணிக்கும் மொத்த விலை கணக்கீடு.",
                basketAddEverythingBtn: "அனைத்தையும் கூடையில் சேர்க்கவும்",
                basketEstimatedTotal: "மதிப்பிடப்பட்ட சந்தை மொத்தம்",
                basketComparisonTitle: "சந்தைகளுக்கு இடையிலான ஒப்பீட்டு பகுப்பாய்வு",
                dambullaName: "தம்புள்ளை", pettahName: "பெட்டா", narahenpitaName: "நாரஹேன்பிட்டா",
                typeWholesale: "மொத்த விற்பனை மையம்", typeRetail: "சில்லறை விற்பனை மையம்", typeEconCenter: "பொருளாதார மையம்"
            }
        },

        init() {
            // Hot reload lucide element vectors dynamically on init wrapper
            this.$nextTick(() => { if(window.lucide) { lucide.createIcons(); } });
        },

        // --- COMPUTED ACCESSORS ---
        get itemsWithPrices() {
            return this.basket.map(item => {
                const veg = this.vegetables.find(v => v.id === item.vegetableId);
                if (!veg) return null;
                const unitPrice = veg.prices[this.currentMarket.id] || 0;
                return {
                    veg: veg,
                    quantity: item.quantity,
                    unitPrice: unitPrice,
                    total: unitPrice * item.quantity
                };
            }).filter(x => x !== null);
        },

        get basketTotal() {
            return this.itemsWithPrices.reduce((sum, item) => sum + item.total, 0);
        },

        get totalWeight() {
            return this.basket.reduce((sum, item) => sum + item.quantity, 0);
        },

        get allProduceTotal() {
            return this.vegetables.reduce((sum, veg) => sum + (veg.prices[this.currentMarket.id] || 0), 0);
        },

        get marketComparisons() {
            return this.markets.map(m => {
                const totalInMarket = this.itemsWithPrices.reduce((sum, item) => {
                    const priceInM = item.veg.prices[m.id] || 0;
                    return sum + (priceInM * item.quantity);
                }, 0);

                return {
                    marketId: m.id,
                    marketType: m.type,
                    total: totalInMarket,
                    saving: this.basketTotal - totalInMarket
                };
            });
        },

        // --- CORE METHODS ---
        getMarketName(id) {
            const m = this.markets.find(x => x.id === id);
            if(!m) return id;
            return this.lang === 'si' ? m.nameSi : (this.lang === 'ta' ? m.nameTa : m.nameEn);
        },

        getMarketType(type) {
            return this.translations[this.lang]['type' + type] || type;
        },

        getVegLocalName(veg) {
            if (this.lang === 'si' && veg.localNameSi) return veg.localNameSi;
            if (this.lang === 'ta' && veg.localNameTa) return veg.localNameTa;
            return veg.name;
        },

        increaseQty(id) {
            let item = this.basket.find(i => i.vegetableId === id);
            if(item) {
                item.quantity += 0.25;
            }
        },

        decreaseQty(id) {
            let item = this.basket.find(i => i.vegetableId === id);
            if(item && item.quantity > 0.25) {
                item.quantity -= 0.25;
            }
        },

        removeItem(id) {
            this.basket = this.basket.filter(i => i.vegetableId !== id);
        },

        clearBasket() {
            this.basket = [];
        },

        addAll() {
            this.basket = this.vegetables.map(v => ({
                vegetableId: v.id,
                quantity: 1.00
            }));
            this.$nextTick(() => { if(window.lucide) { lucide.createIcons(); } });
        }
    };
}
</script>