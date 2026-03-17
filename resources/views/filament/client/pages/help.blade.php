<x-filament-panels::page>
    <div>
        <style>
            .ch-container {
                max-width: 800px;
                margin: 0 auto;
            }

            .ch-hero {
                text-align: center;
                padding: 2rem 1rem;
                background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(139, 92, 246, 0.06));
                border: 1px solid rgba(99, 102, 241, 0.12);
                border-radius: 16px;
                margin-bottom: 2rem;
            }

            .ch-hero h1 {
                font-size: 1.5rem;
                font-weight: 800;
                margin-bottom: 0.5rem;
            }

            .ch-hero p {
                color: #94a3b8;
                font-size: 0.88rem;
            }

            .ch-section {
                background: rgba(15, 23, 42, 0.5);
                border: 1px solid rgba(99, 102, 241, 0.1);
                border-radius: 14px;
                padding: 1.5rem;
                margin-bottom: 1rem;
                transition: all 0.2s;
            }

            .ch-section:hover {
                border-color: rgba(99, 102, 241, 0.25);
            }

            .ch-section-header {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                cursor: pointer;
                user-select: none;
            }

            .ch-section-icon {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                flex-shrink: 0;
            }

            .ch-section-title {
                font-size: 1rem;
                font-weight: 700;
            }

            .ch-section-desc {
                font-size: 0.78rem;
                color: #94a3b8;
                margin-top: 0.1rem;
            }

            .ch-toggle {
                margin-left: auto;
                font-size: 0.75rem;
                color: #64748b;
                transition: transform 0.2s;
            }

            .ch-toggle.open {
                transform: rotate(180deg);
            }

            .ch-body {
                display: none;
                padding-top: 1rem;
                margin-top: 1rem;
                border-top: 1px solid rgba(99, 102, 241, 0.08);
                font-size: 0.85rem;
                color: #cbd5e1;
                line-height: 1.7;
            }

            .ch-body.open {
                display: block;
            }

            .ch-body h4 {
                color: #e2e8f0;
                font-weight: 700;
                margin: 1rem 0 0.5rem;
                font-size: 0.88rem;
            }

            .ch-body h4:first-child {
                margin-top: 0;
            }

            .ch-body ul {
                padding-left: 1.25rem;
                margin: 0.5rem 0;
            }

            .ch-body li {
                margin-bottom: 0.35rem;
            }

            .ch-body code {
                background: rgba(99, 102, 241, 0.12);
                padding: 0.15rem 0.4rem;
                border-radius: 4px;
                font-size: 0.78rem;
                color: #a5b4fc;
            }

            .ch-step {
                display: flex;
                gap: 0.75rem;
                margin-bottom: 0.75rem;
                align-items: flex-start;
            }

            .ch-step-num {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                flex-shrink: 0;
                background: rgba(99, 102, 241, 0.15);
                color: #818cf8;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.7rem;
                font-weight: 800;
            }

            .ch-contact {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
                margin-top: 1.5rem;
            }

            .ch-contact-card {
                background: rgba(30, 41, 59, 0.6);
                border: 1px solid rgba(99, 102, 241, 0.1);
                border-radius: 12px;
                padding: 1rem;
                text-align: center;
            }

            .ch-contact-card .icon {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }

            .ch-contact-card .label {
                font-size: 0.72rem;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                font-weight: 600;
            }

            .ch-contact-card .value {
                font-size: 0.88rem;
                font-weight: 600;
                color: #e2e8f0;
                margin-top: 0.25rem;
            }
        </style>

        <div class="ch-container">
            {{-- Hero --}}
            <div class="ch-hero">
                <h1>👋 Welcome to InfraHub</h1>
                <p>Your Client Portal — view projects, track invoices, and access documents</p>
            </div>

            {{-- Getting Started --}}
            <div class="ch-section" onclick="toggleSection(this)">
                <div class="ch-section-header">
                    <div class="ch-section-icon" style="background:rgba(99,102,241,0.12);color:#818cf8;">🚀</div>
                    <div>
                        <div class="ch-section-title">Getting Started</div>
                        <div class="ch-section-desc">First steps after logging in</div>
                    </div>
                    <span class="ch-toggle">▼</span>
                </div>
                <div class="ch-body">
                    <div class="ch-step">
                        <span class="ch-step-num">1</span>
                        <div><strong>Change your password</strong> — On first login, you'll be asked to set a new
                            password. Choose something secure with at least 8 characters.</div>
                    </div>
                    <div class="ch-step">
                        <span class="ch-step-num">2</span>
                        <div><strong>Explore the Dashboard</strong> — Your dashboard shows an overview of your projects,
                            recent invoices, and available documents.</div>
                    </div>
                    <div class="ch-step">
                        <span class="ch-step-num">3</span>
                        <div><strong>Check your projects</strong> — Click <code>Projects</code> in the sidebar to see
                            all projects associated with your account.</div>
                    </div>
                    <div class="ch-step">
                        <span class="ch-step-num">4</span>
                        <div><strong>Enable 2FA</strong> — Go to your profile and enable Two-Factor Authentication for
                            added security.</div>
                    </div>
                </div>
            </div>

            {{-- Projects --}}
            <div class="ch-section" onclick="toggleSection(this)">
                <div class="ch-section-header">
                    <div class="ch-section-icon" style="background:rgba(59,130,246,0.12);color:#60a5fa;">🏗️</div>
                    <div>
                        <div class="ch-section-title">Projects</div>
                        <div class="ch-section-desc">View and track your construction projects</div>
                    </div>
                    <span class="ch-toggle">▼</span>
                </div>
                <div class="ch-body">
                    <h4>What you can see</h4>
                    <ul>
                        <li><strong>Project overview</strong> — Name, status, code, start/end dates, and budget</li>
                        <li><strong>Progress tracking</strong> — Current project status (Planning, Active, On Hold,
                            Completed)</li>
                        <li><strong>Project manager</strong> — Who is managing your project and their contact
                            information</li>
                    </ul>

                    <h4>Project statuses</h4>
                    <ul>
                        <li>🔵 <strong>Planning</strong> — Project is being set up</li>
                        <li>🟢 <strong>Active</strong> — Work is underway</li>
                        <li>🟡 <strong>On Hold</strong> — Temporarily paused</li>
                        <li>🟣 <strong>Completed</strong> — Project finished</li>
                    </ul>

                    <p>💡 <em>Need changes to a project? Contact your project manager directly or reach out to
                            support.</em></p>
                </div>
            </div>

            {{-- Invoices --}}
            <div class="ch-section" onclick="toggleSection(this)">
                <div class="ch-section-header">
                    <div class="ch-section-icon" style="background:rgba(34,197,94,0.12);color:#4ade80;">💰</div>
                    <div>
                        <div class="ch-section-title">Invoices & Payments</div>
                        <div class="ch-section-desc">View invoices and track payment status</div>
                    </div>
                    <span class="ch-toggle">▼</span>
                </div>
                <div class="ch-body">
                    <h4>Viewing invoices</h4>
                    <ul>
                        <li>Navigate to <code>Invoices</code> from the sidebar</li>
                        <li>See all invoices issued to you with amounts, dates, and status</li>
                        <li>Click on any invoice to view the full details</li>
                    </ul>

                    <h4>Invoice statuses</h4>
                    <ul>
                        <li>📄 <strong>Draft</strong> — Invoice is being prepared</li>
                        <li>📨 <strong>Sent</strong> — Invoice has been sent to you</li>
                        <li>⏳ <strong>Overdue</strong> — Payment is past due date</li>
                        <li>✅ <strong>Paid</strong> — Payment received</li>
                        <li>🚫 <strong>Cancelled</strong> — Invoice was cancelled</li>
                    </ul>

                    <h4>Making payments</h4>
                    <p>Payment instructions are included on each invoice. If you have questions about payment methods or
                        amounts, contact the project team.</p>
                </div>
            </div>

            {{-- Documents --}}
            <div class="ch-section" onclick="toggleSection(this)">
                <div class="ch-section-header">
                    <div class="ch-section-icon" style="background:rgba(245,158,11,0.12);color:#fbbf24;">📄</div>
                    <div>
                        <div class="ch-section-title">Documents</div>
                        <div class="ch-section-desc">Access shared project documents</div>
                    </div>
                    <span class="ch-toggle">▼</span>
                </div>
                <div class="ch-body">
                    <h4>What's available</h4>
                    <ul>
                        <li><strong>Contracts & agreements</strong> — Signed project contracts</li>
                        <li><strong>Drawings & plans</strong> — Architectural and engineering drawings</li>
                        <li><strong>Reports</strong> — Progress reports, safety reports, and more</li>
                        <li><strong>Certificates</strong> — Completion certificates, payment certificates</li>
                    </ul>

                    <h4>Downloading documents</h4>
                    <p>Click on any document to view its details. Use the download button to save a copy to your device.
                        Documents are shared by your project team — you cannot upload or modify them.</p>

                    <p>💡 <em>Need a specific document? Ask your project manager to share it through the portal.</em>
                    </p>
                </div>
            </div>

            {{-- Account & Security --}}
            <div class="ch-section" onclick="toggleSection(this)">
                <div class="ch-section-header">
                    <div class="ch-section-icon" style="background:rgba(239,68,68,0.12);color:#f87171;">🔒</div>
                    <div>
                        <div class="ch-section-title">Account & Security</div>
                        <div class="ch-section-desc">Manage your password and security settings</div>
                    </div>
                    <span class="ch-toggle">▼</span>
                </div>
                <div class="ch-body">
                    <h4>Changing your password</h4>
                    <ul>
                        <li>Click <code>Change Password</code> in the sidebar</li>
                        <li>Enter your current password and your new password</li>
                        <li>Passwords must be at least 8 characters long</li>
                        <li>Passwords expire every 90 days — you'll be prompted to change</li>
                    </ul>

                    <h4>Two-Factor Authentication (2FA)</h4>
                    <ul>
                        <li>After logging in, you may be asked to verify your identity via email</li>
                        <li>A 6-digit code will be sent to your email — enter it within 10 minutes</li>
                        <li>This adds an extra layer of security to your account</li>
                    </ul>

                    <h4>Session security</h4>
                    <ul>
                        <li>Sessions expire after 30 minutes of inactivity</li>
                        <li>Always log out when using shared or public computers</li>
                        <li>If you suspect unauthorized access, change your password immediately</li>
                    </ul>
                </div>
            </div>

            {{-- Mobile Access --}}
            <div class="ch-section" onclick="toggleSection(this)">
                <div class="ch-section-header">
                    <div class="ch-section-icon" style="background:rgba(139,92,246,0.12);color:#a78bfa;">📱</div>
                    <div>
                        <div class="ch-section-title">Mobile Access</div>
                        <div class="ch-section-desc">Access InfraHub on your phone or tablet</div>
                    </div>
                    <span class="ch-toggle">▼</span>
                </div>
                <div class="ch-body">
                    <h4>Using on mobile</h4>
                    <p>InfraHub works on any device with a web browser. For the best mobile experience:</p>

                    <div class="ch-step">
                        <span class="ch-step-num">1</span>
                        <div>Open <code>{{ config('app.url') }}/client/login</code> in your phone's browser</div>
                    </div>
                    <div class="ch-step">
                        <span class="ch-step-num">2</span>
                        <div>Log in with your credentials</div>
                    </div>
                    <div class="ch-step">
                        <span class="ch-step-num">3</span>
                        <div><strong>Add to Home Screen</strong> — In Safari (iOS): tap Share → Add to Home Screen. In
                            Chrome (Android): tap ⋮ → Add to Home Screen</div>
                    </div>

                    <p>💡 <em>The portal is a Progressive Web App (PWA) — once installed, it works like a native app
                            with offline support.</em></p>
                </div>
            </div>

            {{-- FAQ --}}
            <div class="ch-section" onclick="toggleSection(this)">
                <div class="ch-section-header">
                    <div class="ch-section-icon" style="background:rgba(20,184,166,0.12);color:#2dd4bf;">❓</div>
                    <div>
                        <div class="ch-section-title">Frequently Asked Questions</div>
                        <div class="ch-section-desc">Common questions answered</div>
                    </div>
                    <span class="ch-toggle">▼</span>
                </div>
                <div class="ch-body">
                    <h4>I forgot my password</h4>
                    <p>Click <strong>"Forgot Password?"</strong> on the login page. A reset link will be sent to your
                        email. If you don't receive it within 5 minutes, check your spam folder or contact support.</p>

                    <h4>I can't see my project</h4>
                    <p>Only projects that have been assigned to your client account will appear. If a project is
                        missing, ask the project team to link it to your account.</p>

                    <h4>Can I upload documents?</h4>
                    <p>The client portal is read-only for documents. To share files with the project team, send them via
                        email or ask for upload access.</p>

                    <h4>How do I get notified about updates?</h4>
                    <p>You'll receive email notifications for important updates like new invoices, project status
                        changes, and shared documents. Check your <strong>notification bell</strong> (top right) for
                        in-app alerts.</p>

                    <h4>Can I add team members from my company?</h4>
                    <p>Contact the project team to request additional portal accounts for your colleagues.</p>
                </div>
            </div>

            {{-- Contact --}}
            <div style="margin-top:2rem;">
                <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">📞 Need Help?</h3>
                <div class="ch-contact">
                    <div class="ch-contact-card">
                        <div class="icon">📧</div>
                        <div class="label">Email Support</div>
                        <div class="value">support@infrahub.click</div>
                    </div>
                    <div class="ch-contact-card">
                        <div class="icon">🌐</div>
                        <div class="label">Portal</div>
                        <div class="value">{{ config('app.url') }}/client</div>
                    </div>
                    <div class="ch-contact-card">
                        <div class="icon">📱</div>
                        <div class="label">Mobile App</div>
                        <div class="value">{{ config('app.url') }}/mobile</div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function toggleSection(el) {
                const body = el.querySelector('.ch-body');
                const arrow = el.querySelector('.ch-toggle');
                if (body) body.classList.toggle('open');
                if (arrow) arrow.classList.toggle('open');
            }
        </script>
    </div>
</x-filament-panels::page>