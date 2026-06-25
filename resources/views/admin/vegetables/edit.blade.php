<x-admin-layout>
    @slot('header')
        Edit Vegetable: {{ $vegetable->name }}
    @endslot

    <div class="max-w-2xl">
        <form action="{{ route('admin.vegetables.update', $vegetable) }}" method="POST" enctype="multipart/form-data" class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-6">
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-400">Vegetable Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $vegetable->name) }}" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-400">Category <span class="text-red-500">*</span></label>
                        <input type="text" name="category" id="category" list="category-list" value="{{ old('category', $vegetable->category) }}" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm" placeholder="e.g. Upcountry, Lowcountry">
                        <datalist id="category-list">
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                        @error('category') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-400">Status <span class="text-red-500">*</span></label>
                        <select name="status" id="status" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                            <option value="active" {{ old('status', $vegetable->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $vegetable->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-400">Vegetable Image (Optional)</label>
                    @if($vegetable->image)
                        <div class="mt-2 mb-4">
                            <img src="{{ Storage::url($vegetable->image) }}" alt="{{ $vegetable->name }}" class="h-24 w-24 object-cover rounded-md border border-gray-700">
                        </div>
                    @endif
                    <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-[#1f2937] file:text-white hover:file:bg-[#374151]">
                    <p class="mt-1 text-xs text-gray-500">Upload new image to replace the current one. Max size: 2MB.</p>
                    @error('image') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-400">Slug</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $vegetable->slug) }}" required class="mt-1 block w-full border-gray-700 bg-[#0a101d] text-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    @error('slug') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

            </div>
            <div class="px-6 py-4 bg-[#0a101d] border-t border-gray-800 flex items-center justify-between">
                <a href="{{ route('admin.vegetables.index') }}" class="px-4 py-2 text-sm font-medium text-gray-400 hover:text-white transition-colors">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-md transition-colors shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                    Update Vegetable
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
