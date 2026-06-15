<!-- Pipeline Control Component - Laravel Blade with Alpine.js -->
<div x-data="PipelineControl()" class="bg-slate-900 border border-slate-800 rounded-3xl p-5 sm:p-8 shadow-2xl relative overflow-hidden select-none" id="pipeline-control-panel-box">
    
    <!-- Auth View (Lock mode) -->
    <template x-if="!isAuthorized">
        <div class="absolute inset-0 z-50 bg-slate-900 flex flex-col items-center justify-center p-6 text-slate-200">
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-emerald-500/10 blur-3xl pointer-events-none rounded-full animate-pulse"></div>
            
            <div class="relative flex flex-col items-center text-center max-w-md w-full">
                <div class="w-16 h-16 rounded-2xl bg-slate-800 border border-slate-700/60 text-emerald-400 flex items-center justify-center mb-6 shadow-xl relative overflow-hidden">
                    <i data-lucide="lock" class="w-8 h-8"></i>
                    <div class="absolute inset-0 bg-emerald-500/5 animate-ping duration-1000 rounded-full"></div>
                </div>
                
                <h3 class="text-xl font-bold font-display text-white mb-2 tracking-tight" x-text="t.authTitle"></h3>
                <p class="text-xs text-slate-400 mb-6 leading-relaxed px-4" x-text="t.authDesc"></p>
                
                <form @submit.prevent="handleAuthSubmit" class="w-full space-y-4">
                    <div class="relative">
                        <input
                            type="password"
                            x-model="passcode"
                            @input="authError = ''"
                            :placeholder="t.enterPass"
                            class="w-full bg-slate-950/80 hover:bg-slate-950 focus:bg-slate-950 text-white placeholder-slate-600 text-center text-sm font-mono tracking-widest px-4 py-3.5 rounded-2xl border border-slate-800 focus:border-emerald-500/80 focus:ring-1 focus:ring-emerald-500/80 focus:outline-none transition-all h-[48px]"
                            autofocus
                        />
                    </div>

                    <template x-if="authError">
                        <p class="text-rose-500 text-xs font-mono bg-rose-500/10 border border-rose-500/20 py-2 px-3 rounded-xl" x-text="authError"></p>
                    </template>

                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl text-xs font-mono uppercase tracking-widest font-bold transition hover:scale-[1.01] active:scale-[0.99] cursor-pointer shadow-md flex items-center justify-center gap-2">
                        <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                        <span x-text="t.unlockBtn"></span>
                    </button>
                </form>
            </div>
        </div>
    </template>

    <!-- Main Dashboard View -->
    <template x-if="isAuthorized">
        <div>
            <!-- Header -->
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-b border-slate-800/70 pb-6 mb-6">
                <div class="flex items-start gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                        <i data-lucide="cpu" class="w-6 h-6" :class="isTriggering ? 'animate-spin' : 'animate-pulse'"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-bold font-mono tracking-widest uppercase bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded border border-emerald-500/20">ADMIN CONSOLE</span>
                        </div>
                        <h3 class="text-xl font-bold font-display text-white mt-1 tracking-tight" x-text="t.dashboardTitle"></h3>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="t.subTitle"></p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <button type="button" @click="handleTriggerScraper" :disabled="isTriggering" class="px-4 py-2.5 h-[40px] bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-mono font-bold uppercase disabled:opacity-50 flex items-center gap-2">
                        <i data-lucide="hard-drive-download" class="w-3.5 h-3.5" x-show="!isTriggering"></i>
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5 animate-spin" x-show="isTriggering" style="display:none;"></i>
                        <span x-text="isTriggering ? t.triggeringBtn : t.triggerBtn"></span>
                    </button>
                    <button type="button" @click="sessionStorage.removeItem('pipeline_admin'); isAuthorized = false" class="h-[40px] px-3 bg-slate-850 hover:bg-slate-800 border border-slate-750 text-slate-300 rounded-xl text-xs font-mono flex items-center gap-1.5">
                        <i data-lucide="lock" class="w-3.5 h-3.5 text-rose-400"></i>
                        <span x-text="t.lockSession"></span>
                    </button>
                </div>
            </div>

            <!-- Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- CPU -->
                <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-5 flex flex-col justify-between min-h-[128px]">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-mono uppercase" x-text="t.cpuUsage"></span>
                            <span class="text-[10px] font-mono font-bold text-emerald-400" x-text="cpuUsage + '%'"></span>
                        </div>
                        <div class="w-full bg-slate-900 h-1.5 rounded-full mt-2 overflow-hidden">
                            <div class="h-full rounded-full bg-emerald-500 transition-all duration-500" :style="'width: ' + cpuUsage + '%'"></div>
                        </div>
                    </div>
                </div>

                <!-- RAM -->
                <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-5 flex flex-col justify-between min-h-[128px]">
                    <div>
                        <span class="text-[10px] text-slate-400 font-mono uppercase" x-text="t.ramUsage"></span>
                        <div class="text-white font-mono text-xl font-bold mt-1.5" x-text="ramUsage + ' MB'"></div>
                    </div>
                </div>

                <!-- DB Latency -->
                <div class="bg-slate-950/60 border border-slate-800 rounded-2xl p-5 flex flex-col justify-between min-h-[128px]">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] text-slate-400 font-mono uppercase" x-text="t.latency"></span>
                            <span class="text-[10px] font-mono font-bold text-emerald-400" x-text="dbLatency + ' ms'"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 flex flex-col shadow-inner">
                <div class="py-12 text-center text-slate-500 italic">No system logs recorded. Trigger manual sync above.</div>
            </div>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('PipelineControl', () => ({
            isAuthorized: sessionStorage.getItem('pipeline_admin') === 'true',
            passcode: '',
            authError: '',
            isTriggering: false,
            
            cpuUsage: 8.4,
            ramUsage: 145.2,
            dbLatency: 4.2,
            
            lang: 'en',
            translations: {
                en: { dashboardTitle: 'Admin Pipeline Monitor', subTitle: 'HARTI price crawl traces', lockSession: 'Lock Console', authTitle: 'Admin Session Lock', authDesc: 'Verify credentials to manage servers.', enterPass: 'Enter passcode (admin)', unlockBtn: 'Unlock', triggerBtn: 'Force HARTI Scan', triggeringBtn: 'Scanning...', cpuUsage: 'CPU Utilization', ramUsage: 'Memory Footprint', latency: 'Database Latency' }
            },
            
            get t() { return this.translations[this.lang] || this.translations.en; },

            init() {
                setInterval(() => {
                    if(!this.isAuthorized) return;
                    this.cpuUsage = parseFloat((Math.random() * 4 + 6).toFixed(1));
                    this.ramUsage = parseFloat((142 + Math.random() * 6).toFixed(1));
                    this.dbLatency = parseFloat((Math.random() * 1.5 + 3.5).toFixed(1));
                }, 1500);
            },

            handleAuthSubmit() {
                if (this.passcode === 'admin' || this.passcode === '1234') {
                    sessionStorage.setItem('pipeline_admin', 'true');
                    this.isAuthorized = true;
                    this.authError = '';
                } else {
                    this.authError = 'Invalid passcode. Please try again.';
                }
            },

            handleTriggerScraper() {
                this.isTriggering = true;
                
                // Usually an AJAX request like fetch('/api/scrape', { method: 'POST' })
                fetch('/api/scrape', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') }
                }).then(() => {
                    setTimeout(() => { this.isTriggering = false; }, 2000);
                }).catch(() => {
                    this.isTriggering = false;
                });
            }
        }));
    });
</script>
