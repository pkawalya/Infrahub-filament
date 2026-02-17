<x-filament-panels::page>
    <style>
        .upgrade-page {
            --amber: #e8a229;
            --navy: #1e3a5f;
            /* Light mode defaults */
            --card-bg: #ffffff;
            --card-border: #e5e7eb;
            --card-hover-border: #d1d5db;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;
            --stepper-bg: #f3f4f6;
            --stepper-hover: #e5e7eb;
            --stepper-text: #1f2937;
            --input-bg: #f9fafb;
            --input-border: #e5e7eb;
            --input-text: #111827;
            --summary-bg: #fffbf0;
            --summary-border: rgba(232, 162, 41, 0.25);
        }

        /* Dark mode overrides — Filament uses .dark class on <html> */
        .dark .upgrade-page {
            --card-bg: rgba(255, 255, 255, 0.03);
            --card-border: rgba(255, 255, 255, 0.08);
            --card-hover-border: rgba(255, 255, 255, 0.15);
            --text-primary: #f9fafb;
            --text-secondary: rgba(255, 255, 255, 0.6);
            --text-muted: rgba(255, 255, 255, 0.4);
            --stepper-bg: rgba(255, 255, 255, 0.08);
            --stepper-hover: rgba(255, 255, 255, 0.15);
            --stepper-text: #ffffff;
            --input-bg: rgba(255, 255, 255, 0.05);
            --input-border: rgba(255, 255, 255, 0.1);
            --input-text: #ffffff;
            --summary-bg: rgba(232, 162, 41, 0.06);
            --summary-border: rgba(232, 162, 41, 0.15);
        }

        /* ─── Plan Banner (always dark) ─── */
        .plan-banner {
            background: linear-gradient(135deg, var(--navy) 0%, #152d4a 50%, #0f1f35 100%);
            border-radius: 20px;
            padding: 32px 36px;
            position: relative;
            overflow: hidden;
            color: #fff;
        }

        .plan-banner::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(232, 162, 41, 0.15), transparent 70%);
            border-radius: 50%;
        }

        .plan-banner-inner {
            position: relative;
            z-index: 1;
        }

        .plan-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(232, 162, 41, 0.15);
            border: 1px solid rgba(232, 162, 41, 0.3);
            color: var(--amber);
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .plan-name {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .plan-meta {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
        }

        .usage-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 28px;
        }

        .usage-card {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }

        .usage-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .usage-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .usage-icon.users {
            background: rgba(99, 102, 241, 0.2);
            color: #818cf8;
        }

        .usage-icon.projects {
            background: rgba(232, 162, 41, 0.2);
            color: var(--amber);
        }

        .usage-icon.storage {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        .usage-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .usage-value {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: #fff;
        }

        .usage-value span {
            font-size: 15px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.4);
        }

        .usage-bar {
            height: 6px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 100px;
            overflow: hidden;
            margin-top: 10px;
        }

        .usage-fill {
            height: 100%;
            border-radius: 100px;
            transition: width 0.6s ease;
        }

        .usage-fill.low {
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .usage-fill.mid {
            background: linear-gradient(90deg, #f59e0b, #fbbf24);
        }

        .usage-fill.high {
            background: linear-gradient(90deg, #ef4444, #f87171);
        }

        /* ─── Section Titles ─── */
        .section-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.3px;
            margin-bottom: 6px;
            color: var(--text-primary);
        }

        .section-desc {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        /* ─── Plan Cards ─── */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 16px;
        }

        .plan-card {
            border-radius: 16px;
            padding: 28px 24px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s;
            background: var(--card-bg);
            border: 2px solid var(--card-border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .plan-card:hover {
            transform: translateY(-3px);
            border-color: var(--card-hover-border);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        .plan-card.selected {
            border-color: var(--amber);
            background: rgba(232, 162, 41, 0.04);
        }

        .dark .plan-card.selected {
            background: rgba(232, 162, 41, 0.08);
        }

        .plan-card.is-current .current-tag {
            position: absolute;
            top: -1px;
            right: 20px;
            padding: 4px 14px;
            border-radius: 0 0 10px 10px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: var(--amber);
            color: var(--navy);
        }

        .plan-card-name {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .plan-card-price {
            font-size: 32px;
            font-weight: 900;
            letter-spacing: -1px;
            margin-bottom: 16px;
            color: var(--text-primary);
        }

        .plan-card-price small {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .plan-card ul {
            list-style: none;
            padding: 0;
            margin: 0 0 20px 0;
        }

        .plan-card li {
            font-size: 13px;
            padding: 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
        }

        .plan-card li::before {
            content: '✓';
            color: #10b981;
            font-weight: 700;
        }

        .plan-card-btn {
            display: block;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .plan-card-btn.select-btn {
            background: linear-gradient(135deg, var(--amber), #d4911e);
            color: var(--navy);
            box-shadow: 0 4px 16px rgba(232, 162, 41, 0.25);
        }

        .plan-card-btn.select-btn:hover {
            box-shadow: 0 6px 24px rgba(232, 162, 41, 0.35);
        }

        .plan-card-btn.selected-btn {
            background: rgba(232, 162, 41, 0.12);
            color: #b8860b;
            border: 1px solid rgba(232, 162, 41, 0.3);
        }

        .dark .plan-card-btn.selected-btn {
            color: var(--amber);
        }

        /* ─── Addon Cards ─── */
        .addon-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .addon-card {
            border-radius: 16px;
            padding: 24px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            transition: all 0.3s;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .addon-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        .addon-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .addon-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .addon-card-icon.users {
            background: linear-gradient(135deg, #6366f1, #818cf8);
            color: #fff;
        }

        .addon-card-icon.projects {
            background: linear-gradient(135deg, #d4911e, #e8a229);
            color: #fff;
        }

        .addon-card-icon.storage {
            background: linear-gradient(135deg, #059669, #10b981);
            color: #fff;
        }

        .addon-card-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .addon-card-price {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* ─── Stepper ─── */
        .addon-stepper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .addon-stepper button {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid var(--card-border);
            background: var(--stepper-bg);
            color: var(--stepper-text);
            flex-shrink: 0;
        }

        .addon-stepper button:hover {
            background: var(--stepper-hover);
            border-color: var(--card-hover-border);
        }

        .addon-stepper button:active {
            transform: scale(0.95);
        }

        .addon-stepper input {
            flex: 1;
            text-align: center;
            font-size: 22px;
            font-weight: 800;
            border-radius: 12px;
            padding: 10px 4px;
            border: 1px solid var(--input-border);
            outline: none;
            background: var(--input-bg);
            color: var(--input-text);
            min-width: 0;
            transition: border-color 0.2s;
        }

        .addon-stepper input:focus {
            border-color: var(--amber);
            box-shadow: 0 0 0 3px rgba(232, 162, 41, 0.1);
        }

        .addon-info {
            text-align: center;
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 14px;
        }

        .addon-info strong {
            color: var(--text-primary);
        }

        .addon-cost {
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            margin-top: 6px;
        }

        /* ─── Summary Bar ─── */
        .summary-bar {
            margin-top: 24px;
            border-radius: 16px;
            padding: 28px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            flex-wrap: wrap;
            background: var(--summary-bg);
            border: 1px solid var(--summary-border);
        }

        .summary-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-amount {
            font-size: 36px;
            font-weight: 900;
            letter-spacing: -1px;
            color: var(--text-primary);
        }

        .summary-amount small {
            font-size: 16px;
            font-weight: 500;
            color: var(--text-muted);
        }

        .summary-breakdown {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .apply-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 16px 32px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--amber), #d4911e);
            color: var(--navy);
            font-size: 16px;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(232, 162, 41, 0.3);
        }

        .apply-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(232, 162, 41, 0.4);
        }

        .apply-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* ─── Contact Section ─── */
        .contact-section {
            margin-top: 32px;
            border-radius: 16px;
            padding: 28px 32px;
            text-align: center;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
        }

        .contact-section h3 {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .contact-section p {
            font-size: 14px;
            color: var(--text-secondary);
        }

        .contact-section a {
            color: var(--amber);
            font-weight: 700;
            text-decoration: none;
        }

        .contact-section a:hover {
            text-decoration: underline;
        }

        /* ─── Responsive ─── */
        @media (max-width: 768px) {

            .usage-grid,
            .addon-grid {
                grid-template-columns: 1fr;
            }

            .plans-grid {
                grid-template-columns: 1fr;
            }

            .summary-bar {
                flex-direction: column;
                text-align: center;
            }

            .plan-banner {
                padding: 24px 20px;
            }
        }
    </style>

    @php
        $sym = $addonPricing['currency_symbol'];
        $cycle = $addonPricing['billing_cycle'] === 'monthly' ? 'mo' : ($addonPricing['billing_cycle'] === 'yearly' ? 'yr' : 'once');
        $pUser = $addonPricing['price_per_extra_user'];
        $pProj = $addonPricing['price_per_extra_project'];
        $pStor = $addonPricing['price_per_extra_gb'];
    @endphp

    <div class="upgrade-page" x-data="{
        extraUsers: @entangle('extraUsers'),
        extraProjects: @entangle('extraProjects'),
        extraStorage: @entangle('extraStorage'),
        selectedPlanId: @entangle('selectedPlanId'),
        pUser: {{ $pUser }},
        pProj: {{ $pProj }},
        pStor: {{ $pStor }},
        sym: '{{ $sym }}',
        cycle: '{{ $cycle }}',
        plans: @js($plans->map(fn($p) => ['id' => $p->id, 'max_users' => $p->max_users, 'max_projects' => $p->max_projects, 'max_storage_gb' => $p->max_storage_gb, 'monthly_price' => (float) $p->monthly_price])),
        get selectedPlan() {
            return this.plans.find(p => p.id === this.selectedPlanId) || { max_users: 0, max_projects: 0, max_storage_gb: 0, monthly_price: 0 };
        },
        get effectiveUsers()    { return this.selectedPlan.max_users + Math.max(0, this.extraUsers); },
        get effectiveProjects() { return this.selectedPlan.max_projects + Math.max(0, this.extraProjects); },
        get effectiveStorage()  { return this.selectedPlan.max_storage_gb + Math.max(0, this.extraStorage); },
        get addonCost()   { return (Math.max(0, this.extraUsers) * this.pUser) + (Math.max(0, this.extraProjects) * this.pProj) + (Math.max(0, this.extraStorage) * this.pStor); },
        get planCost()    { return this.selectedPlan.monthly_price; },
        get totalCost()   { return this.planCost + this.addonCost; },
        inc(prop) { this[prop] = Math.max(0, this[prop]) + 1; },
        dec(prop) { this[prop] = Math.max(0, this[prop] - 1); }
    }">

        {{-- ═══ CURRENT PLAN BANNER ═══ --}}
        @if($currentPlan)
            <div class="plan-banner">
                <div class="plan-banner-inner">
                    <div class="plan-badge">
                        ⚡ {{ $company->isInTrial() ? 'Trial' : 'Active' }} Subscription
                    </div>
                    <div class="plan-name">{{ $currentPlan->name }}</div>
                    <div class="plan-meta">
                        @if($company->isInTrial())
                            Trial ends {{ $company->trial_ends_at->format('M d, Y') }}
                            ({{ $company->trial_ends_at->diffForHumans() }})
                        @else
                            {{ ucfirst($company->billing_cycle ?? 'monthly') }} billing ·
                            ${{ number_format($currentPlan->monthly_price, 0) }}/mo
                        @endif
                    </div>

                    @php
                        $usersCount = $company->users()->count();
                        $projectsCount = $company->projects()->count();
                        $storageUsed = round(($company->current_storage_bytes ?? 0) / 1073741824, 1);
                        $effUsers = $company->getEffectiveMaxUsers();
                        $effProjects = $company->getEffectiveMaxProjects();
                        $effStorage = $company->getEffectiveMaxStorageGb();
                        $usersPct = $effUsers ? round(($usersCount / $effUsers) * 100) : 0;
                        $projectsPct = $effProjects ? round(($projectsCount / $effProjects) * 100) : 0;
                        $storagePct = $effStorage ? round(($storageUsed / $effStorage) * 100) : 0;
                    @endphp
                    <div class="usage-grid">
                        <div class="usage-card">
                            <div class="usage-header">
                                <div class="usage-icon users">👥</div>
                                <div class="usage-label">Users</div>
                            </div>
                            <div class="usage-value">{{ $usersCount }} <span>/
                                    {{ $effUsers }}{{ $company->extra_users ? ' (' . $company->max_users . '+' . $company->extra_users . ')' : '' }}</span>
                            </div>
                            <div class="usage-bar">
                                <div class="usage-fill {{ $usersPct >= 90 ? 'high' : ($usersPct >= 70 ? 'mid' : 'low') }}"
                                    style="width: {{ min($usersPct, 100) }}%"></div>
                            </div>
                        </div>
                        <div class="usage-card">
                            <div class="usage-header">
                                <div class="usage-icon projects">📁</div>
                                <div class="usage-label">Projects</div>
                            </div>
                            <div class="usage-value">{{ $projectsCount }} <span>/
                                    {{ $effProjects }}{{ $company->extra_projects ? ' (' . $company->max_projects . '+' . $company->extra_projects . ')' : '' }}</span>
                            </div>
                            <div class="usage-bar">
                                <div class="usage-fill {{ $projectsPct >= 90 ? 'high' : ($projectsPct >= 70 ? 'mid' : 'low') }}"
                                    style="width: {{ min($projectsPct, 100) }}%"></div>
                            </div>
                        </div>
                        <div class="usage-card">
                            <div class="usage-header">
                                <div class="usage-icon storage">💾</div>
                                <div class="usage-label">Storage</div>
                            </div>
                            <div class="usage-value">{{ $storageUsed }} <span>/ {{ $effStorage }}
                                    GB{{ $company->extra_storage_gb ? ' (' . $company->max_storage_gb . '+' . $company->extra_storage_gb . ')' : '' }}</span>
                            </div>
                            <div class="usage-bar">
                                <div class="usage-fill {{ $storagePct >= 90 ? 'high' : ($storagePct >= 70 ? 'mid' : 'low') }}"
                                    style="width: {{ min($storagePct, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ═══ CHOOSE YOUR PLAN ═══ --}}
        <div style="margin-top: 32px;">
            <div class="section-title">1. Choose Your Plan</div>
            <div class="section-desc">Select the base plan that fits your team. You can add extra resources on top.
            </div>

            <div class="plans-grid">
                @foreach($plans as $plan)
                    <div class="plan-card"
                        :class="{ 'selected': selectedPlanId === {{ $plan->id }}, 'is-current': {{ $currentPlan?->id === $plan->id ? 'true' : 'false' }} }"
                        @click="selectedPlanId = {{ $plan->id }}">

                        @if($currentPlan && $currentPlan->id === $plan->id)
                            <div class="current-tag">CURRENT</div>
                        @endif

                        <div class="plan-card-name">{{ $plan->name }}</div>
                        <div class="plan-card-price">
                            ${{ number_format($plan->monthly_price, 0) }}<small>/mo</small>
                        </div>

                        <ul>
                            <li>{{ $plan->max_users }} users</li>
                            <li>{{ $plan->max_projects }} projects</li>
                            <li>{{ $plan->max_storage_gb }} GB storage</li>
                            @if($plan->features)
                                @foreach(array_slice($plan->features, 0, 3) as $feat)
                                    <li>{{ $feat }}</li>
                                @endforeach
                            @endif
                        </ul>

                        <button type="button" class="plan-card-btn" :class="{
                                        'select-btn': selectedPlanId !== {{ $plan->id }},
                                        'selected-btn': selectedPlanId === {{ $plan->id }}
                                    }" @click.stop="selectedPlanId = {{ $plan->id }}; $wire.switchPlan({{ $plan->id }})">
                            <span x-text="selectedPlanId === {{ $plan->id }} ? '✓ Selected' : 'Select Plan'"></span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ═══ ADD EXTRA RESOURCES ═══ --}}
        <div style="margin-top: 40px;">
            <div class="section-title">2. Add Extra Resources</div>
            <div class="section-desc">Need more capacity on top of your plan? Adjust the numbers below.</div>

            <div class="addon-grid">
                {{-- Users --}}
                <div class="addon-card">
                    <div class="addon-card-header">
                        <div class="addon-card-icon users">👥</div>
                        <div>
                            <div class="addon-card-title">Extra Users</div>
                            <div class="addon-card-price">{{ $sym }}{{ number_format($pUser, 2) }} / user / {{ $cycle }}
                            </div>
                        </div>
                    </div>
                    <div class="addon-stepper">
                        <button type="button" @click="dec('extraUsers')">−</button>
                        <input type="number" x-model.number="extraUsers" min="0" max="500" />
                        <button type="button" @click="inc('extraUsers')">+</button>
                    </div>
                    <div class="addon-info">
                        Effective total: <strong x-text="effectiveUsers"></strong> users
                    </div>
                    <div class="addon-cost" style="color: #818cf8;" x-show="extraUsers > 0" x-transition>
                        +<span x-text="sym"></span><span
                            x-text="(Math.max(0, extraUsers) * pUser).toFixed(2)"></span>/<span x-text="cycle"></span>
                    </div>
                </div>

                {{-- Projects --}}
                <div class="addon-card">
                    <div class="addon-card-header">
                        <div class="addon-card-icon projects">📁</div>
                        <div>
                            <div class="addon-card-title">Extra Projects</div>
                            <div class="addon-card-price">{{ $sym }}{{ number_format($pProj, 2) }} / project /
                                {{ $cycle }}
                            </div>
                        </div>
                    </div>
                    <div class="addon-stepper">
                        <button type="button" @click="dec('extraProjects')">−</button>
                        <input type="number" x-model.number="extraProjects" min="0" max="500" />
                        <button type="button" @click="inc('extraProjects')">+</button>
                    </div>
                    <div class="addon-info">
                        Effective total: <strong x-text="effectiveProjects"></strong> projects
                    </div>
                    <div class="addon-cost" style="color: #e8a229;" x-show="extraProjects > 0" x-transition>
                        +<span x-text="sym"></span><span
                            x-text="(Math.max(0, extraProjects) * pProj).toFixed(2)"></span>/<span
                            x-text="cycle"></span>
                    </div>
                </div>

                {{-- Storage --}}
                <div class="addon-card">
                    <div class="addon-card-header">
                        <div class="addon-card-icon storage">💾</div>
                        <div>
                            <div class="addon-card-title">Extra Storage</div>
                            <div class="addon-card-price">{{ $sym }}{{ number_format($pStor, 2) }} / GB / {{ $cycle }}
                            </div>
                        </div>
                    </div>
                    <div class="addon-stepper">
                        <button type="button" @click="dec('extraStorage')">−</button>
                        <input type="number" x-model.number="extraStorage" min="0" max="5000" />
                        <button type="button" @click="inc('extraStorage')">+</button>
                    </div>
                    <div class="addon-info">
                        Effective total: <strong x-text="effectiveStorage"></strong> GB
                    </div>
                    <div class="addon-cost" style="color: #34d399;" x-show="extraStorage > 0" x-transition>
                        +<span x-text="sym"></span><span
                            x-text="(Math.max(0, extraStorage) * pStor).toFixed(2)"></span>/<span x-text="cycle"></span>
                    </div>
                </div>
            </div>

            {{-- Summary bar --}}
            <div class="summary-bar">
                <div>
                    <div class="summary-label">Total Estimated Cost</div>
                    <div class="summary-amount">
                        <span x-text="sym"></span><span x-text="totalCost.toFixed(2)"></span>
                        <small>/<span x-text="cycle"></span></small>
                    </div>
                    <div class="summary-breakdown">
                        Plan: <span x-text="sym"></span><span x-text="planCost.toFixed(2)"></span>
                        <template x-if="addonCost > 0">
                            <span> + Addons: <span x-text="sym"></span><span x-text="addonCost.toFixed(2)"></span>
                                (<span x-text="Math.max(0, extraUsers)"></span> user(s),
                                <span x-text="Math.max(0, extraProjects)"></span> project(s),
                                <span x-text="Math.max(0, extraStorage)"></span> GB)
                            </span>
                        </template>
                    </div>
                </div>
                <button type="button" class="apply-btn" wire:click="applyAddons" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="applyAddons">✓ Apply Changes</span>
                    <span wire:loading wire:target="applyAddons">Applying...</span>
                </button>
            </div>
        </div>

        {{-- ═══ CONTACT ═══ --}}
        <div class="contact-section">
            <h3>Need a custom plan?</h3>
            <p>
                Contact us at <a href="mailto:sales@infrahub.io">sales@infrahub.io</a>
                or call <strong>+256 700 000 000</strong> to discuss enterprise pricing.
            </p>
        </div>
    </div>
</x-filament-panels::page>