/**
 * InfraHub Mobile PWA — API Service Module
 * Handles tokens, auth persistence, REST endpoints, and offline local cache fallbacks.
 */
const MobileAPI = (() => {
    'use strict';

    const BASE_URL = '/api/v1';

    function getToken() {
        return localStorage.getItem('m_token');
    }

    function getUser() {
        try {
            return JSON.parse(localStorage.getItem('m_user'));
        } catch {
            return null;
        }
    }

    function setAuth(token, user) {
        localStorage.setItem('m_token', token);
        localStorage.setItem('m_user', JSON.stringify(user));
    }

    function logout() {
        localStorage.removeItem('m_token');
        localStorage.removeItem('m_user');
        window.location.href = '/mobile/login';
    }

    function isLoggedIn() {
        return !!getToken();
    }

    function getHeaders() {
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
        const token = getToken();
        if (token) headers['Authorization'] = `Bearer ${token}`;

        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (csrfMeta?.content) headers['X-CSRF-TOKEN'] = csrfMeta.content;

        return headers;
    }

    async function request(path, options = {}) {
        const url = BASE_URL + path;
        const config = {
            headers: getHeaders(),
            credentials: 'same-origin',
            ...options
        };

        try {
            const res = await fetch(url, config);
            if (res.status === 401) {
                logout();
                return null;
            }
            return await res.json();
        } catch (err) {
            console.warn(`[MobileAPI] Network request error for ${path}:`, err);
            throw err;
        }
    }

    async function get(path, cacheKey = null) {
        try {
            const data = await request(path, { method: 'GET' });
            if (cacheKey && data) {
                localStorage.setItem(`m_cache_${cacheKey}`, JSON.stringify(data));
            }
            return data;
        } catch (err) {
            if (cacheKey) {
                const cached = localStorage.getItem(`m_cache_${cacheKey}`);
                if (cached) {
                    console.log(`[MobileAPI] Loaded cached data for key '${cacheKey}'`);
                    return JSON.parse(cached);
                }
            }
            throw err;
        }
    }

    async function post(path, data = {}) {
        return request(path, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    async function put(path, data = {}) {
        return request(path, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async function login(email, password) {
        const res = await fetch(`${BASE_URL}/auth/login`, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password, device_name: 'mobile-pwa' })
        });
        return res.json();
    }

    return {
        getToken,
        getUser,
        setAuth,
        logout,
        isLoggedIn,
        get,
        post,
        put,
        login
    };
})();

// Legacy backward-compatibility alias
window.API = MobileAPI;
