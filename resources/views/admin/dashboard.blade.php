<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Manual Scraper Trigger</h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Click the button below to manually fetch and update the daily vegetable prices from CBSL (or HARTI fallback). This may take 10-20 seconds to complete.
                    </p>

                    <form action="{{ route('admin.scrape') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Update Prices Manually
                        </button>
                    </form>
                </div>
            </div>

            @if(session('scrape_output'))
            <div class="bg-gray-900 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium leading-6 text-white mb-4">Scraper Logs</h3>
                    <pre class="text-sm text-green-400 font-mono whitespace-pre-wrap overflow-x-auto">{{ session('scrape_output') }}</pre>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
