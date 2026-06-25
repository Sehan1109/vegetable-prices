<x-admin-layout>
    @slot('header')Queue Manager @endslot

    <div class="space-y-6">

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">Pending Jobs</div>
                <div class="text-3xl font-bold {{ $pendingCount > 0 ? 'text-yellow-400' : 'text-white' }}">{{ $pendingCount }}</div>
            </div>
            <div class="bg-[#111827] border border-gray-800 rounded-lg p-5">
                <div class="text-[10px] font-bold text-gray-500 uppercase mb-1">Failed Jobs</div>
                <div class="flex justify-between items-center">
                    <div class="text-3xl font-bold {{ $failedCount > 0 ? 'text-red-400' : 'text-white' }}">{{ $failedCount }}</div>
                    @if($failedCount > 0)
                    <form action="{{ route('admin.queue.clear-failed') }}" method="POST" onsubmit="return confirm('Clear ALL failed jobs?')">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-red-900 hover:bg-red-800 text-red-400 text-xs font-medium rounded-md border border-red-800 transition-colors">
                            Clear All
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Pending Jobs Table --}}
        @if($pendingJobs->isNotEmpty())
        <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-lg font-bold text-white">Pending Jobs</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-[#0a101d]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Queue</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payload</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attempts</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @foreach($pendingJobs as $job)
                            @php $payload = json_decode($job->payload, true); @endphp
                            <tr class="hover:bg-[#1f2937] transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-500 font-mono">{{ $job->id }}</td>
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $job->queue }}</td>
                                <td class="px-4 py-3 text-sm text-gray-300">{{ $payload['displayName'] ?? 'Unknown' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $job->attempts }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::createFromTimestamp($job->created_at)->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Failed Jobs Table --}}
        <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h2 class="text-lg font-bold text-white">Failed Jobs</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-[#0a101d]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">UUID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Connection</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Queue</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Failed At</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-800">
                        @forelse($failedJobs as $job)
                            <tr class="hover:bg-[#1f2937] transition-colors">
                                <td class="px-4 py-3 text-xs text-gray-500 font-mono">{{ substr($job->uuid, 0, 12) }}...</td>
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $job->connection }}</td>
                                <td class="px-4 py-3 text-sm text-gray-400">{{ $job->queue }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($job->failed_at)->diffForHumans() }}</td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <form action="{{ route('admin.queue.retry') }}" method="POST" class="inline">
                                            @csrf <input type="hidden" name="uuid" value="{{ $job->uuid }}">
                                            <button type="submit" class="px-2 py-1 bg-yellow-900 hover:bg-yellow-800 text-yellow-400 text-xs rounded border border-yellow-800">Retry</button>
                                        </form>
                                        <form action="{{ route('admin.queue.delete-failed') }}" method="POST" class="inline" onsubmit="return confirm('Delete this failed job?')">
                                            @csrf <input type="hidden" name="uuid" value="{{ $job->uuid }}">
                                            <button type="submit" class="px-2 py-1 bg-red-900 hover:bg-red-800 text-red-400 text-xs rounded border border-red-800">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="check-circle" class="w-8 h-8 mx-auto mb-3 text-emerald-600"></i>
                                <p>No failed jobs. Queue is healthy.</p>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-admin-layout>
