<?php
App::uses('AuthContainer', 'AuthManager.Lib');

/**
 * Class CriteoAuthContainer
 */
class CriteoAuthContainer extends AuthContainer {

	/**
	 * @var string
	 */
	public $username;

	/**
	 * @var string
	 */
	public $password;

	/**
	 * @var string
	 */
	public $appToken;

}