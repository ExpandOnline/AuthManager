<?php
App::uses('AuthContainer', 'AuthManager.Lib');

/**
 * Class ExampleMediaPlatformAuthContainer
 */
class BingAdsAuthContainer extends AuthContainer {

/**
 * @var League\OAuth2\Client\Provider\Microsoft
 */
	public $microsoftProvider;

/**
 * @var
 */
	public $clientProxyFactory;

}