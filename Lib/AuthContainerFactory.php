<?php
App::uses('MediaPlatformAuthManagerFactory', 'AuthManager.Lib');
App::uses('MediaPlatformUser', 'AuthManager.Model');

/**
 * Class AuthContainerFactory
 */
class AuthContainerFactory {

/**
 * Set the MediaPlatformUser model.
 */
	public function __construct() {
		$this->MediaPlatformUser = ClassRegistry::init('AuthManager.MediaPlatformUser');
	}

/**
 * @param $userId
 *
 * @return GoogleAnalyticsAuthContainer|FacebookAdsAuthContainer
 */
	public function createAuthContainer($userId) {
		$mediaPlatformId = $this->MediaPlatformUser->getMediaPlatformId($userId);
		if (is_null($mediaPlatformId)) {
			throw new NotFoundException('Could not find the MediaPlatformUser #' . $userId);
		}
		$mediaPlatformAuthManagerFactory = new MediaPlatformAuthManagerFactory();
		$authManager = $mediaPlatformAuthManagerFactory->createAuthManager($mediaPlatformId);

		return $authManager->getAuthContainer($userId);
	}

}