<!-- Trends Chart Component in Laravel Blade -->
<div x-data="TrendsChart({ vegetableId: '{{ $vegetableId ?? 'carrot' }}', marketId: '{{ $marketId ?? 'pettah' }}', days: {{ $days ?? 30 }} })" class="w-full relative">
    
    <div class="h-[280px] w-full relative">
        <!-- Loading State -->
        <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm z-10 rounded-xl">
            <i data-lucide="loader-2" class="w-6 h-6 animate-spin text-emerald-500"></i>
        </div>

        <!-- Chart.js Canvas -->
        <canvas x-ref="chartCanvas" class="w-full h-full"></canvas>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('TrendsChart', (config) => ({
            vegetableId: config.vegetableId,
            marketId: config.marketId,
            days: config.days,
            loading: true,
            darkMode: Alpine.store('theme').darkMode, // Access dark mode from global store
            chart: null,

            async init() {
                // Register watch for dark mode
                this.$watch('$store.theme.darkMode', (val) => {
                    this.darkMode = val;
                    this.updateChartColors();
                });

                // Initialize Chart
                const ctx = this.$refs.chartCanvas.getContext('2d');
                
                // Gradient for area under line
                const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)'); // Emerald-500 with opacity
                gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Price per kg',
                            data: [],
                            borderColor: '#10B981', // emerald-500
                            backgroundColor: gradient,
                            borderWidth: 2,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#10B981',
                            pointBorderWidth: 2,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: this.darkMode ? 'rgba(15, 23, 42, 0.9)' : 'rgba(255, 255, 255, 0.9)',
                                titleColor: this.darkMode ? '#94a3b8' : '#334155',
                                bodyColor: this.darkMode ? '#ffffff' : '#1f2937',
                                titleFont: { family: 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace', size: 10 },
                                bodyFont: { family: 'ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif', size: 14, weight: 'bold' },
                                padding: 10,
                                cornerRadius: 8,
                                displayColors: false,
                                callbacks: {
                                    label: function(context) {
                                        return 'Rs. ' + context.parsed.y + ' /kg';
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { font: { size: 10, color: this.darkMode ? '#94a3b8' : '#64748b' }, color: this.darkMode ? '#94a3b8' : '#64748b', maxTicksLimit: 7 }
                            },
                            y: {
                                grid: { color: this.darkMode ? '#1e293b' : '#e2e8f0', drawBorder: false },
                                ticks: {
                                    font: { family: 'ui-monospace', color: this.darkMode ? '#64748b' : '#475569' },
                                    color: this.darkMode ? '#64748b' : '#475569',
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
                try {
                    // Fetch from Laravel API endpoint
                    const res = await fetch(`/api/prices/history?vegetableId=${this.vegetableId}&marketId=${this.marketId}&days=${this.days}`);
                    const data = await res.json();
                    
                    if (data && data.history) {
                        const labels = data.history.map(d => {
                            const date = new Date(d.date);
                            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                        });
                        const prices = data.history.map(d => d.price);

                        this.chart.data.labels = labels;
                        this.chart.data.datasets[0].data = prices;
                        this.chart.update();
                    }
                } catch(e) {
                    console.error("Chart fetch error", e);
                } finally {
                    this.loading = false;
                }
            },

            updateChartColors() {
                this.chart.options.plugins.tooltip.backgroundColor = this.darkMode ? 'rgba(15, 23, 42, 0.9)' : 'rgba(255, 255, 255, 0.9)';
                this.chart.options.plugins.tooltip.titleColor = this.darkMode ? '#94a3b8' : '#334155';
                this.chart.options.plugins.tooltip.bodyColor = this.darkMode ? '#ffffff' : '#1f2937';
                this.chart.options.scales.x.ticks.color = this.darkMode ? '#94a3b8' : '#64748b';
                this.chart.options.scales.y.grid.color = this.darkMode ? '#1e293b' : '#e2e8f0';
                this.chart.options.scales.y.ticks.color = this.darkMode ? '#64748b' : '#475569';
                this.chart.update();
            }
        }));
    });
</script>
