<x-filament-panels::page>
    {{-- ═══════════════ MODULE SUB-TABS ═══════════════ --}}
    @php $schedStats = $this->getScheduleStats(); @endphp
    <div class="schedule-module-tabs">
        <button class="smt-btn {{ $activeTab === 'schedule' ? 'active' : '' }}" data-module="schedule"
            onclick="switchModuleTab('schedule')" wire:click="$set('activeTab','schedule')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="4" rx="1" />
                <rect x="3" y="10" width="12" height="4" rx="1" />
                <rect x="3" y="16" width="15" height="4" rx="1" />
            </svg>
            Schedule <span class="smt-badge">{{ $schedStats['tasks_total'] }}</span>
        </button>
        <button class="smt-btn {{ $activeTab === 'work_orders' ? 'active' : '' }}" data-module="work_orders"
            onclick="switchModuleTab('work_orders')" wire:click="$set('activeTab','work_orders')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63" />
            </svg>
            Work Orders <span class="smt-badge wo">{{ $schedStats['wo_total'] }}</span>
        </button>
        <button class="smt-btn {{ $activeTab === 'milestones' ? 'active' : '' }}" data-module="milestones"
            onclick="switchModuleTab('milestones')" wire:click="$set('activeTab','milestones')">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2l3 9h9l-7 5 3 9-8-6-8 6 3-9-7-5h9z" />
            </svg>
            Milestones <span class="smt-badge ms">{{ $schedStats['ms_total'] }}</span>
        </button>
    </div>

    {{-- ═══════════════ ACTION BUTTONS ═══════════════ --}}
    <div
        style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:12px; width:100%;">
        <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
            <button wire:click="openTaskModal" type="button"
                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;border:none;background:#4f46e5;color:white;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="16" />
                    <line x1="8" y1="12" x2="16" y2="12" />
                </svg>
                New Task
            </button>
            <button wire:click="openMilestoneModal" type="button"
                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;border:none;background:#f59e0b;color:white;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3 9h9l-7 5 3 9-8-6-8 6 3-9-7-5h9z" />
                </svg>
                Add Milestone
            </button>
            <button wire:click="doSaveBaseline"
                wire:confirm="This will snapshot all current task dates as the baseline. Continue?" type="button"
                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;border:1px solid var(--c-300,#d1d5db);background:var(--c-50,#f9fafb);color:var(--c-700,#374151);font-size:13px;font-weight:600;cursor:pointer;transition:all .15s">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z" />
                    <circle cx="12" cy="13" r="4" />
                </svg>
                Save Baseline
            </button>
            <button wire:click="doRebuildWbs"
                wire:confirm="Regenerate all WBS codes based on the current task hierarchy and sort order?"
                type="button"
                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;border:1px solid var(--c-300,#d1d5db);background:var(--c-50,#f9fafb);color:var(--c-700,#374151);font-size:13px;font-weight:600;cursor:pointer;transition:all .15s">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 4v6h6M23 20v-6h-6" />
                    <path d="M20.49 9A9 9 0 005.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 013.51 15" />
                </svg>
                Rebuild WBS
            </button>
            <div wire:loading wire:target="submitNewTask,submitMilestone,doSaveBaseline,doRebuildWbs"
                style="display:inline-flex;align-items:center;gap:6px;color:#6366f1;font-size:12px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    style="animation:spin 1s linear infinite">
                    <path d="M21 12a9 9 0 11-6.219-8.56" />
                </svg>
                Processing...
            </div>
        </div>
    </div>

    {{-- ═══════════════ SCHEDULE TAB ═══════════════ --}}
    <div id="scheduleModule" class="module-content" style="display:{{ $activeTab === 'schedule' ? 'block' : 'none' }};">
        <div class="gantt-toolbar"
            style="display:flex; align-items:center; flex-wrap:wrap; gap:8px; padding-bottom:8px; border-bottom:1px solid var(--c-200,#e5e7eb); margin-bottom:8px;">
            <div class="gantt-view-switcher">
                <button class="gantt-view-btn active" data-view="gantt" title="Gantt Chart">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="4" rx="1" />
                        <rect x="3" y="10" width="12" height="4" rx="1" />
                        <rect x="3" y="16" width="15" height="4" rx="1" />
                    </svg>
                    Gantt
                </button>
                <button class="gantt-view-btn" data-view="table" title="Table View">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" />
                        <line x1="3" y1="9" x2="21" y2="9" />
                        <line x1="3" y1="15" x2="21" y2="15" />
                        <line x1="9" y1="3" x2="9" y2="21" />
                    </svg>
                    Table
                </button>
                <button class="gantt-view-btn" data-view="board" title="Board View">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="5" height="18" rx="1" />
                        <rect x="10" y="3" width="5" height="12" rx="1" />
                        <rect x="17" y="3" width="5" height="15" rx="1" />
                    </svg>
                    Board
                </button>
            </div>

            <div style="width:1px;height:20px;background:var(--c-300,#d1d5db)"></div>

            <div class="gantt-indent-controls" style="display:flex;gap:2px;">
                <button class="gantt-indent-btn"
                    onclick="if(window.selectedTaskId) @this.outdentTask(window.selectedTaskId)" title="Outdent"
                    style="background:none;border:none;cursor:pointer;padding:3px;border-radius:4px;color:var(--c-600,#4b5563)"
                    onmouseover="this.style.background='var(--c-200,#e5e7eb)'"
                    onmouseout="this.style.background='none'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M11 18h10M7 6h14M11 12h10M3 12l4-4v8z" />
                    </svg>
                </button>
                <button class="gantt-indent-btn"
                    onclick="if(window.selectedTaskId) @this.indentTask(window.selectedTaskId)" title="Indent"
                    style="background:none;border:none;cursor:pointer;padding:3px;border-radius:4px;color:var(--c-600,#4b5563)"
                    onmouseover="this.style.background='var(--c-200,#e5e7eb)'"
                    onmouseout="this.style.background='none'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M13 18h8M9 6h12M13 12h8M7 12l-4-4v8z" />
                    </svg>
                </button>
            </div>

            <div style="width:1px;height:20px;background:var(--c-300,#d1d5db)"></div>

            <label
                style="display:flex;align-items:center;gap:4px;font-size:12px;cursor:pointer;font-weight:500;color:var(--c-600,#4b5563);white-space:nowrap"
                title="Toggle progress bars on Gantt bars">
                <input type="checkbox" id="showProgress" checked
                    onchange="window.ganttToggle('showProgress', this.checked)"
                    style="cursor:pointer;accent-color:#4f46e5;width:14px;height:14px;">
                <div style="width:10px;height:3px;background:#4f46e5;border-radius:2px"></div>Progress
            </label>
            <label
                style="display:flex;align-items:center;gap:4px;font-size:12px;cursor:pointer;font-weight:500;color:var(--c-600,#4b5563);white-space:nowrap"
                title="Highlight critical path tasks">
                <input type="checkbox" id="showCritical" onchange="window.ganttToggle('showCritical', this.checked)"
                    style="cursor:pointer;accent-color:#ef4444;width:14px;height:14px;">
                <div style="width:10px;height:3px;background:#ef4444;border-radius:2px"></div>Critical
            </label>
            <label
                style="display:flex;align-items:center;gap:4px;font-size:12px;cursor:pointer;font-weight:500;color:var(--c-600,#4b5563);white-space:nowrap"
                title="Show baseline comparison bars">
                <input type="checkbox" id="showBaseline" onchange="window.ganttToggle('showBaseline', this.checked)"
                    style="cursor:pointer;accent-color:#9ca3af;width:14px;height:14px;">
                <div style="width:10px;height:3px;background:#d1d5db;border-radius:2px"></div>Baseline
            </label>

            <div style="width:1px;height:20px;background:var(--c-300,#d1d5db)"></div>

            <div class="gantt-search-wrapper" style="position:relative; flex-grow:1; max-width:200px; min-width:120px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    style="position:absolute;left:6px;top:50%;transform:translateY(-50%);color:var(--c-400,#94a3b8);pointer-events:none">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
                <input type="text" id="ganttSearch" class="gantt-search-input" placeholder="Search..."
                    style="padding-left:24px; border-radius:6px; border:1px solid var(--c-300,#d1d5db); padding-top:4px; padding-bottom:4px; font-size:12px; outline:none; transition:box-shadow .2s; width:100%;" />
            </div>
            <span class="gantt-task-count" id="ganttTaskCount"
                style="font-size:11px; color:var(--c-500,#64748b); font-weight:600;"></span>

            <div style="margin-left:auto; display:flex; align-items:center; gap:6px;">
                <div class="gantt-zoom-controls">
                    <button class="gantt-zoom-btn" data-zoom="day">Days</button>
                    <button class="gantt-zoom-btn active" data-zoom="week">Weeks</button>
                    <button class="gantt-zoom-btn" data-zoom="month">Months</button>
                    <button class="gantt-zoom-btn" data-zoom="quarter">Quarters</button>
                </div>
                <div style="width:1px;height:20px;background:var(--c-300,#d1d5db)"></div>
                <button onclick="window.toggleGanttFullscreen()" class="gantt-fullscreen-btn" title="Toggle Fullscreen"
                    style="display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;border:1px solid var(--c-300,#d1d5db);background:white;color:var(--c-600,#4b5563);cursor:pointer;transition:all .2s;">
                    <svg id="fsExpandIcon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <polyline points="15 3 21 3 21 9" />
                        <polyline points="9 21 3 21 3 15" />
                        <line x1="21" y1="3" x2="14" y2="10" />
                        <line x1="3" y1="21" x2="10" y2="14" />
                    </svg>
                    <svg id="fsCollapseIcon" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" style="display:none;">
                        <polyline points="4 14 10 14 10 20" />
                        <polyline points="20 10 14 10 14 4" />
                        <line x1="14" y1="10" x2="21" y2="3" />
                        <line x1="3" y1="21" x2="10" y2="14" />
                    </svg>
                </button>
            </div>
        </div>

        @include('filament.app.pages.modules.partials.stat-cards', ['stats' => $this->getStats()])

        <div id="ganttView" class="schedule-view active-view">
            <div class="gantt-container">
                <div class="gantt-split" id="ganttSplit">
                    <div class="gantt-left" id="ganttLeft">
                        <div class="gantt-table-header" id="ganttTableHeader">
                            <div class="gantt-th gantt-col-wbs">#</div>
                            <div class="gantt-th gantt-col-indicator"></div>
                            <div class="gantt-th gantt-col-name">Task Name</div>
                            <div class="gantt-th gantt-col-duration">Duration</div>
                            <div class="gantt-th gantt-col-start">Start</div>
                            <div class="gantt-th gantt-col-finish">Finish</div>
                            <div class="gantt-th gantt-col-pct">%</div>
                            <div class="gantt-th gantt-col-predecessors">Predecessors</div>
                            <div class="gantt-th gantt-col-resource">Resource Names</div>
                        </div>
                        <div class="gantt-table-body" id="ganttTableBody"></div>
                    </div>
                    <div class="gantt-splitter" id="ganttSplitter"></div>
                    <div class="gantt-right" id="ganttRight">
                        <div class="gantt-chart-header" id="ganttChartHeader"></div>
                        <div class="gantt-chart-body" id="ganttChartBody"></div>
                        <div class="gantt-today-line" id="ganttTodayLine"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="tableView" class="schedule-view">
            {{ $this->table }}
        </div>
        <div id="boardView" class="schedule-view">
            <div class="kanban-board" id="kanbanBoard"></div>
        </div>
    </div>

    {{-- ═══════════════ WORK ORDERS TAB ═══════════════ --}}
    @php $woData = $this->getWorkOrdersData(); @endphp
    <div id="workOrdersModule" class="module-content"
        style="display:{{ $activeTab === 'work_orders' ? 'block' : 'none' }};">
        {{-- Toolbar --}}
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;flex-wrap:wrap">
            <div style="position:relative;flex:1;min-width:200px;max-width:320px">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#9ca3af"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="woSearch" placeholder="Search work orders..."
                    style="width:100%;padding:7px 10px 7px 32px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none">
            </div>
            @php $woStatuses = ['' => 'All', 'pending' => 'Pending', 'approved' => 'Approved', 'in_progress' => 'In Progress', 'on_hold' => 'On Hold', 'completed' => 'Completed', 'cancelled' => 'Cancelled']; @endphp
            <div style="display:flex;gap:4px;flex-wrap:wrap">
                @foreach($woStatuses as $val => $lbl)
                    <button wire:click="$set('woStatusFilter','{{ $val }}')"
                        style="padding:4px 12px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid {{ $woStatusFilter === $val ? '#4f46e5' : '#e5e7eb' }};background:{{ $woStatusFilter === $val ? '#4f46e5' : 'white' }};color:{{ $woStatusFilter === $val ? 'white' : '#6b7280' }};cursor:pointer">{{ $lbl }}</button>
                @endforeach
            </div>
            <span style="font-size:12px;color:#9ca3af;margin-left:auto">{{ $woData['total'] }} total</span>
        </div>

        {{-- Table --}}
        @if(count($woData['data']) > 0)
            <div style="overflow-x:auto;border:1px solid #e5e7eb;border-radius:10px">
                <table style="width:100%;border-collapse:collapse;font-size:13px">
                    <thead>
                        <tr style="background:#f9fafb;border-bottom:2px solid #e5e7eb">
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                WO #</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                Title</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                Status</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                Priority</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                Assignee</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                Due Date</th>
                            <th
                                style="padding:10px 12px;text-align:right;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                Cost</th>
                            <th
                                style="padding:10px 12px;text-align:center;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($woData['data'] as $wo)
                            @php
                                $sc = match ($wo['status']) { 'in_progress' => '#3b82f6', 'approved' => '#6366f1', 'pending' => '#94a3b8', 'on_hold' => '#f59e0b', 'completed' => '#059669', 'cancelled' => '#ef4444', default => '#94a3b8'};
                                $pi = match ($wo['priority'] ?? '') { 'urgent' => '🔴', 'high' => '🟠', 'medium' => '🔵', 'low' => '⚪', default => ''};
                            @endphp
                            <tr style="border-bottom:1px solid #f3f4f6;{{ $wo['is_overdue'] ? 'background:#fef2f2' : '' }}"
                                onmouseover="this.style.background='#f9fafb'"
                                onmouseout="this.style.background='{{ $wo['is_overdue'] ? '#fef2f2' : '' }}'">
                                <td style="padding:10px 12px;font-family:monospace;font-size:12px;color:#6b7280">
                                    {{ $wo['wo_number'] ?? '—' }}</td>
                                <td
                                    style="padding:10px 12px;font-weight:600;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                    {{ $wo['title'] }}
                                    @if($wo['type'])<span style="font-size:10px;color:#9ca3af;font-weight:400;margin-left:4px">·
                                    {{ $wo['type'] }}</span>@endif
                                </td>
                                <td style="padding:10px 12px"><span
                                        style="padding:3px 10px;border-radius:10px;font-size:11px;font-weight:600;background:{{ $sc }}15;color:{{ $sc }}">{{ str_replace('_', ' ', ucfirst($wo['status'])) }}</span>
                                </td>
                                <td style="padding:10px 12px;font-size:12px">{{ $pi }} {{ ucfirst($wo['priority'] ?? '') }}</td>
                                <td style="padding:10px 12px;font-size:12px;color:#6b7280">{{ $wo['assignee'] ?? '—' }}</td>
                                <td style="padding:10px 12px;font-size:12px">
                                    {{ $wo['due_date'] ?? '—' }}
                                    @if($wo['is_overdue'])<span
                                        style="color:#ef4444;font-weight:600;font-size:10px;margin-left:4px">⚠
                                    {{ abs($wo['days_until_due']) }}d late</span>@endif
                                </td>
                                <td style="padding:10px 12px;text-align:right;font-weight:700;color:#4f46e5">
                                    {{ \App\Support\CurrencyHelper::format($wo['items_cost'] ?? 0) }}</td>
                                <td style="padding:10px 12px;text-align:center"><span
                                        style="padding:2px 8px;border-radius:8px;background:#f1f5f9;font-size:11px;font-weight:600">{{ $wo['items_count'] }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($woData['pages'] > 1)
                <div
                    style="display:flex;align-items:center;justify-content:space-between;margin-top:12px;font-size:12px;color:#6b7280">
                    <span>Showing
                        {{ ($woData['page'] - 1) * $woData['per_page'] + 1 }}–{{ min($woData['page'] * $woData['per_page'], $woData['total']) }}
                        of {{ $woData['total'] }}</span>
                    <div style="display:flex;gap:4px">
                        <button wire:click="woGoPage({{ max(1, $woData['page'] - 1) }})" @if($woData['page'] <= 1) disabled @endif
                            style="padding:4px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;cursor:pointer;background:white{{ $woData['page'] <= 1 ? ';opacity:.4' : '' }}">←
                            Prev</button>
                        @for($p = max(1, $woData['page'] - 2); $p <= min($woData['pages'], $woData['page'] + 2); $p++)
                            <button wire:click="woGoPage({{ $p }})"
                                style="padding:4px 10px;border-radius:6px;font-size:12px;cursor:pointer;border:1px solid {{ $p == $woData['page'] ? '#4f46e5' : '#e5e7eb' }};background:{{ $p == $woData['page'] ? '#4f46e5' : 'white' }};color:{{ $p == $woData['page'] ? 'white' : '#6b7280' }}">{{ $p }}</button>
                        @endfor
                        <button wire:click="woGoPage({{ min($woData['pages'], $woData['page'] + 1) }})"
                            @if($woData['page'] >= $woData['pages']) disabled @endif
                            style="padding:4px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;cursor:pointer;background:white{{ $woData['page'] >= $woData['pages'] ? ';opacity:.4' : '' }}">Next
                            →</button>
                    </div>
                </div>
            @endif
        @else
            <div style="text-align:center;padding:60px 20px;color:#9ca3af">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                    style="margin:0 auto 12px">
                    <path
                        d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63" />
                </svg>
                <div style="font-weight:700;font-size:15px;color:#6b7280">
                    {{ $woSearch || $woStatusFilter ? 'No matching work orders' : 'No Work Orders' }}</div>
                <div style="font-size:13px;margin-top:6px">
                    {{ $woSearch || $woStatusFilter ? 'Try a different search or filter.' : 'Create work orders from the main module.' }}
                </div>
            </div>
        @endif
    </div>

    {{-- ═══════════════ MILESTONES TAB ═══════════════ --}}
    @php $msData = $this->getMilestonesData(); @endphp
    <div id="milestonesModule" class="module-content"
        style="display:{{ $activeTab === 'milestones' ? 'block' : 'none' }};">
        {{-- Toolbar --}}
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;flex-wrap:wrap">
            <div style="position:relative;flex:1;min-width:200px;max-width:320px">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#9ca3af"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="msSearch" placeholder="Search milestones..."
                    style="width:100%;padding:7px 10px 7px 32px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none">
            </div>
            @php $msStatuses = ['' => 'All', 'in_progress' => 'In Progress', 'pending' => 'Pending', 'delayed' => 'Delayed', 'completed' => 'Completed', 'cancelled' => 'Cancelled']; @endphp
            <div style="display:flex;gap:4px;flex-wrap:wrap">
                @foreach($msStatuses as $val => $lbl)
                    <button wire:click="$set('msStatusFilter','{{ $val }}')"
                        style="padding:4px 12px;border-radius:6px;font-size:12px;font-weight:600;border:1px solid {{ $msStatusFilter === $val ? '#4f46e5' : '#e5e7eb' }};background:{{ $msStatusFilter === $val ? '#4f46e5' : 'white' }};color:{{ $msStatusFilter === $val ? 'white' : '#6b7280' }};cursor:pointer">{{ $lbl }}</button>
                @endforeach
            </div>
            <span style="font-size:12px;color:#9ca3af;margin-left:auto">{{ $msData['total'] }} total</span>
        </div>

        {{-- Table --}}
        @if(count($msData['data']) > 0)
            <div style="overflow-x:auto;border:1px solid #e5e7eb;border-radius:10px">
                <table style="width:100%;border-collapse:collapse;font-size:13px">
                    <thead>
                        <tr style="background:#f9fafb;border-bottom:2px solid #e5e7eb">
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                Milestone</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                Status</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                Priority</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                Target Date</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                Actual Date</th>
                            <th
                                style="padding:10px 12px;text-align:left;font-weight:700;font-size:11px;text-transform:uppercase;letter-spacing:.5px;color:#6b7280">
                                Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($msData['data'] as $ms)
                            @php
                                $msc = match ($ms['status']) { 'in_progress' => '#6366f1', 'pending' => '#94a3b8', 'delayed' => '#ef4444', 'completed' => '#059669', 'cancelled' => '#6b7280', default => '#94a3b8'};
                                $mpc = match ($ms['priority'] ?? '') { 'critical' => '#ef4444', 'high' => '#f59e0b', 'medium' => '#6366f1', 'low' => '#94a3b8', default => '#94a3b8'};
                            @endphp
                            <tr style="border-bottom:1px solid #f3f4f6;{{ $ms['is_overdue'] ? 'background:#fef2f2' : '' }}"
                                onmouseover="this.style.background='#f9fafb'"
                                onmouseout="this.style.background='{{ $ms['is_overdue'] ? '#fef2f2' : '' }}'">
                                <td style="padding:10px 12px">
                                    <div style="display:flex;align-items:center;gap:6px">
                                        <span style="color:{{ $msc }};font-size:12px">◆</span>
                                        <span style="font-weight:600">{{ $ms['name'] }}</span>
                                    </div>
                                    @if($ms['description'])
                                        <div
                                            style="font-size:11px;color:#9ca3af;margin-top:2px;margin-left:18px;max-width:320px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                    {{ $ms['description'] }}</div>@endif
                                </td>
                                <td style="padding:10px 12px"><span
                                        style="padding:3px 10px;border-radius:10px;font-size:11px;font-weight:600;background:{{ $msc }}15;color:{{ $msc }}">{{ str_replace('_', ' ', ucfirst($ms['status'])) }}</span>
                                </td>
                                <td style="padding:10px 12px"><span
                                        style="padding:2px 8px;border-radius:8px;font-size:11px;font-weight:600;background:{{ $mpc }}12;color:{{ $mpc }}">{{ ucfirst($ms['priority'] ?? 'low') }}</span>
                                </td>
                                <td style="padding:10px 12px;font-size:12px">{{ $ms['target_date'] ?? '—' }}</td>
                                <td style="padding:10px 12px;font-size:12px">{{ $ms['actual_date'] ?? '—' }}</td>
                                <td style="padding:10px 12px;font-size:12px">
                                    @if($ms['days_remaining'] !== null)
                                        @if($ms['days_remaining'] < 0)
                                            <span style="color:#ef4444;font-weight:600">⚠ {{ abs($ms['days_remaining']) }}d
                                                overdue</span>
                                        @elseif($ms['days_remaining'] === 0)
                                            <span style="color:#f59e0b;font-weight:600">📌 Due today</span>
                                        @else
                                            <span style="color:#059669">{{ $ms['days_remaining'] }}d left</span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($msData['pages'] > 1)
                <div
                    style="display:flex;align-items:center;justify-content:space-between;margin-top:12px;font-size:12px;color:#6b7280">
                    <span>Showing
                        {{ ($msData['page'] - 1) * $msData['per_page'] + 1 }}–{{ min($msData['page'] * $msData['per_page'], $msData['total']) }}
                        of {{ $msData['total'] }}</span>
                    <div style="display:flex;gap:4px">
                        <button wire:click="msGoPage({{ max(1, $msData['page'] - 1) }})" @if($msData['page'] <= 1) disabled @endif
                            style="padding:4px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;cursor:pointer;background:white{{ $msData['page'] <= 1 ? ';opacity:.4' : '' }}">←
                            Prev</button>
                        @for($p = max(1, $msData['page'] - 2); $p <= min($msData['pages'], $msData['page'] + 2); $p++)
                            <button wire:click="msGoPage({{ $p }})"
                                style="padding:4px 10px;border-radius:6px;font-size:12px;cursor:pointer;border:1px solid {{ $p == $msData['page'] ? '#4f46e5' : '#e5e7eb' }};background:{{ $p == $msData['page'] ? '#4f46e5' : 'white' }};color:{{ $p == $msData['page'] ? 'white' : '#6b7280' }}">{{ $p }}</button>
                        @endfor
                        <button wire:click="msGoPage({{ min($msData['pages'], $msData['page'] + 1) }})"
                            @if($msData['page'] >= $msData['pages']) disabled @endif
                            style="padding:4px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;cursor:pointer;background:white{{ $msData['page'] >= $msData['pages'] ? ';opacity:.4' : '' }}">Next
                            →</button>
                    </div>
                </div>
            @endif
        @else
            <div style="text-align:center;padding:60px 20px;color:#9ca3af">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor"
                    style="margin:0 auto 12px;color:#f59e0b">
                    <path d="M12 2l3 9h9l-7 5 3 9-8-6-8 6 3-9-7-5h9z" />
                </svg>
                <div style="font-weight:700;font-size:15px;color:#6b7280">
                    {{ $msSearch || $msStatusFilter ? 'No matching milestones' : 'No Milestones Yet' }}</div>
                <div style="font-size:13px;margin-top:6px">
                    {{ $msSearch || $msStatusFilter ? 'Try a different search or filter.' : 'Add milestones using the button above.' }}
                </div>
            </div>
        @endif
    </div>

    {{-- ═══════════════ TASK DETAIL PANEL ═══════════════ --}}
    <div class="gantt-detail-panel" id="taskDetailPanel" style="display:none;">
        <div class="gantt-detail-header">
            <h3 id="detailTitle">Task Details</h3>
            <button class="gantt-detail-close" onclick="closeDetailPanel()">&times;</button>
        </div>
        <div class="gantt-detail-body" id="detailBody"></div>
    </div>

    {{-- ═══════════════ STYLES ═══════════════ --}}
    <style>
        /* ── View Switching ── */
        .schedule-view {
            display: none;
        }

        .schedule-view.active-view {
            display: block;
        }

        /* ── Search & Count ── */
        .gantt-search-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .gantt-search-input {
            padding: 5px 8px 5px 28px;
            border: 1px solid var(--c-200, #e2e8f0);
            border-radius: 6px;
            font-size: 12px;
            width: 180px;
            background: white;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .gantt-search-input:focus {
            border-color: var(--primary-400, #818cf8);
            box-shadow: 0 0 0 2px var(--primary-100, #e0e7ff);
        }

        .dark .gantt-search-input {
            background: var(--c-800);
            border-color: var(--c-600);
            color: var(--c-200);
        }

        .gantt-task-count {
            font-size: 11px;
            color: var(--c-500, #64748b);
            white-space: nowrap;
        }

        /* ── Virtual Scrolling ── */
        .gantt-table-body,
        .gantt-chart-body {
            position: relative;
            overflow: hidden;
        }

        /* ── Module Sub-Tabs ── */
        .schedule-module-tabs {
            display: flex;
            gap: 4px;
            padding: 0 0 8px 0;
            border-bottom: 2px solid var(--c-200, #e2e8f0);
            margin-bottom: 8px;
        }

        .smt-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            color: var(--c-500, #64748b);
            border-radius: 8px 8px 0 0;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all .2s;
        }

        .smt-btn:hover {
            color: var(--c-700);
            background: var(--c-50, #f8fafc);
        }

        .smt-btn.active {
            color: var(--primary-600, #4f46e5);
            border-bottom-color: var(--primary-600, #4f46e5);
            background: var(--primary-50, #eef2ff);
        }

        .dark .smt-btn.active {
            color: var(--primary-400, #818cf8);
            border-bottom-color: var(--primary-400);
            background: rgba(99, 102, 241, .1);
        }

        .smt-badge {
            font-size: 10px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 10px;
            background: var(--c-200, #e2e8f0);
            color: var(--c-600, #475569);
        }

        .smt-btn.active .smt-badge {
            background: var(--primary-100, #e0e7ff);
            color: var(--primary-700, #4338ca);
        }

        .smt-badge.wo {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .smt-badge.ms {
            background: #fef3c7;
            color: #92400e;
        }

        /* ── WO Table ── */
        .wo-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .wo-table th {
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--c-500);
            background: var(--c-50);
            border-bottom: 2px solid var(--c-300);
        }

        .wo-table td {
            padding: 8px 10px;
            border-bottom: 1px solid var(--c-100);
            color: var(--c-700);
        }

        .dark .wo-table th {
            background: var(--c-900);
            border-color: var(--c-600);
            color: var(--c-400);
        }

        .dark .wo-table td {
            border-color: var(--c-700);
            color: var(--c-300);
        }

        .wo-table tr:hover td {
            background: var(--primary-50, #eef2ff);
        }

        .wo-status {
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .wo-table-wrap {
            border: 1px solid var(--c-200);
            border-radius: 10px;
            overflow: hidden;
            background: white;
        }

        .dark .wo-table-wrap {
            background: var(--c-800);
            border-color: var(--c-700);
        }

        /* ── Milestone Timeline ── */
        .ms-timeline-wrap {
            padding: 4px 0;
        }

        .ms-card {
            border: 1px solid var(--c-200);
            border-radius: 10px;
            padding: 14px 16px;
            background: white;
            transition: all .15s;
            cursor: pointer;
        }

        .dark .ms-card {
            background: var(--c-800);
            border-color: var(--c-700);
        }

        .ms-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
            transform: translateY(-1px);
        }

        .ms-card.overdue {
            border-left: 3px solid #ef4444;
        }

        .ms-card.completed {
            border-left: 3px solid #059669;
            opacity: .85;
        }

        .ms-card-title {
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .ms-card-meta {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: var(--c-500);
        }

        .ms-card-date {
            font-size: 12px;
            font-weight: 600;
            margin-top: 6px;
        }

        /* ── Toolbar ── */
        .gantt-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
            gap: 12px;
            flex-wrap: wrap;
        }

        .gantt-toolbar-left,
        .gantt-toolbar-center,
        .gantt-toolbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .gantt-view-switcher {
            display: flex;
            background: var(--c-50, #f8fafc);
            border-radius: 8px;
            padding: 2px;
            border: 1px solid var(--c-200, #e2e8f0);
        }

        .gantt-view-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 6px 14px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            color: var(--c-500, #64748b);
            border-radius: 6px;
            transition: all .2s;
        }

        .gantt-view-btn:hover {
            background: var(--c-100, #f1f5f9);
        }

        .gantt-view-btn.active {
            background: white;
            color: var(--primary-600, #4f46e5);
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
        }

        .dark .gantt-view-btn.active {
            background: var(--c-700, #334155);
            color: var(--primary-400, #818cf8);
        }

        .gantt-zoom-controls {
            display: flex;
            background: var(--c-50, #f8fafc);
            border-radius: 8px;
            padding: 2px;
            border: 1px solid var(--c-200, #e2e8f0);
        }

        .gantt-zoom-btn {
            padding: 4px 12px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
            color: var(--c-500, #64748b);
            border-radius: 6px;
            transition: all .15s;
        }

        .gantt-zoom-btn:hover {
            background: var(--c-100, #f1f5f9);
        }

        .gantt-zoom-btn.active {
            background: var(--primary-600, #4f46e5);
            color: white;
        }

        .gantt-toggle {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 11px;
            font-weight: 600;
            color: var(--c-600, #475569);
            cursor: pointer;
            user-select: none;
        }

        .gantt-toggle input {
            accent-color: var(--primary-600, #4f46e5);
        }

        /* ── Container ── */
        .gantt-container {
            border: 1px solid var(--c-200, #e2e8f0);
            border-radius: 12px;
            overflow: hidden;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
            margin-top: 8px;
        }

        .dark .gantt-container {
            background: var(--c-800, #1e293b);
            border-color: var(--c-700, #334155);
        }

        .gantt-split {
            display: flex;
            height: calc(100vh - 340px);
            min-height: 400px;
            position: relative;
        }

        /* ── Left Panel (Table) ── */
        .gantt-left {
            width: 520px;
            min-width: 300px;
            overflow-y: auto;
            overflow-x: auto;
            border-right: 1px solid var(--c-200, #e2e8f0);
            flex-shrink: 0;
        }

        .dark .gantt-left {
            border-color: var(--c-700, #334155);
        }

        .gantt-table-header {
            display: flex;
            position: sticky;
            top: 0;
            z-index: 10;
            background: var(--c-50, #f8fafc);
            border-bottom: 2px solid var(--c-300, #cbd5e1);
            min-width: max-content;
        }

        .dark .gantt-table-header {
            background: var(--c-900, #0f172a);
            border-color: var(--c-600, #475569);
        }

        .gantt-th {
            padding: 8px 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--c-500, #64748b);
            white-space: nowrap;
            border-right: 1px solid var(--c-200, #e2e8f0);
        }

        .dark .gantt-th {
            border-color: var(--c-700, #334155);
            color: var(--c-400, #94a3b8);
        }

        .gantt-col-wbs {
            width: 36px;
            text-align: center;
        }

        .gantt-col-indicator {
            width: 20px;
        }

        .gantt-col-name {
            width: 200px;
            min-width: 160px;
            flex: 1;
        }

        .gantt-col-duration {
            width: 60px;
            text-align: center;
        }

        .gantt-col-start {
            width: 90px;
        }

        .gantt-col-finish {
            width: 90px;
        }

        .gantt-col-pct {
            width: 70px;
            text-align: right;
        }

        .gantt-col-predecessors {
            width: 90px;
        }

        .gantt-col-resource {
            width: 150px;
        }

        .editable-cell {
            cursor: text;
            transition: background-color 0.15s;
        }

        .editable-cell:hover {
            background-color: rgba(79, 70, 229, 0.05);
        }

        .dark .editable-cell:hover {
            background-color: rgba(79, 70, 229, 0.15);
        }

        .inline-edit-input {
            background: white;
            color: #111827;
        }

        .dark .inline-edit-input {
            background: #374151;
            color: #f9fafb;
            border-color: #6366f1 !important;
        }

        .gantt-task-icon {
            width: 14px;
            height: 14px;
            margin-right: 6px;
            display: inline-block;
            vertical-align: -2px;
        }

        /* ── Table Rows ── */
        .gantt-table-body {
            min-width: max-content;
        }

        .gantt-row {
            display: flex;
            min-height: 32px;
            align-items: center;
            border-bottom: 1px solid var(--c-100, #f1f5f9);
            cursor: pointer;
            transition: background .1s;
        }

        .gantt-row:hover {
            background: var(--primary-50, #eef2ff);
        }

        .dark .gantt-row {
            border-color: var(--c-700, #334155);
        }

        .dark .gantt-row:hover {
            background: var(--c-700, #334155);
        }

        .gantt-row.selected {
            background: var(--primary-100, #e0e7ff);
        }

        .dark .gantt-row.selected {
            background: var(--primary-900, #312e81);
        }

        .gantt-row.summary {
            font-weight: 700;
            background: var(--c-50, #f8fafc);
        }

        .dark .gantt-row.summary {
            background: var(--c-800, #1e293b);
        }

        .gantt-row.milestone .gantt-td {
            font-style: italic;
        }

        .gantt-row.critical {
            border-left: 3px solid #ef4444;
        }

        .gantt-td {
            padding: 4px 6px;
            font-size: 12px;
            color: var(--c-700, #334155);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            border-right: 1px solid var(--c-100, #f1f5f9);
        }

        .dark .gantt-td {
            color: var(--c-300, #cbd5e1);
            border-color: var(--c-700, #334155);
        }

        .gantt-td.gantt-col-wbs {
            font-size: 10px;
            color: var(--c-400, #94a3b8);
            text-align: center;
            font-family: monospace;
        }

        .gantt-td.gantt-col-name {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .gantt-indent-toggle {
            width: 16px;
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 3px;
            font-size: 10px;
            color: var(--c-500);
            flex-shrink: 0;
        }

        .gantt-indent-toggle:hover {
            background: var(--c-200);
        }

        .gantt-task-icon {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        .gantt-task-icon.milestone {
            color: #f59e0b;
        }

        .gantt-task-icon.summary {
            color: var(--primary-600, #4f46e5);
        }

        .gantt-td.gantt-col-pct {
            font-family: monospace;
            font-weight: 600;
            font-size: 11px;
            text-align: center;
        }

        /* ── Splitter ── */
        .gantt-splitter {
            width: 5px;
            cursor: col-resize;
            background: var(--c-200, #e2e8f0);
            transition: background .2s;
            flex-shrink: 0;
            z-index: 5;
        }

        .gantt-splitter:hover,
        .gantt-splitter.dragging {
            background: var(--primary-500, #6366f1);
        }

        .dark .gantt-splitter {
            background: var(--c-600, #475569);
        }

        /* ── Right Panel (Chart) ── */
        .gantt-right {
            flex: 1;
            overflow: auto;
            position: relative;
        }

        .gantt-chart-header {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            flex-direction: column;
            background: var(--c-50, #f8fafc);
            border-bottom: 2px solid var(--c-300, #cbd5e1);
        }

        .dark .gantt-chart-header {
            background: var(--c-900, #0f172a);
            border-color: var(--c-600, #475569);
        }

        .gantt-header-row {
            display: flex;
        }

        .gantt-header-cell {
            padding: 4px 0;
            text-align: center;
            font-size: 10px;
            font-weight: 600;
            color: var(--c-500, #64748b);
            border-right: 1px solid var(--c-200, #e2e8f0);
            box-sizing: border-box;
        }

        .dark .gantt-header-cell {
            border-color: var(--c-700, #334155);
            color: var(--c-400, #94a3b8);
        }

        .gantt-header-cell.primary {
            font-size: 11px;
            font-weight: 700;
            color: var(--c-700, #334155);
            border-bottom: 1px solid var(--c-200, #e2e8f0);
        }

        .dark .gantt-header-cell.primary {
            color: var(--c-300, #cbd5e1);
        }

        .gantt-header-cell.weekend {
            background: rgba(239, 68, 68, .04);
        }

        .gantt-header-cell.today {
            background: rgba(99, 102, 241, .08);
            font-weight: 800;
            color: var(--primary-600, #4f46e5);
        }

        /* ── Gantt Bars ── */
        .gantt-chart-body {
            position: relative;
        }

        .gantt-chart-row {
            height: 32px;
            position: relative;
            border-bottom: 1px solid var(--c-100, #f1f5f9);
        }

        .dark .gantt-chart-row {
            border-color: var(--c-700, #334155);
        }

        .gantt-chart-row.weekend-col {
            background: rgba(239, 68, 68, .02);
        }

        .gantt-bar {
            position: absolute;
            top: 6px;
            height: 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: filter .2s, box-shadow .2s;
            display: flex;
            align-items: center;
            z-index: 2;
            min-width: 4px;
        }

        .gantt-bar:hover {
            filter: brightness(1.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
            z-index: 3;
        }

        .gantt-bar.normal {
            background: var(--primary-500, #6366f1);
        }

        .gantt-bar.summary-bar {
            background: var(--c-700, #334155);
            height: 10px;
            top: 11px;
            border-radius: 0;
        }

        .gantt-bar.summary-bar::before,
        .gantt-bar.summary-bar::after {
            content: '';
            position: absolute;
            bottom: -5px;
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid var(--c-700, #334155);
        }

        .gantt-bar.summary-bar::before {
            left: 0;
        }

        .gantt-bar.summary-bar::after {
            right: 0;
        }

        .gantt-bar.milestone-bar {
            width: 14px !important;
            height: 14px;
            top: 9px;
            background: transparent;
            border-radius: 0;
            transform: rotate(45deg);
            border: 2px solid #f59e0b;
        }

        .gantt-bar.critical-bar {
            background: #ef4444;
        }

        /* Progress fill inside bar */
        .gantt-bar-progress {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            border-radius: 4px 0 0 4px;
            background: rgba(255, 255, 255, .3);
            transition: width .3s;
        }

        .gantt-bar-progress.full {
            border-radius: 4px;
        }

        .gantt-resize-handle {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            cursor: col-resize;
            border-radius: 0 4px 4px 0;
            background: transparent;
            transition: background .15s;
            z-index: 3;
        }

        .gantt-bar:hover .gantt-resize-handle {
            background: rgba(255, 255, 255, .45);
        }

        .gantt-resize-handle:hover {
            background: rgba(59, 130, 246, .6) !important;
        }

        .gantt-bar-label {
            position: absolute;
            right: -4px;
            transform: translateX(100%);
            font-size: 10px;
            font-weight: 600;
            color: var(--c-600, #475569);
            white-space: nowrap;
            padding-left: 4px;
        }

        .dark .gantt-bar-label {
            color: var(--c-400, #94a3b8);
        }

        /* Baseline bar */
        .gantt-baseline-bar {
            position: absolute;
            top: 24px;
            height: 4px;
            border-radius: 2px;
            background: var(--c-400, #94a3b8);
            opacity: .5;
            z-index: 1;
        }

        /* Dependency arrows */
        .gantt-dep-line {
            position: absolute;
            z-index: 1;
            pointer-events: none;
        }

        .gantt-dep-line line {
            stroke: var(--c-400, #94a3b8);
            stroke-width: 1.5;
        }

        .gantt-dep-line polygon {
            fill: var(--c-400, #94a3b8);
        }

        /* Today line */
        .gantt-today-line {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #ef4444;
            z-index: 4;
            pointer-events: none;
        }

        .gantt-today-line::before {
            content: 'Today';
            position: absolute;
            top: 2px;
            left: 4px;
            font-size: 9px;
            font-weight: 700;
            color: #ef4444;
            white-space: nowrap;
        }

        /* ── Kanban Board ── */
        .kanban-board {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
            padding: 12px 0;
        }

        .kanban-column {
            background: var(--c-50, #f8fafc);
            border-radius: 10px;
            padding: 10px;
            min-height: 200px;
            border: 1px solid var(--c-200);
        }

        .dark .kanban-column {
            background: var(--c-800, #1e293b);
            border-color: var(--c-700);
        }

        .kanban-column-header {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            padding: 6px 8px;
            margin-bottom: 8px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .kanban-card {
            background: white;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 8px;
            border: 1px solid var(--c-200);
            cursor: pointer;
            transition: all .15s;
            font-size: 12px;
        }

        .dark .kanban-card {
            background: var(--c-700);
            border-color: var(--c-600);
        }

        .kanban-card:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, .1);
            transform: translateY(-1px);
        }

        .kanban-card-title {
            font-weight: 600;
            margin-bottom: 4px;
            color: var(--c-800);
        }

        .dark .kanban-card-title {
            color: var(--c-200);
        }

        .kanban-card-meta {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: var(--c-500);
        }

        .kanban-badge {
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }

        /* ── Detail Panel ── */
        .gantt-detail-panel {
            position: fixed;
            right: 0;
            top: 0;
            bottom: 0;
            width: 400px;
            background: white;
            border-left: 1px solid var(--c-200);
            z-index: 100;
            box-shadow: -4px 0 20px rgba(0, 0, 0, .1);
            overflow-y: auto;
            transition: transform .3s;
        }

        .dark .gantt-detail-panel {
            background: var(--c-800);
            border-color: var(--c-700);
        }

        .gantt-detail-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--c-200);
            position: sticky;
            top: 0;
            background: inherit;
            z-index: 1;
        }

        .gantt-detail-header h3 {
            font-size: 14px;
            font-weight: 700;
            margin: 0;
        }

        .gantt-detail-close {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            border: none;
            background: var(--c-100);
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gantt-detail-body {
            padding: 16px 20px;
        }

        /* ── Priority Colors ── */
        [data-priority="urgent"] {
            border-left: 3px solid #ef4444;
        }

        [data-priority="high"] {
            border-left: 3px solid #f59e0b;
        }

        @media (max-width: 768px) {
            .gantt-left {
                width: 280px !important;
                min-width: 200px;
            }

            .gantt-col-predecessors,
            .gantt-col-resource {
                display: none;
            }

            .gantt-toolbar {
                flex-direction: column;
            }

            .kanban-board {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* ── Fullscreen Toggle ── */
        .gantt-fullscreen-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            border-radius: 6px;
            border: 1px solid var(--c-200, #e2e8f0);
            background: white;
            cursor: pointer;
            color: var(--c-500, #64748b);
            transition: all .2s;
        }

        .gantt-fullscreen-btn:hover {
            background: var(--primary-50, #eef2ff);
            color: var(--primary-600, #4f46e5);
            border-color: var(--primary-300, #a5b4fc);
        }

        .dark .gantt-fullscreen-btn {
            background: var(--c-700);
            border-color: var(--c-600);
            color: var(--c-400);
        }

        .dark .gantt-fullscreen-btn:hover {
            background: var(--c-600);
            color: var(--primary-400);
        }

        /* Fullscreen mode */
        body.gantt-fullscreen .fi-sidebar,
        body.gantt-fullscreen .fi-topbar,
        body.gantt-fullscreen .fi-header,
        body.gantt-fullscreen .fi-breadcrumbs,
        body.gantt-fullscreen .fi-page-header,
        body.gantt-fullscreen .fi-footer {
            display: none !important;
        }

        body.gantt-fullscreen .fi-main {
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
        }

        body.gantt-fullscreen .fi-main-ctn {
            margin: 0 !important;
            padding: 8px !important;
            max-width: 100% !important;
        }

        body.gantt-fullscreen .fi-page {
            padding: 0 !important;
        }

        body.gantt-fullscreen .gantt-split {
            height: calc(100vh - 180px) !important;
        }
    </style>

    {{-- ═══════════════ JAVASCRIPT ENGINE ═══════════════ --}}
    <script>
        // ── Module tab switching (global) ──
        window.switchModuleTab = function (tab) {
            document.querySelectorAll('.module-content').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.smt-btn').forEach(b => b.classList.remove('active'));

            const moduleMap = { schedule: 'scheduleModule', work_orders: 'workOrdersModule', milestones: 'milestonesModule' };
            document.getElementById(moduleMap[tab]).style.display = 'block';
            document.querySelector(`.smt-btn[data-module="${tab}"]`).classList.add('active');
        };

        // Use livewire:navigated which fires on both initial load and wire:navigate
        function initScheduleEngine() {
            // Prevent double-init
            if (window.__scheduleEngineInitialized) return;

            // Ensure DOM elements exist before initializing
            if (!document.getElementById('scheduleModule')) return;

            window.__scheduleEngineInitialized = true;

            // ── Data from server (mutable for refresh) ──
            let ganttData = @json($this->getGanttData());
            let tasks = ganttData.tasks || [];
            let dependencies = ganttData.dependencies || [];

            // ── State ──
            let currentZoom = 'week';
            let currentView = 'gantt';
            let selectedTaskId = null;
            let showBaseline = false;
            let showCritical = false;
            let showProgress = true;
            let collapsedGroups = new Set();
            let searchQuery = '';

            // ── Virtual scroll constants ──
            const ROW_HEIGHT = 32;
            const BUFFER_ROWS = 10;
            let cachedVisibleTasks = null;

            // ── Zoom configs ──
            const ZOOM = {
                day: { cellWidth: 36, format: 'd', headerFormat: 'MMM yyyy', cellsPerUnit: 1 },
                week: { cellWidth: 28, format: 'd', headerFormat: 'MMM yyyy', cellsPerUnit: 7 },
                month: { cellWidth: 60, format: 'MMM', headerFormat: 'yyyy', cellsPerUnit: 30 },
                quarter: { cellWidth: 80, format: 'Q', headerFormat: 'yyyy', cellsPerUnit: 90 },
            };

            // ── Date helpers ──
            function parseDate(str) { return str ? new Date(str + 'T00:00:00') : null; }
            function formatDate(d) {
                if (!d) return '—';
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
            }
            function shortDate(d) {
                if (!d) return '—';
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                return months[d.getMonth()] + ' ' + d.getDate();
            }
            function daysBetween(a, b) { return Math.round((b - a) / 86400000); }
            function addDays(d, n) { const r = new Date(d); r.setDate(r.getDate() + n); return r; }
            function isWeekend(d) { const day = d.getDay(); return day === 0 || day === 6; }

            // ── Calculate timeline bounds ──
            function getTimelineBounds() {
                let minDate = null, maxDate = null;
                tasks.forEach(t => {
                    const s = parseDate(t.start) || parseDate(t.end);
                    const e = parseDate(t.end) || parseDate(t.start);
                    const bs = parseDate(t.baseline_start);
                    const be = parseDate(t.baseline_finish);
                    [s, bs].forEach(d => { if (d && (!minDate || d < minDate)) minDate = d; });
                    [e, be].forEach(d => { if (d && (!maxDate || d > maxDate)) maxDate = d; });
                });
                if (!minDate) minDate = new Date();
                if (!maxDate) maxDate = addDays(minDate, 30);
                minDate = addDays(minDate, -7);
                maxDate = addDays(maxDate, 14);
                return { start: minDate, end: maxDate };
            }

            // ── Filter visible tasks (search + collapsed groups) ──
            function getVisibleTasks() {
                if (cachedVisibleTasks) return cachedVisibleTasks;

                const q = searchQuery.toLowerCase().trim();
                const visible = [];
                const hiddenParents = new Set();

                tasks.forEach(t => {
                    // Collapse logic
                    if (t.parent_id && hiddenParents.has(t.parent_id)) {
                        hiddenParents.add(t.id);
                        return;
                    }
                    if (t.parent_id && collapsedGroups.has(t.parent_id)) {
                        hiddenParents.add(t.id);
                        return;
                    }

                    // Search filter
                    if (q) {
                        const haystack = [
                            t.title, t.wbs, t.assignee, t.resource_names,
                            t.status, t.priority
                        ].filter(Boolean).join(' ').toLowerCase();
                        if (!haystack.includes(q)) return;
                    }

                    visible.push(t);
                });

                cachedVisibleTasks = visible;

                // Update count badge
                const countEl = document.getElementById('ganttTaskCount');
                if (countEl) {
                    countEl.textContent = q
                        ? `${visible.length} / ${tasks.length} tasks`
                        : `${visible.length} tasks`;
                }

                return visible;
            }

            function invalidateVisibleCache() {
                cachedVisibleTasks = null;
            }

            // ── Render Table (Left Panel) — Virtual Scrolling ──
            function renderTable() {
                const body = document.getElementById('ganttTableBody');
                const visibleTasks = getVisibleTasks();
                const totalHeight = visibleTasks.length * ROW_HEIGHT;
                body.style.height = totalHeight + 'px';

                // Get scroll position from the parent container
                const scrollContainer = body.parentElement;
                const scrollTop = scrollContainer.scrollTop || 0;
                const viewHeight = scrollContainer.clientHeight || 600;

                const startIdx = Math.max(0, Math.floor(scrollTop / ROW_HEIGHT) - BUFFER_ROWS);
                const endIdx = Math.min(visibleTasks.length - 1, Math.ceil((scrollTop + viewHeight) / ROW_HEIGHT) + BUFFER_ROWS);

                let html = '';
                for (let idx = startIdx; idx <= endIdx; idx++) {
                    const t = visibleTasks[idx];
                    const topPx = idx * ROW_HEIGHT;
                    const indent = (t.outline_level || 0) * 16;
                    const isSummary = t.is_summary || t.has_children;
                    const isMilestone = t.is_milestone;
                    const isCollapsed = collapsedGroups.has(t.id);
                    const isSelected = selectedTaskId === t.id;

                    const classes = [
                        'gantt-row',
                        isSummary ? 'summary' : '',
                        isMilestone ? 'milestone' : '',
                        isSelected ? 'selected' : '',
                        showCritical && t.is_critical ? 'critical' : '',
                    ].filter(Boolean).join(' ');

                    const toggleIcon = isSummary
                        ? `<span class="gantt-indent-toggle" onclick="event.stopPropagation(); toggleGroup(${t.id})">${isCollapsed ? '▶' : '▼'}</span>`
                        : '<span style="width:16px;display:inline-block"></span>';

                    const taskIcon = isMilestone
                        ? '<svg class="gantt-task-icon milestone" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3 9h9l-7 5 3 9-8-6-8 6 3-9-7-5h9z"/></svg>'
                        : isSummary
                            ? '<svg class="gantt-task-icon summary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/></svg>'
                            : '';

                    const pctColor = t.progress >= 100 ? '#059669' : t.progress >= 50 ? '#2563eb' : 'inherit';
                    const safeTitle = (t.title || '').replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                    const safeStart = t.start ? t.start.split('T')[0] : '';

                    html += `<div class="${classes}" data-task-id="${t.id}" onclick="selectTask(${t.id})" data-priority="${t.priority}" style="position:absolute;top:${topPx}px;left:0;right:0;height:${ROW_HEIGHT}px">
                    <div class="gantt-td gantt-col-wbs">${t.wbs || (idx + 1)}</div>
                    <div class="gantt-td gantt-col-indicator">
                        ${t.status === 'done' ? '<span style="color:#059669">✓</span>' : t.status === 'blocked' ? '<span style="color:#ef4444">⊘</span>' : ''}
                    </div>
                    <div class="gantt-td gantt-col-name editable-cell" style="padding-left:${indent + 6}px" ondblclick="editCell(event, ${t.id}, 'title', '${safeTitle}')">
                        ${toggleIcon} ${taskIcon}
                        <span title="${safeTitle}">${t.title}</span>
                    </div>
                    <div class="gantt-td gantt-col-duration editable-cell" ondblclick="editCell(event, ${t.id}, 'duration_days', '${t.duration || ''}')">${isMilestone ? '0d' : (t.duration || '—') + 'd'}</div>
                    <div class="gantt-td gantt-col-start editable-cell" ondblclick="editCell(event, ${t.id}, 'start_date', '${safeStart}')">${shortDate(parseDate(t.start))}</div>
                    <div class="gantt-td gantt-col-finish">${shortDate(parseDate(t.end))}</div>
                    <div class="gantt-td gantt-col-pct editable-cell" style="color:${pctColor}" ondblclick="editCell(event, ${t.id}, 'progress', '${t.progress}')">${t.progress}%</div>
                    <div class="gantt-td gantt-col-predecessors">${t.predecessors || ''}</div>
                    <div class="gantt-td gantt-col-resource">${t.resource_names || t.assignee || ''}</div>
                </div>`;
                }

                body.innerHTML = html;
            }

            // ── Inline Edit Logic ──
            window.editCell = function (event, taskId, field, currentValue) {
                event.stopPropagation();
                const td = event.currentTarget;
                if (td.querySelector('.inline-edit-input')) return; // Already editing

                // Save original content in case of abort
                const originalHtml = td.innerHTML;

                // Determine input type
                const inputType = field === 'start_date' ? 'date' : (field === 'duration_days' || field === 'progress' ? 'number' : 'text');

                // Create input
                const input = document.createElement('input');
                input.type = inputType;
                input.value = currentValue;
                input.className = 'inline-edit-input';

                // Minimal styling for seamless effect
                input.style.width = '100%';
                input.style.boxSizing = 'border-box';
                input.style.border = '1px solid #4f46e5';
                input.style.borderRadius = '4px';
                input.style.padding = '0 4px';
                input.style.outline = 'none';
                input.style.fontSize = '12px';

                // Swap
                td.innerHTML = '';
                if (field === 'title') {
                    // For title, keep the indent
                    const wrapper = document.createElement('div');
                    wrapper.style.display = 'flex';
                    wrapper.style.alignItems = 'center';
                    wrapper.style.width = '100%';
                    input.style.flex = '1';
                    wrapper.appendChild(input);
                    td.appendChild(wrapper);
                } else {
                    td.appendChild(input);
                }

                input.focus();

                if (inputType === 'text' || inputType === 'number') {
                    input.select();
                }

                const finishEdit = async (save) => {
                    if (save && input.value !== currentValue) {
                        try {
                            td.innerHTML = '<span style="color:#9ca3af;font-size:11px">Saving...</span>';
                            await @this.updateTaskField(taskId, field, input.value);
                        } catch (e) {
                            td.innerHTML = originalHtml;
                            console.error('Save failed', e);
                        }
                    } else {
                        td.innerHTML = originalHtml;
                    }
                };

                input.addEventListener('blur', () => finishEdit(true));
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') finishEdit(true);
                    if (e.key === 'Escape') finishEdit(false);
                });
            };

            // ── Render Timeline Header ──
            function renderTimelineHeader() {
                const header = document.getElementById('ganttChartHeader');
                const bounds = getTimelineBounds();
                const zoom = ZOOM[currentZoom];
                const totalDays = daysBetween(bounds.start, bounds.end);
                const cellWidth = zoom.cellWidth;

                let topRow = '', bottomRow = '';

                if (currentZoom === 'day' || currentZoom === 'week') {
                    // Group by month at top
                    let currentMonth = -1;
                    let monthDays = 0;
                    let monthLabel = '';

                    for (let i = 0; i <= totalDays; i++) {
                        const d = addDays(bounds.start, i);
                        const m = d.getMonth();
                        const today = new Date();
                        const isToday = d.toDateString() === today.toDateString();
                        const wkend = isWeekend(d);

                        if (m !== currentMonth) {
                            if (currentMonth >= 0) {
                                topRow += `<div class="gantt-header-cell primary" style="width:${monthDays * cellWidth}px">${monthLabel}</div>`;
                            }
                            currentMonth = m;
                            monthDays = 0;
                            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                            monthLabel = months[m] + ' ' + d.getFullYear();
                        }
                        monthDays++;

                        const cls = [wkend ? 'weekend' : '', isToday ? 'today' : ''].filter(Boolean).join(' ');
                        bottomRow += `<div class="gantt-header-cell ${cls}" style="width:${cellWidth}px">${d.getDate()}</div>`;
                    }
                    topRow += `<div class="gantt-header-cell primary" style="width:${monthDays * cellWidth}px">${monthLabel}</div>`;

                } else if (currentZoom === 'month') {
                    let currentYear = -1;
                    let yearMonths = 0;
                    let d = new Date(bounds.start);
                    while (d <= bounds.end) {
                        const y = d.getFullYear();
                        if (y !== currentYear) {
                            if (currentYear >= 0) {
                                topRow += `<div class="gantt-header-cell primary" style="width:${yearMonths * cellWidth}px">${currentYear}</div>`;
                            }
                            currentYear = y;
                            yearMonths = 0;
                        }
                        yearMonths++;
                        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        const isCurrentMonth = d.getMonth() === new Date().getMonth() && d.getFullYear() === new Date().getFullYear();
                        bottomRow += `<div class="gantt-header-cell ${isCurrentMonth ? 'today' : ''}" style="width:${cellWidth}px">${months[d.getMonth()]}</div>`;
                        d.setMonth(d.getMonth() + 1);
                    }
                    topRow += `<div class="gantt-header-cell primary" style="width:${yearMonths * cellWidth}px">${currentYear}</div>`;

                } else { // quarter
                    // Similar logic for quarters
                    let d = new Date(bounds.start);
                    let currentYear = -1;
                    let yearQuarters = 0;
                    while (d <= bounds.end) {
                        const y = d.getFullYear();
                        if (y !== currentYear) {
                            if (currentYear >= 0) topRow += `<div class="gantt-header-cell primary" style="width:${yearQuarters * cellWidth}px">${currentYear}</div>`;
                            currentYear = y; yearQuarters = 0;
                        }
                        yearQuarters++;
                        const q = Math.floor(d.getMonth() / 3) + 1;
                        bottomRow += `<div class="gantt-header-cell" style="width:${cellWidth}px">Q${q}</div>`;
                        d.setMonth(d.getMonth() + 3);
                    }
                    topRow += `<div class="gantt-header-cell primary" style="width:${yearQuarters * cellWidth}px">${currentYear}</div>`;
                }

                header.innerHTML = `<div class="gantt-header-row">${topRow}</div><div class="gantt-header-row">${bottomRow}</div>`;
            }

            // ── Render Gantt Bars — Virtual Scrolling ──
            function renderBars() {
                const body = document.getElementById('ganttChartBody');
                const bounds = getTimelineBounds();
                const zoom = ZOOM[currentZoom];
                const totalDays = daysBetween(bounds.start, bounds.end);
                const cellWidth = zoom.cellWidth;
                const visibleTasks = getVisibleTasks();

                const totalWidth = currentZoom === 'month'
                    ? Math.ceil(totalDays / 30) * cellWidth
                    : currentZoom === 'quarter'
                        ? Math.ceil(totalDays / 90) * cellWidth
                        : (totalDays + 1) * cellWidth;

                const totalHeight = visibleTasks.length * ROW_HEIGHT;
                body.style.height = totalHeight + 'px';
                body.style.width = totalWidth + 'px';

                // Virtual scroll range
                const scrollContainer = body.parentElement;
                const scrollTop = scrollContainer.scrollTop || 0;
                const viewHeight = scrollContainer.clientHeight || 600;
                const startIdx = Math.max(0, Math.floor(scrollTop / ROW_HEIGHT) - BUFFER_ROWS);
                const endIdx = Math.min(visibleTasks.length - 1, Math.ceil((scrollTop + viewHeight) / ROW_HEIGHT) + BUFFER_ROWS);

                const statusColors = {
                    'done': '#059669', 'in_progress': '#6366f1', 'blocked': '#ef4444',
                    'review': '#8b5cf6', 'to_do': '#94a3b8',
                };

                let html = '';
                for (let idx = startIdx; idx <= endIdx; idx++) {
                    const t = visibleTasks[idx];
                    const topPx = idx * ROW_HEIGHT;
                    let start = parseDate(t.start);
                    let end = parseDate(t.end);
                    if (!start && end) start = end;
                    if (!end && start) end = start;
                    const isSummary = t.is_summary || t.has_children;
                    const isMilestone = t.is_milestone;

                    let barHtml = '';

                    if (start && end) {
                        const leftDays = daysBetween(bounds.start, start);
                        const durationDays = daysBetween(start, end) + 1;
                        const leftPx = (leftDays / (totalDays + 1)) * totalWidth;
                        const widthPx = Math.max(4, (durationDays / (totalDays + 1)) * totalWidth);

                        const barClass = isMilestone ? 'milestone-bar'
                            : isSummary ? 'summary-bar'
                                : (showCritical && t.is_critical) ? 'critical-bar normal'
                                    : 'normal';

                        const barColor = statusColors[t.status] || '#6366f1';

                        if (isMilestone) {
                            barHtml = `<div class="gantt-bar milestone-bar" style="left:${leftPx}px; cursor:ew-resize" title="${t.title}\n${formatDate(start)}" data-task-id="${t.id}" onclick="selectTask(${t.id})" onmousedown="startDragBar(event, ${t.id}, '${start.toISOString()}')"></div>`;
                        } else {
                            const progWidth = showProgress ? t.progress : 0;
                            const label = t.assignee ? t.assignee.split(' ')[0] : '';
                            const dragHandler = !isSummary ? `onmousedown="startDragBar(event, ${t.id}, '${start.toISOString()}')" style="cursor:ew-resize"` : '';
                            const resizeHandle = !isSummary ? `<div class="gantt-resize-handle" onmousedown="event.stopPropagation(); startResizeBar(event, ${t.id}, '${start.toISOString()}', ${durationDays})"></div>` : '';

                            barHtml = `<div class="gantt-bar ${barClass}" style="left:${leftPx}px; width:${widthPx}px; ${!isSummary ? 'background:' + barColor + '; cursor:ew-resize' : ''}" title="${t.title}\n${t.progress}% · ${formatDate(start)} → ${formatDate(end)}\n${t.assignee || ''}" data-task-id="${t.id}" onclick="selectTask(${t.id})" ${dragHandler}>
                            ${showProgress && !isSummary ? `<div class="gantt-bar-progress ${progWidth >= 100 ? 'full' : ''}" style="width:${progWidth}%"></div>` : ''}
                            ${label ? `<span class="gantt-bar-label">${label}</span>` : ''}
                            ${resizeHandle}
                        </div>`;
                        }

                        // Baseline bar
                        if (showBaseline) {
                            const bs = parseDate(t.baseline_start);
                            const be = parseDate(t.baseline_finish);
                            if (bs && be) {
                                const bLeftDays = daysBetween(bounds.start, bs);
                                const bDurDays = daysBetween(bs, be) + 1;
                                const bLeftPx = (bLeftDays / (totalDays + 1)) * totalWidth;
                                const bWidthPx = Math.max(4, (bDurDays / (totalDays + 1)) * totalWidth);
                                barHtml += `<div class="gantt-baseline-bar" style="left:${bLeftPx}px; width:${bWidthPx}px" title="Baseline: ${formatDate(bs)} → ${formatDate(be)}"></div>`;
                            }
                        }
                    }

                    html += `<div class="gantt-chart-row" style="position:absolute;top:${topPx}px;width:${totalWidth}px;height:${ROW_HEIGHT}px">${barHtml}</div>`;
                }

                body.innerHTML = html;

                // Today line
                const todayLine = document.getElementById('ganttTodayLine');
                const today = new Date();
                if (today >= bounds.start && today <= bounds.end) {
                    const todayDays = daysBetween(bounds.start, today);
                    const todayPx = (todayDays / (totalDays + 1)) * totalWidth;
                    todayLine.style.left = todayPx + 'px';
                    todayLine.style.display = 'block';
                } else {
                    todayLine.style.display = 'none';
                }
            }

            // ── Render Kanban Board ──
            function renderBoard() {
                const board = document.getElementById('kanbanBoard');
                const columns = {
                    'to_do': { label: 'To Do', color: '#94a3b8', bg: '#f1f5f9' },
                    'in_progress': { label: 'In Progress', color: '#6366f1', bg: '#eef2ff' },
                    'review': { label: 'Review', color: '#8b5cf6', bg: '#f5f3ff' },
                    'blocked': { label: 'Blocked', color: '#ef4444', bg: '#fef2f2' },
                    'done': { label: 'Done', color: '#059669', bg: '#ecfdf5' },
                };

                const MAX_CARDS = 50;
                const q = searchQuery.toLowerCase().trim();

                let html = '';
                for (const [status, config] of Object.entries(columns)) {
                    let columnTasks = tasks.filter(t => t.status === status && !t.is_summary);
                    if (q) {
                        columnTasks = columnTasks.filter(t => {
                            const haystack = [t.title, t.wbs, t.assignee, t.resource_names].filter(Boolean).join(' ').toLowerCase();
                            return haystack.includes(q);
                        });
                    }
                    const totalCount = columnTasks.length;
                    const shownTasks = columnTasks.slice(0, MAX_CARDS);

                    html += `<div class="kanban-column">
                    <div class="kanban-column-header" style="background:${config.bg}; color:${config.color}">
                        ${config.label}
                        <span class="kanban-badge" style="background:${config.color}; color:white">${totalCount}</span>
                    </div>`;

                    shownTasks.forEach(t => {
                        const priorityColors = { urgent: '#ef4444', high: '#f59e0b', medium: '#6366f1', low: '#94a3b8' };
                        html += `<div class="kanban-card" onclick="selectTask(${t.id})" data-priority="${t.priority}">
                        <div class="kanban-card-title">${t.title}</div>
                        <div class="kanban-card-meta">
                            <span>${t.assignee || 'Unassigned'}</span>
                            <span style="color:${priorityColors[t.priority] || '#94a3b8'}">${t.priority}</span>
                        </div>
                        ${t.due_date ? `<div style="font-size:10px; color:var(--c-400); margin-top:2px">${shortDate(parseDate(t.end))}</div>` : ''}
                        <div style="margin-top:6px; background:var(--c-200); border-radius:4px; height:4px; overflow:hidden">
                            <div style="width:${t.progress}%; height:100%; background:${config.color}; border-radius:4px; transition:width .3s"></div>
                        </div>
                    </div>`;
                    });

                    if (totalCount > MAX_CARDS) {
                        html += `<div style="text-align:center;padding:8px;font-size:11px;color:var(--c-400)">+ ${totalCount - MAX_CARDS} more</div>`;
                    }

                    html += '</div>';
                }
                board.innerHTML = html;
            }

            // ── Sync scroll + virtual re-render ──
            function syncScroll() {
                const left = document.getElementById('ganttLeft');
                const rightBody = document.getElementById('ganttRight');
                if (!left || !rightBody) return;
                let scrollTicking = false;

                function onScroll() {
                    if (!scrollTicking) {
                        requestAnimationFrame(() => {
                            renderTable();
                            renderBars();
                            scrollTicking = false;
                        });
                        scrollTicking = true;
                    }
                }

                left.addEventListener('scroll', () => {
                    rightBody.scrollTop = left.scrollTop;
                    onScroll();
                });
                rightBody.addEventListener('scroll', () => {
                    left.scrollTop = rightBody.scrollTop;
                    onScroll();
                });
            }

            // ── Splitter drag ──
            function initSplitter() {
                const splitter = document.getElementById('ganttSplitter');
                const left = document.getElementById('ganttLeft');
                if (!splitter || !left) return;
                let dragging = false;
                let startX, startWidth;

                splitter.addEventListener('mousedown', (e) => {
                    dragging = true;
                    startX = e.clientX;
                    startWidth = left.offsetWidth;
                    splitter.classList.add('dragging');
                    document.body.style.cursor = 'col-resize';
                    document.body.style.userSelect = 'none';
                });

                document.addEventListener('mousemove', (e) => {
                    if (!dragging) return;
                    const deltaX = e.clientX - startX;
                    const newWidth = Math.max(200, Math.min(800, startWidth + deltaX));
                    left.style.width = newWidth + 'px';
                });

                document.addEventListener('mouseup', () => {
                    if (dragging) {
                        dragging = false;
                        splitter.classList.remove('dragging');
                        document.body.style.cursor = '';
                        document.body.style.userSelect = '';
                    }
                });
            }

            // ── Task selection & Dragging ──
            window.isDraggingBar = false;

            window.startDragBar = function (event, taskId, startStr) {
                event.stopPropagation();
                event.preventDefault(); // Prevent text selection

                const startX = event.clientX;
                const bar = event.currentTarget;
                const initialLeftStr = bar.style.left;
                const initialLeft = parseFloat(initialLeftStr) || 0;

                // Get pixels per day from the current zoom level and DOM
                const bounds = getTimelineBounds();
                const totalDays = daysBetween(bounds.start, bounds.end);
                // The parent row width is totalWidth. We can get it from body style or recalculate
                const body = document.getElementById('ganttChartBody');
                const totalWidth = parseFloat(body.style.width) || 0;

                const pxPerDay = totalWidth / (totalDays + 1);
                let currentShiftDays = 0;

                const onMouseMove = function (e) {
                    window.isDraggingBar = true;
                    const deltaX = e.clientX - startX;
                    const newLeft = initialLeft + deltaX;
                    if (newLeft < 0) return; // Prevent negative
                    bar.style.left = newLeft + 'px';
                    // We also show a tiny visual cue
                    bar.style.transform = 'scaleY(1.1)';
                    bar.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
                    bar.style.zIndex = '10';
                    currentShiftDays = Math.round(deltaX / pxPerDay);
                };

                const onMouseUp = async function (e) {
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);

                    setTimeout(() => { window.isDraggingBar = false; }, 50); // delay so selectTask ignores

                    bar.style.transform = 'none';
                    bar.style.boxShadow = 'none';
                    bar.style.zIndex = '1';

                    if (currentShiftDays !== 0) {
                        try {
                            bar.style.opacity = '0.5';
                            // Calculate new start date
                            const oldStart = new Date(startStr);
                            oldStart.setDate(oldStart.getDate() + currentShiftDays);

                            const y = oldStart.getFullYear();
                            const m = String(oldStart.getMonth() + 1).padStart(2, '0');
                            const d = String(oldStart.getDate()).padStart(2, '0');
                            const newDateStr = `${y}-${m}-${d}`;

                            // Send to Livewire (triggering auto-schedule forward pass)
                            await @this.updateTaskField(taskId, 'start_date', newDateStr);
                        } catch (err) {
                            console.error('Drag failed', err);
                            bar.style.left = initialLeftStr;
                            bar.style.opacity = '1';
                        }
                    } else {
                        bar.style.left = initialLeftStr; // Reset just in case
                    }
                };

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            };

            // ── Drag-to-Resize (change duration by dragging right edge) ──
            window.startResizeBar = function (event, taskId, startStr, currentDuration) {
                event.stopPropagation();
                event.preventDefault();

                const startX = event.clientX;
                const bar = event.target.closest('.gantt-bar');
                if (!bar) return;

                const initialWidth = parseFloat(bar.style.width) || 20;

                const bounds = getTimelineBounds();
                const totalDays = daysBetween(bounds.start, bounds.end);
                const body = document.getElementById('ganttChartBody');
                const totalWidth = parseFloat(body.style.width) || 0;
                const pxPerDay = totalWidth / (totalDays + 1);

                let newDuration = currentDuration;

                const onMouseMove = function (e) {
                    window.isDraggingBar = true;
                    const deltaX = e.clientX - startX;
                    const newWidth = Math.max(pxPerDay * 0.5, initialWidth + deltaX);
                    bar.style.width = newWidth + 'px';
                    bar.style.boxShadow = '0 4px 12px rgba(0,0,0,0.3)';
                    bar.style.zIndex = '10';
                    newDuration = Math.max(1, Math.round(newWidth / pxPerDay));
                };

                const onMouseUp = async function (e) {
                    document.removeEventListener('mousemove', onMouseMove);
                    document.removeEventListener('mouseup', onMouseUp);

                    setTimeout(() => { window.isDraggingBar = false; }, 50);

                    bar.style.boxShadow = 'none';
                    bar.style.zIndex = '1';

                    if (newDuration !== currentDuration) {
                        try {
                            bar.style.opacity = '0.5';
                            await @this.updateTaskField(taskId, 'duration_days', newDuration);
                        } catch (err) {
                            console.error('Resize failed', err);
                            bar.style.width = initialWidth + 'px';
                            bar.style.opacity = '1';
                        }
                    } else {
                        bar.style.width = initialWidth + 'px';
                    }
                };

                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
            };

            window.selectTask = function (taskId) {
                if (window.isDraggingBar) return; // Prevent selection if just dragged

                selectedTaskId = taskId;
                document.querySelectorAll('.gantt-row').forEach(r => r.classList.remove('selected'));
                const row = document.querySelector(`.gantt-row[data-task-id="${taskId}"]`);
                if (row) row.classList.add('selected');

                // Show detail panel
                const task = tasks.find(t => t.id === taskId);
                if (task) showTaskDetail(task);
            };

            window.toggleGroup = function (taskId) {
                if (collapsedGroups.has(taskId)) {
                    collapsedGroups.delete(taskId);
                } else {
                    collapsedGroups.add(taskId);
                }
                invalidateVisibleCache();
                render();
            };

            window.closeDetailPanel = function () {
                document.getElementById('taskDetailPanel').style.display = 'none';
                selectedTaskId = null;
                document.querySelectorAll('.gantt-row').forEach(r => r.classList.remove('selected'));
            };

            function showTaskDetail(task) {
                const panel = document.getElementById('taskDetailPanel');
                const title = document.getElementById('detailTitle');
                const body = document.getElementById('detailBody');

                title.textContent = task.title;

                const statusColors = { 'done': '#059669', 'in_progress': '#6366f1', 'blocked': '#ef4444', 'review': '#8b5cf6', 'to_do': '#94a3b8' };
                const priorityColors = { 'urgent': '#ef4444', 'high': '#f59e0b', 'medium': '#6366f1', 'low': '#94a3b8' };

                body.innerHTML = `
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:16px;">
                    <div style="padding:8px 12px; border-radius:8px; background:${statusColors[task.status] || '#94a3b8'}15">
                        <div style="font-size:10px; color:var(--c-500); margin-bottom:2px">Status</div>
                        <div style="font-weight:700; color:${statusColors[task.status] || '#94a3b8'}">${task.status.replace('_', ' ').toUpperCase()}</div>
                    </div>
                    <div style="padding:8px 12px; border-radius:8px; background:${priorityColors[task.priority] || '#94a3b8'}15">
                        <div style="font-size:10px; color:var(--c-500); margin-bottom:2px">Priority</div>
                        <div style="font-weight:700; color:${priorityColors[task.priority] || '#94a3b8'}">${task.priority.toUpperCase()}</div>
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <div style="font-size:10px; color:var(--c-500); margin-bottom:4px">Progress</div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <div style="flex:1; height:8px; background:var(--c-200); border-radius:4px; overflow:hidden">
                            <div style="width:${task.progress}%; height:100%; background:${statusColors[task.status] || '#6366f1'}; border-radius:4px; transition:width .3s"></div>
                        </div>
                        <span style="font-weight:700; font-size:13px">${task.progress}%</span>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                    <div><div style="font-size:10px; color:var(--c-500)">Start Date</div><div style="font-weight:600; font-size:13px">${formatDate(parseDate(task.start))}</div></div>
                    <div><div style="font-size:10px; color:var(--c-500)">Finish Date</div><div style="font-weight:600; font-size:13px">${formatDate(parseDate(task.end))}</div></div>
                    <div><div style="font-size:10px; color:var(--c-500)">Duration</div><div style="font-weight:600; font-size:13px">${task.duration || '—'} days</div></div>
                    <div><div style="font-size:10px; color:var(--c-500)">WBS</div><div style="font-weight:600; font-size:13px; font-family:monospace">${task.wbs || '—'}</div></div>
                </div>

                ${task.baseline_start ? `
                <div style="padding:10px; border-radius:8px; background:#fffbeb; border:1px solid #fcd34d; margin-bottom:16px;">
                    <div style="font-size:10px; font-weight:700; color:#92400e; margin-bottom:4px">BASELINE</div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; font-size:12px;">
                        <div><span style="color:#92400e">Start:</span> ${formatDate(parseDate(task.baseline_start))}</div>
                        <div><span style="color:#92400e">Finish:</span> ${formatDate(parseDate(task.baseline_finish))}</div>
                    </div>
                </div>` : ''}

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                    <div><div style="font-size:10px; color:var(--c-500)">Assigned To</div><div style="font-weight:600; font-size:13px">${task.assignee || task.resource_names || 'Unassigned'}</div></div>
                    <div><div style="font-size:10px; color:var(--c-500)">Est. Hours</div><div style="font-weight:600; font-size:13px">${task.estimated_hours || '—'}h</div></div>
                    <div><div style="font-size:10px; color:var(--c-500)">Actual Hours</div><div style="font-weight:600; font-size:13px">${task.actual_hours || '—'}h</div></div>
                    <div><div style="font-size:10px; color:var(--c-500)">Predecessors</div><div style="font-weight:600; font-size:13px; font-family:monospace">${task.predecessors || 'None'}</div></div>
                </div>

                <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:16px;">
                    <button onclick="Livewire.find('${document.querySelector('[wire\\\\:id]').getAttribute('wire:id')}').call('openEditTaskModal', ${task.id})" style="padding:6px 14px; font-size:12px; font-weight:600; border-radius:6px; border:1px solid var(--c-300); background:white; cursor:pointer">✏️ Edit</button>
                    <button onclick="Livewire.find('${document.querySelector('[wire\\\\:id]').getAttribute('wire:id')}').call('openProgressModal', ${task.id})" style="padding:6px 14px; font-size:12px; font-weight:600; border-radius:6px; border:none; background:#6366f1; color:white; cursor:pointer">📊 Update Progress</button>
                </div>
            `;

                panel.style.display = 'block';
            }

            // ── View switching ──
            document.querySelectorAll('.gantt-view-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.gantt-view-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    currentView = btn.dataset.view;

                    document.querySelectorAll('.schedule-view').forEach(v => v.classList.remove('active-view'));
                    const viewMap = { gantt: 'ganttView', table: 'tableView', board: 'boardView' };
                    document.getElementById(viewMap[currentView]).classList.add('active-view');

                    if (currentView === 'board') renderBoard();
                });
            });

            // ── Zoom switching ──
            document.querySelectorAll('.gantt-zoom-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.gantt-zoom-btn').forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');
                    currentZoom = btn.dataset.zoom;
                    render();
                });
            });

            // ── Toggle controls (inline onchange calls this) ──
            window.ganttToggle = function(key, val) {
                if (key === 'showBaseline') showBaseline = val;
                else if (key === 'showCritical') showCritical = val;
                else if (key === 'showProgress') showProgress = val;
                render();
            };

            // ── Main render ──
            function render() {
                invalidateVisibleCache();
                renderTable();
                renderTimelineHeader();
                renderBars();
            }

            // ── Search handler ──
            const searchInput = document.getElementById('ganttSearch');
            let searchTimer = null;
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => {
                        searchQuery = searchInput.value;
                        invalidateVisibleCache();
                        render();
                    }, 200); // 200ms debounce
                });
            }

            // ── Initialize ──
            render();
            syncScroll()  ;
            initSplitter();

            // ── Fullscreen toggle ──
            window.isGanttFullscreen = window.isGanttFullscreen || false;
            
            window.toggleGanttFullscreen = function(forceOff) {
                window.isGanttFullscreen = forceOff === true ? false : !window.isGanttFullscreen;
                document.body.classList.toggle('gantt-fullscreen', window.isGanttFullscreen);
                
                const exp = document.getElementById('fsExpandIcon');
                const col = document.getElementById('fsCollapseIcon');
                
                if (exp) exp.style.display = window.isGanttFullscreen ? 'none' : 'block';
                if (col) col.style.display = window.isGanttFullscreen ? 'block' : 'none';
                
                // Automatically fix chart dimensions
                setTimeout(() => {
                    syncScroll();
                    render();
                }, 50);
            };

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    if (window.isGanttFullscreen) { window.toggleGanttFullscreen(true); }
                    else { closeDetailPanel(); }
                }
            });

            // ── Listen for Livewire gantt-refresh event ──
            if (window.Livewire) {
                Livewire.on('gantt-refresh', () => {
                    setTimeout(() => window.location.reload(), 300);
                });
            }
        }

        // Initialize on both full page load and Livewire SPA navigation
        document.addEventListener('DOMContentLoaded', initScheduleEngine);
        document.addEventListener('livewire:navigated', () => {
            window.__scheduleEngineInitialized = false;
            // Small delay to ensure Livewire has finished rendering the DOM
            setTimeout(initScheduleEngine, 50);
        });
    </script>

    {{-- Re-initialize on Livewire navigate (wire:navigate) --}}
    @script
    <script>
        $wire.on('gantt-refresh', () => {
            setTimeout(() => window.location.reload(), 300);
        });
    </script>
    @endscript
    {{-- ═══════════════ NEW TASK MODAL ═══════════════ --}}
    @if($showTaskModal)
        <div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5);backdrop-filter:blur(2px)"
             wire:click.self="$set('showTaskModal', false)">
            <div style="background:white;border-radius:12px;width:100%;max-width:700px;max-height:90vh;overflow-y:auto;box-shadow:0 25px 50px rgba(0,0,0,.25);padding:24px"
                 class="dark:!bg-gray-800">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                    <h2 style="font-size:18px;font-weight:700;margin:0">New Task</h2>
                    <button wire:click="$set('showTaskModal', false)" type="button"
                        style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--c-500,#6b7280)">&times;</button>
                </div>
                <form wire:submit="submitNewTask">
                    @php $teamOpts = $this->teamOptions();
                    $taskOpts = $this->taskOptions(); @endphp
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                        <div style="grid-column:span 2">
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Title *</label>
                            <input type="text" wire:model="newTask.title" required
                                style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px"
                                placeholder="Task name...">
                            @error('newTask.title') <span style="color:#ef4444;font-size:11px">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Type</label>
                            <select wire:model="newTask.type" style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px">
                                <option value="task">Task</option>
                                <option value="milestone">Milestone</option>
                                <option value="phase">Phase/Summary</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Priority</label>
                            <select wire:model="newTask.priority" style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Start Date *</label>
                            <input type="date" wire:model="newTask.start_date" required
                                style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px">
                            @error('newTask.start_date') <span style="color:#ef4444;font-size:11px">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Due Date</label>
                            <input type="date" wire:model="newTask.due_date"
                                style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Duration (days)</label>
                            <input type="number" wire:model="newTask.duration_days" min="0"
                                style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Status</label>
                            <select wire:model="newTask.status" style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px">
                                <option value="to_do">To Do</option>
                                <option value="in_progress">In Progress</option>
                                <option value="review">Review</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Assigned To</label>
                            <select wire:model="newTask.assigned_to" style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px">
                                <option value="">-- Unassigned --</option>
                                @foreach($teamOpts as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Parent Task</label>
                            <select wire:model="newTask.parent_id" style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px">
                                <option value="">-- None --</option>
                                @foreach($taskOpts as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Resources</label>
                            <input type="text" wire:model="newTask.resource_names"
                                style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px"
                                placeholder="e.g. Crane, Electricians">
                        </div>
                        <div style="grid-column:span 2">
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Description</label>
                            <textarea wire:model="newTask.description" rows="2"
                                style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px;resize:vertical"
                                placeholder="Optional description..."></textarea>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid var(--c-200,#e5e7eb)">
                        <button type="button" wire:click="$set('showTaskModal', false)"
                            style="padding:8px 20px;border-radius:8px;border:1px solid var(--c-300,#d1d5db);background:white;font-size:13px;font-weight:600;cursor:pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#4f46e5;color:white;font-size:13px;font-weight:600;cursor:pointer">
                            <span wire:loading.remove wire:target="submitNewTask">Create Task</span>
                            <span wire:loading wire:target="submitNewTask">Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ ADD MILESTONE MODAL ═══════════════ --}}
    @if($showMilestoneModal)
        <div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5);backdrop-filter:blur(2px)"
             wire:click.self="$set('showMilestoneModal', false)">
            <div style="background:white;border-radius:12px;width:100%;max-width:450px;max-height:90vh;overflow-y:auto;box-shadow:0 25px 50px rgba(0,0,0,.25);padding:24px"
                 class="dark:!bg-gray-800">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
                    <h2 style="font-size:18px;font-weight:700;margin:0">◆ Add Milestone</h2>
                    <button wire:click="$set('showMilestoneModal', false)" type="button"
                        style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--c-500,#6b7280)">&times;</button>
                </div>
                <form wire:submit="submitMilestone">
                    @php $taskOpts2 = $this->taskOptions(); @endphp
                    <div style="display:grid;gap:12px;margin-bottom:16px">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Title *</label>
                            <input type="text" wire:model="newMilestone.title" required
                                style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px"
                                placeholder="Milestone name...">
                            @error('newMilestone.title') <span style="color:#ef4444;font-size:11px">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Target Date *</label>
                            <input type="date" wire:model="newMilestone.start_date" required
                                style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px">
                            @error('newMilestone.start_date') <span style="color:#ef4444;font-size:11px">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600,#4b5563)">Phase (optional)</label>
                            <select wire:model="newMilestone.parent_id" style="width:100%;padding:8px 12px;border:1px solid var(--c-300,#d1d5db);border-radius:8px;font-size:14px">
                                <option value="">-- None --</option>
                                @foreach($taskOpts2 as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:12px;border-top:1px solid var(--c-200,#e5e7eb)">
                        <button type="button" wire:click="$set('showMilestoneModal', false)"
                            style="padding:8px 20px;border-radius:8px;border:1px solid var(--c-300,#d1d5db);background:white;font-size:13px;font-weight:600;cursor:pointer">
                            Cancel
                        </button>
                        <button type="submit"
                            style="padding:8px 20px;border-radius:8px;border:none;background:#f59e0b;color:white;font-size:13px;font-weight:600;cursor:pointer">
                            <span wire:loading.remove wire:target="submitMilestone">Add Milestone</span>
                            <span wire:loading wire:target="submitMilestone">Adding...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ EDIT TASK MODAL ═══════════════ --}}
    @if($showEditModal)
        <div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
             wire:click.self="$set('showEditModal', false)">
            <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:500px;box-shadow:0 20px 60px rgba(0,0,0,.2)" class="dark:!bg-gray-800">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:700">✏️ Edit Task</h3>
                    <button wire:click="$set('showEditModal', false)" type="button" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--c-400)">&times;</button>
                </div>
                <form wire:submit="submitEditTask">
                    <div style="display:grid;gap:12px">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600)">Title</label>
                            <input type="text" wire:model="editTask.title" required style="width:100%;padding:8px 12px;border:1px solid var(--c-300);border-radius:8px;font-size:14px">
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600)">Status</label>
                                <select wire:model="editTask.status" style="width:100%;padding:8px 12px;border:1px solid var(--c-300);border-radius:8px;font-size:14px">
                                    @foreach(\App\Models\Task::$statuses as $k => $v) <option value="{{ $k }}">{{ $v }}</option> @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600)">Priority</label>
                                <select wire:model="editTask.priority" style="width:100%;padding:8px 12px;border:1px solid var(--c-300);border-radius:8px;font-size:14px">
                                    @foreach(\App\Models\Task::$priorities as $k => $v) <option value="{{ $k }}">{{ $v }}</option> @endforeach
                                </select>
                            </div>
                        </div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600)">Start Date</label>
                                <input type="date" wire:model="editTask.start_date" style="width:100%;padding:8px 12px;border:1px solid var(--c-300);border-radius:8px;font-size:14px">
                            </div>
                            <div>
                                <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600)">Due Date</label>
                                <input type="date" wire:model="editTask.due_date" style="width:100%;padding:8px 12px;border:1px solid var(--c-300);border-radius:8px;font-size:14px">
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600)">Description</label>
                            <textarea wire:model="editTask.description" rows="3" style="width:100%;padding:8px 12px;border:1px solid var(--c-300);border-radius:8px;font-size:14px;resize:vertical"></textarea>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;margin-top:16px;border-top:1px solid var(--c-200)">
                        <button type="button" wire:click="$set('showEditModal', false)" style="padding:8px 20px;border-radius:8px;border:1px solid var(--c-300);background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit" style="padding:8px 20px;border-radius:8px;border:none;background:#4f46e5;color:white;font-size:13px;font-weight:600;cursor:pointer">
                            <span wire:loading.remove wire:target="submitEditTask">Save Changes</span>
                            <span wire:loading wire:target="submitEditTask">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ═══════════════ UPDATE PROGRESS MODAL ═══════════════ --}}
    @if($showProgressModal)
        <div style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,.5)"
             wire:click.self="$set('showProgressModal', false)">
            <div style="background:white;border-radius:16px;padding:24px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,.2)" class="dark:!bg-gray-800">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
                    <h3 style="font-size:18px;font-weight:700">📊 Update Progress</h3>
                    <button wire:click="$set('showProgressModal', false)" type="button" style="background:none;border:none;font-size:20px;cursor:pointer;color:var(--c-400)">&times;</button>
                </div>
                <form wire:submit="submitProgress">
                    <div style="display:grid;gap:12px">
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600)">Status</label>
                            <select wire:model="progressTask.status" style="width:100%;padding:8px 12px;border:1px solid var(--c-300);border-radius:8px;font-size:14px">
                                @foreach(\App\Models\Task::$statuses as $k => $v) <option value="{{ $k }}">{{ $v }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600)">% Complete</label>
                            <input type="number" wire:model="progressTask.progress_percent" min="0" max="100" style="width:100%;padding:8px 12px;border:1px solid var(--c-300);border-radius:8px;font-size:14px">
                        </div>
                        <div>
                            <label style="display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:var(--c-600)">Actual Hours Worked</label>
                            <input type="number" wire:model="progressTask.actual_hours" step="0.5" style="width:100%;padding:8px 12px;border:1px solid var(--c-300);border-radius:8px;font-size:14px">
                        </div>
                    </div>
                    <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:16px;margin-top:16px;border-top:1px solid var(--c-200)">
                        <button type="button" wire:click="$set('showProgressModal', false)" style="padding:8px 20px;border-radius:8px;border:1px solid var(--c-300);background:white;font-size:13px;font-weight:600;cursor:pointer">Cancel</button>
                        <button type="submit" style="padding:8px 20px;border-radius:8px;border:none;background:#6366f1;color:white;font-size:13px;font-weight:600;cursor:pointer">
                            <span wire:loading.remove wire:target="submitProgress">Update</span>
                            <span wire:loading wire:target="submitProgress">Updating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</x-filament-panels::page>