const CACHE_NAME = 'infrahub-pwa-v5';
const CORE_SHELL_ASSETS = [
    '/mobile',
    '/offline.html',
    '/manifest.json?v=5',
    '/css/mobile.css',
    '/js/mobile-api.js',
    '/js/mobile-ui.js',
    '/js/mobile-actions.js',
    '/js/pwa-manager.js',
    '/logo/infrahub-icon.png',
    '/images/icons/icon-192x192.png?v=5',
    '/images/icons/icon-512x512.png?v=5',
    '/apple-touch-icon.png?v=5',
    '/favicon.png?v=5'
];

// Install Event - Pre-cache minimal essential shell asynchronously
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(CORE_SHELL_ASSETS).catch((err) => {
                console.warn('[SW] Core shell partial pre-cache warning:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// Activate Event - Instantly purge all stale caches (v1, v2, v3, v4)
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        }).then(() => self.clients.claim())
    );
});

// Helper to safely cache 200 OK basic responses (excluding dynamic HTML pages)
function safeCachePut(request, response) {
    const url = new URL(request.url);
    // Do not cache dynamic HTML pages or panel routes
    if (url.pathname.startsWith('/app') || url.pathname.startsWith('/admin') || url.pathname.startsWith('/client') || url.pathname.includes('/login')) {
        return;
    }

    if (response && response.status === 200 && (response.type === 'basic' || response.type === 'cors')) {
        const copy = response.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(request, copy)).catch(() => {});
    }
}

// Fetch Event - Fast Cache-First with Timeout Fallback for Instant UI Loads
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET & API endpoints
    if (request.method !== 'GET' || !url.protocol.startsWith('http') || url.pathname.startsWith('/api/')) {
        return;
    }

    // HTML Navigation Requests (App opening & page switches)
    if (request.mode === 'navigate') {
        // Never serve cached HTML for Filament panels or login pages to avoid stale CSRF tokens (419 Page Expired)
        if (url.pathname.startsWith('/app') || url.pathname.startsWith('/admin') || url.pathname.startsWith('/client') || url.pathname.includes('/login')) {
            event.respondWith(
                fetch(request).catch(async () => {
                    const cached = await caches.match('/offline.html');
                    return cached || fetch(request);
                })
            );
            return;
        }

        event.respondWith(
            fetch(request).then((networkResponse) => {
                if (networkResponse && networkResponse.status === 200) {
                    safeCachePut(request, networkResponse);
                }
                return networkResponse;
            }).catch(async () => {
                const cached = await caches.match(request) || await caches.match('/mobile') || await caches.match('/offline.html');
                return cached;
            })
        );
        return;
    }

    // Static Assets (CSS, JS, Fonts, Images): Cache First -> Background Network Revalidate
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            const fetchPromise = fetch(request).then((networkResponse) => {
                safeCachePut(request, networkResponse);
                return networkResponse;
            }).catch(() => {});

            return cachedResponse || fetchPromise;
        })
    );
});

// Sync Event (Background Sync)
self.addEventListener('sync', (event) => {
    if (event.tag === 'infrahub-offline-sync') {
        event.waitUntil(
            self.clients.matchAll().then((clients) => {
                clients.forEach((client) => {
                    client.postMessage({ type: 'TRIGGER_SYNC' });
                });
            })
        );
    }
});
