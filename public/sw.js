const CACHE_NAME = 'infrahub-pwa-v3';
const STATIC_ASSETS = [
    '/',
    '/launch',
    '/mobile',
    '/app',
    '/offline.html',
    '/manifest.json?v=3',
    '/images/icons/icon-192x192.png?v=3',
    '/images/icons/icon-512x512.png?v=3',
    '/apple-touch-icon.png?v=3',
    '/favicon.png?v=3'
];

// Install Event - Pre-cache critical core shell
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// Activate Event - Instantly purge all stale caches (v1, v2)
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch Event - Network First for manifest & HTML, Stale-While-Revalidate for static
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignore non-GET requests or browser extension requests
    if (request.method !== 'GET' || !url.protocol.startsWith('http')) {
        return;
    }

    // API calls: Network only
    if (url.pathname.startsWith('/api/')) {
        return;
    }

    // Network-First for Manifest and Launch routes to ensure icon freshness
    if (url.pathname.includes('manifest.json') || url.pathname === '/launch') {
        event.respondWith(
            fetch(request).then((networkResponse) => {
                const resClone = networkResponse.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(request, resClone));
                return networkResponse;
            }).catch(() => caches.match(request))
        );
        return;
    }

    // Navigation requests (HTML pages)
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    const resClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, resClone));
                    return networkResponse;
                })
                .catch(async () => {
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    return caches.match('/offline.html');
                })
        );
        return;
    }

    // Static Assets
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            if (cachedResponse) {
                fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200) {
                        caches.open(CACHE_NAME).then((cache) => cache.put(request, networkResponse));
                    }
                }).catch(() => {});
                return cachedResponse;
            }

            return fetch(request).then((networkResponse) => {
                if (!networkResponse || networkResponse.status !== 200 || networkResponse.type !== 'basic') {
                    return networkResponse;
                }
                const resClone = networkResponse.clone();
                caches.open(CACHE_NAME).then((cache) => cache.put(request, resClone));
                return networkResponse;
            });
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
