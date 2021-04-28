// Set your secret key. Remember to switch to your live secret key in production.
// See your keys here: https://dashboard.stripe.com/apikeys
const stripe = require('stripe')('{{ STRIPE_KEY }}');

// Get the bank token submitted by the form
// Create a Customer
(async () => {
    const charge = await stripe.charges.create({
        amount: '100',
        source: '{{ STRIPE_BTOK }}'
    })



    console.log(charge);
})().catch(err => {
    console.log(err.message);
})
