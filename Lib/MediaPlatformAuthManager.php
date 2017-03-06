<?php
App::uses('MediaPlatformUser','AuthManager.Model');
App::uses('MediaPlatform','AuthManager.Model');

/**
 * Class MediaPlatformAuthManager
 *
 * This class is the abstract class for authentication managers.
 * Each media platform should implement it's own authentication manager, with the interface as seen below.
 */
abstract class MediaPlatformAuthManager {

/**
 * @var MediaPlatformUser
 */
	public $MediaPlatformUser;

/**
 * Initiate the MediaPlatformUser account.
 */
	public function __construct() {
		$this->MediaPlatformUser = ClassRegistry::init('AuthManager.MediaPlatformUser');
	}

/**
 * Get the authentication url to add an user.
 *
 * @return string
 */
	public abstract function getAuthUrl();

/**
 * Setup a AuthContainer object with the given User ID.
 *
 * @param $userId
 *
 * @return Object
 */
	public abstract function getAuthContainer($userId);

/**
 * Handles the request when being returned to the AuthManager plugin.
 *
 * @param CakeRequest $request
 *
 * @return bool
 */
	public abstract function authenticateUser($request);

	/**
	 * @param      $username
	 * @param      $accessToken
	 * @param      $mediaPlatform
	 *
	 * @param null $refreshToken
	 *
	 * @return mixed
	 */
	protected function _saveUser($username, $accessToken, $mediaPlatform) {
		$saveData = array(
			'MediaPlatformUser' => array(
				'username' => $username,
				'media_platform_id' => $mediaPlatform
			),
			'OauthToken' => array(
				'access_token' => $accessToken,
			)
		);

		return $this->MediaPlatformUser->saveOauthUser($saveData);
	}

/**
 * @param $mediaPlatform
 *
 * @return string
 */
	protected function _getCallbackUrl($mediaPlatform) {
		return Router::url(array(
			'plugin' => 'auth_manager',
			'controller' => 'media_platform_users',
			'action' => 'callback',
			$mediaPlatform
		), true);
	}

/**
 * Allows us to test protected methods in unit tests.
 *
 * @param       $methodName
 * @param array $args
 *
 * @return mixed
 */
	public function testProtected($methodName, array $args) {
		return call_user_func_array(array($this, $methodName), $args);
	}

/**
 * @param $timeStamp
 * @param $in
 *
 * @return bool
 */
	protected function _expiresIn($timeStamp, $in) {
		return $timeStamp < (time() + $in);
	}

}