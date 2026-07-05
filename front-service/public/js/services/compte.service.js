const CompteService = {
    creer: function (clientId, soldeInitial) {
        return HttpClient.post('/accounts', { clientId, soldeInitial: parseFloat(soldeInitial) });
    },

    lister: function () {
        return HttpClient.get('/accounts');
    },

    consulter: function (id) {
        return HttpClient.get('/accounts/' + id);
    },

    deposer: function (id, montant) {
        return HttpClient.post('/accounts/' + id + '/deposit', { montant: parseFloat(montant) });
    },

    retirer: function (id, montant) {
        return HttpClient.post('/accounts/' + id + '/withdraw', { montant: parseFloat(montant) });
    },
};
