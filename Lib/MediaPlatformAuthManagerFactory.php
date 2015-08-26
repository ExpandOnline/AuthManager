<?php
App::uses('MediaPlatform','AuthManager.Model');
App::uses('GoogleAnalyticsReadOnlyAuthManager','AuthManager.Lib/GoogleAnalytics');
App::uses('GoogleAnalyticsReadWriteAuthManager','AuthManager.Lib/GoogleAnalytics');

/**
 * Class MediaPlatformAuthManagerFactory
 */
class MediaPlatformAuthManagerFactory {

/**
 * Implemented media platforms with the respectively class name to use.
 * @var array
 */
	protected $_types  = array(
		MediaPlatform::GOOGLE_ANALYTICS_READONLY => 'GoogleAnalyticsReadOnlyAuthManager',
		MediaPlatform::GOOGLE_ANALYTICS_READWRITE => 'GoogleAnalyticsReadWriteAuthManager'
	);

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

		if (!class_exists($className)) {
			throw new Exception('Could not find class \'' . $className . '\'.');
		}

		return new $className();
	}
}