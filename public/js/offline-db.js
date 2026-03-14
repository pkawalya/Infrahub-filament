/**
 * InfraHub Offline Database v2 (IndexedDB)
 * Universal offline store for ALL Filament resources.
 *
 * Stores:
 * - form-queue: Generic queue for ANY Filament form submission
 * - site-diaries, attendance, safety-incidents: Legacy dedicated stores
 *
 * Each record gets a unique ID + timestamp for conflict-free syncing.
 */
const InfraDB = (() => {
    const DB_NAME = 'infrahub-offline';
    const DB_VERSION = 2; // Bumped for schema upgrade
    const STORES = ['form-queue', 'site-diaries', 'attendance', 'safety-incidents'];

    let _db = null;

    function open() {
        if (_db) return Promise.resolve(_db);
        return new Promise((resolve, reject) => {
            const req = indexedDB.open(DB_NAME, DB_VERSION);
            req.onupgradeneeded = (e) => {
                const db = e.target.result;
                STORES.forEach(name => {
                    if (!db.objectStoreNames.contains(name)) {
                        const store = db.createObjectStore(name, { keyPath: '_offlineId' });
                        // Index form-queue by resource type for filtering
                        if (name === 'form-queue') {
                            store.createIndex('resource', '_resource', { unique: false });
                            store.createIndex('createdAt', '_createdAt', { unique: false });
                        }
                    }
                });
            };
            req.onsuccess = (e) => { _db = e.target.result; resolve(_db); };
            req.onerror = (e) => reject(e.target.error);
        });
    }

    function generateId() {
        return 'off_' + Date.now() + '_' + Math.random().toString(36).slice(2, 9);
    }

    async function add(storeName, data) {
        const db = await open();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(storeName, 'readwrite');
            const store = tx.objectStore(storeName);
            const record = {
                ...data,
                _offlineId: generateId(),
                _createdAt: new Date().toISOString(),
                _synced: false,
            };
            const req = store.add(record);
            req.onsuccess = () => resolve(record);
            req.onerror = (e) => reject(e.target.error);
        });
    }

    /**
     * Queue a generic form submission for ANY resource.
     * @param {string} resource - Resource slug (e.g. 'tasks', 'work-orders')
     * @param {string} action - 'create' or 'update'
     * @param {object} data - Form field values
     * @param {string|null} recordId - For updates, the existing record ID
     * @param {string} label - Human-readable label for the queue UI
     */
    async function queueForm(resource, action, data, recordId = null, label = '') {
        return add('form-queue', {
            _resource: resource,
            _action: action,
            _recordId: recordId,
            _label: label || `${action} ${resource}`,
            _url: window.location.pathname,
            data: data,
        });
    }

    async function getAll(storeName) {
        const db = await open();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(storeName, 'readonly');
            const store = tx.objectStore(storeName);
            const req = store.getAll();
            req.onsuccess = () => resolve(req.result);
            req.onerror = (e) => reject(e.target.error);
        });
    }

    async function remove(storeName, offlineId) {
        const db = await open();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(storeName, 'readwrite');
            const store = tx.objectStore(storeName);
            const req = store.delete(offlineId);
            req.onsuccess = () => resolve();
            req.onerror = (e) => reject(e.target.error);
        });
    }

    async function clear(storeName) {
        const db = await open();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(storeName, 'readwrite');
            tx.objectStore(storeName).clear();
            tx.oncomplete = () => resolve();
            tx.onerror = (e) => reject(e.target.error);
        });
    }

    async function count(storeName) {
        const db = await open();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(storeName, 'readonly');
            const req = tx.objectStore(storeName).count();
            req.onsuccess = () => resolve(req.result);
            req.onerror = (e) => reject(e.target.error);
        });
    }

    async function totalPending() {
        let total = 0;
        for (const name of STORES) {
            total += await count(name);
        }
        return total;
    }

    return { open, add, queueForm, getAll, remove, clear, count, totalPending, STORES };
})();

// Make globally available
if (typeof window !== 'undefined') window.InfraDB = InfraDB;
