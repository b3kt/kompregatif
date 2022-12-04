<?php
	namespace App\Helpers\IamPln;

	use Illuminate\Contracts\Auth\Authenticatable;
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Log;
	use IamPln\SsoAgent;

	//class SsoUser implements Authenticatable {
	trait SsoUser {

		private $agent;
		private $username;
		private $accessToken;
		private $payload;

		/*public function __construct(array $attributes = []) {
			parent::__construct($attributes);

			Log::info('initiate sso user');

			// get agent
			$this->agent = app(SsoAgent::class);
		}*/

		/*private function getAgent() {
			if(!$this->agent)
				$this->agent = app(SsoAgent::class);

			return $this->agent;
		}*/

		public function setAgent(SsoAgent $agent): self {
			$this->agent = $agent;

			return $this;
		}

		public function getAuthIdentifierName(): string {
			return "username";
		}

		public function getAuthIdentifier() {
			if(!isset($this->agent))
				return null;

			if($this->agent->isAuthenticated())
				$this->username = $this->agent->getIdTokenPayload()['sub'];

			return $this->username;
		}

		public function getAuthPassword(): ?string {
			if(!isset($this->agent))
				return null;

			if($this->agent->isAuthenticated())
				$this->accessToken = $this->agent->getAccessToken();

			return $this->accessToken;
		}

		public function getRememberToken(): ?string {
			return $this->getAuthPassword();
		}

		public function setRememberToken($value) {

		}

		public function getRememberTokenName(): string {
			return "access_token";
		}

		public function getClaims() {
			if(!isset($this->agent))
				return null;

			if($this->agent->isAuthenticated())
				$this->payload = $this->agent->getIdTokenPayload();

			return $this->payload;
		}
	}
?>
