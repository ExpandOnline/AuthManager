<?php
App::uses('UpdatedGoogleAuthManager','AuthManager.Lib/Google');

/**
 * Class GoogleAnalyticsReadWriteAuthManager
 */
class GoogleAnalyticsReadWriteAuthManager extends UpdatedGoogleAuthManager {

/**
 * @var string
 */
	protected $_configFile;

/**
 * Get the username for the authenticated user.
 * @return mixed
 */
	protected function _getUserName() {
		$webProperties = $this->_service->management_webproperties->listManagementWebproperties("~all");
		return $webProperties['username'];
	}

/**
 * Set the google service.
 */
	protected function _setGoogleService() {
		$this->_service = new Google_Service_Analytics($this->_client);
	}

/**
 * @return int
 */
	protected function _getPlatformId() {
		return MediaPlatform::GOOGLE_ANALYTICS_READWRITE;
	}

/**
 * @return array
 */
	protected function _getScopes() {
		return array(
			Google_Service_Analytics::ANALYTICS_EDIT,
			Google_Service_AnalyticsReporting::ANALYTICS_READONLY
		);
	}

/**
 * @return string
 */
	protected function _getConfigFilePath() {
		return CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'googleAnalyticsReadWrite.json';
	}

}