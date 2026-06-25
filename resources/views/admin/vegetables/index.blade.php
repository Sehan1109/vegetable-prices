<x-admin-layout>
    @slot('header')
        Vegetables Management
    @endslot

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form action="{{ route('admin.vegetables.index') }}" method="GET" class="flex flex-1 gap-2 max-w-2xl">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-4 h-4 text-gray-500"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search vegetables by name..." class="block w-full pl-10 pr-3 py-2 border border-gray-700 rounded-md leading-5 bg-[#0a101d] text-gray-300 placeholder-gray-500 focus:outline-none focus:bg-[#111827] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 sm:text-sm transition-colors">
            </div>
            <select name="category" class="block w-40 pl-3 pr-10 py-2 text-base border-gray-700 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm rounded-md bg-[#0a101d] text-gray-300 transition-colors">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-[#1f2937] hover:bg-[#374151] text-white text-sm font-medium rounded-md border border-gray-700 transition-colors">
                Filter
            </button>
            @if(request()->hasAny(['search', 'category']))
                <a href="{{ route('admin.vegetables.index') }}" class="px-4 py-2 bg-[#450a0a] hover:bg-[#7f1d1d] text-red-400 text-sm font-medium rounded-md border border-red-900 transition-colors">
                    Clear
                </a>
            @endif
        </form>
        
        <div>
            <a href="{{ route('admin.vegetables.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-md transition-colors shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Vegetable
            </a>
        </div>
    </div>

    <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800">
                <thead class="bg-[#0a101d]">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vegetable</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Records</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-[#111827] divide-y divide-gray-800">
                    @forelse($vegetables as $vegetable)
                        <tr class="hover:bg-[#1f2937] transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-[#0a101d] border border-gray-700 rounded-md overflow-hidden flex items-center justify-center text-emerald-500 font-bold">
                                        @if($vegetable->image)
                                            <img src="{{ Storage::url($vegetable->image) }}" alt="{{ $vegetable->name }}" class="h-full w-full object-cover">
                                        @else
                                            <i data-lucide="leaf" class="w-5 h-5 text-gray-500"></i>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-white">{{ $vegetable->name }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $vegetable->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-800 text-gray-300 border border-gray-700">
                                    {{ $vegetable->category }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($vegetable->status === 'active')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-500/10 text-red-400 border border-red-500/20">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                {{ number_format($vegetable->priceRecords()->count()) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('admin.vegetables.edit', $vegetable) }}" class="text-emerald-500 hover:text-emerald-400" title="Edit">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('admin.vegetables.destroy', $vegetable) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this vegetable?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-400" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-3 text-gray-600"></i>
                                <p>No vegetables found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($vegetables->hasPages())
            <div class="px-6 py-4 border-t border-gray-800 bg-[#0a101d]">
                {{ $vegetables->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
