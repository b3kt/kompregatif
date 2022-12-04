<?php
	namespace App\Helpers\IamPln;

	use Illuminate\Contracts\Auth\UserProvider;
	use Illuminate\Contracts\Auth\Authenticatable;
	use Illuminate\Support\Facades\Log;
	use IamPln\SsoAgent;

	class SsoUserProvider implements UserProvider {

		private $agent;
		private $service;

		public function __construct(SsoAppServiceProvider $service) {
			Log::info('initiate sso user provider');

			// lazy authenticate sso agent
			$this->agent = app(SsoAgent::class);
			$this->service = $service;
		}

		public function retrieveById($identifier): Authenticatable {
			if(!$this->agent->isAuthenticated()) {
				$this->agent->authenticate();
			}

			return $this->service->findUser($identifier)->setAgent($this->agent);
		}

		public function retrieveByToken($identifier, $token): Authenticatable {
			return $this->retrieveById($identifier);
		}

		public function updateRememberToken(Authenticatable $user, $token): void {

		}

		public function retrieveByCredentials(array $credentials): Authenticatable {
			if(!$this->agent->isAuthenticated()) {
				$this->agent->authenticate();
			}

			$identifier = $credentials['username'];
			$accessToken = $credentials['access_token'];

			if($this->agent->getAccessToken() === $accessToken)
				return $this->retrieveById($identifier);
			else
				return null;
		}

		public function validateCredentials(Authenticatable $user, array $credentials): bool {
			if($this->retrieveByCredentials($credentials) != null)
				return true;
			else
				return false;
		}

	}
?>
