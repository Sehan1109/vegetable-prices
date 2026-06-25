<x-admin-layout>
    @slot('header')SEO Page Management @endslot

    <div class="space-y-6">

        {{-- Stats + Actions Row --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="bg-[#111827] border border-gray-800 rounded-lg px-5 py-3">
                    <div class="text-[10px] font-bold text-gray-500 uppercase">Total Pages</div>
                    <div class="text-2xl font-bold text-white">{{ number_format($totalPages) }}</div>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <form action="{{ route('admin.seo.generate') }}" method="POST">
                    @csrf <input type="hidden" name="mode" value="missing">
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-md transition-colors flex items-center">
                        <i data-lucide="plus-circle" class="w-4 h-4 mr-2"></i> Generate Missing
                    </button>
                </form>
                <form action="{{ route('admin.seo.generate') }}" method="POST" onsubmit="return confirm('Regenerate ALL SEO pages? This may take a while.')">
                    @csrf <input type="hidden" name="mode" value="all">
                    <button type="submit" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-500 text-white text-sm font-bold rounded-md transition-colors flex items-center">
                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Regenerate All
                    </button>
                </form>
            </div>
        </div>

        {{-- Filters --}}
        <form action="{{ route('admin.seo') }}" method="GET" class="flex flex-wrap gap-2">
            <div class="relative flex-1 min-w-48">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4 text-gray-500"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or slug..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-700 rounded-md bg-[#0a101d] text-gray-300 placeholder-gray-500 focus:outline-none focus:border-emerald-500 sm:text-sm">
            </div>
            <select name="market" class="border border-gray-700 rounded-md py-2 px-3 bg-[#0a101d] text-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500 w-40">
                <option value="">All Markets</option>
                @foreach($markets as $m)
                    <option value="{{ $m->id }}" {{ request('market') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ request('date') }}" class="border border-gray-700 rounded-md py-2 px-3 bg-[#0a101d] text-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
            <button type="submit" class="px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm font-medium rounded-md border border-gray-700 transition-colors">Filter</button>
            @if(request()->hasAny(['search','market','date']))
                <a href="{{ route('admin.seo') }}" class="px-4 py-2 bg-[#450a0a] text-red-400 text-sm rounded-md border border-red-900 hover:bg-[#7f1d1d]">Clear</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-[#0a101d]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title / Slug</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Market</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($pages as $page)
                            <tr class="hover:bg-[#1f2937] transition-colors">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-white truncate max-w-xs">{{ $page->title }}</div>
                                    <div class="text-xs text-gray-500 font-mono truncate">{{ $page->slug }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $page->market_id }}</td>
                                <td class="px-4 py-3 text-sm text-gray-400 whitespace-nowrap">{{ $page->date }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 whitespace-nowrap">{{ $page->created_at->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="{{ url('/' . $page->slug) }}" target="_blank" class="text-blue-400 hover:text-blue-300" title="Preview">
                                            <i data-lucide="external-link" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('admin.seo.delete', $page) }}" method="POST" onsubmit="return confirm('Delete this SEO page?')" class="inline-block">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-400" title="Delete">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500"><i data-lucide="inbox" class="w-8 h-8 mx-auto mb-3 text-gray-600"></i><p>No SEO pages found.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pages->hasPages())
                <div class="px-6 py-4 border-t border-gray-800 bg-[#0a101d]">{{ $pages->links() }}</div>
            @endif
        </div>
    </div>
</x-admin-layout>
