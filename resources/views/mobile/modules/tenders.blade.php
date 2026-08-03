@extends('mobile.layout')
@section('title', 'Tenders & Bids — InfraHub')

@section('content')
    <div class="m-page-title">Tenders & Bids</div>
    <div class="m-page-subtitle">Procurement pipeline & submission tracking</div>

    <div id="tenders-container">
        <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
        <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
    </div>
@endsection

@push('scripts')
    <script>
        let tendersList = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchTenders();
        });

        async function fetchTenders() {
            try {
                const res = await API.get('/tenders?per_page=50');
                if (res?.data) {
                    tendersList = res.data;
                    localStorage.setItem('m_tenders', JSON.stringify(tendersList));
                }
            } catch {
                const cached = localStorage.getItem('m_tenders');
                if (cached) tendersList = JSON.parse(cached);
            }
            renderTenders();
        }

        function renderTenders() {
            const container = document.getElementById('tenders-container');
            if (!tendersList || tendersList.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon">🏛️</div>
                        <div class="m-empty-title">No Active Tenders</div>
                    </div>`;
                return;
            }

            container.innerHTML = tendersList.map(t => `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">🏛️ ${esc(t.title || t.reference_number)}</div>
                            <div class="m-card-subtitle">${esc(t.client_name || t.client?.name || 'Public Procurement')}</div>
                        </div>
                        <span class="m-pill active">${esc(t.status || 'Active')}</span>
                    </div>
                    <div style="font-size:1rem;font-weight:700;color:var(--accent);margin:0.4rem 0;">
                        ${t.estimated_value ? '$' + parseFloat(t.estimated_value).toLocaleString() : 'Bid Stage'}
                    </div>
                    <div class="m-card-footer">
                        ⏳ Submission: ${esc(t.submission_deadline || t.due_date || 'Upcoming')}
                    </div>
                </div>`).join('');
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
