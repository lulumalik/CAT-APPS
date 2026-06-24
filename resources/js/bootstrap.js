import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.withCredentials = true;

window.axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
window.axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';

function readMetaCsrfToken() {
    const el = document.head.querySelector('meta[name="csrf-token"]');
    return el?.content || null;
}

export function syncCsrfToken(token) {
    if (!token) return null;
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    const meta = document.head.querySelector('meta[name="csrf-token"]');
    if (meta) meta.content = token;
    return token;
}

let csrfRefreshPromise = null;

export async function refreshCsrfToken() {
    if (!csrfRefreshPromise) {
        csrfRefreshPromise = window.axios
            .get('/api/csrf-token')
            .then(({ data }) => syncCsrfToken(data.token))
            .finally(() => {
                csrfRefreshPromise = null;
            });
    }
    return csrfRefreshPromise;
}

function applyCsrfHeader(config) {
    const token = window.axios.defaults.headers.common['X-CSRF-TOKEN'];
    if (!token) return config;
    if (typeof config.headers?.set === 'function') {
        config.headers.set('X-CSRF-TOKEN', token);
    } else {
        config.headers = config.headers || {};
        config.headers['X-CSRF-TOKEN'] = token;
    }
    return config;
}

const mutatingMethods = new Set(['post', 'put', 'patch', 'delete']);

// Seed from server-rendered meta tag (matches initial session).
syncCsrfToken(readMetaCsrfToken());

window.axios.interceptors.request.use(async (config) => {
    const method = (config.method || 'get').toLowerCase();
    if (!mutatingMethods.has(method)) {
        return config;
    }

    if (!window.axios.defaults.headers.common['X-CSRF-TOKEN']) {
        const meta = readMetaCsrfToken();
        if (meta) {
            syncCsrfToken(meta);
        } else {
            await refreshCsrfToken();
        }
    }

    return applyCsrfHeader(config);
});

window.axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const config = error.config;
        if (error.response?.status === 419 && config && !config._csrfRetry) {
            config._csrfRetry = true;
            try {
                const token = await refreshCsrfToken();
                if (token) {
                    if (typeof config.headers?.set === 'function') {
                        config.headers.set('X-CSRF-TOKEN', token);
                    } else {
                        config.headers = config.headers || {};
                        config.headers['X-CSRF-TOKEN'] = token;
                    }
                }
                return window.axios.request(config);
            } catch {
                // fall through
            }
        }
        return Promise.reject(error);
    },
);
