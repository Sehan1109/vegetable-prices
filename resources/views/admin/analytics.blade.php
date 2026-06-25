<x-admin-layout>
    @slot('header')Analytics @endslot

    <div class="space-y-6">

        {{-- Daily Records Chart --}}
        <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-lg font-bold text-white">Daily Price Records Imported (Last 30 Days)</h2>
            </div>
            <div class="p-6">
                <canvas id="dailyImportsChart" height="80"></canvas>
            </div>
        </div>

        {{-- SEO Pages Chart --}}
        <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-lg font-bold text-white">Daily SEO Pages Generated (Last 30 Days)</h2>
            </div>
            <div class="p-6">
                <canvas id="dailySeoChart" height="80"></canvas>
            </div>
        </div>

        {{-- Top Vegetables & Markets --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-lg font-bold text-white">Top 10 Vegetables by Records</h2>
                </div>
                <div class="p-6">
                    <canvas id="topVegsChart"></canvas>
                </div>
            </div>
            <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-800">
                    <h2 class="text-lg font-bold text-white">Top 10 Markets by Records</h2>
                </div>
                <div class="p-6">
                    <canvas id="topMarketsChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <script>
        const chartDefaults = {
            color: '#9ca3af',
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { ticks: { color: '#6b7280', font: { size: 10 } }, grid: { color: '#1f2937' } },
                y: { ticks: { color: '#6b7280', font: { size: 10 } }, grid: { color: '#1f2937' } }
            }
        };

        // Daily Imports
        const dailyImports = @json($dailyImports);
        new Chart(document.getElementById('dailyImportsChart'), {
            type: 'bar',
            data: {
                labels: dailyImports.map(d => d.label),
                datasets: [{ label: 'Records', data: dailyImports.map(d => d.total), backgroundColor: 'rgba(16,185,129,0.5)', borderColor: '#10b981', borderWidth: 1, borderRadius: 4 }]
            },
            options: { ...chartDefaults, plugins: { legend: { display: false } } }
        });

        // Daily SEO
        const dailySeo = @json($dailySeo);
        new Chart(document.getElementById('dailySeoChart'), {
            type: 'line',
            data: {
                labels: dailySeo.map(d => d.label),
                datasets: [{ label: 'SEO Pages', data: dailySeo.map(d => d.total), borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,0.15)', fill: true, tension: 0.4, pointBackgroundColor: '#6366f1' }]
            },
            options: { ...chartDefaults }
        });

        // Top Vegetables
        const topVegs = @json($topVegetables);
        new Chart(document.getElementById('topVegsChart'), {
            type: 'bar',
            data: {
                labels: topVegs.map(d => d.vegetable_id),
                datasets: [{ data: topVegs.map(d => d.total), backgroundColor: 'rgba(16,185,129,0.5)', borderColor: '#10b981', borderWidth: 1, borderRadius: 4 }]
            },
            options: { ...chartDefaults, indexAxis: 'y' }
        });

        // Top Markets
        const topMarkets = @json($topMarkets);
        new Chart(document.getElementById('topMarketsChart'), {
            type: 'bar',
            data: {
                labels: topMarkets.map(d => d.market_id),
                datasets: [{ data: topMarkets.map(d => d.total), backgroundColor: 'rgba(99,102,241,0.5)', borderColor: '#6366f1', borderWidth: 1, borderRadius: 4 }]
            },
            options: { ...chartDefaults, indexAxis: 'y' }
        });
    </script>
</x-admin-layout>
