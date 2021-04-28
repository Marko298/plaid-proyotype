// Using Plaid's Node.js bindings (https://github.com/plaid/plaid-node)
var plaid = require('plaid');

var plaidClient = new plaid.Client({
    clientID: '{{ CLIENT_ID }}',
    secret: '{{ CLIENT_SECRET }}',
    env: plaid.environments.development,
});

const clientUserId = 'Stripe test';

plaidClient.createLinkToken({
    user: {
        client_user_id: clientUserId,
    },
    client_name: 'My App',
    products: ['auth'],
    country_codes: ['US'],
    language: 'en',
    webhook: 'https://sample.webhook.com',
}, function(error, linkTokenResponse) {
    // Pass the result to your client-side app to initialize Link
    console.log({ error, link_token: linkTokenResponse.link_token });
});
