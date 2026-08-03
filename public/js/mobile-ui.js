/**
 * InfraHub Mobile PWA — UI Utility Module
 * Handles notifications, haptics, FAB speed dial, launcher filtering, company context switching, and action sheets.
 */
const MobileUI = (() => {
    'use strict';

    // ── Haptic Vibration ─────────────────────────────────────
    function haptic(ms = 15) {
        if ('vibrate' in navigator) {
            try { navigator.vibrate(ms); } catch { }
        }
    }

    // ── Toast Notifications ──────────────────────────────────
    function toast(msg, type = 'success', duration = 3000) {
        const el = document.getElementById('m-toast');
        if (!el) return;
        el.textContent = msg;
        el.className = `m-toast ${type} show`;
        setTimeout(() => { el.classList.remove('show'); }, duration);
    }

    // ── Modules Launcher & Category Filtering ────────────────
    function toggleModulesMenu() {
        haptic(20);
        const modal = document.getElementById('m-modules-modal');
        if (!modal) return;
        const isHidden = modal.style.display === 'none' || !modal.style.display;
        modal.style.display = isHidden ? 'block' : 'none';
        if (isHidden) {
            const input = document.getElementById('m-launcher-search');
            if (input) {
                input.value = '';
                input.focus();
            }
            filterModules('');
        }
    }

    function filterModules(query = '', category = 'all') {
        const grid = document.getElementById('m-launcher-grid');
        if (!grid) return;
        const cards = grid.querySelectorAll('.m-card');
        const q = query.toLowerCase().trim();

        cards.forEach(card => {
            const title = card.querySelector('.m-card-title')?.textContent.toLowerCase() || '';
            const sub = card.querySelector('.m-card-subtitle')?.textContent.toLowerCase() || '';
            const cardCat = card.getAttribute('data-category') || 'all';

            const matchesQuery = !q || title.includes(q) || sub.includes(q);
            const matchesCat = category === 'all' || cardCat === category;

            if (matchesQuery && matchesCat) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });

        // Update category tabs active class
        const tabs = document.querySelectorAll('.m-category-tab');
        tabs.forEach(tab => {
            const tabCat = tab.getAttribute('data-cat') || 'all';
            tab.classList.toggle('active', tabCat === category);
        });
    }

    // ── Floating Action Button (FAB) ──────────────────────────
    function toggleFAB() {
        haptic(22);
        const fab = document.getElementById('m-fab-container');
        if (fab) {
            fab.classList.toggle('active');
        }
    }

    function closeFAB() {
        const fab = document.getElementById('m-fab-container');
        if (fab) {
            fab.classList.remove('active');
        }
    }

    // ── Bottom Sheet Drawer Modals ────────────────────────────
    function openSheet(sheetId) {
        haptic(20);
        closeFAB();
        const backdrop = document.getElementById('m-sheet-backdrop');
        const sheet = document.getElementById(sheetId);
        if (backdrop) backdrop.classList.add('active');
        if (sheet) sheet.classList.add('active');
    }

    function closeSheet(sheetId) {
        haptic(15);
        const sheet = sheetId ? document.getElementById(sheetId) : document.querySelector('.m-sheet.active');
        if (sheet) sheet.classList.remove('active');
        const backdrop = document.getElementById('m-sheet-backdrop');
        if (backdrop) backdrop.classList.remove('active');
    }

    // ── Company Context Switcher ──────────────────────────────
    const COMPANY_THEMES = {
        1: { accent: '#6366f1', glow: 'rgba(99, 102, 241, 0.2)' },     // InfraHub Enterprise (Indigo)
        2: { accent: '#059669', glow: 'rgba(5, 150, 105, 0.2)' },      // BuildCorp Infrastructure (Emerald)
        3: { accent: '#d97706', glow: 'rgba(217, 119, 6, 0.2)' },      // GeoWorld Surveyors (Amber)
        4: { accent: '#ec4899', glow: 'rgba(236, 72, 153, 0.2)' },     // Test Company Ltd (Pink)
    };

    function getActiveCompany() {
        try {
            const comp = localStorage.getItem('m_active_company');
            if (comp) return JSON.parse(comp);
        } catch {}
        return { id: 1, name: 'InfraHub Enterprise', role: 'Engineer' };
    }

    function applyCompanyTheme(companyId) {
        const theme = COMPANY_THEMES[companyId] || COMPANY_THEMES[1];
        document.documentElement.style.setProperty('--accent', theme.accent);
        document.documentElement.style.setProperty('--accent-glow', theme.glow);
    }

    function switchCompany(companyId, companyName, role = 'Engineer') {
        haptic(25);
        const comp = { id: companyId, name: companyName, role };
        localStorage.setItem('m_active_company', JSON.stringify(comp));
        
        // Update DOM elements showing active company
        const badgeText = document.getElementById('m-company-name-text');
        if (badgeText) badgeText.textContent = companyName;

        const cards = document.querySelectorAll('.m-company-card');
        cards.forEach(c => {
            const cId = c.getAttribute('data-company-id');
            c.classList.toggle('active', parseInt(cId) === companyId);
        });

        // Apply tenant branding tint
        applyCompanyTheme(companyId);

        toast(`Context switched to ${companyName} ✓`, 'success');
        closeSheet('sheet-company-switcher');
        
        // Trigger page refresh event
        window.dispatchEvent(new CustomEvent('company-switched', { detail: comp }));
    }

    // ── Global Event Handlers ─────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        // Touch haptic feedback on interactive elements
        document.addEventListener('click', (e) => {
            if (e.target.closest('button, a, .m-card, .m-action, .m-pill, .m-fab-item, .m-category-tab, .m-company-badge')) {
                haptic(12);
            }
            // Close FAB when tapping outside
            const fab = document.getElementById('m-fab-container');
            if (fab && fab.classList.contains('active') && !e.target.closest('.m-fab-container')) {
                closeFAB();
            }
        });

        // Initialize active company badge text and brand theme tint
        const activeComp = getActiveCompany();
        const badgeText = document.getElementById('m-company-name-text');
        if (badgeText && activeComp?.name) {
            badgeText.textContent = activeComp.name;
        }
        if (activeComp?.id) {
            applyCompanyTheme(activeComp.id);
        }
    });

    return {
        haptic,
        toast,
        toggleModulesMenu,
        filterModules,
        toggleFAB,
        closeFAB,
        openSheet,
        closeSheet,
        getActiveCompany,
        switchCompany
    };
})();

// Legacy window shortcuts for Blade template inline calls
window.toast = MobileUI.toast;
window.haptic = MobileUI.haptic;
window.toggleModulesMenu = MobileUI.toggleModulesMenu;
window.filterModules = MobileUI.filterModules;
window.toggleFAB = MobileUI.toggleFAB;
window.openSheet = MobileUI.openSheet;
window.closeSheet = MobileUI.closeSheet;
window.switchCompany = MobileUI.switchCompany;
