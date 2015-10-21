<?php
App::uses('GoogleAuthManager', 'AuthManager.Lib/Google');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');


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
			Google_Service_Plus::USERINFO_EMAIL,
			Google_Service_Tagmanager::TAGMANAGER_READONLY,
		);
	}

	/**
	 * Set the Google Service
	 */
	protected function _setGoogleService() {
		$this->_service = new Google_Service_Tagmanager($this->_client);
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
		return CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'tagManager.json';
	}
}