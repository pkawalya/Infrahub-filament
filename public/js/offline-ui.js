/**
 * InfraHub Offline UI v2 — Universal Offline Support
 *
 * Handles:
 * 1. Network status banner (online/offline)
 * 2. Sync queue badge (pending count)
 * 3. Livewire request interceptor (captures form data when offline)
 * 4. Auto-sync engine (replays queued data when back online)
 * 5. Generic form submission capture for ALL Filament resources
 */
(function () {
    'use strict';

    // ── State ──────────────────────────────────────────────────
    let isOnline = navigator.onLine;
    let syncInProgress = false;

    // ── Resource slug mapping from URL patterns ────────────────
    // Maps Filament URL patterns to resource types
    const RESOURCE_MAP = {
        'tasks': { model: 'Task', label: 'Task' },
        'work-orders': { model: 'WorkOrder', label: 'Work Order' },
        'daily-site-diaries': { model: 'DailySiteDiary', label: 'Site Diary' },
        'crew-attendances': { model: 'CrewAttendance', label: 'Attendance' },
        'safety-incidents': { model: 'SafetyIncident', label: 'Safety Incident' },
        'invoices': { model: 'Invoice', label: 'Invoice' },
        'assets': { model: 'Asset', label: 'Asset' },
        'clients': { model: 'Client', label: 'Client' },
        'subcontractors': { model: 'Subcontractor', label: 'Subcontractor' },
        'tenders': { model: 'Tender', label: 'Tender' },
        'drawings': { model: 'Drawing', label: 'Drawing' },
        'payment-certificates': { model: 'PaymentCertificate', label: 'Payment Certificate' },
        'cde-projects': { model: 'CdeProject', label: 'Project' },
        'change-orders': { model: 'ChangeOrder', label: 'Change Order' },
        'company-users': { model: 'CompanyUser', label: 'User' },
    };

    // ── 1. Network Status Banner ──────────────────────────────
    function createBanner() {
        if (document.getElementById('infrahub-offline-banner')) return;

        const banner = document.createElement('div');
        banner.id = 'infrahub-offline-banner';
        banner.innerHTML = `
            <div id="offline-bar" style="display:none">
                <div class="offline-bar-inner offline-bar-offline">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636a9 9 0 010 12.728M5.636 18.364a9 9 0 010-12.728"/>
                        <line x1="4" y1="4" x2="20" y2="20" stroke="currentColor" stroke-width="2.5"/>
                    </svg>
                    <span class="offline-bar-text">You are offline</span>
                    <span class="offline-bar-sub">You can still view cached pages and save forms — data syncs when you reconnect</span>
                </div>
            </div>
            <div id="online-bar" style="display:none">
                <div class="offline-bar-inner offline-bar-online">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="offline-bar-text">Back online</span>
                    <span class="offline-bar-sub" id="sync-status-text">Syncing pending data...</span>
                </div>
            </div>
        `;
        document.body.prepend(banner);
    }

    // ── 2. Sync Queue Badge ───────────────────────────────────
    function createQueueBadge() {
        if (document.getElementById('infrahub-sync-badge')) return;

        const badge = document.createElement('div');
        badge.id = 'infrahub-sync-badge';
        badge.style.display = 'none';
        badge.title = 'Click to view offline queue';
        badge.innerHTML = `
            <div class="sync-badge-inner" onclick="window.location.href='/app/offline-forms'">
                <svg class="sync-icon" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"/>
                </svg>
                <span class="sync-badge-count" id="sync-count">0</span>
                <span class="sync-badge-label">pending</span>
            </div>
        `;
        document.body.appendChild(badge);
    }

    async function updateQueueBadge() {
        if (typeof InfraDB === 'undefined') return;
        try {
            const total = await InfraDB.totalPending();
            const badge = document.getElementById('infrahub-sync-badge');
            const countEl = document.getElementById('sync-count');
            if (badge && countEl) {
                countEl.textContent = total;
                badge.style.display = total > 0 ? 'block' : 'none';
                const icon = badge.querySelector('.sync-icon');
                if (icon) icon.classList.toggle('spinning', syncInProgress);
            }
        } catch (e) {
            console.warn('Failed to update sync badge:', e);
        }
    }

    // ── 3. Network Event Handlers ─────────────────────────────
    function showOfflineBanner() {
        const offBar = document.getElementById('offline-bar');
        const onBar = document.getElementById('online-bar');
        if (offBar) offBar.style.display = 'block';
        if (onBar) onBar.style.display = 'none';
    }

    function showOnlineBanner() {
        const offBar = document.getElementById('offline-bar');
        const onBar = document.getElementById('online-bar');
        if (offBar) offBar.style.display = 'none';
        if (onBar) {
            onBar.style.display = 'block';
            setTimeout(() => { if (onBar) onBar.style.display = 'none'; }, 6000);
        }
    }

    function handleOffline() {
        isOnline = false;
        showOfflineBanner();
        updateQueueBadge();
    }

    function handleOnline() {
        isOnline = true;
        showOnlineBanner();
        triggerSync();
    }

    // ── 4. Generic Sync Engine ────────────────────────────────
    async function triggerSync() {
        if (syncInProgress || typeof InfraDB === 'undefined') return;
        syncInProgress = true;
        updateQueueBadge();

        const statusText = document.getElementById('sync-status-text');
        let totalSynced = 0;
        let totalFailed = 0;

        // ─ Sync generic form queue ─────────────────────────
        try {
            const records = await InfraDB.getAll('form-queue');
            if (records.length > 0) {
                if (statusText) statusText.textContent = `Syncing ${records.length} form submission${records.length > 1 ? 's' : ''}...`;

                for (const record of records) {
                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                        const resp = await fetch('/api/v1/offline-sync/generic', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken || '',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({
                                resource: record._resource,
                                action: record._action,
                                record_id: record._recordId,
                                data: record.data,
                            }),
                        });

                        if (resp.ok) {
                            await InfraDB.remove('form-queue', record._offlineId);
                            totalSynced++;
                        } else {
                            const err = await resp.json().catch(() => ({}));
                            console.warn(`Sync failed for ${record._resource}:`, resp.status, err);
                            totalFailed++;
                        }
                    } catch (fetchErr) {
                        console.warn(`Sync error:`, fetchErr);
                        totalFailed++;
                    }
                }
            }
        } catch (e) {
            console.error('Form queue sync failed:', e);
        }

        // ─ Sync legacy dedicated stores ────────────────────
        const legacyEndpoints = {
            'site-diaries': '/api/v1/offline-sync/site-diaries',
            'attendance': '/api/v1/offline-sync/attendance',
            'safety-incidents': '/api/v1/offline-sync/safety-incidents',
        };

        for (const [storeName, endpoint] of Object.entries(legacyEndpoints)) {
            try {
                const records = await InfraDB.getAll(storeName);
                for (const record of records) {
                    try {
                        const payload = { ...record };
                        delete payload._offlineId;
                        delete payload._createdAt;
                        delete payload._synced;

                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                        const resp = await fetch(endpoint, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken || '',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify(payload),
                        });

                        if (resp.ok) {
                            await InfraDB.remove(storeName, record._offlineId);
                            totalSynced++;
                        } else {
                            totalFailed++;
                        }
                    } catch { totalFailed++; }
                }
            } catch (e) {
                console.error(`Legacy sync failed for ${storeName}:`, e);
            }
        }

        syncInProgress = false;
        updateQueueBadge();

        if (statusText) {
            if (totalFailed > 0) {
                statusText.textContent = `${totalSynced} synced, ${totalFailed} failed — will retry`;
            } else if (totalSynced > 0) {
                statusText.textContent = `${totalSynced} record${totalSynced > 1 ? 's' : ''} synced successfully ✓`;
            } else {
                statusText.textContent = 'All caught up ✓';
            }
        }
    }

    // ── 5. Livewire/Filament Request Interceptor ──────────────
    // Intercepts Livewire XHR requests when offline to prevent errors
    // and capture form data for offline queuing.
    function installLivewireInterceptor() {
        // Patch fetch to intercept Livewire calls
        const originalFetch = window.fetch;
        window.fetch = async function (...args) {
            const [input, init] = args;
            const url = typeof input === 'string' ? input : input?.url || '';

            // Only intercept Livewire update calls when offline
            if (!navigator.onLine && url.includes('/livewire/update')) {
                console.log('[Offline] Livewire request intercepted');

                // Try to extract form data from the current page
                const formData = extractFilamentFormData();
                const resourceInfo = detectCurrentResource();

                if (formData && resourceInfo && Object.keys(formData).length > 0) {
                    // Queue the form data
                    try {
                        await InfraDB.queueForm(
                            resourceInfo.slug,
                            resourceInfo.action,
                            formData,
                            resourceInfo.recordId,
                            `${resourceInfo.action === 'create' ? 'New' : 'Update'} ${resourceInfo.label}`
                        );
                        updateQueueBadge();
                        showToast(
                            'Saved offline ☁️',
                            `${resourceInfo.label} saved locally. It will sync when you're back online.`,
                            'info'
                        );
                    } catch (e) {
                        console.error('Failed to queue form data:', e);
                    }
                } else {
                    showToast(
                        'You are offline',
                        'This action requires an internet connection. Data you\'ve entered is safe in the form.',
                        'info'
                    );
                }

                // Return a fake "success" response so Livewire doesn't show an error modal
                return new Response(JSON.stringify({ effects: { html: null } }), {
                    status: 200,
                    headers: { 'Content-Type': 'application/json' },
                });
            }

            // Online or non-Livewire request: proceed normally
            return originalFetch.apply(this, args);
        };
    }

    // ── 6. Form Data Extraction from Filament DOM ─────────────
    function extractFilamentFormData() {
        const data = {};
        const form = document.querySelector('form[wire\\:submit]') ||
            document.querySelector('.fi-fo-component-ctn')?.closest('form') ||
            document.querySelector('[x-data] form');

        if (!form) return data;

        // Extract all input values
        form.querySelectorAll('input, select, textarea').forEach(el => {
            const name = el.name || el.getAttribute('wire:model') || el.getAttribute('wire:model.live') || '';
            if (!name || name.startsWith('_') || name === 'components') return;

            // Clean up Livewire wire:model paths like "data.title"
            const cleanName = name.replace(/^data\./, '').replace(/^mountedTableAction(Data|Arguments)\./, '');
            if (!cleanName || cleanName.includes('snapshot') || cleanName.includes('fingerprint')) return;

            if (el.type === 'checkbox') {
                data[cleanName] = el.checked ? 1 : 0;
            } else if (el.type === 'radio') {
                if (el.checked) data[cleanName] = el.value;
            } else if (el.type === 'file') {
                // Skip file uploads for offline
            } else if (el.tagName === 'SELECT' && el.multiple) {
                data[cleanName] = Array.from(el.selectedOptions).map(o => o.value);
            } else if (el.value !== '' && el.value !== null && el.value !== undefined) {
                data[cleanName] = el.value;
            }
        });

        // Also try to extract from Alpine.js x-data/Livewire component data
        try {
            const wireEl = form.closest('[wire\\:id]');
            if (wireEl && wireEl.__livewire) {
                const lwData = wireEl.__livewire.canonical?.data || wireEl.__livewire.data || {};
                if (lwData.data && typeof lwData.data === 'object') {
                    // Filament stores form data under 'data' key
                    Object.assign(data, lwData.data);
                }
            }
        } catch (e) {
            // Livewire internals may not be accessible, that's okay
        }

        return data;
    }

    // ── 7. Current Resource Detection ─────────────────────────
    function detectCurrentResource() {
        const path = window.location.pathname;
        // Pattern: /app/{resource}/create or /app/{resource}/{id}/edit
        const createMatch = path.match(/\/app\/([a-z\-]+)\/create$/);
        const editMatch = path.match(/\/app\/([a-z\-]+)\/([^/]+)\/edit$/);

        if (createMatch) {
            const slug = createMatch[1];
            const info = RESOURCE_MAP[slug] || { model: slug, label: slug };
            return { slug, action: 'create', recordId: null, label: info.label };
        }

        if (editMatch) {
            const slug = editMatch[1];
            const recordId = editMatch[2];
            const info = RESOURCE_MAP[slug] || { model: slug, label: slug };
            return { slug, action: 'update', recordId, label: info.label };
        }

        return null;
    }

    // ── 8. Offline Form Saver (legacy + generic) ──────────────
    window.saveFormOffline = async function (storeName, formData) {
        if (typeof InfraDB === 'undefined') {
            console.error('InfraDB not loaded');
            return false;
        }

        try {
            const record = await InfraDB.add(storeName, formData);
            updateQueueBadge();
            showToast('Saved offline', 'Data saved locally and will sync when you reconnect.', 'info');
            return record;
        } catch (e) {
            console.error('Failed to save offline:', e);
            showToast('Save failed', 'Could not save data locally.', 'danger');
            return false;
        }
    };

    // ── 9. Toast Helper ───────────────────────────────────────
    function showToast(title, message, type = 'info') {
        // Use Filament's notification system if available
        if (window.Livewire && navigator.onLine) {
            try {
                window.Livewire.dispatch('notification', {
                    title: title,
                    body: message,
                    status: type,
                });
                return;
            } catch (e) { /* fallback below */ }
        }

        // Fallback: custom toast
        const toast = document.createElement('div');
        toast.className = `infrahub-toast infrahub-toast-${type}`;
        toast.innerHTML = `<strong>${title}</strong><br><small>${message}</small>`;
        document.body.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('show'));
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 4500);
    }

    // ── 10. Public API ────────────────────────────────────────
    window.isInfraHubOnline = () => isOnline;
    window.infrahubSync = triggerSync;

    // ── 11. Keyboard shortcut: Ctrl+Shift+S to force save ────
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.shiftKey && e.key === 'S') {
            e.preventDefault();
            const resourceInfo = detectCurrentResource();
            if (resourceInfo) {
                const formData = extractFilamentFormData();
                if (Object.keys(formData).length > 0) {
                    InfraDB.queueForm(
                        resourceInfo.slug,
                        resourceInfo.action,
                        formData,
                        resourceInfo.recordId,
                        `${resourceInfo.action === 'create' ? 'New' : 'Update'} ${resourceInfo.label}`
                    ).then(() => {
                        updateQueueBadge();
                        showToast('Saved to offline queue', `${resourceInfo.label} queued for sync.`, 'info');
                    });
                } else {
                    showToast('No form data', 'Could not detect form fields on this page.', 'danger');
                }
            } else {
                showToast('Not on a form', 'Navigate to a create or edit page first.', 'info');
            }
        }
    });

    // ── Boot ──────────────────────────────────────────────────
    function boot() {
        createBanner();
        createQueueBadge();

        window.addEventListener('online', handleOnline);
        window.addEventListener('offline', handleOffline);

        // Show banner immediately if offline on load
        if (!navigator.onLine) {
            isOnline = false;
            showOfflineBanner();
        }

        // Install Livewire interceptor
        installLivewireInterceptor();

        // Update badge on load
        updateQueueBadge();

        // Periodic badge update (every 10s)
        setInterval(updateQueueBadge, 10000);

        // Try to sync on load if online
        if (navigator.onLine) {
            setTimeout(triggerSync, 3000);
        }

        // Listen for SW sync completion
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.addEventListener('message', (event) => {
                if (event.data?.type === 'SYNC_COMPLETE') {
                    updateQueueBadge();
                    showToast('Sync complete ✓', 'All offline data has been synced.', 'success');
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
