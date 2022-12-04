<?php

namespace App\Helpers\IamPln;

use Illuminate\Auth\Access\AuthorizationException;
use Closure;

class SsoCheckRole
{
	private $agent;

	public function __construct() {
		$this->agent = app(SsoAgent::class);
	}

	public function handle($request, Closure $next, ...$roles)
	{
		if(!$this->agent->isAuthenticated()) {
			$this->agent->authenticate();
		}

		$authorized = false;
		foreach($roles as &$role) {
			if($this->agent->hasRole($role)) {
				$authorized = true;
				break;
			}
		}

		if($authorized)
			return $next($request);
		else
			throw new AuthorizationException('you are not authorized to access this resource');
	}

}
