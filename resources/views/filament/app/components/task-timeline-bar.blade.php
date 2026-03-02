@php
    $record = $getRecord();
    $progress = (int) ($record->progress_percent ?? 0);
    $startDate = $record->start_date;
    $dueDate = $record->due_date;
    $status = $record->status ?? '';

    // Calculate % of time elapsed
    $timeElapsed = 0;
    if ($startDate && $dueDate) {
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($dueDate);
        $totalDays = max(1, $start->diffInDays($end));
        $daysSpent = max(0, $start->diffInDays(now()));
        $timeElapsed = min(100, round(($daysSpent / $totalDays) * 100));
    }

    // Determine bar state
    $isDone = in_array($status, ['done', 'cancelled']);
    $isOverdue = $dueDate && \Carbon\Carbon::parse($dueDate)->isPast() && !$isDone;

    // Variance: positive = ahead, negative = behind
    $variance = $progress - $timeElapsed;

    // Color logic: green if on track/ahead, brown/orange if behind, gray if not started
    $completedColor = $isDone ? '#10b981' : ($progress > 0 ? '#22c55e' : '#d1d5db');
    $remainingColor = $isOverdue ? '#c2410c' : '#e5e7eb';

    // Dark mode remaining
    $darkRemainingColor = $isOverdue ? '#9a3412' : 'rgba(255,255,255,0.08)';

    // Tooltip
    $tooltip = "{$progress}% complete";
    if ($startDate && $dueDate) {
        $tooltip .= " · {$timeElapsed}% time elapsed";
        if (!$isDone) {
            $tooltip .= ' · ' . ($variance >= 0 ? "▲ {$variance}% ahead" : "▼ " . abs($variance) . "% behind");
        }
    }
@endphp

<div title="{{ $tooltip }}" style="min-width: 120px; cursor: default;">
    {{-- Progress bar --}}
    <div style="position: relative; height: 14px; border-radius: 7px; overflow: hidden; background: {{ $remainingColor }};"
        class="dark:!bg-[{{ $darkRemainingColor }}]">
        {{-- Completed portion (green) --}}
        <div style="
            position: absolute; top: 0; left: 0; bottom: 0;
            width: {{ $progress }}%;
            background: {{ $completedColor }};
            border-radius: 7px 0 0 7px;
            transition: width 0.3s ease;
        "></div>

        {{-- Time elapsed marker (red line) --}}
        @if($startDate && $dueDate && !$isDone && $timeElapsed > 0 && $timeElapsed < 100)
            <div style="
                    position: absolute; top: -2px; bottom: -2px;
                    left: {{ $timeElapsed }}%;
                    width: 2px;
                    background: #ef4444;
                    z-index: 2;
                    box-shadow: 0 0 3px rgba(239,68,68,0.5);
                "></div>
        @endif
    </div>

    {{-- Labels row --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2px;">
        <span
            style="font-size: 10px; font-weight: 700; color: {{ $isDone ? '#10b981' : ($isOverdue ? '#dc2626' : '#6b7280') }};">
            {{ $progress }}%
        </span>
        @if($startDate && $dueDate && !$isDone)
            @if($variance < 0)
                <span style="font-size: 9px; font-weight: 600; color: #dc2626; letter-spacing: -0.3px;">
                    ▼{{ abs($variance) }}%
                </span>
            @elseif($variance > 0)
                <span style="font-size: 9px; font-weight: 600; color: #10b981; letter-spacing: -0.3px;">
                    ▲{{ $variance }}%
                </span>
            @else
                <span style="font-size: 9px; font-weight: 600; color: #6b7280; letter-spacing: -0.3px;">
                    On track
                </span>
            @endif
        @endif
    </div>
</div>