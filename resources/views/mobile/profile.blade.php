@extends('mobile.layout')
@section('title', 'Profile — InfraHub')

@section('content')
    <div style="text-align:center;padding:1rem 0 1.5rem;">
        <div id="avatar"
            style="width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#8b5cf6);display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;font-size:1.5rem;font-weight:800;color:white;">
        </div>
        <div class="m-page-title" id="p-name" style="margin-bottom:0.1rem;">Loading...</div>
        <div class="m-page-subtitle" id="p-email" style="margin-bottom:0;"></div>
        <div style="margin-top:0.5rem;">
            <span class="m-pill active" id="p-role"></span>
        </div>
    </div>

    {{-- Company Info --}}
    <div class="m-card" id="company-card">
        <div class="m-card-title" style="font-size:0.82rem;">🏢 Company</div>
        <div class="m-card-body" id="p-company">—</div>
    </div>

    {{-- Quick Links --}}
    <div class="m-section" style="margin-top:1rem;"><span class="m-section-title">Settings</span></div>

    <a href="/app" class="m-card" style="display:flex;align-items:center;gap:0.75rem;">
        <span style="font-size:1.2rem;">🖥️</span>
        <div>
            <div class="m-card-title" style="font-size:0.88rem;">Desktop Dashboard</div>
            <div class="m-card-subtitle">Full admin panel</div>
        </div>
    </a>

    <a href="/mobile/forms" class="m-card" style="display:flex;align-items:center;gap:0.75rem;">
        <span style="font-size:1.2rem;">📝</span>
        <div>
            <div class="m-card-title" style="font-size:0.88rem;">Offline Forms</div>
            <div class="m-card-subtitle">Site diary, attendance, safety</div>
        </div>
    </a>

    <div class="m-card" style="display:flex;align-items:center;gap:0.75rem;cursor:pointer;" onclick="clearCache()">
        <span style="font-size:1.2rem;">🗑️</span>
        <div>
            <div class="m-card-title" style="font-size:0.88rem;">Clear Cache</div>
            <div class="m-card-subtitle">Reset offline data</div>
        </div>
    </div>

    <button class="m-btn m-btn-outline" style="margin-top:1.5rem;color:var(--danger);border-color:rgba(239,68,68,0.3);"
        onclick="doLogout()">
        Sign Out
    </button>

    <div style="text-align:center;margin-top:2rem;font-size:0.68rem;color:var(--text-dim);">
        InfraHub Mobile v1.0 · Built with ❤️
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }

            const user = API.getUser();
            if (user) {
                document.getElementById('p-name').textContent = user.name || 'User';
                document.getElementById('p-email').textContent = user.email || '';
                document.getElementById('p-role').textContent = (user.user_type || 'user').replace('_', ' ');
                document.getElementById('avatar').textContent = (user.name || 'U').substring(0, 2).toUpperCase();
                document.getElementById('p-company').textContent = user.company?.name || user.company_id || '—';
            }
        });

        function doLogout() {
            if (!confirm('Sign out of InfraHub?')) return;
            API.logout();
        }

        function clearCache() {
            if (!confirm('Clear all cached data?')) return;
            localStorage.removeItem('m_projects');
            localStorage.removeItem('m_tasks');
            toast('Cache cleared ✓');
        }
    </script>
@endpush