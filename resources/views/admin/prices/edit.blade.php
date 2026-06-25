<x-admin-layout>
    @slot('header')Edit Price Record #{{ $price->id }} @endslot
    <div class="max-w-2xl">
        <form action="{{ route('admin.prices.update', $price) }}" method="POST" class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            @csrf @method('PUT')
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" value="{{ old('date', $price->date) }}" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Market <span class="text-red-500">*</span></label>
                        <select name="market_id" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                            @foreach($markets as $m)
                                <option value="{{ $m->slug }}" {{ old('market_id', $price->market_id) === $m->slug ? 'selected' : '' }}>{{ $m->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Vegetable <span class="text-red-500">*</span></label>
                    <select name="vegetable_id" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        @foreach($vegetables as $v)
                            <option value="{{ $v->slug }}" {{ old('vegetable_id', $price->vegetable_id) === $v->slug ? 'selected' : '' }}>{{ $v->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Price Today (Rs.)</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $price->price) }}" class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Price Yesterday (Rs.)</label>
                        <input type="number" step="0.01" name="price_yesterday" value="{{ old('price_yesterday', $price->price_yesterday) }}" class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Change %</label>
                        <input type="number" step="0.01" name="change_percent" value="{{ old('change_percent', $price->change_percent) }}" class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Trend</label>
                        <select name="trend" class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                            @foreach(['none','up','down','flat'] as $t)
                                <option value="{{ $t }}" {{ old('trend', $price->trend) === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-[#0a101d] border-t border-gray-800 flex justify-between">
                <a href="{{ route('admin.prices.index') }}" class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-white">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-md transition-colors">Update Record</button>
            </div>
        </form>
    </div>
</x-admin-layout>
