<div class="space-y-4">
    {{-- Project Manager --}}
    @if($manager)
        <div class="p-3 rounded-xl bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800">
            <div class="flex items-center gap-3">
                <div
                    class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold text-sm">
                    {{ strtoupper(substr($manager->name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $manager->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $manager->email }}</p>
                </div>
                <span
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-100 dark:bg-primary-800 text-primary-700 dark:text-primary-300">
                    Project Manager
                </span>
            </div>
        </div>
    @endif

    {{-- Team Members --}}
    @if($members->count())
        <div>
            <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Team Members ({{ $members->count() }})
            </h4>
            <div class="space-y-2">
                @foreach($members as $member)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                        <div
                            class="flex-shrink-0 w-9 h-9 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 font-semibold text-xs">
                            {{ strtoupper(substr($member->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $member->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->email }}</p>
                        </div>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                            {{ ucfirst($member->pivot->role ?? 'member') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Pending Invitations --}}
    @if($pendingInvites->count())
        <div>
            <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Pending Invitations ({{ $pendingInvites->count() }})
            </h4>
            <div class="space-y-2">
                @foreach($pendingInvites as $invite)
                    <div
                        class="flex items-center gap-3 p-2 rounded-lg bg-amber-50/50 dark:bg-amber-900/10 border border-amber-200/50 dark:border-amber-800/30">
                        <div
                            class="flex-shrink-0 w-9 h-9 rounded-full bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center text-amber-600 dark:text-amber-400">
                            <x-heroicon-o-envelope class="w-4 h-4" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $invite->name ?? $invite->email }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $invite->email }} · Invited {{ $invite->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300">
                            Pending
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Empty state --}}
    @if(!$manager && $members->isEmpty() && $pendingInvites->isEmpty())
        <div class="text-center py-6">
            <x-heroicon-o-users class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-2" />
            <p class="text-sm text-gray-500 dark:text-gray-400">No team members yet. Use "Invite People" to add your team.
            </p>
        </div>
    @endif
</div>