// Using Plaid's Node.js bindings (https://github.com/plaid/plaid-node)
var plaid = require('plaid');

var plaidClient = new plaid.Client({
    clientID: '{{ CLIENT_ID }}',
    secret: '{{ CLIENT_SECRET }}',
    env: plaid.environments.development,
});

plaidClient.exchangePublicToken('{{ PUBLIC_TOKEN }}', function(err, res) {
    var accessToken = res.access_token;
    // Generate a bank account token
    plaidClient.createStripeToken(accessToken, '{{ ACCOUNT_ID }}', function(err, res) {
        var bankAccountToken = res.stripe_bank_account_token;

        console.log(bankAccountToken);
    });
});
