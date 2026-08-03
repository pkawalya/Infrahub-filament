const CACHE_NAME = 'infrahub-pwa-v2';
const STATIC_ASSETS = [
    '/',
    '/admin',
    '/offline.html',
    '/manifest.json',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png'
];

// Install Event - Pre-cache critical core shell
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(STATIC_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// Activate Event - Clean up stale caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch Event - Stale-While-Revalidate for static, Network-First for Navigation
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignore non-GET requests or browser extension requests
    if (request.method !== 'GET' || !url.protocol.startsWith('http')) {
        return;
    }

    // API calls: Network only (handled by client IndexedDB outbox on failure)
    if (url.pathname.startsWith('/api/')) {
        return;
    }

    // Navigation requests (HTML pages)
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((networkResponse) => {
                    // Update dynamic cache with fresh HTML
                    const resClone = networkResponse.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(request, resClone));
                    return networkResponse;
                })
                .catch(async () => {
                    // Fallback to cache or offline page
                    const cachedResponse = await caches.match(request);
                    if (cachedResponse) {
                        return cachedResponse;
                    }
                    return caches.match('/offline.html');
                })
        );
        return;
    }

    // Static Assets (JS, CSS, Images, Fonts) -> Cache First, fallback to Network
    event.respondWith(
        caches.match(request).then((cachedResponse) => {
            if (cachedResponse) {
                // Fetch in background to update cache
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
