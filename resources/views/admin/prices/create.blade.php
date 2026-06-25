<x-admin-layout>
    @slot('header')Add Price Record @endslot
    <div class="max-w-2xl">
        <form action="{{ route('admin.prices.store') }}" method="POST" class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            @csrf
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        @error('date') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Market <span class="text-red-500">*</span></label>
                        <select name="market_id" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                            <option value="">Select Market</option>
                            @foreach($markets as $m)
                                <option value="{{ $m->slug }}" {{ old('market_id') === $m->slug ? 'selected' : '' }}>{{ $m->name }}</option>
                            @endforeach
                        </select>
                        @error('market_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-400">Vegetable <span class="text-red-500">*</span></label>
                    <select name="vegetable_id" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        <option value="">Select Vegetable</option>
                        @foreach($vegetables as $v)
                            <option value="{{ $v->slug }}" {{ old('vegetable_id') === $v->slug ? 'selected' : '' }}>{{ $v->name }}</option>
                        @endforeach
                    </select>
                    @error('vegetable_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Price Today (Rs.)</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        @error('price') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Price Yesterday (Rs.)</label>
                        <input type="number" step="0.01" name="price_yesterday" value="{{ old('price_yesterday') }}" class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Change %</label>
                        <input type="number" step="0.01" name="change_percent" value="{{ old('change_percent') }}" class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-400">Trend</label>
                        <select name="trend" class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                            <option value="none">None</option>
                            <option value="up" {{ old('trend') === 'up' ? 'selected' : '' }}>Up</option>
                            <option value="down" {{ old('trend') === 'down' ? 'selected' : '' }}>Down</option>
                            <option value="flat" {{ old('trend') === 'flat' ? 'selected' : '' }}>Flat</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 bg-[#0a101d] border-t border-gray-800 flex justify-end space-x-3">
                <a href="{{ route('admin.prices.index') }}" class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-white">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-md transition-colors">Create Record</button>
            </div>
        </form>
    </div>
</x-admin-layout>
