<?php

use PhpTwinfield\Office;
use PhpTwinfield\Secure\OpenIdConnectAuthentication;
use PhpTwinfield\Secure\Provider\OAuthProvider;

App::uses('AuthContainer', 'AuthManager.Lib');

/**
 * Class TwinfieldAuthContainer
 */
class TwinfieldAuthContainer extends AuthContainer {

	/**
	 * @var OAuthProvider
	 */
	public $provider;

	/**
	 * @var string
	 */
	public $refreshToken;

	/**
	 * @param Office $office
	 *
	 * @return OpenIdConnectAuthentication
	 */
	public function createConnection(Office $office) {
		return new OpenIdConnectAuthentication($this->provider, $this->refreshToken, $office);
	}

}