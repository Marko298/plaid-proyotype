<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
</head>
<body>
<button id="link-button" disabled>Request payment method</button>
<h1 id="customer-id"></h1>
<script type="text/javascript">
    async function postRequest(url, body) {
        return fetch(url, {
            method: 'POST',
            body: JSON.stringify(body),
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        }).then(res => res.json());
    }

    async function processPlaid(public_token, account_id) {
       await postRequest('{{ route('plaid.confirm') }}', {
            public_token, account_id,
        });
    }

    (async function() {
        const configs = {
            token: '{{ $token }}',
            onLoad: function() {
                document.querySelector('#link-button').disabled = false;
                document.querySelector('#link-button').click();
            },
            onSuccess: async function(public_token, metadata) {
                const accountId = metadata.accounts[0].id;

                console.log('Public Token: ' + public_token);
                console.log('Account ID: ' + accountId);
                console.log('Account count: ' + metadata.accounts.length);

                try {
                    await processPlaid(public_token, accountId);
                } catch (e) {
                    console.error(e);
                }
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

        document.querySelector('#link-button').onclick = function() {
            linkHandler.open();
        };
    })();
</script>
</body>
</html>
