<x-landing-layout title="InfraHub — User Manual Access" activePage="docs">

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="hero-badge">🔒 Registered User Access Only</div>
            <h1>InfraHub System Manual & Documentation</h1>
            <p>Complete module workflows, ISO 19650 specifications, and operational user guides are available exclusively to registered platform users.</p>
        </div>
    </section>

    <!-- Main Content Container -->
    <section class="container" style="padding-bottom: 80px;">
        <!-- Gate Action Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 28px; margin-bottom: 60px;">
            
            <!-- Book a Demo Card (For Unregistered Visitors) -->
            <div style="background: var(--bg-card); border: 2px solid var(--orange-500); border-radius: 20px; padding: 36px; display: flex; flex-direction: column; justify-space: space-between; box-shadow: var(--shadow-md); position: relative; overflow: hidden;">
                <div style="position: absolute; top: 16px; right: 16px; background: rgba(249, 115, 22, 0.12); color: var(--orange-500); font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 20px; text-transform: uppercase; letter-spacing: 1px;">
                    Recommended
                </div>

                <div>
                    <div style="font-size: 36px; margin-bottom: 16px;">📞</div>
                    <h3 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 10px;">Book a Live Demo Call</h3>
                    <p style="font-size: 14.5px; color: var(--text-muted); line-height: 1.65; margin-bottom: 24px;">
                        Not registered yet? Schedule a live demonstration with our technical engineering team to get a guided walkthrough of the InfraHub platform and access to system specs.
                    </p>
                </div>

                <div>
                    <a href="/schedule-call" class="btn-demo" style="width: 100%; text-align: center; justify-content: center; padding: 14px 24px; font-size: 15px; font-weight: 700;">
                        Book a Live Demo <span>→</span>
                    </a>
                </div>
            </div>

            <!-- Existing User Login Card -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: 20px; padding: 36px; display: flex; flex-direction: column; justify-space: space-between; box-shadow: var(--shadow-sm);">
                <div>
                    <div style="font-size: 36px; margin-bottom: 16px;">🔐</div>
                    <h3 style="font-size: 22px; font-weight: 800; color: var(--text-main); margin-bottom: 10px;">Already Registered?</h3>
                    <p style="font-size: 14.5px; color: var(--text-muted); line-height: 1.65; margin-bottom: 24px;">
                        If your organization is already onboarded on InfraHub, log in with your account credentials to view the full interactive user manual and API guides.
                    </p>
                </div>

                <div>
                    <a href="{{ url('/app/login') }}" class="btn-login" style="width: 100%; text-align: center; justify-content: center; padding: 14px 24px; font-size: 15px; font-weight: 700; border: 1px solid var(--border-subtle);">
                        Log In to Your Account
                    </a>
                </div>
            </div>

        </div>

        <!-- System Capabilities Preview Grid -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: 20px; padding: 40px;">
            <h3 style="font-size: 20px; font-weight: 800; color: var(--text-main); margin-bottom: 24px; text-align: center;">What's Covered in the Full User Manual</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;">
                <div style="display: flex; gap: 14px; align-items: flex-start;">
                    <span style="font-size: 24px; background: rgba(249, 115, 22, 0.1); padding: 8px; border-radius: 12px;">🏗️</span>
                    <div>
                        <strong style="display: block; font-size: 15px; color: var(--text-main); margin-bottom: 4px;">ISO 19650 CDE Gateway</strong>
                        <span style="font-size: 13px; color: var(--text-muted);">Common Data Environment workflows & drawing revision rules.</span>
                    </div>
                </div>

                <div style="display: flex; gap: 14px; align-items: flex-start;">
                    <span style="font-size: 24px; background: rgba(59, 130, 246, 0.1); padding: 8px; border-radius: 12px;">📊</span>
                    <div>
                        <strong style="display: block; font-size: 15px; color: var(--text-main); margin-bottom: 4px;">BOQ & Variation Contracts</strong>
                        <span style="font-size: 13px; color: var(--text-muted);">Excel paste import, IPC valuation certificates & tender bids.</span>
                    </div>
                </div>

                <div style="display: flex; gap: 14px; align-items: flex-start;">
                    <span style="font-size: 24px; background: rgba(16, 185, 129, 0.1); padding: 8px; border-radius: 12px;">📅</span>
                    <div>
                        <strong style="display: block; font-size: 15px; color: var(--text-main); margin-bottom: 4px;">WBS & MS Project Integration</strong>
                        <span style="font-size: 13px; color: var(--text-muted);">XML file import, Gantt chart management & EVM progress control.</span>
                    </div>
                </div>

                <div style="display: flex; gap: 14px; align-items: flex-start;">
                    <span style="font-size: 24px; background: rgba(139, 92, 246, 0.1); padding: 8px; border-radius: 12px;">👷</span>
                    <div>
                        <strong style="display: block; font-size: 15px; color: var(--text-main); margin-bottom: 4px;">AI SHEQ & Site Diaries</strong>
                        <span style="font-size: 13px; color: var(--text-muted);">Safety incident logging, daily site diaries & risk pulse analytics.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

</x-landing-layout>
