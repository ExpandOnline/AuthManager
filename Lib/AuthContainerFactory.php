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
 * @return AuthContainer
 */
	public function createAuthContainer($userId) {
		$mediaPlatformId = $this->MediaPlatformUser->getMediaPlatformId($userId);
		if (empty($mediaPlatformId)) {
			throw new NotFoundException('Could not find the MediaPlatformUser #' . $userId);
		}
		$mediaPlatformAuthManagerFactory = new MediaPlatformAuthManagerFactory();
		$authManager = $mediaPlatformAuthManagerFactory->createAuthManager($mediaPlatformId);
		$authContainer = $authManager->getAuthContainer($userId);
		$authContainer->userId = $userId;
		$authContainer->mediaPlatformId = $mediaPlatformId;

		return $authContainer;
	}

}