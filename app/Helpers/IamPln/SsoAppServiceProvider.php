<?php

namespace App\Helpers\IamPln;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\Authenticatable;
use IamPln\SsoAgent;

abstract class SsoAppServiceProvider extends ServiceProvider
{
    //protected $model = SsoUser::class;

		abstract public function findUser(string $identifier): Authenticatable;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
			Log::info('boot sso app service provider');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
			$iamConfig = config('app.iam_server');
			$this->app->singleton(SsoAgent::class, function($app) use(&$iamConfig) {
				return new SsoAgent(
					$iamConfig['issuer'],
					$iamConfig['auth_endpoint'],
					$iamConfig['token_endpoint'],
					$iamConfig['userinfo_endpoint'],
					$iamConfig['client_id'],
					$iamConfig['client_secret'],
					$iamConfig['redirect_uri'],
					$iamConfig['logout_uri'],
					$iamConfig['scopes'],
					$iamConfig['logging'],
					true
				);
			});

			$this->app->singleton(SsoUserProvider::class, function($app) {
				return new SsoUserProvider($this);
			});
    }
}
