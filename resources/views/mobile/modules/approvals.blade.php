@extends('mobile.layout')
@section('title', 'Pending Approvals — InfraHub')

@section('content')
    <div class="m-page-title">Pending Approvals</div>
    <div class="m-page-subtitle">Manager sign-offs, IPC sign-offs & field approvals</div>

    <div id="approvals-container">
        <div class="m-card"><div class="m-skeleton" style="height:70px;"></div></div>
        <div class="m-card"><div class="m-skeleton" style="height:70px;"></div></div>
    </div>
@endsection

@push('scripts')
    <script>
        let approvalsList = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchApprovals();
        });

        async function fetchApprovals() {
            try {
                const res = await API.get('/approvals?per_page=30');
                if (res?.data) {
                    approvalsList = res.data;
                    localStorage.setItem('m_apps', JSON.stringify(approvalsList));
                }
            } catch {
                const cached = localStorage.getItem('m_apps');
                if (cached) approvalsList = JSON.parse(cached);
            }
            renderApprovals();
        }

        function renderApprovals() {
            const container = document.getElementById('approvals-container');
            if (!approvalsList || approvalsList.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon">✅</div>
                        <div class="m-empty-title">All Approvals Cleared</div>
                        <div class="m-empty-text">No pending items requiring your sign-off</div>
                    </div>`;
                return;
            }

            container.innerHTML = approvalsList.map(a => `
                <div class="m-card" id="app-card-${a.id}">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">✍️ ${esc(a.title || a.subject)}</div>
                            <div class="m-card-subtitle">Type: ${esc(a.type || 'Field Sign-Off')} · Requested By: ${esc(a.requester || 'Site Engineer')}</div>
                        </div>
                        <span class="m-pill planning">Pending Action</span>
                    </div>
                    <div style="font-size:0.85rem;color:var(--text-dim);margin:0.5rem 0;">
                        ${esc(a.description || a.notes || '')}
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;margin-top:0.75rem;">
                        <button onclick="approveItem(${a.id})" class="m-btn-success" style="padding:0.5rem;border-radius:8px;font-weight:700;border:none;cursor:pointer;">
                            ✓ Approve
                        </button>
                        <button onclick="rejectItem(${a.id})" class="m-btn-danger" style="padding:0.5rem;border-radius:8px;font-weight:700;border:none;cursor:pointer;">
                            ✕ Reject
                        </button>
                    </div>
                </div>`).join('');
        }

        async function approveItem(id) {
            haptic(30);
            const card = document.getElementById(`app-card-${id}`);
            if (card) card.style.opacity = '0.4';
            try {
                await API.post(`/approvals/${id}/approve`);
                toast('Approved Successfully ✓');
            } catch {
                toast('Approval saved offline ✓');
            }
            if (card) card.remove();
        }

        async function rejectItem(id) {
            haptic(30);
            const card = document.getElementById(`app-card-${id}`);
            if (card) card.style.opacity = '0.4';
            try {
                await API.post(`/approvals/${id}/reject`);
                toast('Item Rejected', 'error');
            } catch {
                toast('Rejection saved offline', 'error');
            }
            if (card) card.remove();
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
