<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;
use Stripe\StripeClient;
use TomorrowIdeas\Plaid\Plaid;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Plaid::class, function () {
            return new Plaid(
                config('services.plaid.id'),
                config('services.plaid.secret'),
                config('services.plaid.environment'),
            );
        });

        $this->app->bind(StripeClient::class, function () {
            return new StripeClient(config('services.stripe.api_key'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
