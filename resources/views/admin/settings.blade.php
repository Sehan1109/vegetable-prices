<x-admin-layout>
    @slot('header')Settings @endslot

    <div class="max-w-3xl space-y-6">
        <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-lg font-bold text-white">Application Settings</h2>
                <p class="text-xs text-gray-500 mt-1">These settings are managed via your <code class="text-emerald-400">.env</code> file. Edit the .env directly to make changes.</p>
            </div>
            <div class="p-6 space-y-4">
                @foreach([
                    ['APP_NAME',     'Application Name',    env('APP_NAME', 'N/A')],
                    ['APP_URL',      'Application URL',     env('APP_URL', 'N/A')],
                    ['APP_ENV',      'Environment',         env('APP_ENV', 'N/A')],
                    ['DB_CONNECTION','Database Driver',      env('DB_CONNECTION', 'N/A')],
                    ['DB_HOST',      'DB Host',             env('DB_HOST', 'N/A')],
                    ['DB_DATABASE',  'Database Name',       env('DB_DATABASE', 'N/A')],
                    ['QUEUE_CONNECTION','Queue Driver',      env('QUEUE_CONNECTION', 'N/A')],
                    ['CACHE_STORE',  'Cache Driver',        env('CACHE_STORE', env('CACHE_DRIVER', 'N/A'))],
                    ['MAIL_MAILER',  'Mail Driver',         env('MAIL_MAILER', 'N/A')],
                    ['MAIL_HOST',    'SMTP Host',           env('MAIL_HOST', 'N/A')],
                ] as [$key, $label, $value])
                <div class="flex items-center justify-between py-3 border-b border-gray-800/50">
                    <div>
                        <div class="text-sm font-medium text-gray-300">{{ $label }}</div>
                        <div class="text-xs text-gray-600 font-mono">{{ $key }}</div>
                    </div>
                    <div class="text-sm font-mono text-emerald-400 bg-[#0a101d] px-3 py-1 rounded border border-gray-800">
                        {{ str_contains($key, 'PASSWORD') || str_contains($key, 'SECRET') ? '••••••••' : $value }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-lg font-bold text-white">Quick Admin Actions</h2>
            </div>
            <div class="p-6 flex flex-wrap gap-3">
                <a href="{{ route('admin.cache') }}" class="flex items-center px-4 py-2 bg-[#0a101d] hover:bg-[#1f2937] border border-gray-700 text-gray-300 hover:text-white text-sm rounded-md transition-colors">
                    <i data-lucide="zap" class="w-4 h-4 mr-2 text-emerald-500"></i> Manage Cache
                </a>
                <a href="{{ route('admin.logs') }}" class="flex items-center px-4 py-2 bg-[#0a101d] hover:bg-[#1f2937] border border-gray-700 text-gray-300 hover:text-white text-sm rounded-md transition-colors">
                    <i data-lucide="terminal" class="w-4 h-4 mr-2 text-emerald-500"></i> View Logs
                </a>
                <a href="{{ route('admin.queue') }}" class="flex items-center px-4 py-2 bg-[#0a101d] hover:bg-[#1f2937] border border-gray-700 text-gray-300 hover:text-white text-sm rounded-md transition-colors">
                    <i data-lucide="layers" class="w-4 h-4 mr-2 text-emerald-500"></i> Queue Manager
                </a>
                <a href="{{ route('admin.scheduler') }}" class="flex items-center px-4 py-2 bg-[#0a101d] hover:bg-[#1f2937] border border-gray-700 text-gray-300 hover:text-white text-sm rounded-md transition-colors">
                    <i data-lucide="clock" class="w-4 h-4 mr-2 text-emerald-500"></i> Scheduler
                </a>
            </div>
        </div>
    </div>
</x-admin-layout>
