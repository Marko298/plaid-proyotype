<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
</head>
<body>
<button id="link-button" disabled>Link Account</button>
<button id="create-customer" disabled>Create Customer</button>
<h1 id="customer-id"></h1>
<script type="text/javascript">
    async function postRequest(url, body) {
        return await fetch(url, {
            method: 'POST',
            body: JSON.stringify(body),
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        }).then(res => res.json());
    }

    (async function() {
        const configs = {
            token: '{{ $token }}',
            onLoad: function() {
                document.querySelector('#link-button').disabled = false;
            },
            onSuccess: async function(public_token, metadata) {
                console.log('Public Token: ' + public_token);
                console.log('Account ID: ' + metadata.accounts[0].id);
                console.log('Account count: ' + metadata.accounts.length);

                const { token } = await postRequest('{{ route('plaid.confirm') }}', {
                    public_token: public_token,
                    account_id: metadata.accounts[0].id,
                });

                const button = document.querySelector('#create-customer');
                button.disabled = false;
                button.onclick = async function createStripeAccount() {
                    const { id } = await postRequest('{{ route('stripe.create-customer') }}', {
                        user_id: '{{ $user_id }}',
                        token: token,
                    });

                    button.disabled = true;

                    document.querySelector('#customer-id').innerHTML = id;
                };
            },
            onExit: async function(err, metadata) {
                console.log(metadata);

                if (err != null) {
                    document.querySelector('#customer-id').innerHTML = err.error_message;

                    console.error(err);
                }
            },
        };

        const linkHandler = Plaid.create(configs);

        document.getElementById('link-button').onclick = function() {
            linkHandler.open();
        };
    })();
</script>
</body>
</html>
