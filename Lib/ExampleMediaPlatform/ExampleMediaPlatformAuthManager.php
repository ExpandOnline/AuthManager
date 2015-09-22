<?php
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('ExampleMediaPlatformAuthContainer','AuthManager.Lib/Facebook');

/**
 * Class ExampleMediaPlatformAuthManager
 */
class ExampleMediaPlatformAuthManager extends MediaPlatformAuthManager {

/**
 * Get the authentication url to add an user.
 *
 * @return string
 */
	public function getAuthUrl() {
		throw new Exception('Not yet implemented!');
	}

/**
 * Handles the request when being returned to the AuthManager plugin.
 *
 * @param CakeRequest $request
 *
 * @return bool
 */
	public function authenticateUser($request) {
		throw new Exception('Not yet implemented!');
	}

/**
 * @param $userId
 *
 * @return FacebookAdsAuthContainer
 */
	public function getAuthContainer($userId) {
		throw new Exception('Not yet implemented!');
	}

}