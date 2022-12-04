<?php
	namespace App\Helpers\IamPln;

	use Illuminate\Auth\GuardHelpers;
	use Illuminate\Contracts\Auth\Guard;
	use Illuminate\Contracts\Auth\Authenticatable;
	use Illuminate\Contracts\Auth\UserProvider;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Log;
	use IamPln\SsoAgent;

	class SsoSecureGuard implements Guard {



		// initiate agent
		private $agent;
		private $provider;

		public function __construct(UserProvider $provider) {
			Log::info('initiate sso secure guard');

			$this->agent = app(SsoAgent::class);
			$this->provider = $provider;
		}

		public function check(): bool {
			if(!$this->agent->isAuthenticated()) {
				$this->agent->authenticate();
			}

			return $this->agent->isAuthenticated();
		}

		public function guest(): bool {
			return !$this->check();
		}

		public function user(): Authenticatable {
			if(!$this->check())
				return null;

			return $this->provider->retrieveById($this->id());
		}

		public function id() {
			if(!$this->check())
				return null;

			return $this->agent->getIdTokenPayload()['sub'];
		}

		public function validate(array $credentials = []) {
			return $this->provider->validateCredentials(null, $credentials);
		}

		public function setUser(Authenticatable $user): void {

		}
 	}
?>
