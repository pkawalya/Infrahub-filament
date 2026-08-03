@extends('mobile.layout')
@section('title', 'Suggestion Box — InfraHub')

@section('content')
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.25rem;">
        <div class="m-page-title" style="margin:0;">Suggestion Box</div>
        <button class="m-btn m-btn-primary" onclick="openSheet('sheet-suggestion')" style="width:auto;padding:0.4rem 0.85rem;font-size:0.78rem;">+ Submit Idea</button>
    </div>
    <div class="m-page-subtitle">Anonymous feedback, site improvements & innovation ideas</div>

    <div id="suggestions-container">
        <div class="m-card"><div class="m-skeleton" style="height:70px;"></div></div>
        <div class="m-card"><div class="m-skeleton" style="height:70px;"></div></div>
    </div>

    {{-- Submit Suggestion Action Sheet --}}
    <div class="m-sheet" id="sheet-suggestion">
        <div class="m-sheet-handle"></div>
        <div class="m-sheet-header">
            <div class="m-sheet-title">💡 Submit Site Suggestion</div>
            <button type="button" class="m-sheet-close" onclick="closeSheet('sheet-suggestion')">✕</button>
        </div>
        <form onsubmit="event.preventDefault(); submitSuggestion(this);">
            <div class="m-form-group">
                <label class="m-label">Title / Topic</label>
                <input type="text" name="title" class="m-input" placeholder="e.g. Improved site safety lighting at Gate 2" required>
            </div>
            <div class="m-form-group">
                <label class="m-label">Category</label>
                <select name="category" class="m-select" required>
                    <option value="Safety">Safety & Welfare</option>
                    <option value="Efficiency">Site Operations & Efficiency</option>
                    <option value="Equipment">Equipment & Tools</option>
                    <option value="General">General Innovation</option>
                </select>
            </div>
            <div class="m-form-group">
                <label class="m-label">Suggestion Details</label>
                <textarea name="description" class="m-textarea" placeholder="Detail your idea or feedback..." required></textarea>
            </div>
            <button type="submit" class="m-btn m-btn-primary">Submit Suggestion</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let suggestionsList = [];

        document.addEventListener('DOMContentLoaded', async () => {
            if (!API.isLoggedIn()) { window.location.href = '/mobile/login'; return; }
            fetchSuggestions();
        });

        async function fetchSuggestions() {
            try {
                const res = await API.get('/suggestions?per_page=30');
                if (res?.data) suggestionsList = res.data;
                localStorage.setItem('m_suggestions', JSON.stringify(suggestionsList));
            } catch {
                const cached = localStorage.getItem('m_suggestions');
                if (cached) suggestionsList = JSON.parse(cached);
            }
            renderSuggestions();
        }

        function renderSuggestions() {
            const container = document.getElementById('suggestions-container');

            if (!suggestionsList || suggestionsList.length === 0) {
                container.innerHTML = `
                    <div class="m-empty">
                        <div class="m-empty-icon">💡</div>
                        <div class="m-empty-title">No Suggestions Yet</div>
                        <div class="m-empty-text">Be the first to submit a site improvement or safety suggestion!</div>
                    </div>`;
                return;
            }

            container.innerHTML = suggestionsList.map(item => `
                <div class="m-card">
                    <div class="m-card-header">
                        <div>
                            <div class="m-card-title">💡 ${esc(item.title || 'Site Improvement Suggestion')}</div>
                            <div class="m-card-subtitle">Category: ${esc(item.category || 'General')}</div>
                        </div>
                        <span class="m-pill ${item.status === 'approved' ? 'done' : 'pending'}">${esc(item.status || 'Under Review')}</span>
                    </div>
                    <div class="m-card-body" style="margin:0.5rem 0;">${esc(item.description || item.body || '')}</div>
                    <div class="m-card-footer">
                        📅 Submitted: ${esc(item.created_at ? item.created_at.slice(0,10) : 'Recent')}
                        ${item.is_anonymous ? ' · 🕵️ Anonymous' : ''}
                    </div>
                </div>`).join('');
        }

        async function submitSuggestion(form) {
            const data = {
                title: form.title.value,
                category: form.category.value,
                description: form.description.value,
                is_anonymous: true
            };
            try {
                await API.post('/suggestions', data);
                toast('Suggestion submitted successfully! Thank you. ✓', 'success');
            } catch {
                // Queue offline form
                if (window.InfraDB) {
                    window.InfraDB.queueForm('suggestions', 'create', data);
                    toast('Saved offline — will sync when reconnected ✓', 'info');
                }
            }
            closeSheet('sheet-suggestion');
            form.reset();
            fetchSuggestions();
        }

        function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
    </script>
@endpush
