<?php
App::uses('UpdatedGoogleAuthManager', 'AuthManager.Lib/Google');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');

/**
 * Class SearchConsoleAuthManager
 */
class SearchConsoleAuthManager extends UpdatedGoogleAuthManager {

	/**
	 * Get the username for the authenticated user.
	 * @return mixed
	 */
	protected function _getUserName() {
		$service = new Google_Service_Plus($this->_client);
		$user = $service->people->get('me');
		return empty($user['displayName']) ? $user['emails'][0]['value'] : $user['displayName'];
	}

	/**
	 * @return array
	 */
	protected function _getScopes() {
		return [
			Google_Service_Webmasters::WEBMASTERS_READONLY,
			Google_Service_Plus::USERINFO_EMAIL
		];
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
		return MediaPlatform::SEARCH_CONSOLE;
	}

	/**
	 * @return string
	 */
	protected function _getConfigFilePath() {
		return CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'searchConsole.json';
	}

	public function getAuthContainer($userId) {
		$authContainer = parent::getAuthContainer($userId);
		$authContainer->setManager($this);
		return $authContainer;
	}


}