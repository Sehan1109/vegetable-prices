<x-admin-layout>
    @slot('header')Cache Manager @endslot

    <div class="space-y-6">

        <p class="text-sm text-gray-500">Clear individual caches or run Laravel optimization commands directly from this panel.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- Clear Application Cache --}}
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="p-2 bg-red-500/10 text-red-400 rounded-md"><i data-lucide="trash-2" class="w-5 h-5"></i></div>
                    <div>
                        <h3 class="font-semibold text-white">Application Cache</h3>
                        <p class="text-xs text-gray-500">Clears all cached data</p>
                    </div>
                </div>
                <form action="{{ route('admin.cache.clear') }}" method="POST">
                    @csrf <input type="hidden" name="type" value="cache">
                    <button type="submit" class="w-full px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm font-medium rounded-md border border-gray-700 transition-colors">
                        cache:clear
                    </button>
                </form>
            </div>

            {{-- Clear Route Cache --}}
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="p-2 bg-yellow-500/10 text-yellow-400 rounded-md"><i data-lucide="git-merge" class="w-5 h-5"></i></div>
                    <div>
                        <h3 class="font-semibold text-white">Route Cache</h3>
                        <p class="text-xs text-gray-500">Removes compiled routes</p>
                    </div>
                </div>
                <form action="{{ route('admin.cache.clear') }}" method="POST">
                    @csrf <input type="hidden" name="type" value="route">
                    <button type="submit" class="w-full px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm font-medium rounded-md border border-gray-700 transition-colors">
                        route:clear
                    </button>
                </form>
            </div>

            {{-- Clear View Cache --}}
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="p-2 bg-purple-500/10 text-purple-400 rounded-md"><i data-lucide="layout" class="w-5 h-5"></i></div>
                    <div>
                        <h3 class="font-semibold text-white">View Cache</h3>
                        <p class="text-xs text-gray-500">Removes compiled Blade templates</p>
                    </div>
                </div>
                <form action="{{ route('admin.cache.clear') }}" method="POST">
                    @csrf <input type="hidden" name="type" value="view">
                    <button type="submit" class="w-full px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm font-medium rounded-md border border-gray-700 transition-colors">
                        view:clear
                    </button>
                </form>
            </div>

            {{-- Clear Config Cache --}}
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="p-2 bg-blue-500/10 text-blue-400 rounded-md"><i data-lucide="settings" class="w-5 h-5"></i></div>
                    <div>
                        <h3 class="font-semibold text-white">Config Cache</h3>
                        <p class="text-xs text-gray-500">Removes config compilation</p>
                    </div>
                </div>
                <form action="{{ route('admin.cache.clear') }}" method="POST">
                    @csrf <input type="hidden" name="type" value="config">
                    <button type="submit" class="w-full px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm font-medium rounded-md border border-gray-700 transition-colors">
                        config:clear
                    </button>
                </form>
            </div>

            {{-- Clear All --}}
            <div class="bg-[#111827] border border-red-900/50 rounded-lg p-5">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="p-2 bg-red-500/10 text-red-500 rounded-md"><i data-lucide="alert-triangle" class="w-5 h-5"></i></div>
                    <div>
                        <h3 class="font-semibold text-white">Clear Everything</h3>
                        <p class="text-xs text-gray-500">Cache + Routes + Views + Config</p>
                    </div>
                </div>
                <form action="{{ route('admin.cache.clear') }}" method="POST" onsubmit="return confirm('Clear all caches?')">
                    @csrf <input type="hidden" name="type" value="all">
                    <button type="submit" class="w-full px-4 py-2 bg-red-900 hover:bg-red-800 text-red-400 text-sm font-medium rounded-md border border-red-800 transition-colors">
                        Clear All Caches
                    </button>
                </form>
            </div>

            {{-- Optimize --}}
            <div class="bg-[#111827] border border-emerald-900/50 rounded-lg p-5">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="p-2 bg-emerald-500/10 text-emerald-500 rounded-md"><i data-lucide="zap" class="w-5 h-5"></i></div>
                    <div>
                        <h3 class="font-semibold text-white">Optimize App</h3>
                        <p class="text-xs text-gray-500">Caches config, routes & views</p>
                    </div>
                </div>
                <div class="space-y-2">
                    <form action="{{ route('admin.cache.optimize') }}" method="POST">
                        @csrf <input type="hidden" name="mode" value="optimize">
                        <button type="submit" class="w-full px-4 py-2 bg-emerald-700 hover:bg-emerald-600 text-white text-sm font-medium rounded-md transition-colors">
                            php artisan optimize
                        </button>
                    </form>
                    <form action="{{ route('admin.cache.optimize') }}" method="POST">
                        @csrf <input type="hidden" name="mode" value="clear">
                        <button type="submit" class="w-full px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm font-medium rounded-md border border-gray-700 transition-colors">
                            optimize:clear
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
