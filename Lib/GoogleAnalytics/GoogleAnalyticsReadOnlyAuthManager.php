<?php
App::import('Vendor','expandonline/google-api-php-client/src/Google_Client');
App::import('Vendor','expandonline/google-api-php-client/src/contrib/Google_AnalyticsService');
App::uses('GoogleAuthManager','AuthManager.Lib/Google');

/**
 * Class GoogleAnalyticsReadOnlyAuthManager
 */
class GoogleAnalyticsReadOnlyAuthManager extends GoogleAuthManager {

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
 * @return array
 */
	protected function _getScopes() {
		return array(
			Google_Service_Analytics::ANALYTICS_READONLY
		);
	}

/**
 *	Old analytics uses a default config file.
 */
	protected function _setGoogleClient() {
		$this->_client = new Google_Client();
	}

/**
 *
 */
	protected function _setGoogleService() {
		$this->_service = new Google_AnalyticsService($this->_client);
	}

/**
 *
 */
	protected function _getConfigFilePath() {
		throw new InternalErrorException('Analytics ReadOnly does not use a config file.');
	}


}