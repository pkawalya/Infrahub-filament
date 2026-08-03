@extends('mobile.layout')
@section('title', 'Financials & Invoices — InfraHub')

@section('content')
    <div class="m-page-title">Financial Certificates</div>
    <div class="m-page-subtitle">Valuations, IPC certificates & invoice progress</div>

    <div style="display:flex;gap:0.4rem;margin-bottom:1rem;">
        <button class="m-pill active" onclick="switchFinTab('ipc')" id="tab-ipc" style="cursor:pointer;border:none;">Payment Certificates</button>
        <button class="m-pill" onclick="switchFinTab('invoices')" id="tab-invoices" style="cursor:pointer;border:none;">Invoices</button>
    </div>

    <div id="financials-container">
        <div class="m-card"><div class="m-skeleton" style="height:70px;"></div></div>
        <div class="m-card"><div class="m-skeleton" style="height:70px;"></div></div>
    </div>
@endsection

@push('scripts')
    <script>
        let ipcsList = [];
        let invoicesList = [];
        let activeTab = 'ipc';

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchFinancials();
        });

        async function fetchFinancials() {
            try {
                const [ipcRes, invRes] = await Promise.all([
                    API.get('/payment-certificates?per_page=30'),
                    API.get('/invoices?per_page=30')
                ]);
                if (ipcRes?.data) ipcsList = ipcRes.data;
                if (invRes?.data) invoicesList = invRes.data;
                localStorage.setItem('m_ipcs', JSON.stringify(ipcsList));
                localStorage.setItem('m_invoices', JSON.stringify(invoicesList));
            } catch {
                const c1 = localStorage.getItem('m_ipcs');
                const c2 = localStorage.getItem('m_invoices');
                if (c1) ipcsList = JSON.parse(c1);
                if (c2) invoicesList = JSON.parse(c2);
            }
            renderFinancials();
        }

        function switchFinTab(tab) {
            activeTab = tab;
            document.getElementById('tab-ipc').className = `m-pill ${tab === 'ipc' ? 'active' : ''}`;
            document.getElementById('tab-invoices').className = `m-pill ${tab === 'invoices' ? 'active' : ''}`;
            renderFinancials();
        }

        function renderFinancials() {
            const container = document.getElementById('financials-container');
            const data = activeTab === 'ipc' ? ipcsList : invoicesList;

            if (!data || data.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon">💳</div>
                        <div class="m-empty-title">No ${activeTab === 'ipc' ? 'Payment Certificates' : 'Invoices'} Records</div>
                    </div>`;
                return;
            }

            container.innerHTML = data.map(item => `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">💵 ${esc(item.certificate_number || item.invoice_number || 'Record')}</div>
                            <div class="m-card-subtitle">${esc(item.cde_project?.name || item.project_name || 'Site Valuation')}</div>
                        </div>
                        <span class="m-pill active">${esc(item.status || 'Certified')}</span>
                    </div>
                    <div style="font-size:1.1rem;font-weight:700;color:#10b981;margin:0.5rem 0;">
                        $${(item.net_amount || item.total_amount || item.amount || 0).toLocaleString()}
                    </div>
                    <div class="m-card-footer">
                        📅 Date: ${esc(item.issue_date || item.created_at ? (item.issue_date || item.created_at).slice(0,10) : 'Current')}
                        ${item.period ? ' · ⏱️ Period: ' + esc(item.period) : ''}
                    </div>
                </div>`).join('');
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
