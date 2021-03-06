<?php
App::uses('GoogleAnalyticsReadWriteAuthManager','AuthManager.Lib/GoogleAnalytics');
App::uses('GoogleAnalyticsReadOnlyAuthManager','AuthManager.Lib/GoogleAnalytics');
App::uses('LinkedInAdsAuthManager','AuthManager.Lib/LinkedIn');
App::uses('SearchConsoleAuthManager','AuthManager.Lib/SearchConsole');
App::uses('DoubleClickAuthManager','AuthManager.Lib/DoubleClick');
App::uses('DoubleClickDCMAuthManager','AuthManager.Lib/DoubleClickDCM');
App::uses('TagManagerAuthManager','AuthManager.Lib/TagManager');
App::uses('TwitterAdsAuthManager','AuthManager.Lib/TwitterAds');
App::uses('ManualUploadAuthManager','AuthManager.Lib/Salesforce');
App::uses('FacebookAdsAuthManager','AuthManager.Lib/Facebook');
App::uses('InstagramAuthManager','AuthManager.Lib/Instagram');
App::uses('LinkedInAuthManager','AuthManager.Lib/LinkedIn');
App::uses('BingAdsAuthManager','AuthManager.Lib/BingAds');
App::uses('DropboxAuthManager','AuthManager.Lib/Dropbox');
App::uses('AdWordsAuthManager','AuthManager.Lib/AdWords');
App::uses('CoostoAuthManager','AuthManager.Lib/Coosto');
App::uses('BitlyAuthManager','AuthManager.Lib/Bitly');
App::uses('SuperMetricsAuthManager', 'AuthManager.Lib/SuperMetrics');
App::uses('TrafficEstimatorAuthManager', 'AuthManager.Lib/TrafficEstimator');
App::uses('TwinfieldAuthManager', 'AuthManager.Lib/Twinfield');
App::uses('CriteoAuthManager', 'AuthManager.Lib/Criteo');
App::uses('ManualUploadAuthManager', 'AuthManager.Lib/ManualUpload');
App::uses('DoubleClickSearchAuthManager', 'AuthManager.Lib/DoubleClickSearch');
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
		MediaPlatform::SEARCH_CONSOLE => 'SearchConsoleAuthManager',
		MediaPlatform::TAG_MANAGER => 'TagManagerAuthManager',
		MediaPlatform::BING_ADS => 'BingAdsAuthManager',
		MediaPlatform::LINKED_IN_ADS => 'LinkedInAdsAuthManager',
		MediaPlatform::DOUBLE_CLICK => 'DoubleClickAuthManager',
		MediaPlatform::COOSTO => 'CoostoAuthManager',
		MediaPlatform::INSTAGRAM => 'InstagramAuthManager',
		MediaPlatform::DROPBOX => 'DropboxAuthManager',
		MediaPlatform::ADWORDS => 'AdWordsAuthManager',
		MediaPlatform::LINKED_IN => 'LinkedInAuthManager',
		MediaPlatform::TWITTER_ADS => 'TwitterAdsAuthManager',
		MediaPlatform::SALESFORCE => 'SalesforceAuthManager',
		MediaPlatform::DOUBLE_CLICK_DCM => 'DoubleClickDCMAuthManager',
		MediaPlatform::TRAFFIC_ESTIMATOR => 'TrafficEstimatorAuthManager',
		MediaPlatform::TWINFIELD => 'TwinfieldAuthManager',
		MediaPlatform::CRITEO => 'CriteoAuthManager',
		MediaPlatform::MANUAL_UPLOAD => 'ManualUploadAuthManager',
		MediaPlatform::DOUBLE_CLICK_SEARCH => 'DoubleClickSearchAuthManager',
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
		if (is_null($className)) {
			return;
		}
		if (!class_exists($className)) {
			throw new Exception('Could not find class \'' . $className . '\'.');
		}
		return new $className();
	}

}