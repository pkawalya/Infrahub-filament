<style>
    .infrahub-pwa-toast {
        display: none;
        position: fixed;
        bottom: 1rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 99999;
        width: calc(100% - 2rem);
        max-width: 380px;
        padding: 0.5rem 0.75rem;
        border-radius: 9999px;
        background: rgba(15, 23, 42, 0.94);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(99, 102, 241, 0.35);
        color: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    /* Position above bottom mobile navigation bar when .m-nav is active */
    .infrahub-pwa-toast.pwa-has-mobile-nav {
        bottom: 4.5rem !important;
    }
    .pwa-toast-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }
    .pwa-toast-info {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        min-width: 0;
    }
    .pwa-toast-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        padding: 2px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pwa-toast-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 6px;
    }
    .pwa-toast-text-group {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .pwa-toast-title {
        margin: 0;
        font-size: 0.78rem;
        font-weight: 700;
        color: #ffffff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        letter-spacing: 0.01em;
    }
    .pwa-toast-sub {
        margin: 0;
        font-size: 0.68rem;
        color: #94a3b8;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pwa-toast-actions {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        flex-shrink: 0;
    }
    .pwa-btn-install {
        padding: 0.32rem 0.65rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        font-weight: 700;
        font-size: 0.72rem;
        letter-spacing: 0.02em;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.4);
        transition: transform 0.15s ease, background 0.15s ease;
    }
    .pwa-btn-install:hover {
        transform: translateY(-1px);
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
    }
    .pwa-btn-close {
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 0.25rem 0.4rem;
        font-size: 0.95rem;
        line-height: 1;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.15s ease;
    }
    .pwa-btn-close:hover {
        color: #ffffff;
    }
    .ios-pwa-popover {
        display: none;
        margin-top: 0.4rem;
        padding: 0.45rem 0.65rem;
        border-radius: 10px;
        background: rgba(99, 102, 241, 0.15);
        border: 1px solid rgba(99, 102, 241, 0.3);
        font-size: 0.72rem;
        color: #c7d2fe;
        line-height: 1.35;
    }
</style>

<div id="infrahub-pwa-banner" class="infrahub-pwa-toast">
    <div class="pwa-toast-body">
        <div class="pwa-toast-info">
            <div class="pwa-toast-icon">
                <img src="/logo/infrahub-logo-dark.png" alt="InfraHub" onerror="this.src='/favicon.ico';">
            </div>
            <div class="pwa-toast-text-group">
                <h5 class="pwa-toast-title" id="pwa-prompt-heading">Install Field App</h5>
                <p class="pwa-toast-sub" id="pwa-prompt-text">Offline site diaries & fast sync</p>
            </div>
        </div>

        <div class="pwa-toast-actions">
            <button id="pwa-install-btn" onclick="window.pwaManager ? window.pwaManager.triggerInstall() : null" type="button" class="pwa-btn-install">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span id="pwa-btn-label">Install</span>
            </button>

            <button onclick="window.pwaManager ? window.pwaManager.dismiss() : null" type="button" class="pwa-btn-close" aria-label="Close">✕</button>
        </div>
    </div>

    {{-- iOS Step-by-Step Instructions --}}
    <div id="ios-install-instructions" class="ios-pwa-popover">
        📲 <strong>Safari:</strong> Tap <strong>Share (📤)</strong> → <strong>'Add to Home Screen (➕)'</strong>
    </div>
</div>

<script src="/js/pwa-manager.js?v={{ filemtime(public_path('js/pwa-manager.js')) }}"></script>
