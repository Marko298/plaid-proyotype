<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('test-customer', function () {
    if(!app()->environment('local')) {
        $this->error('This command intended only for dev usage');
    }

    $customers = app(\Stripe\StripeClient::class)->customers->all([
        'limit' => 5,
        'email' => 'plaid-test@example.com'
    ]);

    /** @var \Stripe\Customer $customer */
    $customer = $customers->first();

    dd(route('plaid', ['customer-id' => $customer->id]));
});
