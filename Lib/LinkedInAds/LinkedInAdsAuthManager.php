<?php
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('ExampleMediaPlatformAuthContainer','AuthManager.Lib/Facebook');

/**
 * Class LinkedInAdsAuthManager
 *
 * Sadly Linked In Ads does not provide an API.
 * To still use the functionality of the AuthManager, which abstracts away
 * the platform and user, we will be creating a fake Linked In Ads user.
 */
class LinkedInAdsAuthManager extends MediaPlatformAuthManager {

/**
 * Get the authentication url to add an user.
 *
 * @return string
 */
	public function getAuthUrl() {
		return $this->_getCallbackUrl(MediaPlatform::LINKED_IN_ADS);
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
			'media_platform_id' => MediaPlatform::LINKED_IN_ADS
		])) {
			return false;
		}

		return $this->MediaPlatformUser->save([
			'username' => 'Linked In Ads user',
			'media_platform_id' => MediaPlatform::LINKED_IN_ADS,
		]);
	}

/**
 * @param $userId
 *
 * @return FacebookAdsAuthContainer
 */
	public function getAuthContainer($userId) {
		return new LinkedInAdsAuthContainer();
	}

}