<?php
App::import('Vendor','google/apiclient/src/Google/autoload');
App::uses('GoogleAnalyticsAuthManager','AuthManager.Lib/GoogleAnalytics');

/**
 * Class GoogleAnalyticsReadWriteAuthManager
 */
class GoogleAnalyticsReadWriteAuthManager extends GoogleAuthManager {

/**
 * @var string
 */
	protected $_configFile;

/**
 * Set the google client and service.
 */
	public function __construct() {
		parent::__construct();
		$this->_configFile = CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'googleAnalyticsReadWrite.json';
		$this->_setGoogleClient();
		$this->_setGoogleService();
	}

/**
 * Set the google client.
 */
	protected function _setGoogleClient() {
		$googleClient = new Google_Client();
		$googleClient->setAuthConfigFile($this->_configFile);
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
		return $this->_saveUser($webProperties['username'], $oauthTokens, $this->_getPlatformId());
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

}