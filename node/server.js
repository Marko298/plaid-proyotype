const express = require('express');
const plaid = require('plaid');
const app = express();
const port = 3000;

const plaidClient = new plaid.Client({
    clientID: '{{ CLIENT_ID }}',
    secret: '{{ CLIENT_SECRET }}',
    env: plaid.environments.development,
});

app.get('/', (req, res) => {
    res.send('Hello World!');
});

app.get('/token', (req, res) => {
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
        console.log({ error, link_token: linkTokenResponse.link_token });
    });


    res.send('Hello World!');
});

app.get('/exchange', async (req, res) => {
    if(!req.query.token || req.query.account) {
        return res.status(400).json({
            error: 'Missing token or account'
        })
    }

    const token = await plaidClient.exchangePublicToken(req.query.token);
    const backAccount = await plaidClient.createStripeToken(token.access_token, req.query.account);

    res.json({
        token: backAccount.stripe_bank_account_token
    })

    console.log(backAccount.stripe_bank_account_token);
});

app.listen(port, () => {
    console.log(`Example app listening at http://localhost:${port}`);
});
