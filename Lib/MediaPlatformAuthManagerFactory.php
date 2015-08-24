<?php
App::uses('MediaPlatform','AuthManager.Model');
App::uses('GoogleAnalyticsReadOnlyAuthManager','AuthManager.Lib/GoogleAnalytics');
App::uses('GoogleAnalyticsReadWriteAuthManager','AuthManager.Lib/GoogleAnalytics');

/**
 * Class MediaPlatformAuthManagerFactory
 */
class MediaPlatformAuthManagerFactory {

/**
 * @var array
 */
	protected $_types;

/**
 * Set our implemented MediaPlatformAuthManager classes
 */
	public function __construct() {
		$this->_types = array(
			MediaPlatform::GOOGLE_ANALYTICS_READONLY => 'GoogleAnalyticsReadOnlyAuthManager',
			MediaPlatform::GOOGLE_ANALYTICS_READWRITE => 'GoogleAnalyticsReadWriteAuthManager'
		);
	}

/**
 * @param $mediaPlatformId
 *
 * @return MediaPlatformAuthManager
 */
	public function createAuthManager($mediaPlatformId) {
		if (!array_key_exists($mediaPlatformId, $this->_types)) {
			throw new InvalidArgumentException('Media platform ID #' . $mediaPlatformId . ' is not yet implemented.');
		}
		$className = $this->_types[$mediaPlatformId];

		return new $className();
	}
}