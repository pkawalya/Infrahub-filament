<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Get Started — InfraHub</title>
    <meta name="description"
        content="Create your InfraHub account. Choose a plan, set up your company, and start managing construction projects today.">
    <link rel="icon" type="image/svg+xml" href="{{ asset('logo/infrahub-icon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --navy-700: #152d4a;
            --navy-600: #1e3a5f;
            --navy-500: #2a4d7a;
            --amber-300: #f5c563;
            --amber-400: #e8a229;
            --amber-500: #d4911e;
        }

        [data-theme="dark"] {
            --bg-body: #020617;
            --bg-card: rgba(255, 255, 255, 0.02);
            --bg-card-hover: rgba(255, 255, 255, 0.04);
            --border-subtle: rgba(255, 255, 255, 0.06);
            --border-hover: rgba(255, 255, 255, 0.15);
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --input-bg: rgba(255, 255, 255, 0.04);
            --input-border: rgba(255, 255, 255, 0.1);
            --input-text: #ffffff;
            --option-bg: #0f172a;
            --step-bg: rgba(255, 255, 255, 0.04);
            --step-border: rgba(255, 255, 255, 0.06);
            --step-num-bg: rgba(255, 255, 255, 0.06);
            --grid-line: rgba(232, 162, 41, 0.03);
            --glow-opacity: 0.12;
            --ghost-text: #94a3b8;
            --ghost-border: rgba(255, 255, 255, 0.1);
        }

        [data-theme="light"] {
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --bg-card-hover: #f1f5f9;
            --border-subtle: #e2e8f0;
            --border-hover: #cbd5e1;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;
            --input-bg: #ffffff;
            --input-border: #cbd5e1;
            --input-text: #0f172a;
            --option-bg: #ffffff;
            --step-bg: #ffffff;
            --step-border: #e2e8f0;
            --step-num-bg: #e2e8f0;
            --grid-line: rgba(30, 58, 95, 0.04);
            --glow-opacity: 0.05;
            --ghost-text: #475569;
            --ghost-border: #cbd5e1;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image:
                linear-gradient(var(--grid-line) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .bg-glow {
            position: fixed;
            z-index: 0;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            filter: blur(120px);
            opacity: var(--glow-opacity);
            pointer-events: none;
        }

        .bg-glow-1 {
            top: -150px;
            right: -50px;
            background: var(--amber-400);
        }

        .bg-glow-2 {
            bottom: -150px;
            left: -50px;
            background: var(--navy-600);
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 1;
        }

        /* ─── Header ─── */
        .onboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 40px;
        }

        .onboard-header img {
            height: 40px;
            border-radius: 10px;
        }

        .onboard-header a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .onboard-header a:hover {
            color: var(--text-primary);
        }

        /* ─── Theme Toggle ─── */
        .theme-toggle {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--step-bg);
            border: 1px solid var(--border-subtle);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 16px;
            color: var(--text-secondary);
        }

        .theme-toggle:hover {
            border-color: var(--amber-400);
            color: var(--amber-400);
        }

        .theme-toggle .icon-sun {
            display: none;
        }

        .theme-toggle .icon-moon {
            display: block;
        }

        [data-theme="light"] .theme-toggle .icon-sun {
            display: block;
        }

        [data-theme="light"] .theme-toggle .icon-moon {
            display: none;
        }

        /* ─── Stepper ─── */
        .steps {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-bottom: 48px;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 100px;
            font-size: 13px;
            font-weight: 600;
            background: var(--step-bg);
            color: var(--text-muted);
            border: 1px solid var(--step-border);
            transition: all 0.3s;
            cursor: pointer;
        }

        .step.active {
            background: rgba(232, 162, 41, 0.12);
            color: var(--amber-400);
            border-color: rgba(232, 162, 41, 0.3);
        }

        .step.completed {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border-color: rgba(16, 185, 129, 0.25);
        }

        .step-number {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--step-num-bg);
            font-size: 12px;
        }

        .step.active .step-number {
            background: var(--amber-400);
            color: var(--navy-700);
        }

        .step.completed .step-number {
            background: #10b981;
            color: #fff;
        }

        /* ─── Panels ─── */
        .panel {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .panel.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .panel-title {
            font-size: 28px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 6px;
            letter-spacing: -0.5px;
        }

        .panel-desc {
            text-align: center;
            color: var(--text-secondary);
            margin-bottom: 36px;
            font-size: 15px;
        }

        /* ─── Plan Cards ─── */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .plan-card {
            background: var(--bg-card);
            border: 2px solid var(--border-subtle);
            border-radius: 16px;
            padding: 28px 24px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .plan-card:hover {
            border-color: var(--border-hover);
            transform: translateY(-3px);
        }

        .plan-card.selected {
            border-color: var(--amber-400);
            background: rgba(232, 162, 41, 0.05);
        }

        .plan-card.popular::before {
            content: 'POPULAR';
            position: absolute;
            top: -1px;
            right: 20px;
            background: var(--amber-400);
            color: var(--navy-700);
            padding: 4px 12px;
            border-radius: 0 0 8px 8px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .plan-name {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .plan-price {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 4px;
        }

        .plan-price span {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .plan-specs {
            list-style: none;
            margin-top: 16px;
        }

        .plan-specs li {
            font-size: 13px;
            color: var(--text-secondary);
            padding: 4px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .plan-specs li::before {
            content: '✓';
            color: #10b981;
            font-weight: 700;
        }

        /* ─── Form ─── */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            max-width: 640px;
            margin: 0 auto;
        }

        .form-grid .full {
            grid-column: 1 / -1;
        }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .field input,
        .field select {
            width: 100%;
            padding: 12px 16px;
            border-radius: 10px;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            color: var(--input-text);
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .field input::placeholder {
            color: var(--text-muted);
        }

        .field input:focus,
        .field select:focus {
            border-color: var(--amber-400);
            box-shadow: 0 0 0 3px rgba(232, 162, 41, 0.15);
        }

        .field select option {
            background: var(--option-bg);
            color: var(--input-text);
        }

        .error-msg {
            color: #f43f5e;
            font-size: 12px;
            margin-top: 4px;
        }

        /* ─── Buttons ─── */
        .btn-row {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin-top: 32px;
            max-width: 640px;
            margin-left: auto;
            margin-right: auto;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--amber-400), var(--amber-500));
            color: var(--navy-700);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(232, 162, 41, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-ghost {
            background: transparent;
            color: var(--ghost-text);
            border: 1px solid var(--ghost-border);
        }

        .btn-ghost:hover {
            border-color: var(--amber-400);
            color: var(--text-primary);
        }

        /* ─── Alerts ─── */
        .alert-error {
            background: rgba(244, 63, 94, 0.08);
            border: 1px solid rgba(244, 63, 94, 0.25);
            color: #fda4af;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            font-size: 14px;
            max-width: 640px;
            margin-left: auto;
            margin-right: auto;
        }

        @media (max-width: 768px) {
            .plans-grid {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .steps {
                flex-wrap: wrap;
            }

            .step {
                font-size: 12px;
                padding: 8px 14px;
            }
        }
    </style>
</head>

<body>
    <div class="bg-grid"></div>
    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>

    <div class="container">
        {{-- Header --}}
        <div class="onboard-header">
            <a href="/"><img src="{{ asset('logo/infrahub-logo-new.png') }}" alt="InfraHub"></a>
            <div style="display:flex;align-items:center;gap:12px;">
                <button class="theme-toggle" onclick="toggleTheme()" title="Toggle light/dark mode">
                    <span class="icon-moon">🌙</span>
                    <span class="icon-sun">☀️</span>
                </button>
                <a href="/app/login">Already have an account? Log in →</a>
            </div>
        </div>

        {{-- Steps --}}
        <div class="steps">
            <div class="step active" id="step-indicator-1" onclick="goToStep(1)">
                <span class="step-number">1</span> Choose Plan
            </div>
            <div class="step" id="step-indicator-2" onclick="goToStep(2)">
                <span class="step-number">2</span> Company Details
            </div>
            <div class="step" id="step-indicator-3" onclick="goToStep(3)">
                <span class="step-number">3</span> Your Account
            </div>
        </div>

        @if ($errors->any())
            <div class="alert-error">
                <strong>Please fix the following:</strong>
                <ul style="margin: 8px 0 0 16px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('onboarding.store') }}" id="onboardForm">
            @csrf

            {{-- ═══ STEP 1 — Choose Plan ═══ --}}
            <div class="panel active" id="panel-1">
                <div class="panel-title">Choose Your Plan</div>
                <p class="panel-desc">Start with a 14-day free trial. No credit card required. Upgrade anytime.</p>

                <div class="plans-grid">
                    @foreach ($plans as $plan)
                        <div class="plan-card {{ $plan->is_popular ? 'popular' : '' }} {{ old('subscription_id') == $plan->id ? 'selected' : '' }}"
                            onclick="selectPlan({{ $plan->id }}, this)">
                            <div class="plan-name">{{ $plan->name }}</div>
                            <div class="plan-price">
                                ${{ number_format($plan->monthly_price, 0) }}
                                <span>/mo</span>
                            </div>
                            <ul class="plan-specs">
                                <li>{{ $plan->max_users }} users</li>
                                <li>{{ $plan->max_projects }} projects</li>
                                <li>{{ $plan->max_storage_gb }} GB storage</li>
                                @if($plan->features)
                                    @foreach(array_slice($plan->features, 0, 3) as $feat)
                                        <li>{{ $feat }}</li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    @endforeach
                </div>

                <input type="hidden" name="subscription_id" id="subscription_id" value="{{ old('subscription_id') }}">
                @error('subscription_id') <p class="error-msg" style="text-align:center;">{{ $message }}</p> @enderror

                <div class="btn-row" style="justify-content: flex-end;">
                    <button type="button" class="btn btn-primary" onclick="nextStep()" id="btn-plan-next" disabled>
                        Continue →
                    </button>
                </div>
            </div>

            {{-- ═══ STEP 2 — Company ═══ --}}
            <div class="panel" id="panel-2">
                <div class="panel-title">Company Details</div>
                <p class="panel-desc">Tell us about your organization.</p>

                <div class="form-grid">
                    <div class="field full">
                        <label for="company_name">Company / Organization Name *</label>
                        <input type="text" name="company_name" id="company_name"
                            placeholder="e.g. Omega Construction Ltd" value="{{ old('company_name') }}" required>
                        @error('company_name') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label for="industry">Industry *</label>
                        <select name="industry" id="industry" required>
                            <option value="">Select industry…</option>
                            <option value="general_construction" {{ old('industry') == 'general_construction' ? 'selected' : '' }}>General Construction</option>
                            <option value="civil_engineering" {{ old('industry') == 'civil_engineering' ? 'selected' : '' }}>Civil Engineering</option>
                            <option value="building_construction" {{ old('industry') == 'building_construction' ? 'selected' : '' }}>Building Construction</option>
                            <option value="road_works" {{ old('industry') == 'road_works' ? 'selected' : '' }}>Road &
                                Highway Works</option>
                            <option value="water_sanitation" {{ old('industry') == 'water_sanitation' ? 'selected' : '' }}>Water & Sanitation</option>
                            <option value="electrical" {{ old('industry') == 'electrical' ? 'selected' : '' }}>Electrical
                                Engineering</option>
                            <option value="oil_gas" {{ old('industry') == 'oil_gas' ? 'selected' : '' }}>Oil & Gas
                            </option>
                            <option value="real_estate" {{ old('industry') == 'real_estate' ? 'selected' : '' }}>Real
                                Estate Development</option>
                            <option value="consulting" {{ old('industry') == 'consulting' ? 'selected' : '' }}>Consulting
                                / PM</option>
                            <option value="government" {{ old('industry') == 'government' ? 'selected' : '' }}>Government
                                / Public Sector</option>
                            <option value="other" {{ old('industry') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('industry') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label for="team_size">Team Size *</label>
                        <select name="team_size" id="team_size" required>
                            <option value="">Select size…</option>
                            <option value="1-5" {{ old('team_size') == '1-5' ? 'selected' : '' }}>1–5 people</option>
                            <option value="6-20" {{ old('team_size') == '6-20' ? 'selected' : '' }}>6–20 people</option>
                            <option value="21-50" {{ old('team_size') == '21-50' ? 'selected' : '' }}>21–50 people
                            </option>
                            <option value="51-100" {{ old('team_size') == '51-100' ? 'selected' : '' }}>51–100 people
                            </option>
                            <option value="100+" {{ old('team_size') == '100+' ? 'selected' : '' }}>100+ people</option>
                        </select>
                        @error('team_size') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label for="company_email">Company Email</label>
                        <input type="email" name="company_email" id="company_email" placeholder="info@company.com"
                            value="{{ old('company_email') }}">
                    </div>

                    <div class="field">
                        <label for="company_phone">Company Phone</label>
                        <input type="tel" name="company_phone" id="company_phone" placeholder="+256 700 000 000"
                            value="{{ old('company_phone') }}">
                    </div>

                    <div class="field full">
                        <label for="company_country">Country *</label>
                        <select name="company_country" id="company_country" required>
                            <option value="">Select country…</option>
                            <option value="UG" {{ old('company_country') == 'UG' ? 'selected' : '' }}>🇺🇬 Uganda</option>
                            <option value="KE" {{ old('company_country') == 'KE' ? 'selected' : '' }}>🇰🇪 Kenya</option>
                            <option value="TZ" {{ old('company_country') == 'TZ' ? 'selected' : '' }}>🇹🇿 Tanzania
                            </option>
                            <option value="RW" {{ old('company_country') == 'RW' ? 'selected' : '' }}>🇷🇼 Rwanda</option>
                            <option value="SS" {{ old('company_country') == 'SS' ? 'selected' : '' }}>🇸🇸 South Sudan
                            </option>
                            <option value="NG" {{ old('company_country') == 'NG' ? 'selected' : '' }}>🇳🇬 Nigeria
                            </option>
                            <option value="GH" {{ old('company_country') == 'GH' ? 'selected' : '' }}>🇬🇭 Ghana</option>
                            <option value="ZA" {{ old('company_country') == 'ZA' ? 'selected' : '' }}>🇿🇦 South Africa
                            </option>
                            <option value="GB" {{ old('company_country') == 'GB' ? 'selected' : '' }}>🇬🇧 United Kingdom
                            </option>
                            <option value="US" {{ old('company_country') == 'US' ? 'selected' : '' }}>🇺🇸 United States
                            </option>
                            <option value="AE" {{ old('company_country') == 'AE' ? 'selected' : '' }}>🇦🇪 UAE</option>
                            <option value="OTHER" {{ old('company_country') == 'OTHER' ? 'selected' : '' }}>🌍 Other
                            </option>
                        </select>
                        @error('company_country') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn btn-ghost" onclick="prevStep()">← Back</button>
                    <button type="button" class="btn btn-primary" onclick="nextStep()">Continue →</button>
                </div>
            </div>

            {{-- ═══ STEP 3 — Account ═══ --}}
            <div class="panel" id="panel-3">
                <div class="panel-title">Create Your Account</div>
                <p class="panel-desc">This will be the admin account for your company.</p>

                <div class="form-grid">
                    <div class="field full">
                        <label for="name">Full Name *</label>
                        <input type="text" name="name" id="name" placeholder="John Doe" value="{{ old('name') }}"
                            required>
                        @error('name') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label for="email">Email Address *</label>
                        <input type="email" name="email" id="email" placeholder="john@company.com"
                            value="{{ old('email') }}" required>
                        @error('email') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div class="field">
                        <label for="job_title">Job Title</label>
                        <input type="text" name="job_title" id="job_title" placeholder="e.g. Project Manager"
                            value="{{ old('job_title') }}">
                    </div>

                    <div class="field">
                        <label for="phone">Phone Number</label>
                        <input type="tel" name="phone" id="phone" placeholder="+256 700 000 000"
                            value="{{ old('phone') }}">
                    </div>

                    <div class="field">
                        <label for="password">Password *</label>
                        <input type="password" name="password" id="password" placeholder="Min 8 characters" required>
                        @error('password') <p class="error-msg">{{ $message }}</p> @enderror
                    </div>

                    <div class="field full">
                        <label for="password_confirmation">Confirm Password *</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            placeholder="Repeat password" required>
                    </div>
                </div>

                <div class="btn-row">
                    <button type="button" class="btn btn-ghost" onclick="prevStep()">← Back</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        🚀 Submit Application
                    </button>
                </div>

                <p style="text-align:center; color: var(--text-muted); font-size: 13px; margin-top: 20px;">
                    Your company will be reviewed and activated by our team.<br>
                    You'll receive an email once approved.
                </p>
            </div>
        </form>
    </div>

    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('infrahub-theme', next);
        }
        (function () {
            const saved = localStorage.getItem('infrahub-theme');
            if (saved) document.documentElement.setAttribute('data-theme', saved);
            else if (window.matchMedia('(prefers-color-scheme: light)').matches)
                document.documentElement.setAttribute('data-theme', 'light');
        })();

        let currentStep = 1;
        const totalSteps = 3;

        function selectPlan(id, el) {
            document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
            el.classList.add('selected');
            document.getElementById('subscription_id').value = id;
            document.getElementById('btn-plan-next').disabled = false;
        }

        function goToStep(step) {
            if (step > currentStep) return;
            currentStep = step;
            render();
        }

        function nextStep() {
            if (currentStep === 1 && !document.getElementById('subscription_id').value) return;
            if (currentStep < totalSteps) { currentStep++; render(); }
        }

        function prevStep() {
            if (currentStep > 1) { currentStep--; render(); }
        }

        function render() {
            for (let i = 1; i <= totalSteps; i++) {
                document.getElementById('panel-' + i).classList.toggle('active', i === currentStep);
                const ind = document.getElementById('step-indicator-' + i);
                ind.classList.remove('active', 'completed');
                if (i === currentStep) ind.classList.add('active');
                else if (i < currentStep) ind.classList.add('completed');
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        if (document.getElementById('subscription_id').value) {
            document.getElementById('btn-plan-next').disabled = false;
        }

        document.getElementById('onboardForm').addEventListener('submit', function () {
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.textContent = 'Submitting…';
        });
    </script>
</body>

</html>