/*
 * InfraHub Service Worker v3 — Universal Offline
 * Provides aggressive caching for ALL Filament pages + Background Sync.
 *
 * Strategies:
 * - App Shell (CSS, JS, icons): Cache-first
 * - Filament pages: Network-first, cache fallback (stale but viewable)
 * - API calls: Network-first, cache fallback
 * - Static assets: Cache-first, stale-while-revalidate
 * - Offline fallback page for uncached navigation requests
 * - Background Sync for offline form submissions via IndexedDB
 */

const CACHE_VERSION = 'infrahub-v1.0.0';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const DYNAMIC_CACHE = `${CACHE_VERSION}-pages`;
const API_CACHE = `${CACHE_VERSION}-api`;

// Core app shell - cached on install
const APP_SHELL = [
    '/offline',
    '/css/offline.css',
    '/js/offline-db.js',
    '/js/offline-ui.js',
    '/css/filament/filament/app.css',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-512x512.png',
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
                keys.filter(key => !key.startsWith(CACHE_VERSION))
                    .map(key => caches.delete(key))
            )
        ).then(() => self.clients.claim())
    );
});

// ── Fetch Strategy ─────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests (POST/PUT/DELETE pass through to offline-ui.js interceptor)
    if (request.method !== 'GET') return;

    // Skip Livewire, WebSocket, Vite HMR
    if (url.pathname.includes('/livewire/') ||
        url.pathname.includes('/broadcasting/') ||
        url.pathname.includes('/__vite') ||
        url.pathname.includes('/@vite')) return;

    // API calls: Network-first with cache fallback
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(networkFirst(request, API_CACHE, 5000));
        return;
    }

    // Navigation requests (HTML pages): Network-first with aggressive caching
    if (request.mode === 'navigate' || request.headers.get('Accept')?.includes('text/html')) {
        event.respondWith(
            networkFirstWithTimeout(request, DYNAMIC_CACHE, 8000)
        );
        return;
    }

    // Static assets: Cache-first
    if (isStaticAsset(url.pathname)) {
        event.respondWith(cacheFirst(request, STATIC_CACHE));
        return;
    }

    // Everything else: Network-first with short timeout
    event.respondWith(networkFirst(request, DYNAMIC_CACHE, 5000));
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

async function networkFirst(request, cacheName, timeoutMs = 5000) {
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), timeoutMs);

        const response = await fetch(request, { signal: controller.signal });
        clearTimeout(timeoutId);

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

/**
 * Network-first with timeout + offline fallback page.
 * If the network is too slow or down, serves the cached version.
 * If no cache exists, serves the offline page.
 */
async function networkFirstWithTimeout(request, cacheName, timeoutMs) {
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), timeoutMs);

        const response = await fetch(request, { signal: controller.signal });
        clearTimeout(timeoutId);

        if (response.ok) {
            // Cache every successful page load
            const cache = await caches.open(cacheName);
            cache.put(request, response.clone());
        }
        return response;
    } catch {
        // Try cached version first
        const cached = await caches.match(request);
        if (cached) {
            return cached; // Serve stale cached page
        }
        // No cached version — show offline page
        const offlinePage = await caches.match('/offline');
        return offlinePage || new Response('<h1>Offline</h1><p>No cached version available.</p>', {
            status: 503,
            headers: { 'Content-Type': 'text/html' }
        });
    }
}

function isStaticAsset(pathname) {
    return /\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot|webp|avif)$/i.test(pathname);
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

// ── Background Sync ────────────────────────────────────────
self.addEventListener('sync', (event) => {
    if (event.tag === 'infrahub-sync') {
        event.waitUntil(syncAllOfflineData());
    }
    if (event.tag === 'sync-attendance') {
        event.waitUntil(syncStore('attendance', '/api/v1/offline-sync/attendance'));
    }
    if (event.tag === 'sync-diary') {
        event.waitUntil(syncStore('site-diaries', '/api/v1/offline-sync/site-diaries'));
    }
    if (event.tag === 'sync-safety') {
        event.waitUntil(syncStore('safety-incidents', '/api/v1/offline-sync/safety-incidents'));
    }
});

// ── IndexedDB helpers (SW context) ─────────────────────────
const SW_DB_NAME = 'infrahub-offline';
const SW_DB_VERSION = 2;
const SW_STORES = ['form-queue', 'site-diaries', 'attendance', 'safety-incidents'];

function openDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open(SW_DB_NAME, SW_DB_VERSION);
        req.onupgradeneeded = (e) => {
            const db = e.target.result;
            SW_STORES.forEach(name => {
                if (!db.objectStoreNames.contains(name)) {
                    const store = db.createObjectStore(name, { keyPath: '_offlineId' });
                    if (name === 'form-queue') {
                        store.createIndex('resource', '_resource', { unique: false });
                        store.createIndex('createdAt', '_createdAt', { unique: false });
                    }
                }
            });
        };
        req.onsuccess = (e) => resolve(e.target.result);
        req.onerror = (e) => reject(e.target.error);
    });
}

function getAllFromStore(db, storeName) {
    return new Promise((resolve, reject) => {
        const tx = db.transaction(storeName, 'readonly');
        const req = tx.objectStore(storeName).getAll();
        req.onsuccess = () => resolve(req.result);
        req.onerror = (e) => reject(e.target.error);
    });
}

function deleteFromStore(db, storeName, key) {
    return new Promise((resolve, reject) => {
        const tx = db.transaction(storeName, 'readwrite');
        const req = tx.objectStore(storeName).delete(key);
        req.onsuccess = () => resolve();
        req.onerror = (e) => reject(e.target.error);
    });
}

async function syncStore(storeName, endpoint) {
    try {
        const db = await openDB();
        const records = await getAllFromStore(db, storeName);

        for (const record of records) {
            const payload = { ...record };
            delete payload._offlineId;
            delete payload._createdAt;
            delete payload._synced;

            try {
                const resp = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload),
                });
                if (resp.ok) {
                    await deleteFromStore(db, storeName, record._offlineId);
                }
            } catch { /* retry next time */ }
        }
    } catch (e) {
        console.error(`SW sync: failed for ${storeName}`, e);
    }
}

async function syncGenericQueue() {
    try {
        const db = await openDB();
        const records = await getAllFromStore(db, 'form-queue');

        for (const record of records) {
            try {
                const resp = await fetch('/api/v1/offline-sync/generic', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        resource: record._resource,
                        action: record._action,
                        record_id: record._recordId,
                        data: record.data,
                    }),
                });
                if (resp.ok) {
                    await deleteFromStore(db, 'form-queue', record._offlineId);
                }
            } catch { /* retry next time */ }
        }
    } catch (e) {
        console.error('SW sync: generic queue failed', e);
    }
}

async function syncAllOfflineData() {
    // Sync generic form queue first
    await syncGenericQueue();

    // Then legacy stores
    const endpoints = {
        'site-diaries': '/api/v1/offline-sync/site-diaries',
        'attendance': '/api/v1/offline-sync/attendance',
        'safety-incidents': '/api/v1/offline-sync/safety-incidents',
    };
    for (const [store, endpoint] of Object.entries(endpoints)) {
        await syncStore(store, endpoint);
    }

    // Notify clients
    const allClients = await self.clients.matchAll();
    allClients.forEach(client => {
        client.postMessage({ type: 'SYNC_COMPLETE' });
    });
}

// ── Listen for messages from the main thread ───────────────
self.addEventListener('message', (event) => {
    if (event.data === 'SKIP_WAITING') self.skipWaiting();
    if (event.data === 'TRIGGER_SYNC') syncAllOfflineData();
});
