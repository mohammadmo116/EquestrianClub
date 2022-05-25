<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $request=request();
        if($request->is('api/admin/*'))
        {
            Config::set('fortify.prefixApi', 'admin');
            Config::set('fortify.prefix', 'api/admin');
            Config::set('fortify.guard', 'admin');
            Config::set('fortify.passwords', 'admins');

        }
        else if($request->is('api/trainer/*'))
        {
            Config::set('fortify.prefixApi', 'trainer');
            Config::set('fortify.prefix', 'api/trainer');
            Config::set('fortify.guard', 'trainer');
            Config::set('fortify.passwords', 'trainers');

        }
       else if($request->is('api/*'))
        {
            Config::set('fortify.prefixApi', '');
            Config::set('fortify.prefix', 'api/');
            Config::set('fortify.guard', 'user');
            Config::set('fortify.passwords', 'users');

        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
