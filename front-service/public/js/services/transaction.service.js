const TransactionService = {
    virement: function (compteSourceId, compteDestinationId, montant) {
        return HttpClient.post('/transactions', {
            compteSourceId,
            compteDestinationId,
            montant: parseFloat(montant),
        });
    },

    lister: function () {
        return HttpClient.get('/transactions');
    },

    listerParCompte: function (compteId) {
        return HttpClient.get('/accounts/' + compteId + '/transactions');
    },

    consulter: function (id) {
        return HttpClient.get('/transactions/' + id);
    },
};
