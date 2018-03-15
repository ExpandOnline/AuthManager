<?php
App::uses('CriteoAuthContainer','AuthManager.Lib/Criteo');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');

/**
 * Class CriteoAuthManager
 */
class CriteoAuthManager extends MediaPlatformAuthManager {

	/**
	 * @var UserCredentials
	 */
	protected $_UserCredentials;

	/**
	 * @inheritDoc
	 */
	public function __construct() {
		parent::__construct();
		$this->_UserCredentials = ClassRegistry::init('AuthManager.UserCredentials');
	}

	/**
	 * Get the authentication url to add an user.
	 *
	 * @return string
	 */
	public function getAuthUrl() {
		return Router::url([
			'plugin' => 'auth_manager',
			'controller' => 'user_credentials',
			'action' => 'index',
			'?' => [
				'callbackUrl' => $this->_getCallbackUrl(MediaPlatform::CRITEO),
			],
		], true);
	}

	/**
	 * Handles the request when being returned to the AuthManager plugin.
	 *
	 * @param CakeRequest $request
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function authenticateUser($request) {
		$username = $request->data['username'];
		$userId = $this->MediaPlatformUser->save([
			'media_platform_id' => MediaPlatform::CRITEO,
			'username' => $username
		])['MediaPlatformUser']['id'];
		$this->_UserCredentials->saveEncrypted(
			$this->MediaPlatformUser->getLastInsertID(),
			$username,
			$request->data['password'],
			$request->data['app_token']
		);
		return $userId;
	}

	/**
	 * @param $userId
	 *
	 * @return CriteoAuthContainer
	 */
	public function getAuthContainer($userId) {
		$credentials = $this->_UserCredentials->getCredentials($userId);
		$authContainer = new CriteoAuthContainer();
		$authContainer->username = $credentials['UserCredentials']['username'];
		$authContainer->password = $credentials['UserCredentials']['password'];
		$authContainer->appToken = $credentials['UserCredentials']['app_token'];
		return $authContainer;
	}

}