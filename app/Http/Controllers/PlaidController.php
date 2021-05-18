<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\StripeClient;
use Throwable;
use TomorrowIdeas\Plaid\Entities\User;
use TomorrowIdeas\Plaid\Plaid;

class PlaidController extends Controller
{
    public function index(Request $request, Plaid $plaid)
    {
        $customer = $this->getCustomer($request);

        $token = $plaid->tokens->create(
            'Lucky Diem',
            'en',
            ['US'],
            new User($customer->id),
            ['auth'],
            'https://sample.webhook.com',
        );

        Log::debug('Plaid token', (array) $token);

        return view('plaid', [
            'token'   => $token->link_token,
            'user_id' => $customer->id,
        ]);
    }

    public function confirm(Request $request, Plaid $plaid, StripeClient $stripe)
    {
        $request->validate([
            'public_token' => 'required|string',
            'account_id'   => 'required|string',
            'customer_id'  => 'required|string|max:200',
        ]);

        Log::debug('Requesting exchange', (array) $request->json());

        $bankToken = $plaid->items->exchangeToken($request['public_token']);

        Log::debug('Plaid token exchange response', (array) $bankToken);

        $stripeToken = $plaid->processors->createStripeToken(
            $bankToken->access_token,
            $request['account_id']
        );

        Log::debug('Plaid stripe token response', (array) $stripeToken);

        $customer = $stripe->customers->retrieve($request['customer_id']);

        /** @var \Stripe\Source $method */
        $source = $customer->sources->create([
            'source' => $stripeToken->stripe_bank_account_token,
        ]);

        Log::debug('Successfully created method ' . $source->id);

        return [];
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Stripe\Customer
     */
    protected function getCustomer(Request $request): Customer
    {
        try {
            $request->validate([
                'customer-id' => 'required|string|max:200',
            ]);

            Log::debug('Retrieving user ' . $request['customer-id']);

            return app(StripeClient::class)->customers->retrieve($request['customer-id']);
        } catch (Throwable $exception) {
            redirect()->route('wrong_id')->throwResponse();
        }
    }
}
