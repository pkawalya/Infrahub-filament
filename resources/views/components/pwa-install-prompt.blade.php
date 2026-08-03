<style>
    .infrahub-pwa-toast {
        display: none;
        position: fixed;
        bottom: 1rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 99999;
        width: calc(100% - 2rem);
        max-width: 420px;
        padding: 0.5rem 0.75rem;
        border-radius: 9999px;
        background: rgba(15, 23, 42, 0.94);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(99, 102, 241, 0.35);
        color: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
        font-family: 'Outfit', system-ui, -apple-system, sans-serif;
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
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        padding: 2px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
    }
    .pwa-toast-icon img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 8px;
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
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        font-weight: 800;
        font-size: 0.74rem;
        letter-spacing: 0.02em;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        box-shadow: 0 2px 10px rgba(99, 102, 241, 0.45);
        transition: transform 0.15s ease, background 0.15s ease;
    }
    .pwa-btn-install:hover {
        transform: translateY(-1px) scale(1.02);
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
    }
    .pwa-btn-share {
        padding: 0.35rem 0.6rem;
        border-radius: 9999px;
        background: rgba(30, 41, 59, 0.8);
        border: 1px solid rgba(99, 102, 241, 0.4);
        color: #c7d2fe;
        font-weight: 700;
        font-size: 0.72rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: background 0.15s ease;
    }
    .pwa-btn-share:hover {
        background: rgba(51, 65, 85, 0.9);
        color: #ffffff;
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

    /* ── Fullscreen PWA Installation & Share Overlay ── */
    .pwa-install-overlay {
        position: fixed;
        inset: 0;
        z-index: 999999;
        background: rgba(3, 7, 18, 0.92);
        backdrop-filter: blur(28px) saturate(1.4);
        -webkit-backdrop-filter: blur(28px) saturate(1.4);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .pwa-install-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }
    .pwa-install-card {
        width: 100%;
        max-width: 360px;
        background: rgba(15, 23, 42, 0.85);
        border: 1px solid rgba(99, 102, 241, 0.3);
        border-radius: 24px;
        padding: 2.25rem 1.75rem;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6), 0 0 50px rgba(99, 102, 241, 0.2);
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    .pwa-anim-icon-wrapper {
        position: relative;
        width: 86px;
        height: 86px;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pwa-anim-spinner {
        position: absolute;
        inset: -6px;
        border-radius: 24px;
        background: conic-gradient(from 0deg, #6366f1, #8b5cf6, #10b981, transparent 75%);
        animation: spinGlow 1.8s linear infinite;
        opacity: 0.9;
    }
    .pwa-anim-icon-wrapper.completed .pwa-anim-spinner {
        animation: none;
        background: #10b981;
        box-shadow: 0 0 25px #10b981;
    }
    @keyframes spinGlow {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .pwa-anim-icon {
        position: relative;
        z-index: 2;
        width: 76px;
        height: 76px;
        border-radius: 20px;
        background: #030712;
        padding: 6px;
        object-fit: contain;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
    }
    .pwa-anim-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #ffffff;
        margin: 0 0 0.35rem;
    }
    .pwa-anim-status {
        font-size: 0.78rem;
        color: #94a3b8;
        margin: 0 0 1.25rem;
        min-height: 1.2em;
    }
    .pwa-progress-track {
        width: 100%;
        height: 8px;
        background: rgba(30, 41, 59, 0.8);
        border-radius: 9999px;
        overflow: hidden;
        margin-bottom: 0.5rem;
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .pwa-progress-fill {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #6366f1, #8b5cf6, #10b981);
        border-radius: 9999px;
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 12px rgba(99, 102, 241, 0.6);
    }
    .pwa-progress-percent {
        font-size: 0.72rem;
        font-weight: 800;
        color: #818cf8;
        letter-spacing: 0.05em;
    }
    .pwa-anim-success {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 1rem;
        border-radius: 9999px;
        background: rgba(16, 185, 129, 0.18);
        border: 1px solid rgba(16, 185, 129, 0.4);
        color: #34d399;
        font-weight: 800;
        font-size: 0.8rem;
        margin-top: 0.85rem;
        animation: popIn 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes popIn {
        0% { transform: scale(0.6); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

{{-- Toast Installation Banner Prompt --}}
<div id="infrahub-pwa-banner" class="infrahub-pwa-toast">
    <div class="pwa-toast-body">
        <div class="pwa-toast-info">
            <div class="pwa-toast-icon">
                <img src="/images/icons/icon-192x192.png" alt="InfraHub" onerror="this.src='/favicon.ico';">
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

            <button type="button" onclick="window.pwaManager ? window.pwaManager.showShareModal() : null" class="pwa-btn-share" title="Share via QR Code / AirDrop">
                <span>📲 QR</span>
            </button>

            <button onclick="window.pwaManager ? window.pwaManager.dismiss() : null" type="button" class="pwa-btn-close" aria-label="Close">✕</button>
        </div>
    </div>

    {{-- iOS Step-by-Step Instructions --}}
    <div id="ios-install-instructions" class="ios-pwa-popover">
        📲 <strong>Safari:</strong> Tap <strong>Share (📤)</strong> → <strong>'Add to Home Screen (➕)'</strong>
    </div>
</div>

{{-- Fullscreen Animated PWA Installation Overlay --}}
<div id="pwa-install-overlay" class="pwa-install-overlay" style="display:none;">
    <div class="pwa-install-card">
        <div class="pwa-anim-icon-wrapper" id="pwa-anim-wrapper">
            <div class="pwa-anim-spinner"></div>
            <img src="/images/icons/icon-192x192.png" alt="InfraHub" class="pwa-anim-icon">
        </div>

        <h3 id="pwa-anim-title" class="pwa-anim-title">Installing InfraHub App</h3>
        <p id="pwa-anim-status" class="pwa-anim-status">Configuring offline telemetry & service worker...</p>

        <div class="pwa-progress-track">
            <div id="pwa-progress-fill" class="pwa-progress-fill"></div>
        </div>
        <div id="pwa-progress-percent" class="pwa-progress-percent">0%</div>

        <div id="pwa-anim-success" class="pwa-anim-success" style="display:none;">
            <span>✓ Field App Installed & Ready</span>
        </div>
    </div>
</div>

{{-- PWA Share & Quick Install Modal (Zero-Link Camera QR Code & AirDrop) --}}
<div id="pwa-share-modal" class="pwa-install-overlay" style="display:none;">
    <div class="pwa-install-card" style="max-width:380px;padding:1.75rem 1.5rem;">
        <div style="display:flex;align-items:center;justify-space-between;width:100%;margin-bottom:1rem;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <div style="width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg, #6366f1, #8b5cf6);display:flex;align-items:center;justify-content:center;">
                    <img src="/images/icons/icon-192x192.png" style="width:20px;height:20px;object-fit:contain;">
                </div>
                <h4 style="margin:0;font-size:0.95rem;font-weight:800;color:#fff;">Share InfraHub App</h4>
            </div>
            <button type="button" onclick="window.pwaManager ? window.pwaManager.hideShareModal() : null" style="background:none;border:none;color:#94a3b8;font-size:1.1rem;cursor:pointer;padding:0.2rem;">✕</button>
        </div>

        {{-- QR Code Container --}}
        <div style="background:#ffffff;padding:0.85rem;border-radius:18px;box-shadow:0 10px 25px rgba(0,0,0,0.5);margin-bottom:1rem;position:relative;display:inline-block;">
            <img id="pwa-share-qr-img" src="" alt="Scan to Install PWA" style="width:200px;height:200px;display:block;border-radius:12px;">
            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:38px;height:38px;background:#0f172a;border-radius:10px;padding:3px;box-shadow:0 4px 12px rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;">
                <img src="/images/icons/icon-192x192.png" style="width:100%;height:100%;object-fit:contain;border-radius:6px;">
            </div>
        </div>

        <p style="font-size:0.75rem;color:#94a3b8;margin:0 0 1.25rem;line-height:1.4;">
            Point phone camera at this QR code to open & install <strong>InfraHub Field App</strong> instantly without typing a link.
        </p>

        {{-- Action Buttons --}}
        <div style="display:flex;flex-direction:column;gap:0.5rem;width:100%;">
            <button type="button" onclick="window.pwaManager ? window.pwaManager.shareNative() : null" class="pwa-btn-install" style="justify-content:center;padding:0.6rem 1rem;font-size:0.8rem;width:100%;">
                <span>📤 Share via AirDrop / Nearby / WhatsApp</span>
            </button>
            
            <button type="button" onclick="window.pwaManager ? window.pwaManager.copyLink() : null" style="background:rgba(30,41,59,0.9);border:1px solid rgba(99,102,241,0.3);color:#e2e8f0;padding:0.55rem 1rem;border-radius:9999px;font-size:0.78rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:0.4rem;transition:all 0.15s ease;">
                <span>📋 Copy Install Link</span>
            </button>
        </div>
    </div>
</div>

<script src="/js/pwa-manager.js?v={{ filemtime(public_path('js/pwa-manager.js')) }}"></script>
