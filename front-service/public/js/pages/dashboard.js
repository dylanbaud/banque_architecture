document.addEventListener('DOMContentLoaded', function () {
    if (!AuthService.isAuthenticated()) {
        window.location.href = '/index.html';
        return;
    }

    // --- Navigation --------------------------------------------

    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.section');

    navLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const target = link.dataset.section;

            navLinks.forEach(function (l) { l.classList.remove('active'); });
            sections.forEach(function (s) { s.classList.remove('active'); });

            link.classList.add('active');
            document.getElementById('section-' + target).classList.add('active');
            clearResults();
        });
    });

    document.getElementById('btn-logout').addEventListener('click', function () {
        AuthService.logout();
    });

    // --- Clients -------------------------------------------------------------

    loadClientsTable();
    loadComptesTable();
    loadTransactionsTable();

    document.getElementById('form-creer-client').addEventListener('submit', async function (e) {
        e.preventDefault();
        const nom = document.getElementById('client-nom').value.trim();
        const prenom = document.getElementById('client-prenom').value.trim();
        const email = document.getElementById('client-email').value.trim();

        try {
            const client = await ClientService.creer(nom, prenom, email);
            showNotification('Client créé avec succès', 'success');
            this.reset();
            loadClientsTable();
        } catch (err) {
            showNotification(err.message, 'error');
        }
    });

    document.getElementById('form-consulter-client').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('client-id').value.trim();

        try {
            const client = await ClientService.consulter(id);
            showResult('result-client', client);
        } catch (err) {
            showNotification(err.message, 'error');
        }
    });

    // --- Comptes -------------------------------------------------------------

    document.getElementById('form-creer-compte').addEventListener('submit', async function (e) {
        e.preventDefault();
        const clientId = document.getElementById('compte-client-id').value.trim();
        const solde = document.getElementById('compte-solde-initial').value;

        try {
            const compte = await CompteService.creer(clientId, solde);
            showResult('result-compte', compte);
            showNotification('Compte créé avec succès', 'success');
            this.reset();
            loadComptesTable();
        } catch (err) {
            showNotification(err.message, 'error');
        }
    });

    document.getElementById('form-consulter-compte').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('compte-id').value.trim();

        try {
            const compte = await CompteService.consulter(id);
            showResult('result-compte', compte);
        } catch (err) {
            showNotification(err.message, 'error');
        }
    });

    document.getElementById('form-deposer').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('depot-compte-id').value.trim();
        const montant = document.getElementById('depot-montant').value;

        try {
            const compte = await CompteService.deposer(id, montant);
            showResult('result-compte', compte);
            showNotification('Dépôt effectué', 'success');
            this.reset();
        } catch (err) {
            showNotification(err.message, 'error');
        }
    });

    document.getElementById('form-retirer').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('retrait-compte-id').value.trim();
        const montant = document.getElementById('retrait-montant').value;

        try {
            const compte = await CompteService.retirer(id, montant);
            showResult('result-compte', compte);
            showNotification('Retrait effectué', 'success');
            this.reset();
        } catch (err) {
            showNotification(err.message, 'error');
        }
    });

    // --- Transactions -------------------------------------------------------------

    document.getElementById('form-virement').addEventListener('submit', async function (e) {
        e.preventDefault();
        const source = document.getElementById('virement-source').value.trim();
        const destination = document.getElementById('virement-destination').value.trim();
        const montant = document.getElementById('virement-montant').value;

        try {
            const transaction = await TransactionService.virement(source, destination, montant);
            showResult('result-transaction', transaction);
            showNotification('Virement effectué', 'success');
            this.reset();
            loadTransactionsTable();
        } catch (err) {
            showNotification(err.message, 'error');
        }
    });

    document.getElementById('form-consulter-transaction').addEventListener('submit', async function (e) {
        e.preventDefault();
        const id = document.getElementById('transaction-id').value.trim();

        try {
            const transaction = await TransactionService.consulter(id);
            showResult('result-transaction', transaction);
        } catch (err) {
            showNotification(err.message, 'error');
        }
    });

    document.getElementById('form-transactions-par-compte').addEventListener('submit', async function (e) {
        e.preventDefault();
        const compteId = document.getElementById('transactions-compte-id').value.trim();

        try {
            const transactions = await TransactionService.listerParCompte(compteId);
            showResult('result-transaction', transactions);
        } catch (err) {
            showNotification(err.message, 'error');
        }
    });

    // --- Utilitaires -------------------------------------------------------------

    async function loadTransactionsTable() {
        const container = document.getElementById('transactions-table-container');
        try {
            const transactions = await TransactionService.lister();
            if (!transactions || transactions.length === 0) {
                container.innerHTML = '<p class="table-empty">Aucune transaction enregistrée.</p>';
                return;
            }
            container.innerHTML = '<table class="data-table">'
                + '<thead><tr><th>ID transaction</th></tr></thead>'
                + '<tbody>'
                + transactions.map(function (t) {
                    return '<tr><td><span class="id-badge">' + t.id + '</span></td></tr>';
                }).join('')
                + '</tbody></table>';
        } catch (err) {
            container.innerHTML = '<p class="table-empty">Impossible de charger les transactions.</p>';
        }
    }

    async function loadComptesTable() {
        const container = document.getElementById('comptes-table-container');
        try {
            const comptes = await CompteService.lister();
            if (!comptes || comptes.length === 0) {
                container.innerHTML = '<p class="table-empty">Aucun compte enregistré.</p>';
                return;
            }
            container.innerHTML = '<table class="data-table">'
                + '<thead><tr><th>ID compte</th></tr></thead>'
                + '<tbody>'
                + comptes.map(function (c) {
                    return '<tr><td><span class="id-badge">' + c.id + '</span></td></tr>';
                }).join('')
                + '</tbody></table>';
        } catch (err) {
            container.innerHTML = '<p class="table-empty">Impossible de charger les comptes.</p>';
        }
    }

    async function loadClientsTable() {
        const container = document.getElementById('clients-table-container');
        try {
            const clients = await ClientService.lister();
            if (!clients || clients.length === 0) {
                container.innerHTML = '<p class="table-empty">Aucun client enregistré.</p>';
                return;
            }
            container.innerHTML = '<table class="data-table">'
                + '<thead><tr><th>ID client</th></tr></thead>'
                + '<tbody>'
                + clients.map(function (c) {
                    return '<tr><td><span class="id-badge">' + c.id + '</span></td></tr>';
                }).join('')
                + '</tbody></table>';
        } catch (err) {
            container.innerHTML = '<p class="table-empty">Impossible de charger les clients.</p>';
        }
    }

    function showResult(containerId, data) {
        const el = document.getElementById(containerId);
        el.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
        el.classList.remove('hidden');
    }

    function clearResults() {
        document.querySelectorAll('.result-box').forEach(function (el) {
            el.classList.add('hidden');
            el.innerHTML = '';
        });
    }

    function showNotification(message, type) {
        const notif = document.getElementById('notification');
        notif.textContent = message;
        notif.className = 'notification ' + type;
        notif.classList.remove('hidden');

        setTimeout(function () {
            notif.classList.add('hidden');
        }, 3500);
    }
});
