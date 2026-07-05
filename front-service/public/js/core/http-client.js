const API_BASE = 'http://localhost:8080';

const HttpClient = {
    _request: async function (method, path, body = null) {
        const token = localStorage.getItem('jwt_token');

        const headers = { 'Content-Type': 'application/json' };
        if (token) {
            headers['Authorization'] = 'Bearer ' + token;
        }

        const options = { method, headers };
        if (body !== null) {
            options.body = JSON.stringify(body);
        }

        const response = await fetch(API_BASE + path, options);

        if (response.status === 401) {
            localStorage.removeItem('jwt_token');
            window.location.href = '/index.html';
            return;
        }

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const error = new Error(data.error || 'Erreur serveur');
            error.status = response.status;
            error.details = data.details || null;
            throw error;
        }

        return data;
    },

    get: function (path) {
        return this._request('GET', path);
    },

    post: function (path, body) {
        return this._request('POST', path, body);
    },
};
