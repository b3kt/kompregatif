<?php

namespace App\Helpers\IamPln;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SsoAuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
      'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
			Log::info('boot sso auth service provider');

      $this->registerPolicies();

      Auth::extend('ssoagent', function ($app, $name, array $config) {
				$provider = app(SsoUserProvider::class);

				return new SsoSecureGuard($provider);
			});
    }
}
