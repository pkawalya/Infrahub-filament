/**
 * InfraHub PWA Manager
 * Manages service worker lifecycle, beforeinstallprompt event,
 * user dismissal state, and compact UI prompt rendering.
 */
class PwaManager {
    constructor() {
        this.deferredPrompt = null;
        this.DISMISS_KEY = 'infrahub_pwa_dismissed';
        this.DISMISS_DURATION_MS = 7 * 24 * 60 * 60 * 1000; // 7 days
        this.bannerEl = null;
        this.iosInstructionsEl = null;
        this.btnLabelEl = null;
        this.initialized = false;
    }

    init() {
        if (this.initialized) return;
        this.initialized = true;

        this.bannerEl = document.getElementById('infrahub-pwa-banner');
        this.iosInstructionsEl = document.getElementById('ios-install-instructions');
        this.btnLabelEl = document.getElementById('pwa-btn-label');

        if (!this.bannerEl) return;

        if (this.isStandalone() || this.isDismissed()) {
            return;
        }

        this.bindEvents();
        this.checkMobileNavSpacing();
    }

    isStandalone() {
        return window.matchMedia('(display-mode: standalone)').matches || 
               window.navigator.standalone === true;
    }

    isDismissed() {
        const dismissedAt = localStorage.getItem(this.DISMISS_KEY);
        if (!dismissedAt) return false;
        const timePassed = Date.now() - parseInt(dismissedAt, 10);
        return timePassed < this.DISMISS_DURATION_MS;
    }

    bindEvents() {
        // Catch native PWA prompt (Android / Chrome)
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            this.showBanner();
        });

        // Auto-detect mobile and prompt after short delay
        const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth <= 800;
        if (isMobile) {
            setTimeout(() => {
                this.showBanner();
            }, 1200);
        }

        window.addEventListener('appinstalled', () => {
            this.hideBanner();
            this.deferredPrompt = null;
            localStorage.removeItem(this.DISMISS_KEY);
        });
    }

    checkMobileNavSpacing() {
        const hasMobileNav = document.querySelector('.m-nav') !== null;
        if (hasMobileNav && this.bannerEl) {
            this.bannerEl.classList.add('pwa-has-mobile-nav');
        } else if (this.bannerEl) {
            this.bannerEl.classList.remove('pwa-has-mobile-nav');
        }
    }

    showBanner() {
        if (!this.bannerEl || this.isStandalone()) return;

        this.checkMobileNavSpacing();
        const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
        
        if (isIOS && this.btnLabelEl) {
            this.btnLabelEl.textContent = 'How to Install';
        }

        this.bannerEl.style.display = 'block';
    }

    hideBanner() {
        if (this.bannerEl) {
            this.bannerEl.style.display = 'none';
        }
    }

    dismiss() {
        this.hideBanner();
        localStorage.setItem(this.DISMISS_KEY, Date.now().toString());
    }

    async triggerInstall() {
        const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);

        if (this.deferredPrompt) {
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            if (outcome === 'accepted') {
                this.hideBanner();
            }
            this.deferredPrompt = null;
        } else if (isIOS) {
            if (this.iosInstructionsEl) {
                const currentDisplay = window.getComputedStyle(this.iosInstructionsEl).display;
                this.iosInstructionsEl.style.display = currentDisplay === 'none' ? 'block' : 'none';
            }
        } else {
            alert('To install InfraHub Field App:\n\n1. Open browser menu (⋮ or Share icon).\n2. Tap "Add to Home screen" or "Install App".');
        }
    }
}

// Global instance helper
window.pwaManager = new PwaManager();
document.addEventListener('DOMContentLoaded', () => {
    window.pwaManager.init();
});
if (document.readyState === 'interactive' || document.readyState === 'complete') {
    window.pwaManager.init();
}
