<?php

namespace Metrogistics\AzureSocialite;

use Illuminate\Support\Facades\Auth;
use SocialiteProviders\Manager\SocialiteWasCalled;
use Metrogistics\AzureSocialite\Middleware\Authenticate;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        // $this->app->bind('azure-user', function(){
        //     return new AzureUser(
        //         session('azure_user')
        //     );
        // });
    }

    public function boot()
    {
        // Auth::extend('azure', function(){
        //     dd('test');
        //     return new Authenticate();
        // });

        $this->publishes([
            __DIR__.'/config/azure-oath.php' => config_path('azure-oath.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/config/azure-oath.php', 'azure-oath'
        );

        if (request()->getHttpHost() === config('azure-oath.alt-domain')) {
            $this->app['Laravel\Socialite\Contracts\Factory']->extend('azure-oauth', function ($app) {
                return $app['Laravel\Socialite\Contracts\Factory']->buildProvider(
                    'Metrogistics\AzureSocialite\AzureOauthProvider',
                    config('azure-oath.alt-credentials')
                );
            });
        } elseif (request()->getHttpHost() === config('azure-oath.alt2-domain')) {
            $this->app['Laravel\Socialite\Contracts\Factory']->extend('azure-oauth', function ($app) {
                return $app['Laravel\Socialite\Contracts\Factory']->buildProvider(
                    'Metrogistics\AzureSocialite\AzureOauthProvider',
                    config('azure-oath.alt2-credentials')
                );
            });
        } elseif (request()->getHttpHost() === config('azure-oath.alt3-domain')) {
            $this->app['Laravel\Socialite\Contracts\Factory']->extend('azure-oauth', function ($app) {
                return $app['Laravel\Socialite\Contracts\Factory']->buildProvider(
                    'Metrogistics\AzureSocialite\AzureOauthProvider',
                    config('azure-oath.alt3-credentials')
                );
            });
        } else {
            $this->app['Laravel\Socialite\Contracts\Factory']->extend('azure-oauth', function ($app) {
                return $app['Laravel\Socialite\Contracts\Factory']->buildProvider(
                    'Metrogistics\AzureSocialite\AzureOauthProvider',
                    config('azure-oath.credentials')
                );
            });
        }

        $this->app['router']->group(['middleware' => config('azure-oath.routes.middleware')], function($router){
            $router->get(config('azure-oath.routes.login'), 'Metrogistics\AzureSocialite\AuthController@redirectToOauthProvider');
            $router->get(config('azure-oath.routes.callback'), 'Metrogistics\AzureSocialite\AuthController@handleOauthResponse');
        });
    }
}
