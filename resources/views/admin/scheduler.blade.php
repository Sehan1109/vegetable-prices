<x-admin-layout>
    @slot('header')Scheduler Monitor @endslot

    <div class="space-y-6">

        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-500">All registered scheduled tasks and their next execution times.</p>
            <form action="{{ route('admin.scheduler.run') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-bold rounded-md transition-colors">
                    <i data-lucide="play" class="w-4 h-4 mr-2"></i> Run Scheduler Now
                </button>
            </form>
        </div>

        <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-[#0a101d]">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Command / Task</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cron Expression</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Run</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($events as $event)
                            <tr class="hover:bg-[#1f2937] transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-white font-mono">{{ $event['command'] }}</div>
                                    @if($event['description'])
                                        <div class="text-xs text-gray-500 mt-1">{{ $event['description'] }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-400 font-mono">{{ $event['expression'] }}</td>
                                <td class="px-6 py-4 text-sm text-gray-300">{{ $event['nextRun'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Scheduled</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="clock" class="w-8 h-8 mx-auto mb-3 text-gray-600"></i>
                                <p>No scheduled tasks found in the Kernel.</p>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-admin-layout>
