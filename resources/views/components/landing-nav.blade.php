@props(['activePage' => 'home'])

<!-- Global Landing Header Navigation -->
<header class="landing-header">
    <div class="nav-container">
        <nav class="landing-nav">
            <a href="/" class="brand-logo">
                <img id="header-brand-logo" src="{{ asset('logo/infrahub-logo-new.png') }}?v=logo4" alt="InfraHub" style="height: 44px; object-fit: contain;">
            </a>

            <div class="nav-center">
                <a href="/" class="nav-item {{ $activePage === 'home' ? 'active' : '' }}">Home</a>
                
                <!-- Products Dropdown -->
                <div class="nav-dropdown-wrapper">
                    <a href="/products" class="nav-item {{ $activePage === 'products' ? 'active' : '' }}">
                        Products <span class="caret">▾</span>
                    </a>
                    <div class="nav-dropdown-menu mega-menu">
                        <div class="dropdown-header">Explore InfraHub Modules</div>
                        <div class="dropdown-grid">
                            <a href="/products#operations" class="dropdown-item">
                                <span class="item-icon">📅</span>
                                <div class="item-text">
                                    <strong>Operations & Scheduling</strong>
                                    <small>Gantt charts, WBS hierarchy & MS Project XML import</small>
                                </div>
                            </a>
                            <a href="/products#site-resources" class="dropdown-item">
                                <span class="item-icon">📦</span>
                                <div class="item-text">
                                    <strong>Site & Resources</strong>
                                    <small>Inventory, SHEQ safety, equipment & daily site logs</small>
                                </div>
                            </a>
                            <a href="/products#commercial-cost" class="dropdown-item">
                                <span class="item-icon">📊</span>
                                <div class="item-text">
                                    <strong>Commercial & Cost</strong>
                                    <small>BOQ paste import, contracts, variations & tenders</small>
                                </div>
                            </a>
                            <a href="/products#collaboration" class="dropdown-item">
                                <span class="item-icon">📁</span>
                                <div class="item-text">
                                    <strong>Collaboration & CDE</strong>
                                    <small>ISO 19650 document gateway, RFIs & AI project pulse</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Solutions Dropdown -->
                <div class="nav-dropdown-wrapper">
                    <a href="/solutions" class="nav-item {{ $activePage === 'solutions' ? 'active' : '' }}">
                        Solutions <span class="caret">▾</span>
                    </a>
                    <div class="nav-dropdown-menu">
                        <a href="/solutions#contractors" class="dropdown-item">
                            <span class="item-icon">🏗️</span>
                            <div class="item-text">
                                <strong>For Main Contractors</strong>
                                <small>Full lifecycle project management</small>
                            </div>
                        </a>
                        <a href="/solutions#subcontractors" class="dropdown-item">
                            <span class="item-icon">🤝</span>
                            <div class="item-text">
                                <strong>For Subcontractors</strong>
                                <small>Work orders & material requisitions</small>
                            </div>
                        </a>
                        <a href="/solutions#field-safety" class="dropdown-item">
                            <span class="item-icon">👷</span>
                            <div class="item-text">
                                <strong>For Field & Safety Officers</strong>
                                <small>Daily site logs & AI safety analysis</small>
                            </div>
                        </a>
                        <a href="/solutions#cost-managers" class="dropdown-item">
                            <span class="item-icon">💳</span>
                            <div class="item-text">
                                <strong>For Quantity Surveyors</strong>
                                <small>BOQ variations & payment certificates</small>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Resources Dropdown -->
                <div class="nav-dropdown-wrapper">
                    <a href="/academy" class="nav-item {{ in_array($activePage, ['academy', 'docs']) ? 'active' : '' }}">
                        Resources <span class="caret">▾</span>
                    </a>
                    <div class="nav-dropdown-menu">
                        <a href="/docs" class="dropdown-item">
                            <span class="item-icon">📖</span>
                            <div class="item-text">
                                <strong>User Manual & Docs</strong>
                                <small>Complete system workflows & specs</small>
                            </div>
                        </a>
                        <a href="/academy" class="dropdown-item">
                            <span class="item-icon">🎓</span>
                            <div class="item-text">
                                <strong>InfraHub Academy</strong>
                                <small>Tutorials & ISO 19650 guides</small>
                            </div>
                        </a>
                        <a href="/get-started" class="dropdown-item">
                            <span class="item-icon">🚀</span>
                            <div class="item-text">
                                <strong>Company Onboarding</strong>
                                <small>Setup your organization & teams</small>
                            </div>
                        </a>
                        <a href="/schedule-call" class="dropdown-item">
                            <span class="item-icon">📞</span>
                            <div class="item-text">
                                <strong>Book a Demo Call</strong>
                                <small>Personalized walk-through</small>
                            </div>
                        </a>
                    </div>
                </div>

                <a href="/academy" class="nav-item {{ $activePage === 'academy' ? 'active' : '' }}">Academy</a>
                <a href="/pricing" class="nav-item {{ $activePage === 'pricing' ? 'active' : '' }}">Pricing</a>
                <a href="/about" class="nav-item {{ $activePage === 'about' ? 'active' : '' }}">About Us</a>
            </div>

            <div class="nav-right">
                <div class="lang-select" title="Select Language">
                    🌐 <span>EN</span>
                </div>
                @auth
                    <a href="{{ url('/app') }}" class="btn-login">Dashboard</a>
                @else
                    <a href="{{ url('/app/login') }}" class="btn-login">Login</a>
                @endauth
                <a href="/schedule-call" class="btn-demo">
                    Book a Demo <span>→</span>
                </a>
                <button class="theme-toggle-btn" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                    <span id="theme-icon">🌙</span>
                </button>
            </div>
        </nav>
    </div>
</header>
