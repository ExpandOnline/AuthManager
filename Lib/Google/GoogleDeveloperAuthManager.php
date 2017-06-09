<?php
App::uses('MediaPlatformAuthManager', 'AuthManager.Lib');

abstract class SelfAuthenticationAuthManager extends MediaPlatformAuthManager {

	/**
	 * Get the authentication url to add an user.
	 *
	 * @return string
	 */
	public function getAuthUrl() {
		return $this->_getCallbackUrl($this->getPlatformType());
	}

	/**
	 * Handles the request when being returned to the AuthManager plugin.
	 *
	 * @param CakeRequest $request
	 *
	 * @return bool
	 */
	public function authenticateUser($request) {
		if ($this->MediaPlatformUser->hasAny(['media_platform_id' => $this->getPlatformType()])) {
			return false;
		}

		return $this->MediaPlatformUser->save([
			'username' => $this->getPlatformName() . ' user',
			'media_platform_id' => $this->getPlatformType(),
		]);
	}

	abstract public function getPlatformType();

	abstract public function getPlatformName();
}