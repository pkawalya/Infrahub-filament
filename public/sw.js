/*
 * InfraHub Service Worker
 * Provides offline-first caching for the construction management PWA.
 *
 * Strategies:
 * - App Shell (HTML, CSS, JS): Cache-first with network fallback
 * - API calls: Network-first with cache fallback
 * - Images: Cache-first, stale-while-revalidate
 * - Offline fallback page for navigation requests
 */

const CACHE_VERSION = 'infrahub-v1';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const DYNAMIC_CACHE = `${CACHE_VERSION}-dynamic`;
const API_CACHE = `${CACHE_VERSION}-api`;

// Core app shell - cached on install
const APP_SHELL = [
    '/offline',
    '/css/filament/filament/app.css',
];

// ── Install ────────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => cache.addAll(APP_SHELL))
            .then(() => self.skipWaiting())
    );
});

// ── Activate (clean old caches) ────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then(keys =>
            Promise.all(
                keys.filter(key => key !== STATIC_CACHE && key !== DYNAMIC_CACHE && key !== API_CACHE)
                    .map(key => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

// ── Fetch Strategy ─────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') return;

    // Skip Livewire/WebSocket requests
    if (url.pathname.includes('/livewire/') || url.pathname.includes('/broadcasting/')) return;

    // API calls: Network-first with cache fallback
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(networkFirst(request, API_CACHE));
        return;
    }

    // Navigation requests: Network-first, offline fallback
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then(response => {
                    // Cache successful navigation responses
                    const clone = response.clone();
                    caches.open(DYNAMIC_CACHE).then(cache => cache.put(request, clone));
                    return response;
                })
                .catch(() => caches.match(request).then(cached => cached || caches.match('/offline')))
        );
        return;
    }

    // Static assets: Cache-first
    if (isStaticAsset(url.pathname)) {
        event.respondWith(cacheFirst(request, STATIC_CACHE));
        return;
    }

    // Everything else: Network-first
    event.respondWith(networkFirst(request, DYNAMIC_CACHE));
});

// ── Caching Strategies ─────────────────────────────────────

async function cacheFirst(request, cacheName) {
    const cached = await caches.match(request);
    if (cached) return cached;

    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        return new Response('Offline', { status: 503 });
    }
}

async function networkFirst(request, cacheName) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        const cached = await caches.match(request);
        return cached || new Response(JSON.stringify({ success: false, message: 'Offline' }), {
            status: 503,
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

function isStaticAsset(pathname) {
    return /\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/i.test(pathname);
}

// ── Push Notifications ─────────────────────────────────────
self.addEventListener('push', (event) => {
    if (!event.data) return;

    const data = event.data.json();
    const options = {
        body: data.body || '',
        icon: '/images/icons/icon-192x192.png',
        badge: '/images/icons/icon-72x72.png',
        vibrate: [100, 50, 100],
        data: { url: data.url || '/app' },
        actions: data.actions || [],
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'InfraHub', options)
    );
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || '/app';
    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(windowClients => {
            for (const client of windowClients) {
                if (client.url.includes(url) && 'focus' in client) return client.focus();
            }
            return clients.openWindow(url);
        })
    );
});

// ── Background Sync (for offline form submissions) ─────────
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-attendance') {
        event.waitUntil(syncOfflineData('attendance'));
    }
    if (event.tag === 'sync-diary') {
        event.waitUntil(syncOfflineData('site-diaries'));
    }
    if (event.tag === 'sync-safety') {
        event.waitUntil(syncOfflineData('safety-incidents'));
    }
});

async function syncOfflineData(endpoint) {
    try {
        const cache = await caches.open('infrahub-offline-queue');
        const requests = await cache.keys();

        for (const request of requests) {
            if (request.url.includes(endpoint)) {
                const cachedResponse = await cache.match(request);
                const body = await cachedResponse.json();

                await fetch(`/api/v1/${endpoint}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body),
                });

                await cache.delete(request);
            }
        }
    } catch (error) {
        console.error('Background sync failed:', error);
    }
}
