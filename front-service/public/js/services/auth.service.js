const AuthService = {
    login: function (email, password) {
        return HttpClient.post('/auth/login', { email, password });
    },

    register: function (email, password) {
        return HttpClient.post('/auth/register', { email, password });
    },

    logout: function () {
        localStorage.removeItem('jwt_token');
        window.location.href = '/index.html';
    },

    isAuthenticated: function () {
        const token = localStorage.getItem('jwt_token');
        return token !== null && token !== 'undefined' && token !== '';
    },

    saveToken: function (token) {
        localStorage.setItem('jwt_token', token);
    },
};
