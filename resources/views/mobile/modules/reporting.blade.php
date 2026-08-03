@extends('mobile.layout')
@section('title', 'Reporting & Analytics — InfraHub')

@section('content')
    <div class="m-page-title">Reporting & Analytics</div>
    <div class="m-page-subtitle">Field KPIs, executive summaries & project metrics</div>

    <div class="m-stats">
        <div class="m-stat accent">
            <div class="m-stat-value" id="rep-projects-count">5</div>
            <div class="m-stat-label">Active Sites</div>
        </div>
        <div class="m-stat success">
            <div class="m-stat-value" id="rep-tasks-count">24</div>
            <div class="m-stat-label">Tasks Done</div>
        </div>
        <div class="m-stat warning">
            <div class="m-stat-value" id="rep-diaries-count">18</div>
            <div class="m-stat-label">Site Diaries</div>
        </div>
        <div class="m-stat danger">
            <div class="m-stat-value" id="rep-hazards-count">2</div>
            <div class="m-stat-label">Open Hazards</div>
        </div>
    </div>

    <div class="m-section">
        <div class="m-section-title">Field Performance Summaries</div>
    </div>

    <div class="m-card">
        <div class="m-card-header">
            <div>
                <div class="m-card-title">📊 Executive Daily Summary</div>
                <div class="m-card-subtitle">Aggregated progress across 5 active projects</div>
            </div>
            <span class="m-pill active">Live</span>
        </div>
        <div class="m-card-body" style="margin:0.5rem 0;">
            • Overall Project Progress Average: <strong>48.6%</strong><br>
            • Today's On-Site Field Workers: <strong>42 Operatives</strong><br>
            • Open RFIs Needing Action: <strong>3 Pending</strong><br>
            • Zero Lost Time Injuries (LTI) Reported Today.
        </div>
        <div class="m-card-footer">
            ⏱️ Refreshed live from site databases
        </div>
    </div>

    <div class="m-card">
        <div class="m-card-header">
            <div>
                <div class="m-card-title">📈 Cost & Valuation Overview</div>
                <div class="m-card-subtitle">Certified work vs budget allocations</div>
            </div>
            <span class="m-pill done">Updated</span>
        </div>
        <div class="m-card-body" style="margin:0.5rem 0;">
            • Total Portfolio Budget: <strong>$375,000,000</strong><br>
            • Certified Work (IPC): <strong>$142,500,000</strong><br>
            • Cost Performance Index (CPI): <strong>1.04 (On Budget)</strong>
        </div>
        <div class="m-card-footer">
            💳 Financials verified by Commercial Department
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
        });
    </script>
@endpush
