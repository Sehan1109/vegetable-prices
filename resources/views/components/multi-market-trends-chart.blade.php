<div x-data="MultiMarketChart({ vegetableId: '{{ $vegetableId ?? 'carrot' }}', days: {{ $days ?? 30 }} })" class="w-full relative select-none" id="multi-market-chart-component-root">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 pb-4 border-b border-slate-100" id="multi-market-insights-header">
        <div>
            <span class="text-[10px] font-mono font-bold tracking-wider text-slate-400 uppercase">
                <span x-text="hoverStats ? 'Selected Date' : 'Overall Multi-Market Stats'"></span>
            </span>
            <div class="text-sm font-bold text-slate-800 mt-1.5 font-display flex items-center gap-1.5">
                <template x-if="hoverStats">
                    <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-xs font-mono" x-text="formatDateLabel(hoverStats.date, 'full')"></span>
                </template>
                <template x-if="!hoverStats">
                    <span>Avg Spread Across Centers</span>
                </template>
            </div>
        </div>

        <div>
            <span class="text-[10px] font-mono font-bold tracking-wider text-slate-400 uppercase">
                <span x-text="hoverStats ? 'Cheapest Market Value' : 'Max Price Spread'"></span>
            </span>
            <div class="mt-1 flex items-baseline gap-1.5 text-slate-900">
                <template x-if="hoverStats && hoverStats.cheapest">
                    <div class="flex items-center gap-2">
                        <span class="text-lg font-bold font-mono text-emerald-600" x-text="'Rs. ' + hoverStats.cheapest.price"></span>
                        <span class="text-[10px] tracking-tight text-slate-500 font-medium truncate max-w-[130px]" x-text="'at ' + hoverStats.cheapest.market.name.split(' Econ')[0].split(' Manning')[0]"></span>
                    </div>
                </template>
                <template x-if="!hoverStats">
                    <span class="text-base font-bold font-mono text-slate-700" x-text="'Rs. ' + maxSpread + ' / kg'"></span>
                </template>
            </div>
        </div>

        <div class="text-left md:text-right">
            <span class="text-[10px] font-mono font-bold tracking-wider text-slate-400 uppercase">
                <span x-text="hoverStats ? 'Pricing Spread (Gap)' : 'Pricing Dynamics'"></span>
            </span>
            <p class="text-sm font-semibold text-slate-700 mt-1 font-display">
                <template x-if="hoverStats">
                    <span class="font-mono text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded text-xs" x-text="'Gap: Rs. ' + hoverStats.spread + ' / kg'"></span>
                </template>
                <template x-if="!hoverStats">
                    <span class="text-xs text-slate-500 font-sans">Varies by district proximity to farmgate centers</span>
                </template>
            </p>
        </div>
    </div>

    <div class="h-[280px] w-full relative">
        <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm z-10 rounded-xl">
            <i data-lucide="loader-2" class="w-6 h-6 animate-spin text-indigo-500"></i>
        </div>

        <canvas x-ref="chartCanvas" class="w-full h-full" id="multi-market-svg-canvas"></canvas>
    </div>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('MultiMarketChart', (config) => ({
            vegetableId: config.vegetableId,
            days: config.days,
            loading: true,
            chart: null,
            maxSpread: 0,
            darkMode: Alpine.store('theme').darkMode, // Access dark mode from global store
            hoverStats: null,
            
            markets: [
                { id: 'dambulla', name: 'Dambulla', color: '#10B981' }, // Emerald
                { id: 'peliyagoda', name: 'Peliyagoda', color: '#06B6D4' },     // Cyan
                { id: 'narahenpita', name: 'Narahenpita', color: '#3B82F6' }, // Blue
                { id: 'keppetipola', name: 'Keppetipola', color: '#8B5CF6' }  // Purple
            ],

            formatDateLabel(dateStr, format = 'short') {
                const d = new Date(dateStr);
                if (isNaN(d.getTime())) return dateStr;
                if (format === 'full') {
                    return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
                }
                return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            },
            
            async init() {
                // Watch for theme changes globally
                this.$watch('$store.theme.darkMode', (val) => {
                    this.darkMode = val;
                    this.updateChartColors();
                });

                const ctx = this.$refs.chartCanvas.getContext('2d');
                const _this = this; // store reference to alpine instance

                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: { labels: [], datasets: [] },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        onHover: function(e, elements) {
                            if (elements && elements.length > 0) {
                                const index = elements[0].index;
                                const dateStr = _this.chart.data.labels[index];
                                
                                const comparisons = _this.chart.data.datasets.map(ds => ({
                                    market: _this.markets.find(m => m.name === ds.label),
                                    price: ds.data[index],
                                    color: ds.borderColor
                                }));

                                const prices = comparisons.map(c => c.price);
                                const minPrice = Math.min(...prices);
                                const maxPrice = Math.max(...prices);

                                _this.hoverStats = {
                                    date: dateStr,
                                    comparisons: comparisons,
                                    spread: maxPrice - minPrice,
                                    cheapest: comparisons.find(c => c.price === minPrice),
                                    priciest: comparisons.find(c => c.price === maxPrice)
                                };
                            } else {
                                _this.hoverStats = null;
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                align: 'center',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8,
                                    color: this.darkMode ? '#e2e8f0' : '#334155',
                                    font: { family: 'ui-sans-serif, system-ui', size: 12, weight: '600' },
                                    padding: 20
                                }
                            },
                            tooltip: {
                                backgroundColor: this.darkMode ? 'rgba(15, 23, 42, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                                titleColor: this.darkMode ? '#94a3b8' : '#334155',
                                bodyColor: this.darkMode ? '#ffffff' : '#1f2937',
                                borderColor: this.darkMode ? '#1e293b' : '#d1d5db',
                                borderWidth: 1,
                                titleFont: { family: 'ui-monospace, SFMono-Regular', size: 11, weight: 'bold' },
                                bodyFont: { family: 'ui-sans-serif, system-ui', size: 12, weight: 'bold' },
                                padding: 12,
                                cornerRadius: 12,
                                usePointStyle: true,
                                callbacks: {
                                    title: function(context) {
                                        return 'Value Chain Snapshot | ' + _this.formatDateLabel(context[0].label, 'short');
                                    },
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) { label += ': '; }
                                        if (context.parsed.y !== null) { label += 'Rs. ' + context.parsed.y; }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { 
                                    font: { size: 10, color: this.darkMode ? '#94a3b8' : '#6b7280' }, 
                                    color: this.darkMode ? '#94a3b8' : '#6b7280', 
                                    maxTicksLimit: 7,
                                    callback: function(value, index, values) {
                                        // Ensure labels are nicely formatted as Dates
                                        return _this.formatDateLabel(this.getLabelForValue(value), 'short');
                                    }
                                }
                            },
                            y: {
                                grid: { color: this.darkMode ? '#1e293b' : '#e5e7eb', drawBorder: false },
                                ticks: {
                                    font: { family: 'ui-monospace', color: this.darkMode ? '#64748b' : '#4b5563' },
                                    color: this.darkMode ? '#64748b' : '#4b5563',
                                    callback: function(value) { return 'Rs. ' + value; }
                                }
                            }
                        }
                    }
                });

                await this.fetchData();
            },

            async fetchData() {
                this.loading = true;
                this.maxSpread = 0;
                let maxGlobalPrice = 0;
                let minGlobalPrice = Infinity;
                
                try {
                    const datasets = [];
                    let labels = [];

                    for (const market of this.markets) {
                        const res = await fetch(`/api/prices/history?vegetableId=${this.vegetableId}&marketId=${market.id}&days=${this.days}`);
                        const data = await res.json();
                        
                        if (data && data.history && data.history.length > 0) {
                            if (labels.length === 0) {
                                labels = data.history.map(d => d.date); // Keep original date string for tooltips
                            }
                            
                            const prices = data.history.map(d => d.price);
                            
                            // Calculate global spread
                            prices.forEach(p => {
                                if (p > maxGlobalPrice) maxGlobalPrice = p;
                                if (p < minGlobalPrice) minGlobalPrice = p;
                            });

                            datasets.push({
                                label: market.name,
                                data: prices,
                                borderColor: market.color,
                                backgroundColor: market.color,
                                borderWidth: 2.5,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: market.color,
                                pointBorderWidth: 2,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                fill: false,
                                tension: 0.4
                            });
                        }
                    }

                    if (maxGlobalPrice >= minGlobalPrice) {
                        this.maxSpread = Math.round(maxGlobalPrice - minGlobalPrice);
                    }

                    this.chart.data.labels = labels;
                    this.chart.data.datasets = datasets;
                    this.chart.update();

                } catch(e) {
                    console.error("Multi-Market Chart fetch error", e);
                } finally {
                    this.loading = false;
                }
            },

            updateChartColors() {
                this.chart.options.plugins.tooltip.backgroundColor = this.darkMode ? 'rgba(15, 23, 42, 0.95)' : 'rgba(255, 255, 255, 0.95)';
                this.chart.options.scales.x.ticks.color = this.darkMode ? '#94a3b8' : '#6b7280';
                this.chart.options.scales.y.grid.color = this.darkMode ? '#1e293b' : '#e5e7eb';
                this.chart.options.scales.y.ticks.color = this.darkMode ? '#64748b' : '#4b5563';
                this.chart.options.plugins.legend.labels.color = this.darkMode ? '#e2e8f0' : '#334155';
                this.chart.update();
            }
        }));
    });
</script>