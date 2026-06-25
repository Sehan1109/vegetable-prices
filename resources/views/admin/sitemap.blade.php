<x-admin-layout>
    @slot('header')Sitemap Management @endslot

    <div class="space-y-6">

        {{-- Status Card --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">Status</div>
                <div class="flex items-center space-x-2 mt-2">
                    <div class="w-2.5 h-2.5 rounded-full {{ $exists ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]' : 'bg-red-500' }}"></div>
                    <span class="text-white font-semibold">{{ $exists ? 'Available' : 'Not Generated' }}</span>
                </div>
            </div>
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">Total URLs</div>
                <div class="text-3xl font-bold text-white">{{ number_format($urlCount) }}</div>
            </div>
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">Last Generated</div>
                <div class="text-sm font-semibold text-white mt-2">{{ $lastModified ?? 'Never' }}</div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-lg font-bold text-white">Sitemap Actions</h2>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                <div class="bg-[#0a101d] border border-gray-800 rounded-lg p-5">
                    <h3 class="font-semibold text-white mb-1">Generate / Regenerate</h3>
                    <p class="text-xs text-gray-500 mb-4">Build a fresh sitemap.xml saved to public/.</p>
                    <form action="{{ route('admin.sitemap.generate') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-md transition-colors">
                            <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i>
                            {{ $exists ? 'Regenerate Sitemap' : 'Generate Sitemap' }}
                        </button>
                    </form>
                </div>

                @if($exists)
                <div class="bg-[#0a101d] border border-gray-800 rounded-lg p-5">
                    <h3 class="font-semibold text-white mb-1">Download Sitemap</h3>
                    <p class="text-xs text-gray-500 mb-4">Download sitemap.xml to your local machine.</p>
                    <a href="{{ route('admin.sitemap.download') }}" class="flex items-center px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm font-medium rounded-md border border-gray-700 transition-colors w-fit">
                        <i data-lucide="download" class="w-4 h-4 mr-2"></i> Download
                    </a>
                </div>

                <div class="bg-[#0a101d] border border-gray-800 rounded-lg p-5">
                    <h3 class="font-semibold text-white mb-1">Preview Sitemap</h3>
                    <p class="text-xs text-gray-500 mb-4">Open the live sitemap.xml in a new browser tab.</p>
                    <a href="{{ url('sitemap.xml') }}" target="_blank" class="flex items-center px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm font-medium rounded-md border border-gray-700 transition-colors w-fit">
                        <i data-lucide="external-link" class="w-4 h-4 mr-2"></i> Preview
                    </a>
                </div>

                <div class="bg-[#0a101d] border border-gray-800 rounded-lg p-5">
                    <h3 class="font-semibold text-white mb-1">Google Search Console</h3>
                    <p class="text-xs text-gray-500 mb-4">Submit your sitemap URL directly to Google Search Console.</p>
                    <a href="https://search.google.com/search-console/sitemaps" target="_blank" class="flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium rounded-md transition-colors w-fit">
                        <i data-lucide="globe" class="w-4 h-4 mr-2"></i> Open GSC
                    </a>
                </div>
                @endif

            </div>
        </div>

    </div>
</x-admin-layout>
