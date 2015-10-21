<?php
App::uses('GoogleAuthManager', 'AuthManager.Lib/Google');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');
App::uses('WebmasterToolsAuthContainer','AuthManager.Lib/WebmasterTools');


/**
 * Class ExampleMediaPlatformAuthManager
 */
class WebmasterToolsAuthManager extends GoogleAuthManager {

/**
 * @var string
 */
	protected $_configFile = null;

/**
 * Set the google client and service.
 */
	public function __construct() {
		$this->_configFile = CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'webmasterTools.json';
		parent::__construct();
	}

/**
 * Get the username for the authenticated user.
 * @return mixed
 */
	protected function _getUserName() {
		$service = new Google_Service_Plus($this->_client);
		$user = $service->people->get('me');
		return $user['displayName'];
	}



	protected function _getScopes() {
		return array(
			Google_Service_Webmasters::WEBMASTERS_READONLY,
			'http://www.google.com/webmasters/tools/feeds/',
			Google_Service_Plus::USERINFO_EMAIL
		);
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


/**
 * @return string
 */
	protected function _getConfigFilePath() {
		return CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'webmasterTools.json';
	}
}