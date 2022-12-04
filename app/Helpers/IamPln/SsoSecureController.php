<?php
namespace App\Helpers\IamPln;

defined('BASEPATH') OR exit('No direct script access allowed');

use CI_Controller;
use Exception;

class SsoSecureController extends CI_Controller {
	private $agent;

	protected $redirectUri = '/notauthorized';

	public function __construct(array $allowRoles = []) {
		// initiate parent constructor
		parent::__construct();

		// initiate sso agent
		$iamConfig = $this->config->item('iam_server');
		if(!isset($iamConfig))
			throw new Exception('no iam server config found');

		$this->agent = new SsoAgent(
			$iamConfig['issuer'],
			$iamConfig['auth_endpoint'],
			$iamConfig['token_endpoint'],
			$iamConfig['userinfo_endpoint'],
			$iamConfig['client_id'],
			$iamConfig['client_secret'],
			$iamConfig['redirect_uri'],
			$iamConfig['logout_uri'],
			$iamConfig['scopes'],
			$iamConfig['logging']
		);

		if(!empty($allowRoles)) {
			$authorized = false;
			foreach($allowRoles as &$role) {
				if($this->hasRole($role)) {
					$authorized = true;
					break;
				}
			}

			if(!$authorized) {
				header("HTTP/1.1 401 Unauthorized");
				header("Location: $this->redirectUri");
				die();
			}
		}
	}

	public function setLogHandlers(array $handlers): self {
		$this->agent->setLogHandlers($handlers);

		return $this;
	}

	public function signOut($redirect): self {
		$this->agent->signOut($redirect);

		return $this;
	}

	public function getIdToken() {
		return $this->agent->getIdToken();
	}

	public function getIdTokenHeader() {
		return $this->agent->getIdTokenHeader();
	}

	public function getIdTokenPayload() {
		return $this->agent->getIdTokenPayload();
	}

	public function getAccessToken() {
		return $this->agent->getAccessToken();
	}

	public function getRefreshToken() {
		return $this->agent->getRefreshToken();
	}

	public function getApiToken() {
		return $this->agent->getApiToken();
	}

	public function getAccessTokenHeader() {
		return $this->agent->getAccessTokenHeader();
	}

	public function getAccessTokenPayload() {
		return $this->agent->getAccessTokenPayload();
	}

	public function hasRole(string $role) : bool {
		return $this->agent->hasRole($role);
	}

	public function refreshToken(string $token) {
		return $this->agent->refreshToken($token);
	}

	public function debug($str) {
		$this->debug($str);
	}
}
