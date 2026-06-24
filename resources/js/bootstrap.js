import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.withCredentials = true;

// Laravel session cookie — refreshed after login / session regenerate (unlike static meta tag).
window.axios.defaults.xsrfCookieName = 'XSRF-TOKEN';
window.axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN';

export function syncCsrfToken(token) {
    if (!token) return;
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    const meta = document.head.querySelector('meta[name="csrf-token"]');
    if (meta) meta.content = token;
}

let csrfRefreshPromise = null;

export async function refreshCsrfToken() {
    if (!csrfRefreshPromise) {
        csrfRefreshPromise = window.axios
            .get('/api/csrf-token')
            .then(({ data }) => {
                syncCsrfToken(data.token);
                return data.token;
            })
            .finally(() => {
                csrfRefreshPromise = null;
            });
    }
    return csrfRefreshPromise;
}

// Drop stale meta header so Laravel uses the session cookie (X-XSRF-TOKEN) on each request.
delete window.axios.defaults.headers.common['X-CSRF-TOKEN'];

window.axios.interceptors.response.use(
    (response) => response,
    async (error) => {
        const config = error.config;
        if (error.response?.status === 419 && config && !config._csrfRetry) {
            config._csrfRetry = true;
            try {
                await refreshCsrfToken();
                return window.axios.request(config);
            } catch {
                // fall through
            }
        }
        return Promise.reject(error);
    },
);
