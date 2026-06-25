<x-admin-layout>
    @slot('header')
        Price Records
    @endslot

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form action="{{ route('admin.prices.index') }}" method="GET" class="flex flex-wrap gap-2">
            <input type="date" name="date" value="{{ request('date') }}"
                class="border border-gray-700 rounded-md py-2 px-3 bg-[#0a101d] text-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
            <select name="market" class="border border-gray-700 rounded-md py-2 px-3 bg-[#0a101d] text-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500 w-44">
                <option value="">All Markets</option>
                @foreach($markets as $m)
                    <option value="{{ $m->slug }}" {{ request('market') === $m->slug ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
            <select name="vegetable" class="border border-gray-700 rounded-md py-2 px-3 bg-[#0a101d] text-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500 w-44">
                <option value="">All Vegetables</option>
                @foreach($vegetables as $v)
                    <option value="{{ $v->slug }}" {{ request('vegetable') === $v->slug ? 'selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm rounded-md border border-gray-700 transition-colors">Filter</button>
            @if(request()->hasAny(['date','market','vegetable']))
                <a href="{{ route('admin.prices.index') }}" class="px-4 py-2 bg-[#450a0a] text-red-400 text-sm rounded-md border border-red-900 hover:bg-[#7f1d1d] transition-colors">Clear</a>
            @endif
        </form>
        <a href="{{ route('admin.prices.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-md transition-colors shrink-0">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Record
        </a>
    </div>

    <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800">
                <thead class="bg-[#0a101d]">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vegetable</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Market</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price (Rs.)</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Yesterday</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Change %</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($records as $record)
                        <tr class="hover:bg-[#1f2937] transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-white whitespace-nowrap">{{ $record->vegetable_id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-400 whitespace-nowrap">{{ $record->market_id }}</td>
                            <td class="px-4 py-3 text-sm text-white text-right whitespace-nowrap">{{ $record->price !== null ? number_format($record->price, 2) : 'n.a.' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-400 text-right whitespace-nowrap">{{ $record->price_yesterday !== null ? number_format($record->price_yesterday, 2) : 'n.a.' }}</td>
                            <td class="px-4 py-3 text-sm text-right whitespace-nowrap">
                                @if($record->change_percent !== null)
                                    <span class="{{ $record->change_percent > 0 ? 'text-red-400' : ($record->change_percent < 0 ? 'text-emerald-400' : 'text-gray-400') }}">
                                        {{ $record->change_percent > 0 ? '+' : '' }}{{ number_format($record->change_percent, 1) }}%
                                    </span>
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm whitespace-nowrap">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('admin.prices.edit', $record) }}" class="text-emerald-500 hover:text-emerald-400"><i data-lucide="edit" class="w-4 h-4"></i></a>
                                    <form action="{{ route('admin.prices.destroy', $record) }}" method="POST" onsubmit="return confirm('Delete this record?')" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-400"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500"><i data-lucide="inbox" class="w-8 h-8 mx-auto mb-3 text-gray-600"></i><p>No records found.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($records->hasPages())
            <div class="px-6 py-4 border-t border-gray-800 bg-[#0a101d]">{{ $records->links() }}</div>
        @endif
    </div>
</x-admin-layout>
