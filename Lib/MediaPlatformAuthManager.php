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
 * @return AuthContainer
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

}