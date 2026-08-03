/**
 * InfraHub Mobile PWA — Native View Transition Router
 * Provides zero-reload push/pop slide transitions strictly for /mobile/* views.
 */
const MobileRouter = (() => {
    'use strict';

    let isNavigating = false;
    const historyStack = [window.location.pathname];

    function init() {
        // Intercept all link clicks inside /mobile/*
        document.addEventListener('click', (e) => {
            const anchor = e.target.closest('a');
            if (!anchor) return;

            const href = anchor.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || anchor.hasAttribute('download') || anchor.target === '_blank') {
                return;
            }

            // Only transition within /mobile/* paths
            if (href.startsWith('/mobile')) {
                e.preventDefault();
                navigate(href, 'push');
            }
        });

        // Touch-start prefetching for 0ms transition start time
        document.addEventListener('touchstart', (e) => {
            const anchor = e.target.closest('a');
            if (!anchor) return;
            const href = anchor.getAttribute('href');
            if (href && href.startsWith('/mobile') && !href.startsWith('#')) {
                prefetch(href);
            }
        }, { passive: true });

        // Handle back/forward gestures
        window.addEventListener('popstate', () => {
            const currentPath = window.location.pathname;
            const prevPath = historyStack[historyStack.length - 2];
            const direction = (prevPath === currentPath) ? 'pop' : 'push';

            if (direction === 'pop') historyStack.pop();
            else historyStack.push(currentPath);

            navigate(currentPath, direction, false);
        });
    }

    const prefetchCache = new Map();

    async function prefetch(url) {
        if (prefetchCache.has(url) || isNavigating) return;
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (res.ok) {
                const html = await res.text();
                prefetchCache.set(url, html);
            }
        } catch { }
    }

    async function navigate(url, direction = 'push', pushState = true) {
        if (isNavigating) return;
        if (window.location.pathname === url && pushState) return;

        isNavigating = true;
        
        // Trigger haptic vibration on transition
        if (navigator.vibrate) navigator.vibrate(8);

        const container = document.getElementById('mobile-view-container') || document.body;
        
        // Add loading indicator bar
        let progressBar = document.getElementById('m-route-progress');
        if (!progressBar) {
            progressBar = document.createElement('div');
            progressBar.id = 'm-route-progress';
            progressBar.className = 'm-route-progress-bar';
            document.body.appendChild(progressBar);
        }
        progressBar.style.width = '30%';
        progressBar.style.opacity = '1';

        try {
            let html = prefetchCache.get(url);
            if (!html) {
                const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                html = await res.text();
            }

            progressBar.style.width = '80%';

            // Parse response DOM
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.getElementById('mobile-view-container');
            const newTitle = doc.querySelector('title')?.textContent;

            if (!newContent) {
                window.location.href = url;
                return;
            }

            // Perform transition animation
            const oldContent = container.cloneNode(true);
            oldContent.id = 'mobile-view-outgoing';
            oldContent.style.position = 'fixed';
            oldContent.style.top = container.offsetTop + 'px';
            oldContent.style.left = '0';
            oldContent.style.width = '100%';
            oldContent.style.zIndex = '10';

            const animOut = direction === 'push' ? 'm-slide-out-left' : 'm-slide-out-right';
            const animIn = direction === 'push' ? 'm-slide-in-right' : 'm-slide-in-left';

            oldContent.classList.add(animOut);
            document.body.appendChild(oldContent);

            // Replace current container inner HTML
            container.innerHTML = newContent.innerHTML;
            container.classList.add(animIn);

            if (newTitle) document.title = newTitle;
            if (pushState) {
                history.pushState(null, '', url);
                historyStack.push(url);
            }

            // Scroll view back to top smooth
            window.scrollTo({ top: 0, behavior: 'instant' });

            // Re-run inline scripts from new content
            const scripts = doc.querySelectorAll('#mobile-view-container script, @stack("scripts") script');
            scripts.forEach(s => {
                const newScript = document.createElement('script');
                if (s.src) newScript.src = s.src;
                else newScript.textContent = s.textContent;
                document.body.appendChild(newScript).parentNode.removeChild(newScript);
            });

            // Re-initialize Mobile UI bindings
            if (window.MobileUI && typeof MobileUI.init === 'function') {
                MobileUI.init();
            }

            // Clean up animation classes
            setTimeout(() => {
                oldContent.remove();
                container.classList.remove(animIn);
                progressBar.style.width = '100%';
                setTimeout(() => { progressBar.style.opacity = '0'; progressBar.style.width = '0%'; }, 200);
                isNavigating = false;
            }, 250);

        } catch (err) {
            console.warn('[MobileRouter] Transition failed, fallback to hard load:', err);
            window.location.href = url;
            isNavigating = false;
        }
    }

    return {
        init,
        navigate
    };
})();

document.addEventListener('DOMContentLoaded', () => {
    MobileRouter.init();
});
