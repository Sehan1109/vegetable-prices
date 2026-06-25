<x-admin-layout>
    @slot('header')
        Add New Market
    @endslot

    <div class="max-w-2xl">
        <form action="{{ route('admin.markets.store') }}" method="POST" class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            @csrf
            <div class="p-6 space-y-6">
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-400">Market Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="district" class="block text-sm font-medium text-gray-400">District <span class="text-red-500">*</span></label>
                        <input type="text" name="district" id="district" value="{{ old('district') }}" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        @error('district') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="province" class="block text-sm font-medium text-gray-400">Province <span class="text-red-500">*</span></label>
                        <input type="text" name="province" id="province" value="{{ old('province') }}" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        @error('province') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="coordinates" class="block text-sm font-medium text-gray-400">Coordinates (Optional)</label>
                    <input type="text" name="coordinates" id="coordinates" value="{{ old('coordinates') }}" placeholder="e.g. 6.9271, 79.8612" class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Latitude and Longitude comma separated.</p>
                    @error('coordinates') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-400">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-400">Slug (Optional)</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug') }}" placeholder="Leave blank to auto-generate" class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    @error('slug') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

            </div>
            <div class="px-6 py-4 bg-[#0a101d] border-t border-gray-800 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.markets.index') }}" class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-white transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-md transition-colors shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                    Create Market
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
