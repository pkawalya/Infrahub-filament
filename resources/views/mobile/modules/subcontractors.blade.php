@extends('mobile.layout')
@section('title', 'Subcontractors — InfraHub')

@section('content')
    <div class="m-page-title">Subcontractor Directory</div>
    <div class="m-page-subtitle">Trade vendors, specialist contractors & status</div>

    <div id="subcontractors-container">
        <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
        <div class="m-card"><div class="m-skeleton" style="height:60px;"></div></div>
    </div>
@endsection

@push('scripts')
    <script>
        let subsList = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchSubcontractors();
        });

        async function fetchSubcontractors() {
            try {
                const res = await API.get('/subcontractors?per_page=50');
                if (res?.data) {
                    subsList = res.data;
                    localStorage.setItem('m_subs', JSON.stringify(subsList));
                }
            } catch {
                const cached = localStorage.getItem('m_subs');
                if (cached) subsList = JSON.parse(cached);
            }
            renderSubcontractors();
        }

        function renderSubcontractors() {
            const container = document.getElementById('subcontractors-container');
            if (!subsList || subsList.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon">🤝</div>
                        <div class="m-empty-title">No Subcontractors Registered</div>
                    </div>`;
                return;
            }

            container.innerHTML = subsList.map(s => `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">🏢 ${esc(s.company_name || s.name)}</div>
                            <div class="m-card-subtitle">${esc(s.trade || s.specialty || 'General Trade Contractor')}</div>
                        </div>
                        <span class="m-pill active">${esc(s.status || 'Active')}</span>
                    </div>
                    <div class="m-card-footer" style="margin-top:0.5rem;">
                        👤 Contact: ${esc(s.contact_person || s.email || 'N/A')}
                        ${s.phone ? ' · 📞 ' + esc(s.phone) : ''}
                    </div>
                </div>`).join('');
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
