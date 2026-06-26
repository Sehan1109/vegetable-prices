<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page->title }}</title>
    <meta name="description" content="{{ $page->meta_description }}">
    <link rel="canonical" href="{{ url($page->slug) }}">
    
    <!-- Open Graph -->
    <meta property="og:title" content="{{ $page->title }}">
    <meta property="og:description" content="{{ $page->meta_description }}">
    <meta property="og:url" content="{{ url($page->slug) }}">
    <meta property="og:type" content="article">
    
    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $page->title }}">
    <meta name="twitter:description" content="{{ $page->meta_description }}">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Product",
      "name": "{{ $page->vegetable->name }}",
      "description": "Price of {{ $page->vegetable->name }} at {{ $page->market->name }} on {{ $page->date }}",
      "offers": {
        "@@type": "Offer",
        "priceCurrency": "LKR",
        "price": "{{ $page->priceRecord->price_average ?? $page->priceRecord->price ?? 0 }}",
        "priceValidUntil": "{{ \Carbon\Carbon::parse($page->date)->addDay()->format('Y-m-d') }}",
        "availability": "https://schema.org/InStock"
      }
    }
    </script>

    <!-- Dark Mode Initializer: apply saved preference before render to prevent flash -->
    <script>
        (function () {
            try {
                var saved = localStorage.getItem('darkMode');
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (saved === 'true' || (saved === null && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
</head>
<body class="bg-gray-50 dark:bg-slate-900 text-gray-900 dark:text-slate-100 font-sans antialiased selection:bg-green-500 selection:text-white transition-colors duration-200">
    <!-- Navbar -->
    <nav class="bg-white dark:bg-slate-800 shadow-sm border-b border-gray-100 dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="/" class="flex-shrink-0 flex items-center gap-2">
                        <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-green-600 to-emerald-500">Sri Lanka Veg Prices</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumbs -->
        <nav class="flex text-sm text-gray-500 dark:text-slate-400 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="/" class="hover:text-green-600 dark:hover:text-green-400">Home</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2">Prices</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 text-gray-700 dark:text-slate-300 font-medium">{{ $page->vegetable->name }} in {{ $page->market->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <header class="mb-10 text-center lg:text-left">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white sm:text-5xl mb-4">
                Today's {{ $page->vegetable->name }} Price in {{ $page->market->name }}
            </h1>
            <p class="text-xl text-gray-500 dark:text-slate-400">
                Data recorded on {{ \Carbon\Carbon::parse($page->date)->format('d F Y') }}
            </p>
        </header>

        <!-- Price Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 flex flex-col items-center justify-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-400 mb-2 relative z-10">Average Price</dt>
                <dd class="text-4xl font-bold text-green-600 dark:text-green-400 relative z-10">Rs. {{ number_format($page->priceRecord->price_average ?? $page->priceRecord->price ?? 0, 2) }}</dd>
                <span class="text-xs text-gray-400 dark:text-slate-500 mt-2 relative z-10">per KG</span>
            </div>
            
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 flex flex-col items-center justify-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-400 mb-2 relative z-10">Minimum Price</dt>
                <dd class="text-3xl font-bold text-blue-600 dark:text-blue-400 relative z-10">Rs. {{ number_format($page->priceRecord->price_min ?? $page->priceRecord->price ?? 0, 2) }}</dd>
                <span class="text-xs text-gray-400 dark:text-slate-500 mt-2 relative z-10">per KG</span>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-6 flex flex-col items-center justify-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <dt class="text-sm font-medium text-gray-500 dark:text-slate-400 mb-2 relative z-10">Maximum Price</dt>
                <dd class="text-3xl font-bold text-red-600 dark:text-red-400 relative z-10">Rs. {{ number_format($page->priceRecord->price_max ?? $page->priceRecord->price ?? 0, 2) }}</dd>
                <span class="text-xs text-gray-400 dark:text-slate-500 mt-2 relative z-10">per KG</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Market Information -->
                <section class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">About {{ $page->market->name }} Market</h2>
                    <p class="text-gray-600 dark:text-slate-300 leading-relaxed">
                        The {{ $page->market->name }} market is located in the {{ $page->market->district ?? 'local' }} district. It serves as a major hub for fresh produce distribution. The prices listed above reflect the official rates collected and published by the Hector Kobbekaduwa Agrarian Research and Training Institute (HARTI) or the Central Bank of Sri Lanka (CBSL).
                    </p>
                </section>

                <!-- Historical Trend -->
                @if($historicalPrices->count() > 1)
                <section class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-700 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Recent Price Trend</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Price (Rs)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                @foreach($historicalPrices as $historical)
                                <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-slate-200">{{ \Carbon\Carbon::parse($historical->date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-slate-200 text-right font-medium">{{ number_format($historical->price_average ?? $historical->price ?? 0, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
                @endif
            </div>

            <!-- Sidebar (Internal Links for SEO) -->
            <div class="space-y-8">
                @if($relatedMarkets->count() > 0)
                <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 border border-gray-200 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ $page->vegetable->name }} in Other Markets</h3>
                    <ul class="space-y-3">
                        @foreach($relatedMarkets as $related)
                        <li>
                            <a href="{{ url($related->slug) }}" class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 hover:underline flex justify-between items-center group">
                                <span>{{ $related->market->name }}</span>
                                <svg class="w-4 h-4 text-gray-400 dark:text-slate-500 group-hover:text-green-600 dark:group-hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if($relatedVegetables->count() > 0)
                <div class="bg-gray-50 dark:bg-slate-800 rounded-2xl p-6 border border-gray-200 dark:border-slate-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Other Vegetables in {{ $page->market->name }}</h3>
                    <ul class="space-y-3">
                        @foreach($relatedVegetables as $related)
                        <li>
                            <a href="{{ url($related->slug) }}" class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 hover:underline flex justify-between items-center group">
                                <span>{{ $related->vegetable->name }}</span>
                                <svg class="w-4 h-4 text-gray-400 dark:text-slate-500 group-hover:text-green-600 dark:group-hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-slate-800 border-t border-gray-100 dark:border-slate-700 mt-12 py-8 text-center text-gray-500 dark:text-slate-400 text-sm">
        <p>&copy; {{ date('Y') }} Sri Lanka Vegetable Prices. Automatically generated data pages.</p>
    </footer>
</body>
</html>
