<div class="space-y-6 p-2">
    {{-- Status & Priority row --}}
    <div class="flex flex-wrap gap-3">
        @php
            $statusColors = [
                'draft' => 'bg-gray-100 text-gray-700',
                'open' => 'bg-amber-100 text-amber-800',
                'under_review' => 'bg-blue-100 text-blue-800',
                'answered' => 'bg-emerald-100 text-emerald-800',
                'closed' => 'bg-gray-100 text-gray-600',
                'void' => 'bg-red-100 text-red-800',
            ];
            $priorityColors = [
                'low' => 'bg-gray-100 text-gray-600',
                'medium' => 'bg-blue-100 text-blue-700',
                'high' => 'bg-orange-100 text-orange-800',
                'urgent' => 'bg-red-100 text-red-800',
            ];
        @endphp
        <span
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$rfi->status] ?? 'bg-gray-100 text-gray-700' }}">
            Status: {{ \App\Models\Rfi::$statuses[$rfi->status] ?? $rfi->status }}
        </span>
        <span
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $priorityColors[$rfi->priority] ?? 'bg-gray-100 text-gray-700' }}">
            Priority: {{ \App\Models\Rfi::$priorities[$rfi->priority] ?? $rfi->priority }}
        </span>
        @if($rfi->due_date)
            <span
                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium
                    {{ $rfi->due_date->isPast() && !in_array($rfi->status, ['answered', 'closed']) ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-600' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                Due: {{ $rfi->due_date->format('M d, Y') }}
            </span>
        @endif
    </div>

    {{-- Meta grid --}}
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider mb-1">Raised By</span>
            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $rfi->submitter?->name ?? '—' }}</span>
        </div>
        <div>
            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider mb-1">Assigned
                To</span>
            <span
                class="font-medium text-gray-900 dark:text-gray-100">{{ $rfi->assignee?->name ?? 'Unassigned' }}</span>
        </div>
        <div>
            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider mb-1">Cost
                Impact</span>
            <span
                class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($rfi->cost_impact ?? 'Unknown') }}</span>
        </div>
        <div>
            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider mb-1">Schedule
                Impact</span>
            <span
                class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($rfi->schedule_impact ?? 'Unknown') }}</span>
        </div>
        <div>
            <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider mb-1">Created</span>
            <span
                class="font-medium text-gray-900 dark:text-gray-100">{{ $rfi->created_at->format('M d, Y g:i A') }}</span>
        </div>
        @if($rfi->answered_at)
            <div>
                <span class="text-gray-500 dark:text-gray-400 block text-xs uppercase tracking-wider mb-1">Answered</span>
                <span
                    class="font-medium text-gray-900 dark:text-gray-100">{{ $rfi->answered_at->format('M d, Y g:i A') }}</span>
            </div>
        @endif
    </div>

    {{-- Question --}}
    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
        <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Question</h4>
        <div class="prose prose-sm max-w-none dark:prose-invert">
            {!! $rfi->question !!}
        </div>
    </div>

    {{-- Answer --}}
    @if($rfi->answer)
        <div
            class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-800/50">
            <h4 class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider mb-2">Response
            </h4>
            <div class="prose prose-sm max-w-none dark:prose-invert">
                {!! $rfi->answer !!}
            </div>
        </div>
    @endif

    {{-- Response time --}}
    @if($rfi->answered_at && $rfi->created_at)
        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Response time: {{ $rfi->created_at->diffInDays($rfi->answered_at) }} days
        </div>
    @endif
</div>