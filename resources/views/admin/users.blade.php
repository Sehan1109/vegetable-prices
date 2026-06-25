<x-admin-layout>
    @slot('header')User Management @endslot

    <div class="bg-[#111827] border border-gray-800 rounded-xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-800 flex justify-between items-center">
            <h2 class="text-lg font-bold text-white">Admin Users</h2>
            <span class="text-sm text-gray-500">{{ $users->total() }} users total</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-800">
                <thead class="bg-[#0a101d]">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Verified</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse($users as $user)
                        <tr class="hover:bg-[#1f2937] transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white font-bold text-sm shrink-0">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium text-white">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($user->email_verified_at)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Verified</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-800 bg-[#0a101d]">{{ $users->links() }}</div>
        @endif
    </div>
</x-admin-layout>
