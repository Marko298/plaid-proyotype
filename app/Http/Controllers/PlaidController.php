<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use TomorrowIdeas\Plaid\Entities\User;
use TomorrowIdeas\Plaid\Plaid;

class PlaidController extends Controller
{
    public function index(Plaid $plaid)
    {
        $user_id = 'Plaid ' . Str::uuid();

        Log::debug('Creating token for user: ' . $user_id);

        $token = $plaid->tokens->create(
            'Lucky Diem',
            'en',
            ['US'],
            new User($user_id),
            ['auth'],
            'https://sample.webhook.com',
        );

        Log::debug('Plaid token', (array) $token);

        return view('plaid', [
            'token'   => $token->link_token,
            'user_id' => $user_id,
        ]);
    }

    public function confirm(Request $request, Plaid $plaid)
    {
        $request->validate([
            'account_id'   => 'required|string',
            'public_token' => 'required|string',
        ]);

        Log::debug('Requesting exchange', (array) $request->json());

        $bankToken = $plaid->items->exchangeToken($request['public_token']);

        Log::debug('Plaid token exchange response', (array) $bankToken);

        $stripeToken = $plaid->processors->createStripeToken(
            $bankToken->access_token,
            $request['account_id']
        );

        Log::debug('Plaid stripe token response', (array) $stripeToken);

        return [
            'stripe_token' => $stripeToken->stripe_bank_account_token,
        ];
    }
}
