<?php
App::uses('LinkedInAuthManager', 'AuthManager.Lib/LinkedIn');

/**
 * Class LinkedInAdsAuthManager
 */
class LinkedInAdsAuthManager extends LinkedInAuthManager {

	/**
	 * @return int
	 */
	protected function getMediaPlatformId() {
		return MediaPlatform::LINKED_IN_ADS;
	}

}