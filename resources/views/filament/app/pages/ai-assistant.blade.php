<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Tool Selector --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
            @foreach ([
                'chat'     => ['💬', 'Ask AI'],
                'document' => ['📄', 'Documents'],
                'safety'   => ['🛡️', 'Safety'],
                'tasks'    => ['📋', 'Tasks'],
                'boq'      => ['🧮', 'BOQ'],
                'tender'   => ['🎯', 'Tenders'],
                'diary'    => ['📝', 'Diary'],
            ] as $key => [$emoji, $label])
                <button wire:click="switchTool('{{ $key }}')"
                    class="flex flex-col items-center gap-1 p-3 rounded-xl border text-sm font-semibold transition-all duration-150
                        {{ $activeTool === $key
                            ? 'bg-primary-600 text-white border-primary-600 shadow-lg ring-2 ring-primary-300 dark:ring-primary-800'
                            : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    <span class="text-xl leading-none">{{ $emoji }}</span>
                    <span class="text-xs">{{ $label }}</span>
                </button>
            @endforeach
        </div>

        {{-- Active Tool --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden">

            {{-- CHAT --}}
            @if ($activeTool === 'chat')
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">💬 Ask InfraHub AI</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Ask anything about construction, safety, procurement, or project management.</p>
                </div>
                <div class="flex gap-3">
                    <input type="text" wire:model="chatQuestion" wire:keydown.enter="askChat"
                        placeholder="e.g. What are the key steps for concrete curing?"
                        class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm focus:border-primary-500 focus:ring-primary-500" />
                    <button wire:click="askChat" wire:loading.attr="disabled" wire:target="askChat"
                        class="fi-btn fi-btn-size-md inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50 whitespace-nowrap">
                        <span wire:loading.remove wire:target="askChat">Ask</span>
                        <span wire:loading wire:target="askChat">Thinking…</span>
                    </button>
                </div>
                @if ($chatAnswer)
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-gray-700">
                    <div class="prose dark:prose-invert prose-sm max-w-none">{!! \Illuminate\Support\Str::markdown($chatAnswer) !!}</div>
                </div>
                @endif
            </div>
            @endif

            {{-- DOCUMENT --}}
            @if ($activeTool === 'document')
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">📄 Document Intelligence</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Summarise documents and auto-classify discipline & status.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="text" wire:model="docTitle" placeholder="Document title *"
                        class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm" />
                    <input type="text" wire:model="docDiscipline" placeholder="Discipline (e.g. structural)"
                        class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm" />
                </div>
                <textarea wire:model="docDescription" rows="3" placeholder="Description or key contents…"
                    class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm"></textarea>
                <button wire:click="summariseDocument" wire:loading.attr="disabled" wire:target="summariseDocument"
                    class="fi-btn fi-btn-size-md inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50">
                    <span wire:loading.remove wire:target="summariseDocument">✨ Summarise</span>
                    <span wire:loading wire:target="summariseDocument">Analysing…</span>
                </button>
                @if ($docResult)
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-gray-700">
                    <div class="prose dark:prose-invert prose-sm max-w-none">{!! \Illuminate\Support\Str::markdown($docResult) !!}</div>
                </div>
                @endif
            </div>
            @endif

            {{-- SAFETY --}}
            @if ($activeTool === 'safety')
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">🛡️ Safety Incident Analyser</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Get AI root cause analysis and corrective actions.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <input type="text" wire:model="incidentTitle" placeholder="Incident title *"
                        class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm" />
                    <select wire:model="incidentSeverity"
                        class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm">
                        <option value="">Severity…</option>
                        <option value="minor">Minor</option>
                        <option value="moderate">Moderate</option>
                        <option value="major">Major</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
                <textarea wire:model="incidentDescription" rows="3" placeholder="Describe the incident…"
                    class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm"></textarea>
                <button wire:click="analyseIncident" wire:loading.attr="disabled" wire:target="analyseIncident"
                    class="fi-btn fi-btn-size-md inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50">
                    <span wire:loading.remove wire:target="analyseIncident">🔍 Analyse</span>
                    <span wire:loading wire:target="analyseIncident">Analysing…</span>
                </button>
                @if (!empty($incidentResult) && !isset($incidentResult['error']))
                <div class="space-y-3">
                    <div class="p-4 rounded-lg border {{ ($incidentResult['risk_level'] ?? '') === 'critical' ? 'bg-red-50 dark:bg-red-950/20 border-red-300 dark:border-red-700' : 'bg-amber-50 dark:bg-amber-950/20 border-amber-300 dark:border-amber-700' }}">
                        <p class="font-semibold text-sm">Risk: <span class="uppercase">{{ $incidentResult['risk_level'] ?? 'N/A' }}</span></p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $incidentResult['root_cause'] ?? '' }}</p>
                    </div>
                    @foreach (['corrective_actions' => '✅ Corrective Actions', 'prevention_tips' => '🛡️ Prevention'] as $k => $label)
                        @if (!empty($incidentResult[$k]))
                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-gray-700">
                            <p class="font-semibold text-sm mb-1.5">{{ $label }}</p>
                            <ul class="list-disc list-inside text-sm space-y-1 text-gray-700 dark:text-gray-300">
                                @foreach ($incidentResult[$k] as $item)<li>{{ $item }}</li>@endforeach
                            </ul>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            {{-- TASKS --}}
            @if ($activeTool === 'tasks')
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">📋 Task Generator</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Generate a structured task breakdown from a scope of work.</p>
                </div>
                <textarea wire:model="scopeOfWork" rows="4" placeholder="Paste or describe your scope of work…"
                    class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm"></textarea>
                <button wire:click="generateTasks" wire:loading.attr="disabled" wire:target="generateTasks"
                    class="fi-btn fi-btn-size-md inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50">
                    <span wire:loading.remove wire:target="generateTasks">⚡ Generate Tasks</span>
                    <span wire:loading wire:target="generateTasks">Generating…</span>
                </button>
                @if (!empty($taskResult))
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-2.5 text-left font-medium text-gray-600 dark:text-gray-400">Task</th>
                                <th class="px-4 py-2.5 text-left font-medium text-gray-600 dark:text-gray-400">Phase</th>
                                <th class="px-4 py-2.5 text-center font-medium text-gray-600 dark:text-gray-400">Days</th>
                                <th class="px-4 py-2.5 text-center font-medium text-gray-600 dark:text-gray-400">Priority</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach ($taskResult as $task)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-950 dark:text-white">{{ $task['title'] ?? '' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $task['description'] ?? '' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-400/10 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-400 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-400/30">{{ $task['phase'] ?? '' }}</span>
                                </td>
                                <td class="px-4 py-3 text-center font-mono text-gray-600 dark:text-gray-400">{{ $task['estimated_days'] ?? '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @php $p = $task['priority'] ?? 'medium'; @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                        {{ $p === 'high' ? 'bg-red-50 text-red-700 ring-red-600/10 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/20'
                                            : ($p === 'medium' ? 'bg-amber-50 text-amber-700 ring-amber-600/10 dark:bg-amber-400/10 dark:text-amber-400 dark:ring-amber-400/20'
                                            : 'bg-gray-50 text-gray-600 ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20') }}">{{ ucfirst($p) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            @endif

            {{-- BOQ --}}
            @if ($activeTool === 'boq')
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">🧮 BOQ Description Expander</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Turn a short label into a professional BOQ item description.</p>
                </div>
                <div class="flex gap-3">
                    <input type="text" wire:model="boqLabel" wire:keydown.enter="expandBOQ"
                        placeholder="e.g. Reinforced concrete slab 150mm"
                        class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm" />
                    <button wire:click="expandBOQ" wire:loading.attr="disabled" wire:target="expandBOQ"
                        class="fi-btn fi-btn-size-md inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50 whitespace-nowrap">
                        <span wire:loading.remove wire:target="expandBOQ">📝 Expand</span>
                        <span wire:loading wire:target="expandBOQ">Writing…</span>
                    </button>
                </div>
                @if ($boqResult)
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-gray-700">
                    <div class="prose dark:prose-invert prose-sm max-w-none">{!! \Illuminate\Support\Str::markdown($boqResult) !!}</div>
                </div>
                @endif
            </div>
            @endif

            {{-- TENDER --}}
            @if ($activeTool === 'tender')
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">🎯 Tender Analyser</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Extract requirements, risks, and get a Bid/No-Bid recommendation.</p>
                </div>
                <textarea wire:model="tenderDescription" rows="5" placeholder="Paste the tender description or key requirements…"
                    class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm"></textarea>
                <button wire:click="analyseTender" wire:loading.attr="disabled" wire:target="analyseTender"
                    class="fi-btn fi-btn-size-md inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50">
                    <span wire:loading.remove wire:target="analyseTender">🎯 Analyse Tender</span>
                    <span wire:loading wire:target="analyseTender">Analysing…</span>
                </button>
                @if (!empty($tenderResult) && !isset($tenderResult['error']))
                <div class="space-y-3">
                    <div class="p-4 rounded-lg border {{ ($tenderResult['bid_recommendation'] ?? '') === 'Bid' ? 'bg-green-50 dark:bg-green-950/20 border-green-300 dark:border-green-700' : 'bg-amber-50 dark:bg-amber-950/20 border-amber-300 dark:border-amber-700' }}">
                        <p class="font-bold text-lg text-gray-950 dark:text-white">{{ $tenderResult['bid_recommendation'] ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-0.5">{{ $tenderResult['bid_reason'] ?? '' }}</p>
                    </div>
                    @foreach (['key_requirements' => '📋 Key Requirements', 'evaluation_criteria' => '📊 Evaluation Criteria', 'risks' => '⚠️ Risks'] as $k => $label)
                        @if (!empty($tenderResult[$k]))
                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-gray-700">
                            <p class="font-semibold text-sm mb-1.5">{{ $label }}</p>
                            <ul class="list-disc list-inside text-sm space-y-1 text-gray-700 dark:text-gray-300">
                                @foreach ($tenderResult[$k] as $item)<li>{{ $item }}</li>@endforeach
                            </ul>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            {{-- DIARY --}}
            @if ($activeTool === 'diary')
            <div class="p-6 space-y-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">📝 Daily Diary Drafter</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Generate a professional site diary entry from quick inputs.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <input type="number" wire:model="diaryCrewCount" placeholder="Crew count" min="0"
                        class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm" />
                    <select wire:model="diaryWeather"
                        class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm">
                        <option value="clear">☀️ Clear</option>
                        <option value="cloudy">☁️ Cloudy</option>
                        <option value="rainy">🌧️ Rainy</option>
                        <option value="stormy">⛈️ Stormy</option>
                        <option value="hot">🔥 Hot</option>
                    </select>
                    <div class="flex items-center text-sm text-gray-500 dark:text-gray-400 px-3">📅 {{ now()->format('l, M d, Y') }}</div>
                </div>
                <textarea wire:model="diaryActivities" rows="3" placeholder="Activities carried out today…"
                    class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm"></textarea>
                <textarea wire:model="diaryIssues" rows="2" placeholder="Issues or observations (optional)…"
                    class="fi-input block w-full rounded-lg border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white sm:text-sm"></textarea>
                <button wire:click="draftDiary" wire:loading.attr="disabled" wire:target="draftDiary"
                    class="fi-btn fi-btn-size-md inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-semibold text-white bg-primary-600 hover:bg-primary-500 disabled:opacity-50">
                    <span wire:loading.remove wire:target="draftDiary">📋 Draft Entry</span>
                    <span wire:loading wire:target="draftDiary">Drafting…</span>
                </button>
                @if ($diaryResult)
                <div class="p-4 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-gray-700">
                    <div class="prose dark:prose-invert prose-sm max-w-none">{!! \Illuminate\Support\Str::markdown($diaryResult) !!}</div>
                </div>
                @endif
            </div>
            @endif

        </div>

        {{-- Session History --}}
        @if (!empty($history))
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-semibold text-gray-950 dark:text-white">🕐 Session History</h4>
                <button wire:click="clearHistory" class="text-xs text-gray-500 hover:text-red-500">Clear</button>
            </div>
            <div class="space-y-1">
                @foreach (array_reverse($history) as $item)
                <div class="flex items-center gap-3 text-sm py-1.5 px-3 rounded-lg bg-gray-50 dark:bg-white/5">
                    <span class="text-xs text-gray-400 font-mono">{{ $item['time'] }}</span>
                    <span class="inline-flex items-center rounded-md bg-primary-50 dark:bg-primary-400/10 px-2 py-0.5 text-xs font-medium text-primary-700 dark:text-primary-400">{{ $item['tool'] }}</span>
                    <span class="truncate text-gray-600 dark:text-gray-400">{{ $item['question'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</x-filament-panels::page>
