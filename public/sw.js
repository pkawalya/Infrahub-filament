const CACHE_NAME = 'infrahub-pwa-v4';
const CORE_SHELL_ASSETS = [
    '/mobile',
    '/mobile/login',
    '/offline.html',
    '/manifest.json?v=4',
    '/css/mobile.css',
    '/js/mobile-api.js',
    '/js/mobile-ui.js',
    '/js/mobile-actions.js',
    '/js/pwa-manager.js',
    '/logo/infrahub-icon.png',
    '/images/icons/icon-192x192.png?v=4',
    '/images/icons/icon-512x512.png?v=4',
    '/apple-touch-icon.png?v=4',
    '/favicon.png?v=4'
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

// Activate Event - Instantly purge all stale caches (v1, v2, v3)
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        }).then(() => self.clients.claim())
    );
});

// Helper to safely cache 200 OK basic responses
function safeCachePut(request, response) {
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
        event.respondWith(
            new Promise((resolve) => {
                let fetchedFromNetwork = false;

                // Race network fetch with a 450ms timeout fallback to cached UI
                const timeoutId = setTimeout(async () => {
                    if (!fetchedFromNetwork) {
                        const cached = await caches.match(request);
                        if (cached) {
                            console.log('[SW] Serving instant cache fallback for navigation:', url.pathname);
                            resolve(cached);
                        }
                    }
                }, 450);

                fetch(request)
                    .then((networkResponse) => {
                        fetchedFromNetwork = true;
                        clearTimeout(timeoutId);

                        if (networkResponse && networkResponse.status === 200) {
                            safeCachePut(request, networkResponse);
                            resolve(networkResponse);
                        } else {
                            // If 302 redirect or non-200, return network response directly
                            resolve(networkResponse);
                        }
                    })
                    .catch(async () => {
                        clearTimeout(timeoutId);
                        const cached = await caches.match(request) || await caches.match('/mobile') || await caches.match('/offline.html');
                        resolve(cached);
                    });
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
