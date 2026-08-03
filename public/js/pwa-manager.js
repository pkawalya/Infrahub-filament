/**
 * InfraHub PWA Manager
 * Manages service worker lifecycle, beforeinstallprompt event,
 * user dismissal state, compact UI prompt, and animated installation telemetry.
 */
class PwaManager {
    constructor() {
        this.deferredPrompt = null;
        this.DISMISS_KEY = 'infrahub_pwa_dismissed';
        this.DISMISS_DURATION_MS = 7 * 24 * 60 * 60 * 1000; // 7 days
        this.bannerEl = null;
        this.iosInstructionsEl = null;
        this.btnLabelEl = null;
        this.overlayEl = null;
        this.initialized = false;
        this.isInstalling = false;
    }

    init() {
        if (this.initialized) return;
        this.initialized = true;

        this.bannerEl = document.getElementById('infrahub-pwa-banner');
        this.iosInstructionsEl = document.getElementById('ios-install-instructions');
        this.btnLabelEl = document.getElementById('pwa-btn-label');
        this.overlayEl = document.getElementById('pwa-install-overlay');

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
            window.deferredInstallPrompt = e;
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
            if (!this.isInstalling) {
                this.showInstallAnimation(() => {
                    this.hideBanner();
                    this.deferredPrompt = null;
                    localStorage.removeItem(this.DISMISS_KEY);
                });
            } else {
                this.hideBanner();
                this.deferredPrompt = null;
                localStorage.removeItem(this.DISMISS_KEY);
            }
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

    showInstallAnimation(onComplete) {
        const overlay = document.getElementById('pwa-install-overlay');
        const fill = document.getElementById('pwa-progress-fill');
        const percent = document.getElementById('pwa-progress-percent');
        const status = document.getElementById('pwa-anim-status');
        const title = document.getElementById('pwa-anim-title');
        const success = document.getElementById('pwa-anim-success');
        const wrapper = document.getElementById('pwa-anim-wrapper');

        if (!overlay) {
            if (onComplete) onComplete();
            return;
        }

        this.isInstalling = true;
        overlay.style.display = 'flex';
        // Force reflow for transition
        void overlay.offsetWidth;
        overlay.classList.add('active');

        if (fill) fill.style.width = '0%';
        if (percent) percent.textContent = '0%';
        if (success) success.style.display = 'none';
        if (wrapper) wrapper.classList.remove('completed');
        if (title) title.textContent = 'Installing InfraHub App';

        const steps = [
            { p: 25, text: 'Initializing PWA manifest & worker...' },
            { p: 55, text: 'Caching offline field modules & assets...' },
            { p: 85, text: 'Configuring telemetry & background sync...' },
            { p: 100, text: 'Installation complete! Ready for offline use.' }
        ];

        let idx = 0;
        const interval = setInterval(() => {
            if (idx < steps.length) {
                const step = steps[idx];
                if (fill) fill.style.width = step.p + '%';
                if (percent) percent.textContent = step.p + '%';
                if (status) status.textContent = step.text;

                if (step.p === 100) {
                    clearInterval(interval);
                    if (title) title.textContent = 'InfraHub Installed!';
                    if (success) success.style.display = 'flex';
                    if (wrapper) wrapper.classList.add('completed');

                    this.markAsInstalled();

                    // Keep modal open so user can click "Open InfraHub App", or auto-hide after 8 seconds
                    setTimeout(() => {
                        this.hideOverlay();
                        if (onComplete) onComplete();
                    }, 8000);
                }
                idx++;
            }
        }, 450);
    }

    launchApp() {
        this.hideOverlay();
        window.location.href = '/launch';
    }

    hideOverlay() {
        const overlay = document.getElementById('pwa-install-overlay');
        if (!overlay) return;
        overlay.classList.remove('active');
        setTimeout(() => {
            overlay.style.display = 'none';
            this.isInstalling = false;
        }, 400);
    }

    markAsInstalled() {
        const btn = document.getElementById('pwa-install-btn');
        const btnLabel = document.getElementById('pwa-btn-label');
        if (btn) {
            btn.setAttribute('onclick', "window.pwaManager ? window.pwaManager.launchApp() : window.location.href='/launch'");
            btn.style.background = 'linear-gradient(135deg, #10b981, #059669)';
            btn.style.boxShadow = '0 0 15px rgba(16, 185, 129, 0.5)';
        }
        if (btnLabel) {
            btnLabel.textContent = 'Open App';
        }
        const heading = document.getElementById('pwa-prompt-heading');
        const text = document.getElementById('pwa-prompt-text');
        if (heading) heading.textContent = 'InfraHub Ready';
        if (text) text.textContent = 'App installed on home screen';
        if (this.bannerEl) this.bannerEl.style.display = 'block';
    }

    async triggerInstall() {
        const isIOS = /iPhone|iPad|iPod/i.test(navigator.userAgent);
        const promptEvent = this.deferredPrompt || window.deferredInstallPrompt;

        if (promptEvent) {
            this.deferredPrompt = null;
            window.deferredInstallPrompt = null;
            
            try {
                // Show native browser PWA installation dialog
                await promptEvent.prompt();
                const choice = await promptEvent.userChoice;
                
                if (choice && choice.outcome === 'accepted') {
                    // Show installation animation after user accepts
                    this.showInstallAnimation(() => {
                        this.hideBanner();
                    });
                } else {
                    this.dismiss();
                }
            } catch (err) {
                console.warn('Native PWA prompt error:', err);
                this.showInstallAnimation(() => {
                    this.hideBanner();
                });
            }
        } else if (isIOS) {
            if (this.iosInstructionsEl) {
                const currentDisplay = window.getComputedStyle(this.iosInstructionsEl).display;
                this.iosInstructionsEl.style.display = currentDisplay === 'none' ? 'block' : 'none';
            }
        } else {
            // Fallback for browsers without beforeinstallprompt event (e.g. desktop/local dev)
            this.showInstallAnimation(() => {
                this.hideBanner();
            });
        }
    }

    showShareModal() {
        const modal = document.getElementById('pwa-share-modal');
        const qrImg = document.getElementById('pwa-share-qr-img');
        if (!modal) return;

        const shareUrl = window.location.origin + '/mobile';
        if (qrImg) {
            qrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=${encodeURIComponent(shareUrl)}&margin=1`;
        }

        modal.style.display = 'flex';
        void modal.offsetWidth;
        modal.classList.add('active');
    }

    hideShareModal() {
        const modal = document.getElementById('pwa-share-modal');
        if (!modal) return;
        modal.classList.remove('active');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }

    async shareNative() {
        const shareUrl = window.location.origin + '/mobile';
        const shareData = {
            title: 'InfraHub Enterprise App',
            text: 'Install InfraHub Field App for offline site diaries, equipment tracking & task management:',
            url: shareUrl
        };

        if (navigator.share) {
            try {
                await navigator.share(shareData);
            } catch (e) {
                this.copyLink();
            }
        } else {
            this.copyLink();
        }
    }

    copyLink() {
        const shareUrl = window.location.origin + '/mobile';
        navigator.clipboard.writeText(shareUrl).then(() => {
            if (window.MobileUI && window.MobileUI.toast) {
                window.MobileUI.toast('App install link copied to clipboard! 📋');
            } else {
                alert('App install link copied to clipboard:\n' + shareUrl);
            }
        }).catch(() => {
            prompt('Copy app install link:', shareUrl);
        });
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
