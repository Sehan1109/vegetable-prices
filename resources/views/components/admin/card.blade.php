<div {{ $attributes->merge(['class' => 'bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden']) }}>
    @if(isset($title))
    <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center">
        <h2 class="text-lg font-bold text-white">{{ $title }}</h2>
        {{ $action ?? '' }}
    </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>
