<?php
App::uses('UpdatedGoogleAuthManager', 'AuthManager.Lib/Google');
App::uses('DoubleClickAuthContainer', 'AuthManager.Lib/DoubleClick');

/**
 * Class DoubleClickAuthManager
 */
class DoubleClickAuthManager extends UpdatedGoogleAuthManager {

	/**
	 * @var string
	 */
	protected $_configFile;

	/**
	 * @var Google_Service_DoubleClickBidManager
	 */
	protected $_dbmService;

	/**
	 * Get the username for the authenticated user.
	 *
	 * @return mixed
	 */
	protected function _getUserName() {
		$googleOauth = new Google_Service_Oauth2($this->_client);

		return $googleOauth->userinfo->get()->email;
	}

	/**
	 * Set the google service.
	 */
	protected function _setGoogleService() {
		$this->_service = new Google_Service_Dfareporting($this->_client);
		$this->_dbmService = new Google_Service_DoubleClickBidManager($this->_client);
	}

	/**
	 * @return int
	 */
	protected function _getPlatformId() {
		return MediaPlatform::DOUBLE_CLICK;
	}

	/**
	 * @return array
	 */
	protected function _getScopes() {
		return array(
			'https://www.googleapis.com/auth/dfareporting',
			'https://www.googleapis.com/auth/dfatrafficking',
			'https://www.googleapis.com/auth/userinfo.email',
			'https://www.googleapis.com/auth/doubleclickbidmanager',
		);
	}

	/**
	 * @return string
	 */
	protected function _getConfigFilePath() {
		return CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'doubleClick.json';
	}

	/**
	 * @param $userId
	 *
	 * @return GoogleAuthContainer
	 */
	public function getAuthContainer($userId) {
		$this->_setTokenOnClient($userId);
		$authContainer = new DoubleClickAuthContainer();
		$authContainer->client = $this->_client;
		$authContainer->service = $this->_service;
		$authContainer->dbmService = $this->_dbmService;
		$authContainer->userId = $userId;
		return $authContainer;
	}

}