<?php
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('SuperMetricsAuthContainer','AuthManager.Lib/SuperMetrics');

/**
 * Class SuperMetricsAuthManager
 */
abstract class SuperMetricsAuthManager extends MediaPlatformAuthManager {

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
	 * @return mixed
	 */
	protected abstract function getMediaPlatform();

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
				'callbackUrl' => $this->_getCallbackUrl($this->getMediaPlatform())
			]
		], true);
	}

	/**
	 * Handles the request when being returned to the AuthManager plugin.
	 *
	 * @param CakeRequest $request
	 *
	 * @return bool
	 */
	public function authenticateUser($request) {
		$username = $request->data['username'];
		return $this->MediaPlatformUser->save([
				'media_platform_id' => $this->getMediaPlatform(),
				'username' => $username
			]) && $this->_UserCredentials->saveEncrypted(
				$this->MediaPlatformUser->getLastInsertID(),
				$username,
				null
			);
	}

	/**
	 * @param $userId
	 *
	 * @return SuperMetricsAuthContainer
	 */
	public function getAuthContainer($userId) {
		$credentials = $this->_UserCredentials->getCredentials($userId);
		$authContainer = new SuperMetricsAuthContainer();
		$authContainer->dsUser = $credentials['UserCredentials']['username'];
		return $authContainer;
	}

}