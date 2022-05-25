<?php

namespace App\Providers;

use PayPalHttp\Environment;
use Illuminate\Support\ServiceProvider;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use Illuminate\Auth\Notifications\ResetPassword;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('paypal.client',function($app){
            $clientId = config('services.paypal.client_id');
            $clientSecret = config('services.paypal.client_secret');

            if(config('services.paypal.env')=='sandbox')
            $environment = new SandboxEnvironment($clientId, $clientSecret);
            else
            $environment=new Environment($clientId, $clientSecret);

            $client = new PayPalHttpClient($environment);
            return  $client;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {


        ResetPassword::createUrlUsing(function($notifiable,$token){
            return "http://localhost:3000/reset-password/?t={$token}&email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
