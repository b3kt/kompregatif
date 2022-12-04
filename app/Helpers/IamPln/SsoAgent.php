<?php
	namespace App\Helpers\IamPln;

	use Jumbojett\OpenIDConnectClient;
	use Monolog\Logger;
	use Monolog\Handler\BrowserConsoleHandler;
	use Monolog\Handler\HandlerInterface;

	function b64url2b64($base64url) {
    // "Shouldn't" be necessary, but why not
    $padding = strlen($base64url) % 4;
    if ($padding > 0) {
			$base64url .= str_repeat("=", 4 - $padding);
    }
    return strtr($base64url, '-_', '+/');
	}

	function base64url_decode($base64url) {
    return base64_decode(b64url2b64($base64url));
	}

	class SsoAgent {
		private $log;
		private $enableLogging;
		private $oidc;
		private $clientId;
		private $idToken;
		private $accessToken;
		private $refreshToken;
		private $apiToken;

		public function __construct(string $_issuer, string $_authEndpoint, string $_tokenEndpoint, string $_userinfoEndpoint, string $_clientId, string $_clientSecret, string $_redirectUri, string $_logoutUri, array $_scopes, bool $_enableLogging, bool $_lazy = false) {

			$uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$host = parse_url($uri, PHP_URL_HOST);
			$child = preg_match('/pln.co.id$/', $host);

			$this->clientId = $_clientId;

			// initiate openid client
			$this->oidc = new OpenIDConnectClient(
				$_issuer,
				$_clientId,
				$_clientSecret
			);

			$this->oidc->providerConfigParam(array(
				'issuer' => $_issuer,
				'authorization_endpoint' => $_authEndpoint,
				'token_endpoint' => $_tokenEndpoint,
				'userinfo_endpoint' => $_userinfoEndpoint,
				'end_session_endpoint' => $_logoutUri
			));
			$this->oidc->addScope($_scopes);
			$this->oidc->setRedirectURL($_redirectUri);
			$this->oidc->setVerifyPeer(false);

			if($_enableLogging) {
				$this->enableLogging = $_enableLogging;
				$this->log = new Logger('iam-agent-php');
				// set default handler
				$this->log->setHandlers(array(new BrowserConsoleHandler()));
			}

			// check if has session
			if(session_status() == PHP_SESSION_NONE)
				session_start();

			$rootCookieName = $_clientId . '_code';
			//echo $_SESSION['iam-agent-php'];
			if(isset($_SESSION['iam-agent-php'])) {
				$tmp = json_decode($_SESSION['iam-agent-php'], true);
				$this->idToken = $tmp['idToken'];
				$this->accessToken = $tmp['accessToken'];

				if(isset($tmp['refreshToken']))
					$this->refreshToken = $tmp['refreshToken'];

				if(isset($tmp['apiToken']))
					$this->apiToken = $tmp['apiToken'];

				$payload = $this->getIdTokenPayload();
				//echo $rootCookieName;
				$tosync = false;
				if($child) {
					$this->debug('sub domain');
					if(isset($_COOKIE[$rootCookieName])) {
						$rootCode = $_COOKIE[$rootCookieName];
						$code = $_SESSION['iam-agent-php-code'];

						$tosync = (strcmp($rootCode, $code) != 0);
					} else {
						$this->debug('no root cookie');
						$tosync = true;
					}
				}

				// check if access token still valid
				// check if token is expired
				$now = time();
				$exp = (int)$payload['exp'];

				// can also do refresh token
				if($now > $exp || $tosync) {
					$this->debug('require sync');

					unset($_SESSION['iam-agent-php']);
					unset($_SESSION['iam-agent-php-code']);
					// remove tokens
					unset($this->idToken);
					unset($this->accessToken);
					unset($this->refreshToken);
					unset($_GET['code']);

					if(!$_lazy)
						$authenticated = $this->oidc->authenticate();
				}
			} else {
				$this->debug('not authorized');
				// check if code exists
				if(isset($_GET['code'])) {
					unset($_SESSION['iam-agent-php-code']);

					$_SESSION['iam-agent-php-code'] = $_GET['code'];
				}

				if(!$_lazy)
					$this->authenticate();
			}
		}

		public function authenticate(): void {
			// start authentication process
			$authenticated = $this->oidc->authenticate();

			if($authenticated) {
				// get access token and id token
				$this->idToken = $this->oidc->getIdToken();
				$this->accessToken = $this->oidc->getAccessToken();
				$this->refreshToken = $this->oidc->getRefreshToken();
				$this->apiToken = $this->oidc->getTokenResponse()->api_token;

				// save access token adn id token in session
				$json_session = array(
					'idToken' => $this->getIdToken(),
					'accessToken' => $this->getAccessToken()
				);

				if(isset($this->refreshToken))
					$json_session['refreshToken'] = $this->getRefreshToken();

				if(isset($this->apiToken))
					$json_session['apiToken'] = $this->apiToken;

				$_SESSION['iam-agent-php'] = json_encode($json_session);

				$this->debug('user ' . $this->getIdTokenPayload()['sub'] . ' authenticated on ' . $this->clientId . ' at ' . date('d-m-Y H:i:s'));
			}
		}

		public function isAuthenticated(): bool {
			return (isset($this->accessToken) ? true : false);
		}

		public function setLogHandlers(array $handlers): self {
			if($this->enableLogging)
				$this->log->setHandlers($handlers);

			return $this;
		}

		public function signOut($redirect): self {
			// remove session
			unset($_SESSION['iam-agent-php']);
			unset($_SESSION['iam-agent-php-code']);

			$tmpToken = $this->accessToken;

			// remove tokens
			unset($this->idToken);
			unset($this->accessToken);
			unset($this->refreshToken);

			$this->oidc->signOut($tmpToken, $redirect);

			return $this;
		}

		public function getIdToken() {
			return $this->idToken;
		}

		public function getIdTokenHeader() {
			return $this->decodeJWT($this->idToken, 0);
		}

		public function getIdTokenPayload() {
			return $this->decodeJWT($this->idToken, 1);
		}

		public function getRefreshToken() {
			return $this->refreshToken;
		}

		public function getApiToken() {
			return $this->apiToken;
		}

		public function getAccessToken() {
			return $this->accessToken;
		}

		public function getAccessTokenHeader() {
			return $this->decodeJWT($this->accessToken, 0);
		}

		public function getAccessTokenPayload() {
			return $this->decodeJWT($this->accessToken, 1);
		}

		public function hasRole(string $role) : bool {
			$roles = $this->getIdTokenPayload()['iam.pln.co.id/account/roles'];

			return in_array($role, $roles);
		}

		public function refreshToken(string $token) {
			return $this->oidc->refreshToken($token);
		}

		public function decodeJWT($jwt, $section = 0) {
			$parts = explode(".", $jwt);
			return json_decode(base64url_decode($parts[$section]), true);
		}

		public function debug($str) {
			if($this->enableLogging)
				$this->log->debug($str);
		}
	}
?>
