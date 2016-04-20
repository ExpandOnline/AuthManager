<?php
App::uses('CoostoAuthContainer','AuthManager.Lib/Coosto');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('CoostoApiRequest','AuthManager.Lib/Coosto');
App::uses('CoostoApi','AuthManager.Lib/Coosto');

/**
 * Class CoostoAuthManager
 */
class CoostoAuthManager extends MediaPlatformAuthManager {

	/**
	 * @var CoostoApi
	 */
	protected $_coostoApi;

	/**
	 * @var UserCredentials
	 */
	protected $_UserCredentials;
	
	/**
	 * @inheritDoc
	 */
	public function __construct() {
		parent::__construct();
		$this->_coostoApi = new CoostoApi();
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
				'callbackUrl' => $this->_getCallbackUrl(MediaPlatform::COOSTO)
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
		$password = $request->data['password'];
		try {
			$loginTest = $this->_login($username, $password);
		} catch (Exception $e) {
			return false;
		}
		if ($loginTest['status'] !== 'success') {
			return false;
		}
		return $this->_saveCoostoUser($username, $password);
	}

	/**
	 * @param $username
	 * @param $password
	 *
	 * @return bool
	 * @throws Exception
	 */
	protected function _saveCoostoUser($username, $password) {
		return $this->MediaPlatformUser->save([
			'media_platform_id' => MediaPlatform::COOSTO,
			'username' => $username
		]) && $this->_UserCredentials->saveEncrypted($this->MediaPlatformUser->getLastInsertID(), $username, $password);
	}

	/**
	 * @param $username
	 * @param $password
	 *
	 * @return mixed
	 */
	protected function _login($username, $password) {
		$request = new CoostoApiRequest();
		$request->addOption('username', $username);
		$request->addOption('password', $password);
		$request->setEndPoint('users/login');
		return $this->_coostoApi->post($request);
	}

	/**
	 * @param $userId
	 *
	 * @return CoostoAuthContainer
	 * @throws Exception
	 */
	public function getAuthContainer($userId) {
		$userCredentials = $this->_UserCredentials->getCredentials($userId);
		$login = $this->_login(
			$userCredentials['UserCredentials']['username'],
			$userCredentials['UserCredentials']['password']
		);
		if ($login['status'] !== 'success') {
			throw new Exception(sprintf(
				'Could not authenticate Costoo user %s', $userCredentials['UserCredentials']['username']
			));
		}
		$this->_coostoApi->addDefaultQueryString('sessionid', $login['data']['sessionid']);
		$authContainer = new CoostoAuthContainer();
		$authContainer->coostoApi = $this->_coostoApi;
		return $authContainer;
	}

}