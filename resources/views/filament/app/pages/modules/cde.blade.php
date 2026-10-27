<x-filament-panels::page>

    @push('styles')
        <style>
            /* ═════════════════════════════════════════════════════════════════
               ASITE CDE (Common Data Environment) Enterprise Styling
               ═════════════════════════════════════════════════════════════════ */
            :root {
                --asite-primary: #1a73e8;
                --asite-primary-hover: #1557b0;
                --asite-primary-light: #e8f0fe;
                --asite-dark-top: #0a2540;
                --asite-dark-tab: #14355a;
                --asite-bg: #ffffff;
                --asite-surface: #f8fafc;
                --asite-border: #e2e8f0;
                --asite-border-dark: #cbd5e1;
                --asite-text: #1e293b;
                --asite-text-muted: #64748b;
                --asite-text-subtle: #94a3b8;
                --asite-folder-active-bg: #fef08a;
                --asite-folder-active-border: #d97706;
                --asite-folder-active-text: #78350f;
                --asite-hover: #f1f5f9;
                --asite-radius: 6px;
            }

            .dark {
                --asite-primary: #38bdf8;
                --asite-primary-hover: #0284c7;
                --asite-primary-light: rgba(56, 189, 248, 0.12);
                --asite-dark-top: #0b1329;
                --asite-dark-tab: #132247;
                --asite-bg: #0f172a;
                --asite-surface: #1e293b;
                --asite-border: #334155;
                --asite-border-dark: #475569;
                --asite-text: #f1f5f9;
                --asite-text-muted: #94a3b8;
                --asite-text-subtle: #64748b;
                --asite-folder-active-bg: #854d0e;
                --asite-folder-active-border: #facc15;
                --asite-folder-active-text: #fef08a;
                --asite-hover: #1e293b;
            }

            /* ── Asite Top Header Ribbon ───────────────────────────────────── */
            .asite-topbar {
                background: linear-gradient(180deg, #102a45 0%, #0d2238 100%);
                color: #ffffff;
                border-radius: 8px 8px 0 0;
                padding: 0 16px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                height: 48px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
            }

            .asite-topbar-left {
                display: flex;
                align-items: center;
                gap: 16px;
            }

            .asite-logo-badge {
                display: flex;
                align-items: center;
                gap: 6px;
                font-weight: 800;
                font-size: 15px;
                letter-spacing: -0.02em;
                color: #ffffff;
            }

            .asite-logo-icon {
                width: 26px;
                height: 26px;
                background: #1a73e8;
                border-radius: 5px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 900;
                font-size: 13px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            }

            .asite-project-selector {
                background: rgba(255, 255, 255, 0.12);
                border: 1px solid rgba(255, 255, 255, 0.2);
                color: #ffffff;
                padding: 4px 12px;
                border-radius: 5px;
                font-size: 13px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .asite-mode-badge {
                background: #0284c7;
                color: #ffffff;
                padding: 3px 8px;
                border-radius: 4px;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: 0.05em;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .asite-topbar-right {
                display: flex;
                align-items: center;
                gap: 12px;
                font-size: 13px;
            }

            .asite-top-icon-btn {
                background: transparent;
                border: none;
                color: rgba(255, 255, 255, 0.8);
                cursor: pointer;
                padding: 6px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.15s;
            }

            .asite-top-icon-btn:hover {
                background: rgba(255, 255, 255, 0.15);
                color: #ffffff;
            }

            .asite-top-avatar {
                width: 28px;
                height: 28px;
                border-radius: 50%;
                background: #475569;
                color: white;
                font-size: 11px;
                font-weight: 700;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 1px solid rgba(255, 255, 255, 0.3);
            }

            /* ── Asite Main Sub-Navigation Bar ────────────────────────────── */
            .asite-nav-bar {
                background: #ffffff;
                border-bottom: 1px solid var(--asite-border);
                display: flex;
                align-items: center;
                padding: 0 16px;
                overflow-x: auto;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            }

            .dark .asite-nav-bar {
                background: #111827;
                border-bottom-color: var(--asite-border);
            }

            .asite-nav-tab {
                padding: 12px 18px;
                font-size: 13px;
                font-weight: 500;
                color: var(--asite-text-muted);
                border: none;
                background: transparent;
                cursor: pointer;
                border-bottom: 2px solid transparent;
                white-space: nowrap;
                transition: all 0.12s;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .asite-nav-tab:hover {
                color: var(--asite-text);
                background: var(--asite-hover);
            }

            .asite-nav-tab.is-active {
                color: var(--asite-primary);
                border-bottom-color: var(--asite-primary);
                font-weight: 700;
            }

            /* ── Explorer Container (3-Column Layout) ─────────────────────── */
            .asite-explorer {
                display: flex;
                border: 1px solid var(--asite-border);
                background: var(--asite-bg);
                border-radius: 0 0 8px 8px;
                min-height: 680px;
                overflow: hidden;
            }

            /* ── Left Quick Task Strip (~72px) ────────────────────────────── */
            .asite-strip {
                width: 76px;
                background: #f8fafc;
                border-right: 1px solid var(--asite-border);
                display: flex;
                flex-direction: column;
                align-items: stretch;
                padding: 8px 4px;
                gap: 6px;
                flex-shrink: 0;
            }

            .dark .asite-strip {
                background: #0f172a;
            }

            .asite-strip-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 8px 4px;
                border-radius: 6px;
                border: 1px solid transparent;
                background: transparent;
                cursor: pointer;
                transition: all 0.15s;
                color: var(--asite-text-muted);
                text-decoration: none;
            }

            .asite-strip-item:hover {
                background: var(--asite-hover);
                color: var(--asite-text);
            }

            .asite-strip-item.is-active {
                background: #1a73e8;
                color: #ffffff !important;
                border-color: #1a73e8;
                box-shadow: 0 2px 4px rgba(26, 115, 232, 0.3);
            }

            .asite-strip-item.is-active .asite-strip-count {
                color: #ffffff;
            }

            .asite-strip-item.is-active .asite-strip-label {
                color: #ffffff;
            }

            .asite-strip-item.is-active svg {
                color: #ffffff;
            }

            .asite-strip-count {
                font-size: 14px;
                font-weight: 800;
                line-height: 1.1;
                margin-bottom: 2px;
                color: #1a73e8;
            }

            .asite-strip-count.count-danger {
                color: #dc2626;
            }

            .asite-strip-count.count-warning {
                color: #d97706;
            }

            .asite-strip-count.count-neutral {
                color: var(--asite-text-muted);
            }

            .asite-strip-label {
                font-size: 10px;
                line-height: 1.15;
                font-weight: 600;
            }

            /* ── Folder Tree Explorer Panel ───────────────────────────────── */
            .asite-tree-panel {
                width: 320px;
                background: #ffffff;
                border-right: 1px solid var(--asite-border);
                display: flex;
                flex-direction: column;
                flex-shrink: 0;
                transition: width 0.2s ease;
            }

            .dark .asite-tree-panel {
                background: #111827;
            }

            .asite-tree-panel.is-collapsed {
                width: 0;
                padding: 0;
                overflow: hidden;
                border-right: none;
            }

            .asite-tree-search {
                padding: 10px 12px;
                border-bottom: 1px solid var(--asite-border);
                background: var(--asite-surface);
            }

            .asite-search-input-wrap {
                position: relative;
                display: flex;
                align-items: center;
            }

            .asite-search-input-wrap svg {
                position: absolute;
                left: 10px;
                color: var(--asite-text-subtle);
                pointer-events: none;
            }

            .asite-search-input {
                width: 100%;
                font-size: 12px;
                padding: 7px 10px 7px 32px;
                border-radius: 5px;
                border: 1px solid var(--asite-border-dark);
                background: var(--asite-bg);
                color: var(--asite-text);
                outline: none;
                transition: border-color 0.15s;
            }

            .asite-search-input:focus {
                border-color: var(--asite-primary);
                box-shadow: 0 0 0 2px var(--asite-primary-light);
            }

            .asite-tree-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 6px 12px;
                border-bottom: 1px solid var(--asite-border);
                font-size: 11px;
                color: var(--asite-text-muted);
                background: var(--asite-surface);
            }

            .asite-tree-btn {
                background: transparent;
                border: none;
                color: var(--asite-primary);
                font-size: 11px;
                font-weight: 600;
                cursor: pointer;
                padding: 2px 6px;
                border-radius: 3px;
            }

            .asite-tree-btn:hover {
                background: var(--asite-primary-light);
            }

            .asite-tree-body {
                flex: 1;
                overflow-y: auto;
                padding: 6px 0;
                max-height: 720px;
            }

            /* Tree Row Styles */
            .asite-folder-row-wrap {
                width: 100%;
            }

            .asite-folder-row {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 6px 10px 6px 8px;
                cursor: pointer;
                font-size: 12px;
                color: var(--asite-text);
                border-left: 3px solid transparent;
                transition: background 0.1s;
                user-select: none;
                position: relative;
            }

            .asite-folder-row:hover {
                background: var(--asite-hover);
            }

            .asite-folder-row.is-selected {
                background: var(--asite-folder-active-bg) !important;
                border-left-color: var(--asite-folder-active-border) !important;
                color: var(--asite-folder-active-text) !important;
                font-weight: 700;
            }

            .asite-tree-chevron {
                background: transparent;
                border: none;
                cursor: pointer;
                color: var(--asite-text-subtle);
                width: 16px;
                height: 16px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 3px;
                padding: 0;
            }

            .asite-tree-chevron:hover {
                color: var(--asite-text);
                background: rgba(0, 0, 0, 0.05);
            }

            .asite-tree-spacer {
                width: 16px;
                display: inline-block;
            }

            .asite-folder-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #f59e0b;
                flex-shrink: 0;
            }

            .asite-folder-name {
                flex: 1;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                font-size: 12px;
            }

            .asite-folder-count {
                font-size: 10px;
                padding: 1px 6px;
                border-radius: 99px;
                background: rgba(0, 0, 0, 0.06);
                color: var(--asite-text-muted);
                font-weight: 600;
                margin-left: 4px;
            }

            .asite-folder-actions {
                display: flex;
                gap: 2px;
                margin-left: 4px;
            }

            .asite-icon-btn {
                background: transparent;
                border: none;
                cursor: pointer;
                color: var(--asite-text-subtle);
                padding: 2px;
                border-radius: 3px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .asite-icon-btn:hover {
                color: var(--asite-primary);
                background: var(--asite-primary-light);
            }

            .asite-icon-btn.asite-danger:hover {
                color: #ef4444;
                background: #fee2e2;
            }

            /* ── Splitter Handle ──────────────────────────────────────────── */
            .asite-splitter {
                width: 6px;
                background: var(--asite-border);
                cursor: col-resize;
                position: relative;
                flex-shrink: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .asite-splitter:hover {
                background: var(--asite-primary);
            }

            .asite-splitter-toggle {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 16px;
                height: 28px;
                background: var(--asite-bg);
                border: 1px solid var(--asite-border);
                border-radius: 3px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                color: var(--asite-text-muted);
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                font-size: 10px;
            }

            /* ── Right Content Area (Files View) ──────────────────────────── */
            .asite-content-pane {
                flex: 1;
                background: #ffffff;
                display: flex;
                flex-direction: column;
                min-width: 0;
            }

            .dark .asite-content-pane {
                background: #0f172a;
            }

            /* Subtabs Bar (Files / Distribution / Comments) */
            .asite-subtabs-bar {
                display: flex;
                align-items: center;
                border-bottom: 2px solid var(--asite-border);
                padding: 0 16px;
                background: #ffffff;
            }

            .dark .asite-subtabs-bar {
                background: #111827;
            }

            .asite-subtab-btn {
                padding: 10px 18px;
                font-size: 13px;
                font-weight: 600;
                color: var(--asite-text-muted);
                background: transparent;
                border: none;
                border-bottom: 2px solid transparent;
                margin-bottom: -2px;
                cursor: pointer;
                transition: all 0.12s;
            }

            .asite-subtab-btn:hover {
                color: var(--asite-text);
            }

            .asite-subtab-btn.is-active {
                color: #1a73e8;
                border-bottom-color: #1a73e8;
                font-weight: 700;
            }

            /* Action Toolbar */
            .asite-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 10px 16px;
                border-bottom: 1px solid var(--asite-border);
                background: var(--asite-surface);
                gap: 12px;
                flex-wrap: wrap;
            }

            .asite-toolbar-left {
                display: flex;
                align-items: center;
                gap: 10px;
                flex: 1;
                min-width: 240px;
            }

            .asite-toolbar-right {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .asite-doc-search {
                position: relative;
                flex: 1;
                max-width: 320px;
            }

            .asite-doc-search input {
                width: 100%;
                font-size: 12px;
                padding: 6px 10px 6px 30px;
                border-radius: 5px;
                border: 1px solid var(--asite-border-dark);
                background: var(--asite-bg);
                color: var(--asite-text);
                outline: none;
            }

            .asite-doc-search svg {
                position: absolute;
                left: 8px;
                top: 50%;
                transform: translateY(-50%);
                color: var(--asite-text-subtle);
            }

            .asite-btn-secondary {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                font-size: 12px;
                font-weight: 600;
                border-radius: 5px;
                border: 1px solid var(--asite-border-dark);
                background: var(--asite-bg);
                color: var(--asite-text);
                cursor: pointer;
                transition: all 0.12s;
            }

            .asite-btn-secondary:hover {
                background: var(--asite-hover);
                border-color: #94a3b8;
            }

            .asite-btn-primary {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                font-size: 12px;
                font-weight: 700;
                border-radius: 5px;
                border: 1px solid #1a73e8;
                background: #1a73e8;
                color: #ffffff;
                cursor: pointer;
                transition: all 0.12s;
                box-shadow: 0 1px 2px rgba(26, 115, 232, 0.2);
            }

            .asite-btn-primary:hover {
                background: #1557b0;
                border-color: #1557b0;
            }

            /* Breadcrumb row */
            .asite-breadcrumb-bar {
                display: flex;
                align-items: center;
                padding: 8px 16px;
                font-size: 12px;
                color: var(--asite-text-muted);
                border-bottom: 1px solid var(--asite-border);
                background: #ffffff;
                gap: 6px;
            }

            .dark .asite-breadcrumb-bar {
                background: #111827;
            }

            .asite-breadcrumb-item {
                color: #1a73e8;
                cursor: pointer;
                font-weight: 500;
            }

            .asite-breadcrumb-item:hover {
                text-decoration: underline;
            }

            .asite-breadcrumb-current {
                color: var(--asite-text);
                font-weight: 700;
            }

            /* ── Asite Data Table ─────────────────────────────────────────── */
            .asite-table-wrap {
                flex: 1;
                overflow-x: auto;
                min-height: 420px;
            }

            .asite-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
                text-align: left;
            }

            .asite-table th {
                background: #f8fafc;
                color: #475569;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                padding: 10px 12px;
                border-bottom: 1px solid var(--asite-border);
                white-space: nowrap;
                user-select: none;
            }

            .dark .asite-table th {
                background: #1e293b;
                color: #94a3b8;
            }

            .asite-table th.sortable {
                cursor: pointer;
            }

            .asite-table th.sortable:hover {
                color: #1a73e8;
            }

            .asite-table td {
                padding: 9px 12px;
                border-bottom: 1px solid var(--asite-border);
                color: var(--asite-text);
                vertical-align: middle;
            }

            .asite-table tr:hover td {
                background: #f8fafc;
            }

            .dark .asite-table tr:hover td {
                background: #1e293b;
            }

            .asite-table tr.is-selected td {
                background: #eff6ff !important;
            }

            .dark .asite-table tr.is-selected td {
                background: #1e3a5f !important;
            }

            /* Doc Ref blue link */
            .asite-doc-link {
                color: #1a73e8;
                font-weight: 700;
                text-decoration: none;
                cursor: pointer;
                display: inline-block;
                transition: color 0.12s;
            }

            .asite-doc-link:hover {
                text-decoration: underline;
                color: #1557b0;
            }

            .asite-doc-title {
                color: var(--asite-text-muted);
                font-size: 11px;
                margin-top: 1px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 320px;
            }

            /* Flag button */
            .asite-flag-btn {
                background: transparent;
                border: none;
                cursor: pointer;
                color: #cbd5e1;
                padding: 2px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: color 0.15s;
            }

            .asite-flag-btn:hover {
                color: #f59e0b;
            }

            .asite-flag-btn.is-flagged {
                color: #ea580c;
            }

            /* File Type Icon container */
            .asite-ft-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 24px;
                height: 24px;
            }

            /* Badges */
            .asite-badge {
                display: inline-block;
                padding: 2px 6px;
                border-radius: 4px;
                font-size: 10px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.03em;
            }

            .asite-badge-rev {
                background: #e2e8f0;
                color: #475569;
            }

            .asite-badge-status {
                background: #dbeafe;
                color: #1d4ed8;
            }

            .asite-badge-s4 {
                background: #dcfce7;
                color: #15803d;
            }

            .asite-badge-s3 {
                background: #fef3c7;
                color: #b45309;
            }

            .asite-badge-s2 {
                background: #e0f2fe;
                color: #0369a1;
            }

            .asite-badge-s0 {
                background: #f1f5f9;
                color: #64748b;
            }

            /* Footer Bar */
            .asite-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 8px 16px;
                border-top: 1px solid var(--asite-border);
                background: var(--asite-surface);
                font-size: 12px;
                color: var(--asite-text-muted);
                flex-wrap: wrap;
                gap: 8px;
            }

            .asite-footer-left {
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .asite-footer-right {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .asite-pagination {
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .asite-page-btn {
                width: 26px;
                height: 26px;
                border-radius: 4px;
                border: 1px solid var(--asite-border-dark);
                background: var(--asite-bg);
                color: var(--asite-text);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                font-size: 11px;
                font-weight: 600;
            }

            .asite-page-btn:hover {
                background: var(--asite-hover);
            }

            .asite-page-btn.is-active {
                background: #1a73e8;
                color: white;
                border-color: #1a73e8;
            }

            /* ── Slide-over Drawer (Document Details & Comments) ──────────── */
            .asite-drawer-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.45);
                backdrop-filter: blur(2px);
                z-index: 9999;
                display: flex;
                justify-content: flex-end;
            }

            .asite-drawer-panel {
                width: 100%;
                max-width: 520px;
                background: var(--asite-bg);
                height: 100%;
                box-shadow: -4px 0 24px rgba(0, 0, 0, 0.2);
                display: flex;
                flex-direction: column;
                animation: slideInRight 0.2s ease-out;
            }

            @keyframes slideInRight {
                from { transform: translateX(100%); }
                to { transform: translateX(0); }
            }

            .asite-drawer-header {
                padding: 16px 20px;
                border-bottom: 1px solid var(--asite-border);
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: var(--asite-surface);
            }

            .asite-drawer-title {
                font-size: 15px;
                font-weight: 700;
                color: var(--asite-text);
            }

            .asite-drawer-tabs {
                display: flex;
                border-bottom: 1px solid var(--asite-border);
                background: var(--asite-surface);
                padding: 0 16px;
            }

            .asite-drawer-tab-btn {
                padding: 10px 14px;
                font-size: 12px;
                font-weight: 600;
                color: var(--asite-text-muted);
                background: transparent;
                border: none;
                border-bottom: 2px solid transparent;
                cursor: pointer;
            }

            .asite-drawer-tab-btn.is-active {
                color: #1a73e8;
                border-bottom-color: #1a73e8;
                font-weight: 700;
            }

            .asite-drawer-body {
                flex: 1;
                overflow-y: auto;
                padding: 20px;
            }

            .asite-meta-grid {
                display: grid;
                grid-template-columns: 120px 1fr;
                gap: 12px 16px;
                font-size: 12px;
            }

            .asite-meta-label {
                color: var(--asite-text-muted);
                font-weight: 600;
            }

            .asite-meta-val {
                color: var(--asite-text);
                font-weight: 500;
            }

            /* ── Comments & Collaboration ── */
            .asite-comment-card {
                padding: 14px 16px;
                border-radius: 8px;
                border: 1px solid var(--asite-border);
                background: var(--asite-surface);
                margin-bottom: 12px;
                transition: box-shadow 0.15s ease, border-color 0.15s ease;
            }

            .asite-comment-card:hover {
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
            }

            .asite-comment-card.is-resolved {
                border-color: rgba(34, 197, 94, 0.35);
                background: rgba(34, 197, 94, 0.03);
            }

            .asite-avatar {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 11px;
                font-weight: 700;
                color: #ffffff;
                flex-shrink: 0;
            }

            .asite-comment-target {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                font-size: 11px;
                font-weight: 600;
                padding: 2px 8px;
                border-radius: 4px;
                background: var(--asite-bg);
                border: 1px solid var(--asite-border);
                color: var(--asite-blue);
                cursor: pointer;
                text-decoration: none;
                transition: background 0.12s;
            }

            .asite-comment-target:hover {
                background: rgba(0, 102, 204, 0.08);
            }

            .asite-status-pill {
                font-size: 10.5px;
                font-weight: 600;
                padding: 3px 10px;
                border-radius: 12px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
                border: none;
                cursor: pointer;
                transition: transform 0.1s ease, opacity 0.15s ease;
            }

            .asite-status-pill:hover {
                transform: translateY(-1px);
                opacity: 0.9;
            }

            .asite-status-open {
                background: #fef3c7;
                color: #92400e;
            }

            .dark .asite-status-open {
                background: #78350f;
                color: #fde68a;
            }

            .asite-status-resolved {
                background: #dcfce7;
                color: #166534;
            }

            .dark .asite-status-resolved {
                background: #14532d;
                color: #86efac;
            }

            .asite-comment-count-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 2px 7px;
                border-radius: 10px;
                background: #e0f2fe;
                color: #0369a1;
                font-size: 10.5px;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: background 0.12s ease;
                text-decoration: none;
                vertical-align: middle;
            }

            .asite-comment-count-badge:hover {
                background: #bae6fd;
            }

            .dark .asite-comment-count-badge {
                background: #0c4a6e;
                color: #7dd3fc;
            }

            .asite-reply-connector {
                margin-top: 12px;
                padding-left: 18px;
                border-left: 2px solid var(--asite-border);
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .asite-reply-card {
                padding: 10px 12px;
                border-radius: 6px;
                background: var(--asite-bg);
                border: 1px solid var(--asite-border);
            }

            /* ── Folder Summary & Guidelines Bar ── */
            .asite-folder-summary {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 8px 16px;
                background: var(--asite-surface);
                border-bottom: 1px solid var(--asite-border);
                font-size: 12px;
                flex-wrap: wrap;
            }

            .asite-folder-guidelines {
                display: flex;
                align-items: center;
                gap: 8px;
                flex: 1;
                min-width: 240px;
            }

            /* ── Clean Modals (Folder Creation / Rename - No ISO) ── */
            .asite-modal-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.55);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 16px;
            }

            .asite-modal-card {
                background: var(--asite-surface);
                width: 100%;
                max-width: 480px;
                border-radius: 12px;
                border: 1px solid var(--asite-border);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
                overflow: hidden;
            }

            .asite-modal-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 16px 20px;
                border-bottom: 1px solid var(--asite-border);
                background: var(--asite-surface);
            }

            .asite-modal-title {
                font-size: 14px;
                font-weight: 700;
                color: var(--asite-text);
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .asite-modal-body {
                padding: 20px;
                background: var(--asite-surface);
            }

            .asite-modal-footer {
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 10px;
                padding: 14px 20px;
                border-top: 1px solid var(--asite-border);
                background: var(--asite-bg);
            }

            .asite-form-label {
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 11px;
                font-weight: 600;
                color: var(--asite-text);
                margin-bottom: 5px;
            }

            .asite-form-input {
                width: 100%;
                font-size: 12px;
                padding: 8px 12px;
                border-radius: 6px;
                border: 1px solid var(--asite-border-dark);
                background: var(--asite-bg);
                color: var(--asite-text);
                outline: none;
                box-sizing: border-box;
                transition: border-color 0.15s ease, box-shadow 0.15s ease;
            }

            .asite-form-input:focus {
                border-color: var(--asite-blue);
                box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.15);
            }

            .asite-form-hint {
                font-size: 11px;
                color: var(--asite-text-muted);
                margin-top: 5px;
                line-height: 1.4;
            }
        </style>
    @endpush

    {{-- ══════════════════════════════════════════════════════════════════════
         1. ASITE TOP CONTEXT HEADER BAR
         ══════════════════════════════════════════════════════════════════════ --}}
    <div class="asite-topbar">
        <div class="asite-topbar-left">
            <div class="asite-logo-badge">
                <span class="asite-logo-icon">A</span>
                <span>Asite</span>
            </div>

            <div class="asite-project-selector" title="Active CDE Project">
                <svg viewBox="0 0 20 20" width="14" height="14" fill="#fbbf24">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span>Projects: {{ $this->record->name }}</span>
                <svg viewBox="0 0 20 20" width="12" height="12" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </div>

            <span class="asite-mode-badge">
                CDE
                <svg viewBox="0 0 20 20" width="10" height="10" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            </span>
        </div>

        <div class="asite-topbar-right">
            <button type="button" class="asite-top-icon-btn" title="Search"><svg viewBox="0 0 20 20" width="16" height="16" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" /></svg></button>
            <button type="button" class="asite-top-icon-btn" title="Video Meeting"><svg viewBox="0 0 20 20" width="16" height="16" fill="currentColor"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" /></svg></button>
            <button type="button" class="asite-top-icon-btn" title="Notifications"><svg viewBox="0 0 20 20" width="16" height="16" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" /></svg></button>
            <button type="button" class="asite-top-icon-btn" title="Help"><svg viewBox="0 0 20 20" width="16" height="16" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg></button>
            <div class="asite-top-avatar" title="{{ auth()->user()?->name ?? 'User' }}">
                {{ strtoupper(substr(auth()->user()?->name ?? 'PG', 0, 2)) }}
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         2. ASITE MODULE TABS NAVIGATION BAR
         ══════════════════════════════════════════════════════════════════════ --}}
    <div class="asite-nav-bar">
        <a href="{{ route('filament.app.resources.cde-projects.view', ['record' => $this->record->id]) }}" class="asite-nav-tab">
            Dashboard
        </a>
        <a href="{{ route('filament.app.resources.cde-projects.index') }}" class="asite-nav-tab">
            Projects
        </a>
        <button type="button" class="asite-nav-tab is-active">
            Files
        </button>
        <a href="{{ route('filament.app.resources.cde-projects.module-rfi-submittals', ['record' => $this->record->id]) }}" class="asite-nav-tab">
            Project Forms
        </a>
        <a href="{{ route('filament.app.resources.cde-projects.module-task-workflow', ['record' => $this->record->id]) }}" class="asite-nav-tab">
            Workflows
        </a>
        <button type="button" wire:click="$set('activeDocTab', 'comments')" class="asite-nav-tab {{ $this->activeDocTab === 'comments' ? 'is-active' : '' }}">
            aMessages
        </button>
        <button type="button" wire:click="setQuickFilter('incomplete')" class="asite-nav-tab">
            Tasks
        </button>
        <a href="{{ route('filament.app.resources.cde-projects.module-reporting', ['record' => $this->record->id]) }}" class="asite-nav-tab">
            Reports
        </a>
        <button type="button" wire:click="initializeAsiteStructure" class="asite-nav-tab" title="Initialize Standard ASITE Tree">
            <span style="color:#f59e0b;font-weight:700;">★ ASITE Template</span>
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         3. ASITE 3-COLUMN EXPLORER
         ══════════════════════════════════════════════════════════════════════ --}}
    @php
        $counts = $this->getQuickFilterCounts();
        $folders = $this->getFolderTree();
        $docs = $this->getDocuments();
    @endphp

    <div class="asite-explorer">

        {{-- ── COLUMN 1: LEFT QUICK TASK STRIP (~76px) ────────────────── --}}
        <div class="asite-strip">
            {{-- All --}}
            <button type="button"
                    wire:click="setQuickFilter('all')"
                    class="asite-strip-item {{ $this->quickFilter === 'all' ? 'is-active' : '' }}"
                    title="All Documents in Folder">
                <svg viewBox="0 0 20 20" width="18" height="18" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
                <span class="asite-strip-label" style="margin-top:4px;">All</span>
            </button>

            {{-- Incomplete Tasks --}}
            <button type="button"
                    wire:click="setQuickFilter('incomplete')"
                    class="asite-strip-item {{ $this->quickFilter === 'incomplete' ? 'is-active' : '' }}"
                    title="Incomplete Tasks / WIP / Under Review">
                <span class="asite-strip-count">{{ $counts['incomplete'] }}</span>
                <span class="asite-strip-label">Incomplete Tasks</span>
            </button>

            {{-- Overdue Tasks --}}
            <button type="button"
                    wire:click="setQuickFilter('overdue')"
                    class="asite-strip-item {{ $this->quickFilter === 'overdue' ? 'is-active' : '' }}"
                    title="Overdue Tasks">
                <span class="asite-strip-count count-danger">{{ $counts['overdue'] }}</span>
                <span class="asite-strip-label">Overdue Tasks</span>
            </button>

            {{-- Due Today --}}
            <button type="button"
                    wire:click="setQuickFilter('due_today')"
                    class="asite-strip-item {{ $this->quickFilter === 'due_today' ? 'is-active' : '' }}"
                    title="Tasks Due Today">
                <span class="asite-strip-count count-warning">{{ $counts['due_today'] }}</span>
                <span class="asite-strip-label">Due Today</span>
            </button>

            {{-- Unread Comments --}}
            <button type="button"
                    wire:click="setQuickFilter('unread_comments')"
                    class="asite-strip-item {{ $this->quickFilter === 'unread_comments' ? 'is-active' : '' }}"
                    title="Unread Document Comments">
                <span class="asite-strip-count count-neutral">{{ $counts['unread_comments'] }}</span>
                <span class="asite-strip-label">Unread Comments</span>
            </button>
        </div>

        {{-- ── COLUMN 2: CENTER FOLDER TREE PANEL (~320px) ────────────── --}}
        <div class="asite-tree-panel {{ $this->isTreeCollapsed ? 'is-collapsed' : '' }}">
            {{-- Search Folders Input --}}
            <div class="asite-tree-search">
                <div class="asite-search-input-wrap">
                    <svg viewBox="0 0 20 20" width="14" height="14" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <input type="text"
                           wire:model.live.debounce.250ms="folderSearch"
                           placeholder="Search Folders"
                           class="asite-search-input" />
                </div>
            </div>

            {{-- Tree Quick Actions Toolbar --}}
            <div class="asite-tree-toolbar">
                <span style="font-weight:700;">Folders</span>
                <div style="display:flex;gap:4px;">
                    <button type="button" wire:click="openNewFolderModal(null)" class="asite-tree-btn" title="Add Root Folder">+ New</button>
                    <button type="button" wire:click="expandAllFolders" class="asite-tree-btn" title="Expand All">Expand</button>
                    <button type="button" wire:click="collapseAllFolders" class="asite-tree-btn" title="Collapse All">Collapse</button>
                </div>
            </div>

            {{-- Tree Hierarchy Body --}}
            <div class="asite-tree-body">
                {{-- Root Asite Folder Node --}}
                <div class="asite-folder-row {{ $this->currentFolderId === null ? 'is-selected' : '' }}"
                     style="padding-left: 6px;"
                     wire:click="selectFolder(null)">
                    <span class="asite-folder-icon">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="#f59e0b">
                            <path d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-15a3 3 0 00-3 3V18a3 3 0 003 3h15zM1.5 10.146V6a3 3 0 013-3h5.379a2.25 2.25 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 013 3v1.146A4.483 4.483 0 0019.5 9h-15a4.483 4.483 0 00-3 1.146z" />
                        </svg>
                    </span>
                    <span class="asite-folder-name" style="font-weight:700;">Asite</span>
                </div>

                {{-- Project Root Level Node --}}
                <div class="asite-folder-row"
                     style="padding-left: 18px;"
                     wire:click="selectFolder(null)">
                    <span style="color:#f59e0b;margin-right:2px;">★</span>
                    <span class="asite-folder-icon">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="#f59e0b">
                            <path d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-15a3 3 0 00-3 3V18a3 3 0 003 3h15zM1.5 10.146V6a3 3 0 013-3h5.379a2.25 2.25 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 013 3v1.146A4.483 4.483 0 0019.5 9h-15a4.483 4.483 0 00-3 1.146z" />
                        </svg>
                    </span>
                    <span class="asite-folder-name" style="font-weight:600;">{{ $this->record->name }}</span>
                    @php $totalRootDocs = $this->record->documents()->count(); @endphp
                    @if($totalRootDocs > 0)
                        <span class="asite-folder-count">{{ $totalRootDocs }}</span>
                    @endif
                </div>

                {{-- Render Top-Level Folders & Recursive Children --}}
                @forelse($folders as $folder)
                    @include('filament.app.pages.modules.partials.asite-folder-node', [
                        'node' => $folder,
                        'depth' => 1,
                        'expandedFolderIds' => $this->expandedFolderIds,
                        'currentFolderId' => $this->currentFolderId
                    ])
                @empty
                    <div style="padding: 20px 12px; text-align: center; color: var(--asite-text-muted); font-size: 11px;">
                        No folders found.
                        <div style="margin-top: 8px;">
                            <button type="button" wire:click="initializeAsiteStructure" class="asite-btn-primary" style="font-size:11px;padding:4px 10px;">
                                Setup ASITE Folders
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ── SPLITTER HANDLE ────────────────────────────────────────── --}}
        <div class="asite-splitter" wire:click="toggleTreeCollapse" title="{{ $this->isTreeCollapsed ? 'Expand Folders' : 'Collapse Folders' }}">
            <span class="asite-splitter-toggle">
                {{ $this->isTreeCollapsed ? '▶' : '◀' }}
            </span>
        </div>

        {{-- ── COLUMN 3: RIGHT FILES & DOCUMENTS AREA ──────────────────── --}}
        <div class="asite-content-pane">

            {{-- 1. Subtabs Header (Files | Distribution | Comments | Pipeline) --}}
            <div class="asite-subtabs-bar">
                <button type="button"
                        wire:click="$set('activeDocTab', 'files')"
                        class="asite-subtab-btn {{ $this->activeDocTab === 'files' ? 'is-active' : '' }}">
                    Files
                </button>
                <button type="button"
                        wire:click="$set('activeDocTab', 'distribution')"
                        class="asite-subtab-btn {{ $this->activeDocTab === 'distribution' ? 'is-active' : '' }}">
                    Distribution
                    @php $trCount = $this->getTransmittals()->count(); @endphp
                    @if($trCount > 0)
                        <span class="asite-badge asite-badge-rev" style="margin-left:4px;">{{ $trCount }}</span>
                    @endif
                </button>
                <button type="button"
                        wire:click="$set('activeDocTab', 'comments')"
                        class="asite-subtab-btn {{ $this->activeDocTab === 'comments' ? 'is-active' : '' }}">
                    Comments
                    @php
                        $openCommTotal = \App\Models\CdeDocumentComment::where(function($q) {
                            $q->whereHas('document', fn($d) => $d->where('cde_project_id', $this->record->id))
                              ->orWhereHas('folder', fn($f) => $f->where('cde_project_id', $this->record->id));
                        })->where('is_resolved', false)->whereNull('parent_id')->count();
                    @endphp
                    @if($openCommTotal > 0)
                        <span class="asite-badge asite-badge-s3" style="margin-left:4px;" title="{{ $openCommTotal }} open discussion(s)">{{ $openCommTotal }}</span>
                    @endif
                </button>
                <button type="button"
                        wire:click="$set('activeDocTab', 'pipeline')"
                        class="asite-subtab-btn {{ $this->activeDocTab === 'pipeline' ? 'is-active' : '' }}">
                    ISO 19650 Pipeline
                </button>
            </div>

            @if($this->activeDocTab === 'files')
                {{-- 2. Action Toolbar --}}
                <div class="asite-toolbar">
                    <div class="asite-toolbar-left">
                        {{-- Search Input --}}
                        <div class="asite-doc-search">
                            <svg viewBox="0 0 20 20" width="14" height="14" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                            <input type="text"
                                   wire:model.live.debounce.300ms="searchDoc"
                                   placeholder="Search" />
                        </div>

                        {{-- Filters Dropdown --}}
                        <div x-data="{ openFilters: false }" style="position:relative;">
                            <button type="button"
                                    @click="openFilters = !openFilters"
                                    class="asite-btn-secondary">
                                Filters
                                <svg viewBox="0 0 20 20" width="12" height="12" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>

                            <div x-show="openFilters"
                                 @click.outside="openFilters = false"
                                 x-cloak
                                 style="position:absolute;left:0;top:100%;margin-top:4px;width:280px;background:var(--asite-bg);border:1px solid var(--asite-border);border-radius:6px;box-shadow:0 10px 25px rgba(0,0,0,0.15);padding:14px;z-index:99;">
                                <div style="font-weight:700;font-size:12px;margin-bottom:10px;">Filter Documents</div>

                                <div style="margin-bottom:10px;">
                                    <label style="display:block;font-size:11px;font-weight:600;color:var(--asite-text-muted);margin-bottom:4px;">Discipline</label>
                                    <select wire:model.live="filterDiscipline" style="width:100%;font-size:12px;padding:5px;border-radius:4px;border:1px solid var(--asite-border);">
                                        <option value="">All Disciplines</option>
                                        <option value="civil">Civil</option>
                                        <option value="structural">Structural</option>
                                        <option value="architecture">Architecture</option>
                                        <option value="mechanical">Mechanical</option>
                                        <option value="electrical">Electrical</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>

                                <div style="margin-bottom:10px;">
                                    <label style="display:block;font-size:11px;font-weight:600;color:var(--asite-text-muted);margin-bottom:4px;">Suitability / Status</label>
                                    <select wire:model.live="filterStatus" style="width:100%;font-size:12px;padding:5px;border-radius:4px;border:1px solid var(--asite-border);">
                                        <option value="">All Statuses</option>
                                        <option value="S0">S0 - Work In Progress</option>
                                        <option value="S1">S1 - For Coordination</option>
                                        <option value="S2">S2 - For Information</option>
                                        <option value="S3">S3 - For Review & Comment</option>
                                        <option value="S4">S4 - For Stage Approval</option>
                                        <option value="S7">S7 - For AIM Handover</option>
                                    </select>
                                </div>

                                <div style="margin-bottom:12px;">
                                    <label style="display:block;font-size:11px;font-weight:600;color:var(--asite-text-muted);margin-bottom:4px;">File Format</label>
                                    <select wire:model.live="filterFileType" style="width:100%;font-size:12px;padding:5px;border-radius:4px;border:1px solid var(--asite-border);">
                                        <option value="">All Formats</option>
                                        <option value="pdf">PDF Documents</option>
                                        <option value="docx">Word (.docx)</option>
                                        <option value="xlsx">Excel (.xlsx)</option>
                                        <option value="zip">ZIP / Archives</option>
                                        <option value="dwg">CAD (.dwg)</option>
                                    </select>
                                </div>

                                <div style="display:flex;justify-content:space-between;">
                                    <button type="button"
                                            wire:click="$set('filterDiscipline', ''); $set('filterStatus', ''); $set('filterFileType', '');"
                                            style="font-size:11px;color:var(--asite-text-muted);background:none;border:none;cursor:pointer;">
                                        Reset
                                    </button>
                                    <button type="button" @click="openFilters = false" class="asite-btn-primary" style="padding:4px 10px;font-size:11px;">
                                        Apply
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="asite-toolbar-right">
                        {{-- Upload Button (calls Filament upload document action) --}}
                        <button type="button"
                                wire:click="mountAction('uploadDocument')"
                                class="asite-btn-primary">
                            <svg viewBox="0 0 20 20" width="14" height="14" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                            Upload
                            <svg viewBox="0 0 20 20" width="11" height="11" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>

                        {{-- Download Button (Batch / Folder download) --}}
                        <button type="button"
                                wire:click="batchDownload"
                                class="asite-btn-secondary"
                                title="Download Selected or Folder Documents">
                            <svg viewBox="0 0 20 20" width="14" height="14" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            Download
                        </button>

                        {{-- Column view toggle icon --}}
                        <button type="button" class="asite-btn-secondary" style="padding:6px 8px;" title="View options">
                            <svg viewBox="0 0 20 20" width="14" height="14" fill="currentColor"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                            <svg viewBox="0 0 20 20" width="10" height="10" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>

                        {{-- Drawer / Info icon --}}
                        <button type="button"
                                wire:click="$set('activeDocTab', 'pipeline')"
                                class="asite-btn-secondary"
                                style="padding:6px 8px;"
                                title="Pipeline & Health">
                            !
                        </button>
                    </div>
                </div>

                {{-- 3. Breadcrumb Path Bar --}}
                <div class="asite-breadcrumb-bar">
                    <span class="asite-breadcrumb-item" wire:click="selectFolder(null)">Root</span>
                    @if($this->currentFolderId)
                        @php
                            $breadcrumbs = [];
                            $curr = \App\Models\CdeFolder::find($this->currentFolderId);
                            while ($curr) {
                                array_unshift($breadcrumbs, $curr);
                                $curr = $curr->parent;
                            }
                        @endphp
                        @foreach($breadcrumbs as $bc)
                            <span style="color:var(--asite-text-subtle);">›</span>
                            @if($loop->last)
                                <span class="asite-breadcrumb-current">{{ $bc->name }}</span>
                            @else
                                <span class="asite-breadcrumb-item" wire:click="selectFolder({{ $bc->id }})">{{ $bc->name }}</span>
                            @endif
                        @endforeach
                    @endif
                    <span style="flex:1;"></span>
                </div>

                {{-- Folder Comments & Purpose Guidelines Banner (Clean Design) --}}
                @if($this->currentFolderId)
                    @php
                        $currFolder = $this->getCurrentFolder();
                        $folderCommentTotal = $currFolder ? $currFolder->comments()->whereNull('parent_id')->count() : 0;
                    @endphp
                    @if($currFolder)
                        <div class="asite-folder-summary">
                            <div class="asite-folder-guidelines">
                                <span style="display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:4px;background:#fef3c7;color:#d97706;flex-shrink:0;">
                                    <svg viewBox="0 0 20 20" width="14" height="14" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-5 0h-2v2h2V9z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                                <div>
                                    @if(!empty($currFolder->description))
                                        <span style="font-weight:600;color:var(--asite-text);">Folder Guidelines / Purpose:</span>
                                        <span style="color:var(--asite-text-muted);margin-left:4px;">{{ $currFolder->description }}</span>
                                    @else
                                        <span style="color:var(--asite-text-muted);font-style:italic;">No guidelines or purpose notes added to this folder yet.</span>
                                    @endif
                                </div>
                            </div>

                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($folderCommentTotal > 0)
                                    <button type="button"
                                            wire:click="$set('activeDocTab', 'comments'); $set('commentSearch', '{{ addslashes($currFolder->name) }}')"
                                            class="asite-comment-count-badge"
                                            title="View discussions for this folder">
                                        <svg viewBox="0 0 20 20" width="12" height="12" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-5 0h-2v2h2V9z" clip-rule="evenodd" />
                                        </svg>
                                        <span>{{ $folderCommentTotal }} discussion(s)</span>
                                    </button>
                                @endif

                                <button type="button"
                                        wire:click="openRenameFolderModal({{ $currFolder->id }})"
                                        class="asite-btn-secondary"
                                        style="padding:3px 8px;font-size:11px;"
                                        title="Edit folder name and guidelines">
                                    <svg viewBox="0 0 20 20" width="11" height="11" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                    <span>Edit Guidelines</span>
                                </button>
                            </div>
                        </div>
                    @endif
                @endif

                {{-- Batch Action Notification Bar (if items selected) --}}
                @if(count($this->selectedDocIds) > 0)
                    <div style="background:#eff6ff;border-bottom:1px solid #bfdbfe;padding:8px 16px;display:flex;align-items:center;gap:12px;font-size:12px;color:#1e40af;">
                        <span style="font-weight:700;">{{ count($this->selectedDocIds) }} item(s) selected</span>
                        <button type="button" wire:click="batchDownload" class="asite-btn-primary" style="padding:4px 10px;font-size:11px;">
                            Download ZIP
                        </button>
                        <button type="button" wire:click="batchFlag" class="asite-btn-secondary" style="padding:4px 10px;font-size:11px;">
                            Toggle Flag
                        </button>
                        <button type="button" wire:click="batchDelete" wire:confirm="Delete selected documents?" class="asite-btn-secondary" style="padding:4px 10px;font-size:11px;color:#dc2626;">
                            Delete
                        </button>
                        <button type="button" wire:click="$set('selectedDocIds', []); $set('selectAll', false);" style="background:none;border:none;color:#64748b;cursor:pointer;font-size:11px;margin-left:auto;">
                            Clear selection
                        </button>
                    </div>
                @endif

                {{-- 4. ASITE DOCUMENT DATA TABLE --}}
                <div class="asite-table-wrap">
                    <table class="asite-table">
                        <thead>
                            <tr>
                                {{-- Checkbox --}}
                                <th style="width: 36px; text-align: center;">
                                    <input type="checkbox"
                                           wire:click="toggleSelectAll"
                                           {{ $this->selectAll ? 'checked' : '' }}
                                           style="border-radius:3px;cursor:pointer;" />
                                </th>

                                {{-- P (Priority Flag) --}}
                                <th style="width: 34px; text-align: center; cursor: pointer;" wire:click="setSort('flag')" title="Sort by Flag / Priority">
                                    P
                                </th>

                                {{-- T (File Type Icon) --}}
                                <th style="width: 38px; text-align: center;" title="File Format">
                                    T
                                </th>

                                {{-- Doc Ref --}}
                                <th class="sortable" wire:click="setSort('doc_ref')">
                                    Doc Ref
                                    @if($this->sortBy === 'doc_ref')
                                        <span>{{ $this->sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </th>

                                {{-- Date --}}
                                <th class="sortable" style="width: 140px;" wire:click="setSort('date')">
                                    Date
                                    @if($this->sortBy === 'date')
                                        <span>{{ $this->sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                    @endif
                                </th>

                                {{-- My Tasks --}}
                                <th style="width: 140px;">
                                    My Tasks
                                </th>

                                {{-- Task Time --}}
                                <th style="width: 120px;">
                                    Task Time
                                </th>

                                {{-- Drawer trigger icon --}}
                                <th style="width: 36px; text-align: center;">
                                    <svg viewBox="0 0 20 20" width="14" height="14" fill="currentColor"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($docs as $doc)
                                @php
                                    $isSelectedRow = in_array($doc->id, $this->selectedDocIds, true);
                                    $suitabilityBadgeClass = match($doc->status) {
                                        'S4', 'approved' => 'asite-badge-s4',
                                        'S3', 'under_review' => 'asite-badge-s3',
                                        'S1', 'S2' => 'asite-badge-s2',
                                        default => 'asite-badge-s0',
                                    };
                                @endphp
                                <tr class="{{ $isSelectedRow ? 'is-selected' : '' }}">
                                    {{-- Checkbox --}}
                                    <td style="text-align: center;">
                                        <input type="checkbox"
                                               wire:click="toggleSelectDoc({{ $doc->id }})"
                                               {{ $isSelectedRow ? 'checked' : '' }}
                                               style="border-radius:3px;cursor:pointer;" />
                                    </td>

                                    {{-- Flag (P) --}}
                                    <td style="text-align: center;">
                                        <button type="button"
                                                wire:click="toggleFlag({{ $doc->id }})"
                                                class="asite-flag-btn {{ $doc->is_flagged ? 'is-flagged' : '' }}"
                                                title="{{ $doc->is_flagged ? 'Flagged Priority (Click to unflag)' : 'Click to flag' }}">
                                            @if($doc->is_flagged)
                                                <svg viewBox="0 0 20 20" width="15" height="15" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg viewBox="0 0 20 20" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" />
                                                </svg>
                                            @endif
                                        </button>
                                    </td>

                                    {{-- File Type Icon (T) --}}
                                    <td style="text-align: center;">
                                        @include('filament.app.pages.modules.partials.asite-file-icon', ['type' => $doc->file_type])
                                    </td>

                                    {{-- Doc Ref (Bold Blue Link) --}}
                                    <td>
                                        <div>
                                            <a wire:click="openDrawer({{ $doc->id }})" class="asite-doc-link" title="{{ $doc->title }}">
                                                {{ $doc->document_number }}
                                            </a>
                                            <div class="asite-doc-title">
                                                <span class="asite-badge asite-badge-rev">{{ $doc->revision }}</span>
                                                <span class="asite-badge {{ $suitabilityBadgeClass }}" style="margin-left:2px;">{{ $doc->status }}</span>
                                                @php $docCommentTotal = $doc->comments->count(); @endphp
                                                @if($docCommentTotal > 0)
                                                    <button type="button"
                                                            wire:click="openDrawer({{ $doc->id }}, 'comments')"
                                                            class="asite-comment-count-badge"
                                                            style="margin-left:3px;"
                                                            title="{{ $docCommentTotal }} discussion(s) - click to view">
                                                        <svg viewBox="0 0 20 20" width="11" height="11" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-5 0h-2v2h2V9z" clip-rule="evenodd" />
                                                        </svg>
                                                        <span>{{ $docCommentTotal }}</span>
                                                    </button>
                                                @endif
                                                <span style="margin-left:4px;">{{ $doc->title }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Date --}}
                                    <td style="color:var(--asite-text);font-weight:500;">
                                        {{ $doc->created_at->format('d-M-Y') }}
                                    </td>

                                    {{-- My Tasks --}}
                                    <td>
                                        @if(in_array($doc->status, ['S3', 'under_review']))
                                            <span style="color:#d97706;font-weight:600;">For Review</span>
                                        @elseif(in_array($doc->status, ['S4', 'approved']))
                                            <span style="color:#16a34a;font-weight:600;">Approved</span>
                                        @elseif($doc->status === 'revision')
                                            <span style="color:#dc2626;font-weight:600;">Revise</span>
                                        @else
                                            <span style="color:var(--asite-text-subtle);">—</span>
                                        @endif
                                    </td>

                                    {{-- Task Time --}}
                                    <td>
                                        @if($doc->due_date)
                                            @if($doc->due_date->isPast())
                                                <span style="color:#dc2626;font-weight:600;">Overdue</span>
                                            @elseif($doc->due_date->isToday())
                                                <span style="color:#ea580c;font-weight:600;">Due Today</span>
                                            @else
                                                <span style="color:var(--asite-text-muted);">{{ $doc->due_date->diffForHumans(['parts' => 1]) }}</span>
                                            @endif
                                        @else
                                            <span style="color:var(--asite-text-subtle);">—</span>
                                        @endif
                                    </td>

                                    {{-- Drawer trigger button --}}
                                    <td style="text-align: center;">
                                        <button type="button"
                                                wire:click="openDrawer({{ $doc->id }})"
                                                class="asite-icon-btn"
                                                title="View details & comments">
                                            <svg viewBox="0 0 20 20" width="15" height="15" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="padding: 40px; text-align: center; color: var(--asite-text-muted);">
                                        <div style="font-size: 14px; font-weight: 600; margin-bottom: 6px;">No documents in this folder</div>
                                        <div style="font-size: 12px; margin-bottom: 14px;">Upload documents or switch folders using the folder tree on the left.</div>
                                        <button type="button" wire:click="mountAction('uploadDocument')" class="asite-btn-primary">
                                            Upload Document
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- 5. ASITE TABLE FOOTER & PAGINATION --}}
                <div class="asite-footer">
                    <div class="asite-footer-left">
                        <span>Rows per Page</span>
                        <select wire:model.live="perPage"
                                style="font-size:12px;padding:3px 6px;border-radius:4px;border:1px solid var(--asite-border);background:var(--asite-bg);color:var(--asite-text);">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="250">250</option>
                        </select>
                    </div>

                    <div class="asite-footer-right">
                        <span>
                            Showing {{ $docs->firstItem() ?? 0 }} - {{ $docs->lastItem() ?? 0 }} of {{ $docs->total() }}
                        </span>

                        {{-- Pagination arrows --}}
                        <div class="asite-pagination">
                            <button type="button"
                                    wire:click="$set('currentPage', {{ max(1, $this->currentPage - 1) }})"
                                    class="asite-page-btn"
                                    {{ $this->currentPage <= 1 ? 'disabled style=opacity:0.4;cursor:default;' : '' }}>
                                ‹
                            </button>

                            <button type="button" class="asite-page-btn is-active">
                                {{ $this->currentPage }}
                            </button>

                            <button type="button"
                                    wire:click="$set('currentPage', {{ $this->currentPage + 1 }})"
                                    class="asite-page-btn"
                                    {{ $docs->hasMorePages() ? '' : 'disabled style=opacity:0.4;cursor:default;' }}>
                                ›
                            </button>
                        </div>

                        {{-- Customise dropdown --}}
                        <button type="button" class="asite-btn-secondary" style="font-size:11px;padding:4px 8px;">
                            Customise
                            <svg viewBox="0 0 20 20" width="10" height="10" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </button>
                    </div>
                </div>

            @elseif($this->activeDocTab === 'distribution')
                {{-- ── TAB 2: DISTRIBUTION / TRANSMITTALS ───────────────── --}}
                <div style="padding: 16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                        <div>
                            <h3 style="font-size:15px;font-weight:700;">Document Distribution & Transmittals</h3>
                            <p style="font-size:12px;color:var(--asite-text-muted);">Formal document distribution packages sent to contractors, clients, and consultants.</p>
                        </div>
                        <button type="button" wire:click="mountAction('createTransmittal')" class="asite-btn-primary">
                            + Create Transmittal
                        </button>
                    </div>

                    @php $transmittals = $this->getTransmittals(); @endphp
                    @if($transmittals->isNotEmpty())
                        <div class="asite-table-wrap">
                            <table class="asite-table">
                                <thead>
                                    <tr>
                                        <th>Transmittal #</th>
                                        <th>Subject</th>
                                        <th>Recipient Organization</th>
                                        <th>Purpose</th>
                                        <th>Documents</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transmittals as $tr)
                                        <tr>
                                            <td style="font-weight:700;color:#1a73e8;">{{ $tr->transmittal_number }}</td>
                                            <td style="font-weight:600;">{{ $tr->subject }}</td>
                                            <td>{{ $tr->to_organization }} @if($tr->to_contact) ({{ $tr->to_contact }}) @endif</td>
                                            <td><span class="asite-badge asite-badge-rev">{{ $tr->purpose }}</span></td>
                                            <td>{{ $tr->items->count() }} item(s)</td>
                                            <td><span class="asite-badge asite-badge-status">{{ $tr->status }}</span></td>
                                            <td>{{ $tr->created_at->format('d-M-Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="padding: 40px; text-align: center; color: var(--asite-text-muted); background: var(--asite-surface); border-radius: 8px;">
                            No transmittals issued yet.
                        </div>
                    @endif
                </div>

            @elseif($this->activeDocTab === 'comments')
                {{-- ── TAB 3: COMMENTS & DISCUSSIONS COLLABORATION HUB ─────── --}}
                <div style="padding: 16px 20px;">
                    @php
                        $currFolder = $this->getCurrentFolder();
                        $allComments = $this->getAllProjectComments();
                        $projectDocs = \App\Models\CdeDocument::where('cde_project_id', $this->record->id)->orderBy('document_number')->get();
                    @endphp

                    {{-- Header & Controls Bar --}}
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:16px;flex-wrap:wrap;">
                        <div>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <h3 style="font-size:16px;font-weight:700;color:var(--asite-text);margin:0;">Discussions & Review Comments</h3>
                                <span class="asite-badge asite-badge-rev">{{ $allComments->count() }} thread(s)</span>
                            </div>
                            <p style="font-size:12px;color:var(--asite-text-muted);margin:4px 0 0 0;">
                                Unified collaboration threads across folders, drawings, and project deliverables.
                            </p>
                        </div>

                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                            {{-- Filter Pills: All / Open / Resolved --}}
                            <div style="display:inline-flex;padding:2px;background:var(--asite-surface);border:1px solid var(--asite-border);border-radius:6px;gap:2px;">
                                <button type="button"
                                        wire:click="$set('commentFilterStatus', 'all')"
                                        style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:4px;border:none;cursor:pointer;background:{{ $this->commentFilterStatus === 'all' ? 'var(--asite-blue)' : 'transparent' }};color:{{ $this->commentFilterStatus === 'all' ? '#ffffff' : 'var(--asite-text)' }};">
                                    All
                                </button>
                                <button type="button"
                                        wire:click="$set('commentFilterStatus', 'open')"
                                        style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:4px;border:none;cursor:pointer;background:{{ $this->commentFilterStatus === 'open' ? '#d97706' : 'transparent' }};color:{{ $this->commentFilterStatus === 'open' ? '#ffffff' : 'var(--asite-text)' }};">
                                    ● Open
                                </button>
                                <button type="button"
                                        wire:click="$set('commentFilterStatus', 'resolved')"
                                        style="font-size:11px;font-weight:600;padding:4px 10px;border-radius:4px;border:none;cursor:pointer;background:{{ $this->commentFilterStatus === 'resolved' ? '#16a34a' : 'transparent' }};color:{{ $this->commentFilterStatus === 'resolved' ? '#ffffff' : 'var(--asite-text)' }};">
                                    ✓ Resolved
                                </button>
                            </div>

                            {{-- Search input --}}
                            <div class="asite-doc-search" style="width:200px;">
                                <svg viewBox="0 0 20 20" width="13" height="13" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                                <input type="text"
                                       wire:model.live.debounce.300ms="commentSearch"
                                       placeholder="Search comments..."
                                       style="width:100%;" />
                            </div>

                            {{-- + New Comment Button --}}
                            <button type="button"
                                    wire:click="$toggle('showNewCommentBox')"
                                    class="asite-btn-primary"
                                    style="padding:6px 12px;font-size:11.5px;">
                                <svg viewBox="0 0 20 20" width="12" height="12" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                <span>New Discussion</span>
                            </button>
                        </div>
                    </div>

                    {{-- ── NEW COMMENT COMPOSER CARD ──────────────────────────── --}}
                    @if($this->showNewCommentBox)
                        <div style="background:var(--asite-surface);border:1px solid var(--asite-blue);border-radius:8px;padding:16px;margin-bottom:20px;box-shadow:0 8px 24px rgba(0,102,204,0.08);">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                                <div style="font-size:13px;font-weight:700;color:var(--asite-text);">
                                    Create New Discussion / Comment Thread
                                </div>
                                <button type="button" wire:click="$set('showNewCommentBox', false)" class="asite-icon-btn" title="Close">
                                    <svg viewBox="0 0 20 20" width="13" height="13" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <div style="display:grid;grid-template-columns:1fr;gap:12px;margin-bottom:12px;">
                                <div>
                                    <label class="asite-form-label">
                                        <span>Target Item (Folder or Document)</span>
                                    </label>
                                    <select wire:model="commentTargetDocId" class="asite-form-input">
                                        <option value="">📁 Current Folder: {{ $currFolder ? $currFolder->name : 'Project Root' }}</option>
                                        <optgroup label="Specific Project Document">
                                            @foreach($projectDocs as $pdoc)
                                                <option value="{{ $pdoc->id }}">📄 {{ $pdoc->document_number }} — {{ Str::limit($pdoc->title, 45) }}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                            <div style="margin-bottom:12px;">
                                <label class="asite-form-label">
                                    <span>Comment / Instructions</span>
                                </label>
                                <textarea wire:model="newCommentText"
                                          placeholder="Type discussion thread, comment, or action item for the team..."
                                          rows="3"
                                          class="asite-form-input"
                                          autofocus></textarea>
                            </div>

                            <div style="display:flex;justify-content:flex-end;gap:8px;">
                                <button type="button" wire:click="$set('showNewCommentBox', false)" class="asite-btn-secondary" style="font-size:11px;">
                                    Cancel
                                </button>
                                <button type="button" wire:click="postProjectComment" class="asite-btn-primary" style="font-size:11px;">
                                    Post Discussion
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- ── COMMENTS THREAD LIST ──────────────────────────────── --}}
                    @forelse($allComments as $comm)
                        @php
                            $uName = $comm->user?->name ?? 'Team Member';
                            $parts = explode(' ', trim($uName));
                            $initials = strtoupper(substr($parts[0] ?? 'T', 0, 1) . substr($parts[1] ?? '', 0, 1));
                            if (strlen($initials) < 2 && strlen($parts[0]) >= 2) {
                                $initials = strtoupper(substr($parts[0], 0, 2));
                            }
                            $colors = ['#2563eb', '#7c3aed', '#059669', '#d97706', '#dc2626', '#0891b2', '#4f46e5', '#db2777'];
                            $avatarColor = $colors[abs(crc32($uName)) % count($colors)];
                        @endphp
                        <div class="asite-comment-card {{ $comm->is_resolved ? 'is-resolved' : '' }}">
                            {{-- Thread Top Row: Avatar, Author, Target, Status, Delete --}}
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:8px;">
                                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                    <div class="asite-avatar" style="background:{{ $avatarColor }};">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <div style="font-size:12px;font-weight:700;color:var(--asite-text);">
                                            {{ $uName }}
                                        </div>
                                        <div style="font-size:10.5px;color:var(--asite-text-muted);">
                                            {{ $comm->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    {{-- Target Badge: Doc or Folder --}}
                                    @if($comm->document)
                                        <span class="asite-comment-target"
                                              wire:click="openDrawer({{ $comm->cde_document_id }}, 'comments')"
                                              title="Document: {{ $comm->document->title }} (Click to inspect)">
                                            @include('filament.app.pages.modules.partials.asite-file-icon', ['type' => $comm->document->file_type])
                                            <span style="font-weight:700;">{{ $comm->document->document_number }}</span>
                                            <span style="color:var(--asite-text-muted);margin-left:2px;">{{ Str::limit($comm->document->title, 35) }}</span>
                                        </span>
                                    @elseif($comm->folder)
                                        <span class="asite-comment-target"
                                              wire:click="selectFolder({{ $comm->cde_folder_id }})"
                                              title="Folder: {{ $comm->folder->name }} (Click to open folder)">
                                            <svg viewBox="0 0 24 24" width="13" height="13" fill="#f59e0b">
                                                <path d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-15a3 3 0 00-3 3V18a3 3 0 003 3h15zM1.5 10.146V6a3 3 0 013-3h5.379a2.25 2.25 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 013 3v1.146A4.483 4.483 0 0019.5 9h-15a4.483 4.483 0 00-3 1.146z" />
                                            </svg>
                                            <span style="font-weight:700;">{{ $comm->folder->name }}</span>
                                        </span>
                                    @endif
                                </div>

                                <div style="display:flex;align-items:center;gap:6px;">
                                    {{-- Status Pill --}}
                                    <button type="button"
                                            wire:click="toggleCommentResolved({{ $comm->id }})"
                                            class="asite-status-pill {{ $comm->is_resolved ? 'asite-status-resolved' : 'asite-status-open' }}"
                                            title="Click to toggle status">
                                        {{ $comm->is_resolved ? '✓ Resolved' : '● Action Required' }}
                                    </button>

                                    {{-- Delete Comment --}}
                                    <button type="button"
                                            wire:click="deleteComment({{ $comm->id }})"
                                            wire:confirm="Delete this comment thread?"
                                            class="asite-icon-btn asite-danger"
                                            title="Delete thread">
                                        <svg viewBox="0 0 20 20" width="12" height="12" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Comment Body --}}
                            <div style="font-size:12.5px;color:var(--asite-text);line-height:1.55;background:var(--asite-bg);padding:10px 14px;border-radius:6px;border:1px solid var(--asite-border);margin-top:6px;word-break:break-word;">
                                {!! nl2br(e($comm->comment)) !!}
                            </div>

                            {{-- Thread Footer / Actions --}}
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-top:8px;">
                                <button type="button"
                                        wire:click="$set('replyToCommentId', {{ $this->replyToCommentId === $comm->id ? 'null' : $comm->id }})"
                                        style="font-size:11px;font-weight:600;color:var(--asite-blue);background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                                    <svg viewBox="0 0 20 20" width="11" height="11" fill="currentColor">
                                        <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span>{{ $this->replyToCommentId === $comm->id ? 'Cancel Reply' : 'Reply' }}</span>
                                </button>

                                @if($comm->replies->count() > 0)
                                    <span style="font-size:11px;color:var(--asite-text-muted);">
                                        {{ $comm->replies->count() }} {{ Str::plural('reply', $comm->replies->count()) }}
                                    </span>
                                @endif
                            </div>

                            {{-- ── THREADED REPLIES ──────────────────────────── --}}
                            @if($comm->replies->isNotEmpty() || $this->replyToCommentId === $comm->id)
                                <div class="asite-reply-connector">
                                    @foreach($comm->replies as $reply)
                                        @php
                                            $rName = $reply->user?->name ?? 'Team Member';
                                            $rParts = explode(' ', trim($rName));
                                            $rInitials = strtoupper(substr($rParts[0] ?? 'T', 0, 1) . substr($rParts[1] ?? '', 0, 1));
                                            $rAvatarColor = $colors[abs(crc32($rName)) % count($colors)];
                                        @endphp
                                        <div class="asite-reply-card">
                                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                                                <div style="display:flex;align-items:center;gap:6px;">
                                                    <div class="asite-avatar" style="width:22px;height:22px;font-size:9.5px;background:{{ $rAvatarColor }};">
                                                        {{ $rInitials }}
                                                    </div>
                                                    <span style="font-size:11px;font-weight:700;color:var(--asite-text);">{{ $rName }}</span>
                                                    <span style="font-size:10px;color:var(--asite-text-muted);">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <button type="button"
                                                        wire:click="deleteComment({{ $reply->id }})"
                                                        wire:confirm="Delete this reply?"
                                                        class="asite-icon-btn asite-danger"
                                                        style="padding:2px;"
                                                        title="Delete reply">
                                                    <svg viewBox="0 0 20 20" width="10" height="10" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div style="font-size:11.5px;color:var(--asite-text);line-height:1.45;word-break:break-word;">
                                                {!! nl2br(e($reply->comment)) !!}
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- Inline Reply Composer --}}
                                    @if($this->replyToCommentId === $comm->id)
                                        <div style="padding:10px;background:var(--asite-surface);border:1px solid var(--asite-blue);border-radius:6px;">
                                            <div style="font-size:11px;font-weight:600;color:var(--asite-text);margin-bottom:4px;">
                                                Replying to {{ $uName }}
                                            </div>
                                            <textarea wire:model="replyCommentText"
                                                      placeholder="Write your reply..."
                                                      rows="2"
                                                      class="asite-form-input"
                                                      style="margin-bottom:6px;"
                                                      autofocus></textarea>
                                            <div style="display:flex;justify-content:flex-end;gap:6px;">
                                                <button type="button" wire:click="$set('replyToCommentId', null)" class="asite-btn-secondary" style="font-size:10.5px;padding:3px 8px;">
                                                    Cancel
                                                </button>
                                                <button type="button" wire:click="replyToComment({{ $comm->id }})" class="asite-btn-primary" style="font-size:10.5px;padding:3px 10px;">
                                                    Send Reply
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div style="padding:48px 24px;text-align:center;background:var(--asite-surface);border-radius:10px;border:1px dashed var(--asite-border);">
                            <div style="width:48px;height:48px;border-radius:50%;background:#f1f5f9;display:inline-flex;align-items:center;justify-content:center;color:#64748b;margin-bottom:12px;">
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0112 2.25c2.43 0 4.817.177 7.152.521 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 01-3.476.383.39.39 0 00-.297.17l-2.755 4.133a.75.75 0 01-1.256 0l-2.755-4.133a.39.39 0 00-.297-.17 48.9 48.9 0 01-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97zM6.75 8.25a.75.75 0 01.75-.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H7.5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <h4 style="font-size:14px;font-weight:700;color:var(--asite-text);margin:0 0 4px 0;">No comments found</h4>
                            <p style="font-size:12px;color:var(--asite-text-muted);margin:0 0 16px 0;max-width:360px;margin-left:auto;margin-right:auto;">
                                @if(!empty($this->commentSearch) || $this->commentFilterStatus !== 'all')
                                    No discussions match your current filter or search criteria.
                                @else
                                    Start a discussion thread or leave comments on drawings and folders to collaborate with your project team.
                                @endif
                            </p>
                            <button type="button" wire:click="$set('showNewCommentBox', true)" class="asite-btn-primary" style="font-size:11.5px;">
                                + Start a Discussion
                            </button>
                        </div>
                    @endforelse
                </div>

            @elseif($this->activeDocTab === 'pipeline')
                {{-- ── TAB 4: ISO 19650 PIPELINE & HEALTH ────────────────── --}}
                <div style="padding: 16px;">
                    <h3 style="font-size:15px;font-weight:700;margin-bottom:12px;">ISO 19650 Status Pipeline</h3>
                    @php $pipeline = $this->getStatusPipeline(); @endphp
                    <div style="display:grid;grid-template-columns:repeat(5, 1fr);gap:10px;margin-bottom:20px;">
                        @foreach($pipeline as $stage)
                            <div style="background:{{ $stage['bg'] }};color:{{ $stage['color'] }};padding:14px;border-radius:6px;text-align:center;">
                                <div style="font-size:24px;font-weight:800;line-height:1;">{{ $stage['count'] }}</div>
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;margin-top:4px;">{{ $stage['label'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    @php $docHealth = $this->getDocumentHealth(); @endphp
                    <div style="display:flex;gap:10px;margin-bottom:20px;">
                        <div style="flex:1;padding:12px;border-radius:6px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;font-size:12px;">
                            <strong>{{ $docHealth['needs_revision'] }}</strong> documents requiring revision
                        </div>
                        <div style="flex:1;padding:12px;border-radius:6px;background:#fffbeb;border:1px solid #fde68a;color:#92400e;font-size:12px;">
                            <strong>{{ $docHealth['review_backlog'] }}</strong> awaiting review
                        </div>
                        <div style="flex:1;padding:12px;border-radius:6px;background:#f1f5f9;border:1px solid #e2e8f0;color:#475569;font-size:12px;">
                            <strong>{{ $docHealth['stale'] }}</strong> stale (30+ days without update)
                        </div>
                    </div>

                    @php $sm = $this->getSubmissionMatrix(); @endphp
                    <h4 style="font-size:13px;font-weight:700;margin-bottom:8px;">Deliverable Matrix by Discipline</h4>
                    <div style="overflow-x:auto;">
                        <table style="width:100%;font-size:12px;border-collapse:collapse;">
                            <thead>
                                <tr style="border-bottom:2px solid #e2e8f0;">
                                    <th style="text-align:left;padding:8px;">Discipline</th>
                                    @foreach($sm['levels'] as $level)
                                        <th style="text-align:center;padding:8px;color:{{ $level['color'] }};">{{ $level['label'] }}</th>
                                    @endforeach
                                    <th style="text-align:center;padding:8px;">Completion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sm['matrix'] as $row)
                                    <tr style="border-bottom:1px solid #f1f5f9;">
                                        <td style="padding:8px;font-weight:600;">{{ $row['discipline'] }} ({{ $row['total'] }})</td>
                                        @foreach(array_keys($sm['levels']) as $status)
                                            <td style="text-align:center;padding:8px;">{{ $row[$status] > 0 ? $row[$status] : '—' }}</td>
                                        @endforeach
                                        <td style="text-align:center;padding:8px;font-weight:700;">{{ $row['completion_pct'] }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════
         4. ASITE SLIDE-OVER DOCUMENT DETAIL DRAWER
         ══════════════════════════════════════════════════════════════════════ --}}
    @if($this->drawerOpen && $this->selectedDocId)
        @php $selectedDoc = $this->getSelectedDocument(); @endphp
        @if($selectedDoc)
            <div class="asite-drawer-overlay" wire:click.self="closeDrawer">
                <div class="asite-drawer-panel">
                    {{-- Header --}}
                    <div class="asite-drawer-header">
                        <div>
                            <div class="asite-drawer-title">{{ $selectedDoc->document_number }}</div>
                            <div style="font-size:12px;color:var(--asite-text-muted);margin-top:2px;">{{ $selectedDoc->title }}</div>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <button type="button" wire:click="downloadDocument({{ $selectedDoc->id }})" class="asite-btn-primary" style="padding:4px 10px;font-size:11px;">
                                Download
                            </button>
                            <button type="button" wire:click="closeDrawer" class="asite-icon-btn" style="padding:6px;">
                                ✕
                            </button>
                        </div>
                    </div>

                    {{-- Drawer Subtabs --}}
                    <div class="asite-drawer-tabs">
                        <button type="button" wire:click="$set('drawerTab', 'attributes')" class="asite-drawer-tab-btn {{ $this->drawerTab === 'attributes' ? 'is-active' : '' }}">
                            Attributes
                        </button>
                        <button type="button" wire:click="$set('drawerTab', 'versions')" class="asite-drawer-tab-btn {{ $this->drawerTab === 'versions' ? 'is-active' : '' }}">
                            Versions ({{ $selectedDoc->versions->count() + 1 }})
                        </button>
                        <button type="button" wire:click="$set('drawerTab', 'comments')" class="asite-drawer-tab-btn {{ $this->drawerTab === 'comments' ? 'is-active' : '' }}">
                            Comments ({{ $selectedDoc->comments->count() }})
                        </button>
                    </div>

                    {{-- Drawer Body --}}
                    <div class="asite-drawer-body">
                        @if($this->drawerTab === 'attributes')
                            <div class="asite-meta-grid">
                                <span class="asite-meta-label">Doc Reference:</span>
                                <span class="asite-meta-val" style="font-weight:700;color:#1a73e8;">{{ $selectedDoc->document_number }}</span>

                                <span class="asite-meta-label">Title:</span>
                                <span class="asite-meta-val">{{ $selectedDoc->title }}</span>

                                <span class="asite-meta-label">Revision:</span>
                                <span class="asite-meta-val"><span class="asite-badge asite-badge-rev">{{ $selectedDoc->revision }}</span></span>

                                <span class="asite-meta-label">Suitability / Status:</span>
                                <span class="asite-meta-val"><span class="asite-badge asite-badge-status">{{ $selectedDoc->status }}</span></span>

                                <span class="asite-meta-label">Discipline:</span>
                                <span class="asite-meta-val">{{ ucfirst($selectedDoc->discipline ?? 'Other') }}</span>

                                <span class="asite-meta-label">File Type:</span>
                                <span class="asite-meta-val" style="text-transform:uppercase;">{{ $selectedDoc->file_type }}</span>

                                <span class="asite-meta-label">File Size:</span>
                                <span class="asite-meta-val">{{ $selectedDoc->formattedSize() }}</span>

                                <span class="asite-meta-label">Folder:</span>
                                <span class="asite-meta-val">{{ $selectedDoc->folder?->name ?? 'Root' }}</span>

                                <span class="asite-meta-label">Uploaded By:</span>
                                <span class="asite-meta-val">{{ $selectedDoc->uploadedBy?->name ?? 'System' }}</span>

                                <span class="asite-meta-label">Upload Date:</span>
                                <span class="asite-meta-val">{{ $selectedDoc->created_at->format('d-M-Y H:i') }}</span>

                                <span class="asite-meta-label">Task Due Date:</span>
                                <span class="asite-meta-val">{{ $selectedDoc->due_date ? $selectedDoc->due_date->format('d-M-Y') : 'None' }}</span>
                            </div>

                            @if($selectedDoc->description)
                                <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--asite-border);">
                                    <div style="font-size:12px;font-weight:700;margin-bottom:6px;">Description / Purpose</div>
                                    <div style="font-size:12px;color:var(--asite-text-muted);line-height:1.5;">{{ $selectedDoc->description }}</div>
                                </div>
                            @endif

                        @elseif($this->drawerTab === 'versions')
                            <div style="margin-bottom:14px;">
                                <div style="font-weight:700;font-size:13px;margin-bottom:4px;">Current Version: {{ $selectedDoc->revision }}</div>
                                <div style="font-size:11px;color:var(--asite-text-muted);">Uploaded {{ $selectedDoc->created_at->format('d-M-Y') }} by {{ $selectedDoc->uploadedBy?->name ?? 'User' }}</div>
                            </div>

                            @if($selectedDoc->versions->isNotEmpty())
                                <div style="border-top:1px solid var(--asite-border);padding-top:12px;">
                                    <div style="font-size:12px;font-weight:700;margin-bottom:10px;">Previous Revisions</div>
                                    @foreach($selectedDoc->versions as $v)
                                        <div style="padding:10px;border-radius:5px;border:1px solid var(--asite-border);background:var(--asite-surface);margin-bottom:8px;">
                                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                                <span style="font-weight:700;font-size:12px;">Rev {{ $v->version_number }}</span>
                                                <span style="font-size:11px;color:var(--asite-text-muted);">{{ $v->created_at->format('d-M-Y') }}</span>
                                            </div>
                                            @if($v->change_description)
                                                <div style="font-size:11px;color:var(--asite-text-muted);margin-top:4px;">{{ $v->change_description }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        @elseif($this->drawerTab === 'comments')
                            <div style="margin-bottom:16px;">
                                <div style="font-size:12px;font-weight:700;margin-bottom:6px;color:var(--asite-text);">
                                    Add Review Note / Comment
                                </div>
                                <textarea wire:model="newCommentText"
                                          placeholder="Type a comment or instruction for this document..."
                                          rows="3"
                                          class="asite-form-input"></textarea>
                                <div style="display:flex;justify-content:flex-end;margin-top:8px;">
                                    <button type="button" wire:click="postComment" class="asite-btn-primary" style="padding:5px 14px;font-size:11px;">
                                        Post Note
                                    </button>
                                </div>
                            </div>

                            <div style="border-top:1px solid var(--asite-border);padding-top:14px;">
                                <div style="font-size:12px;font-weight:700;margin-bottom:10px;color:var(--asite-text);">
                                    Discussion History ({{ $selectedDoc->comments->count() }})
                                </div>
                                @forelse($selectedDoc->comments as $c)
                                    @php
                                        $cName = $c->user?->name ?? 'User';
                                        $cParts = explode(' ', trim($cName));
                                        $cInitials = strtoupper(substr($cParts[0] ?? 'U', 0, 1) . substr($cParts[1] ?? '', 0, 1));
                                        if (strlen($cInitials) < 2 && strlen($cParts[0]) >= 2) {
                                            $cInitials = strtoupper(substr($cParts[0], 0, 2));
                                        }
                                        $colors = ['#2563eb', '#7c3aed', '#059669', '#d97706', '#dc2626', '#0891b2', '#4f46e5', '#db2777'];
                                        $cColor = $colors[abs(crc32($cName)) % count($colors)];
                                    @endphp
                                    <div class="asite-comment-card {{ $c->is_resolved ? 'is-resolved' : '' }}" style="padding:10px 12px;margin-bottom:8px;">
                                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                                            <div style="display:flex;align-items:center;gap:6px;">
                                                <div class="asite-avatar" style="width:22px;height:22px;font-size:9.5px;background:{{ $cColor }};">
                                                    {{ $cInitials }}
                                                </div>
                                                <span style="font-weight:700;font-size:11.5px;color:var(--asite-text);">{{ $cName }}</span>
                                                <span style="font-size:10px;color:var(--asite-text-muted);">{{ $c->created_at->diffForHumans() }}</span>
                                            </div>
                                            <button type="button"
                                                    wire:click="toggleCommentResolved({{ $c->id }})"
                                                    class="asite-status-pill {{ $c->is_resolved ? 'asite-status-resolved' : 'asite-status-open' }}"
                                                    style="font-size:9.5px;padding:2px 7px;">
                                                {{ $c->is_resolved ? '✓ Resolved' : '● Open' }}
                                            </button>
                                        </div>
                                        <div style="font-size:12px;color:var(--asite-text);line-height:1.45;word-break:break-word;">
                                            {!! nl2br(e($c->comment)) !!}
                                        </div>
                                    </div>
                                @empty
                                    <div style="text-align:center;color:var(--asite-text-muted);font-size:11px;padding:20px;background:var(--asite-surface);border-radius:6px;">
                                        No comments on this document yet. Type above to leave the first note.
                                    </div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════
         5. FOLDER MODALS: NEW FOLDER & RENAME (CLEAN DESIGN - NO ISO)
         ══════════════════════════════════════════════════════════════════════ --}}
    @if($this->showNewFolderModal)
        @php
            $parentFolder = $this->newFolderParentId ? \App\Models\CdeFolder::find($this->newFolderParentId) : null;
        @endphp
        <div class="asite-modal-backdrop" wire:click.self="$set('showNewFolderModal', false)">
            <div class="asite-modal-card">
                {{-- Header --}}
                <div class="asite-modal-header">
                    <div class="asite-modal-title">
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#fef3c7;color:#d97706;flex-shrink:0;">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                <path d="M19.5 21a3 3 0 003-3v-4.5a3 3 0 00-3-3h-15a3 3 0 00-3 3V18a3 3 0 003 3h15zM1.5 10.146V6a3 3 0 013-3h5.379a2.25 2.25 0 011.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 013 3v1.146A4.483 4.483 0 0019.5 9h-15a4.483 4.483 0 00-3 1.146z" />
                            </svg>
                        </span>
                        <div>
                            <div>Create New Folder</div>
                            <div style="font-size:11px;font-weight:400;color:var(--asite-text-muted);">
                                Location: <span style="font-weight:600;color:var(--asite-text);">{{ $parentFolder ? $parentFolder->name : 'Project Root' }}</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('showNewFolderModal', false)" class="asite-icon-btn" title="Close">
                        <svg viewBox="0 0 20 20" width="14" height="14" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="asite-modal-body">
                    <div style="margin-bottom:16px;">
                        <label class="asite-form-label">
                            <span>Folder Name <span style="color:#ef4444;">*</span></span>
                        </label>
                        <input type="text"
                               wire:model="newFolderName"
                               wire:keydown.enter="submitNewFolder"
                               placeholder="e.g. 01.1.6 Environmental Management Plan"
                               class="asite-form-input"
                               autofocus />
                    </div>

                    <div>
                        <label class="asite-form-label">
                            <span>Comments & Purpose</span>
                            <span style="font-weight:400;color:var(--asite-text-muted);font-size:10.5px;">Optional</span>
                        </label>
                        <textarea wire:model="newFolderDescription"
                                  placeholder="Add notes, purpose, or guidelines for files and deliverables in this folder..."
                                  rows="3"
                                  class="asite-form-input"></textarea>
                        <p class="asite-form-hint">
                            Comments added here will be saved as the folder's guidelines and visible to all project collaborators.
                        </p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="asite-modal-footer">
                    <button type="button" wire:click="$set('showNewFolderModal', false)" class="asite-btn-secondary">
                        Cancel
                    </button>
                    <button type="button" wire:click="submitNewFolder" class="asite-btn-primary">
                        <svg viewBox="0 0 20 20" width="13" height="13" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Create Folder</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if($this->showRenameFolderModal)
        <div class="asite-modal-backdrop" wire:click.self="$set('showRenameFolderModal', false)">
            <div class="asite-modal-card">
                {{-- Header --}}
                <div class="asite-modal-header">
                    <div class="asite-modal-title">
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#e0f2fe;color:#0369a1;flex-shrink:0;">
                            <svg viewBox="0 0 20 20" width="16" height="16" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </span>
                        <div>
                            <div>Edit Folder & Guidelines</div>
                            <div style="font-size:11px;font-weight:400;color:var(--asite-text-muted);">
                                Update name, notes, or purpose guidelines
                            </div>
                        </div>
                    </div>
                    <button type="button" wire:click="$set('showRenameFolderModal', false)" class="asite-icon-btn" title="Close">
                        <svg viewBox="0 0 20 20" width="14" height="14" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="asite-modal-body">
                    <div style="margin-bottom:16px;">
                        <label class="asite-form-label">
                            <span>Folder Name <span style="color:#ef4444;">*</span></span>
                        </label>
                        <input type="text"
                               wire:model="renameFolderName"
                               wire:keydown.enter="submitRenameFolder"
                               class="asite-form-input" />
                    </div>

                    <div>
                        <label class="asite-form-label">
                            <span>Comments & Purpose Guidelines</span>
                        </label>
                        <textarea wire:model="renameFolderDescription"
                                  placeholder="Update notes, purpose, or instructions for this folder..."
                                  rows="3"
                                  class="asite-form-input"></textarea>
                        <p class="asite-form-hint">
                            Update instructions or guidelines for files kept in this folder.
                        </p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="asite-modal-footer">
                    <button type="button" wire:click="$set('showRenameFolderModal', false)" class="asite-btn-secondary">
                        Cancel
                    </button>
                    <button type="button" wire:click="submitRenameFolder" class="asite-btn-primary">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    @endif

    <x-filament-actions::modals />

</x-filament-panels::page>