document.addEventListener('DOMContentLoaded', function () {
    if (AuthService.isAuthenticated()) {
        window.location.href = '/dashboard.html';
        return;
    }

    const tabs = document.querySelectorAll('.auth-tab');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const messageEl = document.getElementById('auth-message');

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(function (t) { t.classList.remove('active'); });
            tab.classList.add('active');

            const target = tab.dataset.tab;
            loginForm.classList.toggle('hidden', target !== 'login');
            registerForm.classList.toggle('hidden', target !== 'register');
            messageEl.classList.add('hidden');
        });
    });

    loginForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const email = document.getElementById('login-email').value.trim();
        const password = document.getElementById('login-password').value;

        try {
            const data = await AuthService.login(email, password);
            if (!data || !data.token) {
                throw new Error('Réponse invalide du serveur');
            }
            AuthService.saveToken(data.token);
            window.location.href = '/dashboard.html';
        } catch (err) {
            showMessage(err.message || 'Identifiants invalides', 'error');
        }
    });

    registerForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const email = document.getElementById('register-email').value.trim();
        const password = document.getElementById('register-password').value;

        try {
            await AuthService.register(email, password);
            showMessage('Compte créé. Vous pouvez vous connecter.', 'success');
            tabs[0].click();
        } catch (err) {
            showMessage(err.message || 'Erreur lors de l\'inscription', 'error');
        }
    });

    function showMessage(text, type) {
        messageEl.textContent = text;
        messageEl.className = 'message ' + type;
    }
});
