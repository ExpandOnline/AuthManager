<?php
App::uses('GoogleAnalyticsReadWriteAuthManager','AuthManager.Lib/GoogleAnalytics');
App::uses('GoogleAnalyticsReadOnlyAuthManager','AuthManager.Lib/GoogleAnalytics');
App::uses('WebmasterToolsAuthManager','AuthManager.Lib/WebmasterTools');
App::uses('TagManagerAuthManager','AuthManager.Lib/TagManager');
App::uses('FacebookAdsAuthManager','AuthManager.Lib/Facebook');
App::uses('LinkedInAdsAuthManager','AuthManager.Lib/LinkedInAds');
App::uses('BingAdsAuthManager','AuthManager.Lib/BingAds');
App::uses('BitlyAuthManager','AuthManager.Lib/Bitly');
App::uses('MediaPlatform','AuthManager.Model');

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
		MediaPlatform::GOOGLE_ANALYTICS_READWRITE => 'GoogleAnalyticsReadWriteAuthManager',
		MediaPlatform::FACEBOOK_ADS => 'FacebookAdsAuthManager',
		MediaPlatform::BITLY => 'BitlyAuthManager',
		MediaPlatform::WEBMASTER_TOOLS => 'WebmasterToolsAuthManager',
		MediaPlatform::TAG_MANAGER => 'TagManagerAuthManager',
		MediaPlatform::BING_ADS => 'BingAdsAuthManager',
		MediaPlatform::LINKED_IN_ADS => 'LinkedInAdsAuthManager',
	);

/**
 * @param $mediaPlatformId
 *
 * @return MediaPlatformAuthManager
 * @throws Exception
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