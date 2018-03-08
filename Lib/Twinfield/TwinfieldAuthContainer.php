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
	 * @var string
	 */
	public $accessToken;

}