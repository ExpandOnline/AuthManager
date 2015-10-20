<?php
App::import('Vendor','expandonline/google-api-php-client/src/Google_Client');
App::import('Vendor','expandonline/google-api-php-client/src/contrib/Google_AnalyticsService');
App::uses('GoogleAnalyticsAuthManager','AuthManager.Lib/GoogleAnalytics');

/**
 * Class GoogleAnalyticsReadOnlyAuthManager
 */
class GoogleAnalyticsReadOnlyAuthManager extends GoogleAuthManager {

/**
 * Setup the Google Analytics API version from december 2013.
 * The API version from 2013 uses the config.php located in the vendor map.
 * TODO: Upgrade this to the latest API version.
 */
	public function __construct() {
		parent::__construct();
		$this->_client = new Google_Client();
		$this->_service = new Google_AnalyticsService($this->_client);
	}

/**
 * @param CakeRequest $request
 *
 * @return bool
 */
	public function authenticateUser($request) {
		$data = $request->query;
		if (!array_key_exists('code', $data)) {
			return false;
		}
		$oauthTokens = $this->_getOauthTokens($data['code']);
		$webProperties = $this->_service->management_webproperties->listManagementWebproperties("~all");
		return $this->_saveUser($webProperties['username'], $oauthTokens, MediaPlatform::GOOGLE_ANALYTICS_READONLY);
	}

/**
 * @return int
 */
	protected function _getPlatformId() {
		return MediaPlatform::WEBMASTER_TOOLS;
	}

}