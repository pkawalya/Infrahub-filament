<x-landing-layout title="InfraHub — Products & Enterprise Modules" activePage="products">

    <!-- Hero Section -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-badge">🛠️ Modular Infrastructure System</div>
            <h1>18 Integrated Construction Modules.<br><span style="color: var(--orange-500);">One Unified Platform.</span></h1>
            <p>From WBS scheduling and MS Project XML sync to ISO 19650 CDE document control, AI incident analysis, and BOQ cost tracking.</p>
        </div>
    </section>

    <!-- Content Sections based on User Manual Groups -->
    <section class="container" style="padding-bottom: 60px;">
        @foreach ($groupedSections as $groupName => $sections)
            <div id="{{ strtolower(str_replace([' ', '&'], ['-', ''], $groupName)) }}" style="margin-top: 60px; scroll-margin-top: 100px;">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
                    <span style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; color: var(--orange-500);">
                        {{ $groupName }}
                    </span>
                    <div style="flex: 1; height: 1px; background: var(--border-subtle);"></div>
                </div>

                <div class="cards-grid">
                    @foreach ($sections as $section)
                        <div class="feature-card">
                            <div class="card-icon">{{ $section['icon'] ?? '⚡' }}</div>
                            <h3>{{ $section['title'] }}</h3>
                            <p>{{ Str::limit(strip_tags(Illuminate\Support\Str::markdown($section['content'])), 140) }}</p>
                            
                            <ul class="feature-list">
                                @if (str_contains($section['content'], 'WBS'))
                                    <li>Work Breakdown Structure (WBS) recalculation</li>
                                    <li>MS Project & Excel XML import/export</li>
                                @elseif (str_contains($section['content'], 'SHEQ'))
                                    <li>AI-assisted root cause analysis</li>
                                    <li>Snagging registry & inspection checklists</li>
                                @elseif (str_contains($section['content'], 'BOQ'))
                                    <li>Excel Paste-to-Import populator</li>
                                    <li>Contract variation tracking & alerts</li>
                                @elseif (str_contains($section['content'], 'ISO 19650'))
                                    <li>ISO 19650 document suitability gateways</li>
                                    <li>Revision control & transmittal logging</li>
                                @else
                                    <li>Real-time telemetry & audit tracking</li>
                                    <li>Role-based permission access</li>
                                @endif
                            </ul>

                            <div style="margin-top: 20px; text-align: right;">
                                <a href="/docs#{{ $section['slug'] }}" style="color: var(--orange-500); font-size: 13px; font-weight: 700; text-decoration: none;">
                                    Read in User Manual →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </section>

    <!-- CTA Banner -->
    <section style="background: var(--bg-subtle); padding: 80px 0; border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
        <div class="container" style="text-align: center;">
            <h2 style="font-size: 36px; font-weight: 800; margin-bottom: 16px;">Ready to digitize your jobsite?</h2>
            <p style="color: var(--text-muted); font-size: 16px; margin-bottom: 30px;">Get full access to all 18 modules with our 30-day trial or book a personal demo with our team.</p>
            <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
                <a href="/get-started" class="btn-demo">Start Free Trial <span>→</span></a>
                <a href="/schedule-call" class="btn-login" style="padding: 12px 24px; border: 1px solid var(--border-subtle);">Book a Personal Demo</a>
            </div>
        </div>
    </section>

</x-landing-layout>
