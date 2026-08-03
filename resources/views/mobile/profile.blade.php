@extends('mobile.layout', ['active' => 'profile'])
@section('title', 'Profile & Settings — InfraHub Mobile')

@section('content')
    <div style="text-align:center;padding:1rem 0 1.5rem;">
        <div id="avatar"
            style="width:68px;height:68px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#8b5cf6);display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;font-size:1.5rem;font-weight:800;color:white;box-shadow:0 6px 20px rgba(99,102,241,0.4);border:2px solid rgba(255,255,255,0.2);">
        </div>
        <div class="m-page-title" id="p-name" style="margin-bottom:0.1rem;">Loading...</div>
        <div class="m-page-subtitle" id="p-email" style="margin-bottom:0;"></div>
        <div style="margin-top:0.5rem;">
            <x-mobile.pill status="active" label="Senior Engineer" id="p-role" />
        </div>
    </div>

    {{-- Company Info --}}
    <div class="m-card" id="company-card">
        <div class="m-card-header" style="margin-bottom:0.2rem;">
            <div class="m-card-title" style="font-size:0.84rem;display:flex;align-items:center;gap:0.4rem;">
                <x-mobile.icon name="company" size="18" class="text-indigo-400" /> Company Workspace
            </div>
        </div>
        <div class="m-card-body" id="p-company" style="font-weight:700;color:var(--text);">—</div>
    </div>

    {{-- Settings Links --}}
    <div class="m-section" style="margin-top:1.25rem;"><span class="m-section-title">Account & System Settings</span></div>

    <a href="/app" class="m-card" style="display:flex;align-items:center;gap:0.85rem;">
        <div class="m-icon-badge" style="background:rgba(59,130,246,0.15);color:#60a5fa;margin-bottom:0;">
            <x-mobile.icon name="tenders" size="20" />
        </div>
        <div>
            <div class="m-card-title" style="font-size:0.88rem;">Desktop Admin Dashboard</div>
            <div class="m-card-subtitle">Access full web enterprise console</div>
        </div>
    </a>

    <a href="/mobile/forms" class="m-card" style="display:flex;align-items:center;gap:0.85rem;">
        <div class="m-icon-badge" style="background:rgba(34,197,94,0.15);color:#4ade80;margin-bottom:0;">
            <x-mobile.icon name="forms" size="20" />
        </div>
        <div>
            <div class="m-card-title" style="font-size:0.88rem;">Offline Field Forms</div>
            <div class="m-card-subtitle">Site diary, crew attendance, safety</div>
        </div>
    </a>

    <div class="m-card" style="display:flex;align-items:center;gap:0.85rem;cursor:pointer;" onclick="clearCache()">
        <div class="m-icon-badge" style="background:rgba(245,158,11,0.15);color:#fbbf24;margin-bottom:0;">
            <x-mobile.icon name="change-orders" size="20" />
        </div>
        <div>
            <div class="m-card-title" style="font-size:0.88rem;">Purge Local Cache</div>
            <div class="m-card-subtitle">Refresh offline storage & data index</div>
        </div>
    </div>

    <button class="m-btn m-btn-outline" style="margin-top:1.5rem;color:var(--danger);border-color:rgba(239,68,68,0.3);"
        onclick="doLogout()">
        Sign Out of InfraHub
    </button>

    <div style="text-align:center;margin-top:2rem;font-size:0.72rem;color:var(--text-dim);font-weight:600;">
        InfraHub Mobile PWA · Enterprise Field Ops v3.0
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!MobileAPI.isLoggedIn()) { window.location.href = '/mobile/login'; return; }

            const user = MobileAPI.getUser();
            if (user) {
                document.getElementById('p-name').textContent = user.name || 'User';
                document.getElementById('p-email').textContent = user.email || '';
                const roleEl = document.getElementById('p-role');
                if (roleEl) {
                    const textEl = roleEl.querySelector('.m-pill-text');
                    if (textEl) textEl.textContent = (user.user_type || 'user').replace('_', ' ').toUpperCase();
                }
                document.getElementById('avatar').textContent = (user.name || 'U').substring(0, 2).toUpperCase();
                document.getElementById('p-company').textContent = user.company?.name || user.company_id || 'InfraHub Enterprise';
            }
        });

        function doLogout() {
            if (!confirm('Sign out of InfraHub Mobile?')) return;
            MobileAPI.logout();
        }

        function clearCache() {
            if (!confirm('Clear local cache and refresh data?')) return;
            localStorage.removeItem('m_projects');
            localStorage.removeItem('m_tasks');
            MobileUI.toast('Cache cleared ✓');
        }
    </script>
@endpush