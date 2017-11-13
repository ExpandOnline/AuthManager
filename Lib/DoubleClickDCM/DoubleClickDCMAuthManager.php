<?php
App::uses('UpdatedGoogleAuthManager', 'AuthManager.Lib/Google');
App::uses('DoubleClickAuthManager', 'AuthManager.Lib/DoubleClick');

class DoubleClickDCMAuthManager extends DoubleClickAuthManager {

	/**
	 * @return int
	 */
	protected function _getPlatformId() {
		return MediaPlatform::DOUBLE_CLICK_DCM;
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
		return CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'doubleClickDCM.json';
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