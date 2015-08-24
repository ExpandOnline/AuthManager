<?php
App::import('Vendor','google/apiclient/src/Google/autoload');
App::uses('GoogleAnalyticsAuthManager','AuthManager.Lib/GoogleAnalytics');

/**
 * Class GoogleAnalyticsReadWriteAuthManager
 */
class GoogleAnalyticsReadWriteAuthManager extends GoogleAnalyticsAuthManager {

/**
 * @var Google_Client
 */
	public $_client;

/**
 * @var Google_Service_Analytics
 */
	public $_service;

/**
 * Set the google client and service.
 */
	public function __construct() {
		parent::__construct();
		$this->_setGoogleClient();
		$this->_setGoogleService();
	}

/**
 * Set the google client.
 */
	protected function _setGoogleClient() {
		$googleClient = new Google_Client();
		$googleClient->setAuthConfigFile(GOOGLE_ANALYTICS_READ_WRITE_SETTINGS_JSON_FILE);
		$googleClient->addScope(Google_Service_Analytics::ANALYTICS_EDIT);
		$googleClient->setRedirectUri(Router::url(array(
			'plugin' => 'auth_manager',
			'controller' => 'media_platform_users',
			'action' => 'callback',
			MediaPlatform::GOOGLE_ANALYTICS_READWRITE
		), true));

		// This will force Google to always return the refresh_token.
		$googleClient->setAccessType('offline');
		$googleClient->setApprovalPrompt('force');

		$this->_client = $googleClient;
	}

/**
 * Set the google service.
 */
	protected function _setGoogleService() {
		$this->_service = new Google_Service_Analytics($this->_client);
	}

/**
 * Get the authentication URL.
 *
 * @return string
 */
	public function getAuthUrl() {
		return $this->_client->createAuthUrl();
	}

/**
 * @param array $data
 *
 * @return bool
 */
	public function authenticateUser($data) {
		if (!array_key_exists('code', $data)) {
			return false;
		}
		$authenticationData = $this->_getOauthTokens($data['code']);
		$webProperties = $this->_service->management_webproperties->listManagementWebproperties("~all");

		return $this->_saveUser($webProperties->username, $authenticationData, MediaPlatform::GOOGLE_ANALYTICS_READWRITE);
	}

}