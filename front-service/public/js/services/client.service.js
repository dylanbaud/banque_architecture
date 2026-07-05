const ClientService = {
    creer: function (nom, prenom, email) {
        return HttpClient.post('/clients', { nom, prenom, email });
    },

    consulter: function (id) {
        return HttpClient.get('/clients/' + id);
    },

    lister: function () {
        return HttpClient.get('/clients');
    },
};
