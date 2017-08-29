<?php

App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('SuperMetricsAuthContainer', 'AuthManager.Lib/SuperMetrics');

class SuperMetricsAuthManager extends MediaPlatformAuthManager {

	/**
	 * Get the authentication url to add an user.
	 *
	 * @return string
	 */
	public function getAuthUrl() {
		return $this->_getCallbackUrl(MediaPlatform::SM_LINKED_IN_ADS);
	}

	/**
	 * Setup a AuthContainer object with the given User ID.
	 *
	 * @param $userId
	 *
	 * @return SuperMetricsAuthContainer
	 */
	public function getAuthContainer($userId) {
		return new SuperMetricsAuthContainer();
	}

	/**
	 * Handles the request when being returned to the AuthManager plugin.
	 *
	 * @param CakeRequest $request
	 *
	 * @return bool
	 */
	public function authenticateUser($request) {
		if ($this->MediaPlatformUser->hasAny([
			'media_platform_id' => MediaPlatform::SM_LINKED_IN_ADS
		])) {
			return false;
		}

		return $this->MediaPlatformUser->save([
			'username' => 'SuperMetrics user',
			'media_platform_id' => MediaPlatform::SM_LINKED_IN_ADS,
		]);
	}
}