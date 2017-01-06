<?php
App::uses('AuthContainer', 'AuthManager.Lib');

/**
 * Class LinkedInAuthContainer
 *
 */
class LinkedInAuthContainer extends AuthContainer {
	/**
	 * @var League\OAuth2\Client\Provider\LinkedIn
	 */
	public $linkedInProvider;

	/**
	 * @var \Happyr\LinkedIn\LinkedIn
	 */
	public $linkedInApi;
}