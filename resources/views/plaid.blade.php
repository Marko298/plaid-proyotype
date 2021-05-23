<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
    <script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
    <style>
        .loader,
        .loader:after {
            border-radius: 50%;
            width: 10em;
            height: 10em;
        }

        .loader {
            margin: 60px auto;
            font-size: 10px;
            position: relative;
            text-indent: -9999em;
            border-top: 1.1em solid rgba(0, 0, 0, 0.2);
            border-right: 1.1em solid rgba(0, 0, 0, 0.2);
            border-bottom: 1.1em solid rgba(0, 0, 0, 0.2);
            border-left: 1.1em solid #000000;
            -webkit-transform: translateZ(0);
            -ms-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-animation: load8 1.1s infinite linear;
            animation: load8 1.1s infinite linear;
        }

        @-webkit-keyframes load8 {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        @keyframes load8 {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }

        body {
            height: 100vh;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        #link-button {
            visibility: hidden;
        }
    </style>
</head>
<body>
<div class="loader">Loading...</div>
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
            public_token, account_id, customer_id: '{{ $user_id }}',
        });
    }

    (async function() {
        const configs = {
            token: '{{ $token }}',
            onLoad: function() {
                document.querySelector('.loader').style.visibility = 'hidden';
                document.querySelector('#link-button').disabled = false;
                document.querySelector('#link-button').click();
            },
            onSuccess: async function(public_token, metadata) {
                const accountId = metadata.accounts[0].id;

                // console.log('Public Token: ' + public_token);
                // console.log('Account ID: ' + accountId);
                // console.log('Account count: ' + metadata.accounts.length);

                try {
                    await processPlaid(public_token, accountId);
                } catch (e) {
                    console.error(e);
                }

                window.close();
            },
            onExit: async function(err, metadata) {
                window.close();

                // console.log({err, metadata});
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
