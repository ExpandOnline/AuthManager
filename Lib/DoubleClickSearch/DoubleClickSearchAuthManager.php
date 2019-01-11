<?php
App::uses('DoubleClickAuthManager', 'AuthManager.Lib/DoubleClick');

class DoubleClickSearchAuthManager extends DoubleClickAuthManager {

	/**
	 * @return int
	 */
	protected function _getPlatformId() {
		return MediaPlatform::DOUBLE_CLICK_SEARCH;
	}

	/**
	 * @return array
	 */
	protected function _getScopes() {
		return array(
			'https://www.googleapis.com/auth/doubleclicksearch',
			'https://www.googleapis.com/auth/userinfo.email',
		);
	}

	/**
	 * @return string
	 */
	protected function _getConfigFilePath() {
		return CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'doubleClickSearch.json';
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
		$authContainer->dsaService = $this->_dsaService;
		$authContainer->userId = $userId;
		return $authContainer;
	}

	/**
	 * Set the google service.
	 */
	protected function _setGoogleService() {
		$this->_dsaService = new Google_Service_Doubleclicksearch($this->_client);
	}

}