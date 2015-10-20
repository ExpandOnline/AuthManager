<?php
App::uses('GoogleAuthManager', 'AuthManager.Lib/GoogleAnalytics');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('WebmasterToolsAuthContainer','AuthManager.Lib/WebmasterTools');


/**
 * Class ExampleMediaPlatformAuthManager
 */
class WebmasterToolsAuthManager extends GoogleAuthManager {

/**
 * @var string
 */
protected $_configFile;

/**
 * Set the google client and service.
 */
public function __construct() {
	parent::__construct();
	$this->_configFile = CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'webmasterTools.json';
	$this->_setGoogleClient();
	$this->_setGoogleService();
}

/**
 * Handles the request when being returned to the AuthManager plugin.
 *
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
		$service = new Google_Service_Plus($this->_client);
		$user = $service->people->get('me');
		return $this->_saveUser($user['displayName'], $oauthTokens, $this->_getPlatformId());
	}

/**
 * @param $userId
 *
 * @return GoogleAnalyticsAuthContainer
 */
	public function getAuthContainer($userId) {
		$this->_setTokenOnClient($userId);
		$authContainer = new WebmasterToolsAuthContainer();
		$authContainer->client = $this->_client;
		$authContainer->service = $this->_service;
		return $authContainer;
	}

/**
 * Set the google client.
 */
	protected function _setGoogleClient() {
		$googleClient = new Google_Client();
		$googleClient->setAuthConfigFile($this->_configFile);
		$googleClient->addScope(Google_Service_Webmasters::WEBMASTERS_READONLY);
		$googleClient->addScope('http://www.google.com/webmasters/tools/feeds/');
		$googleClient->addScope(Google_Service_Plus::USERINFO_EMAIL);
		$googleClient->setRedirectUri(Router::url(array(
			'plugin' => 'auth_manager',
			'controller' => 'media_platform_users',
			'action' => 'callback',
			MediaPlatform::WEBMASTER_TOOLS
		), true));

		// This will force Google to always return the refresh_token.
		$googleClient->setAccessType('offline');
		$googleClient->setApprovalPrompt('force');

		$this->_client = $googleClient;
	}

/**
 * Set the Google Service
 */
	protected function _setGoogleService() {
		$this->_service = new Google_Service_Webmasters($this->_client);
	}

/**
 * @return int
 */
	public function _getPlatformId() {
		return MediaPlatform::WEBMASTER_TOOLS;
	}


}