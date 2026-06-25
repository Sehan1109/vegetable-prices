<button {{ $attributes->merge(['class' => 'px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-md transition-colors shadow-[0_0_15px_rgba(16,185,129,0.2)] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50']) }}>
    {{ $slot }}
</button>
