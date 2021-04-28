<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeController extends Controller
{
    public function charge(Request $request, StripeClient $stripe)
    {
        $request->validate([
            'token'   => 'required|string',
            'user_id' => 'required|string',
        ]);

        $customer = $stripe->customers->create([
            'name'        => $request['user_id'],
            'description' => 'Created by Plaid integration',
        ]);

        $stripe->customers->createSource($customer->id, [
            'source' => $request['token'],
        ]);

        return [
            'id' => $customer->id
        ];
    }
}
