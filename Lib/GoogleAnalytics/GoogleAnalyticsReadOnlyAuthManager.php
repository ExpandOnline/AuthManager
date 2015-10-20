<?php
App::import('Vendor','expandonline/google-api-php-client/src/Google_Client');
App::import('Vendor','expandonline/google-api-php-client/src/contrib/Google_AnalyticsService');
App::uses('GoogleAuthManager','AuthManager.Lib/GoogleAnalytics');

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
	 * Get the username for the authenticated user.
	 * @return mixed
	 */
	protected function _getUserName() {
		$webProperties = $this->_service->management_webproperties->listManagementWebproperties("~all");
		return $webProperties['username'];
	}


/**
 * @return int
 */
	protected function _getPlatformId() {
		return MediaPlatform::GOOGLE_ANALYTICS_READONLY;
	}

/**
 * @return GoogleAnalyticsAuthContainer
 */
	protected function _getContainer() {
		return new GoogleAnalyticsAuthContainer();
	}

}