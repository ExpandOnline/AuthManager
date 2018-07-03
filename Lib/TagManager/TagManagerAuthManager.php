<?php
App::uses('UpdatedGoogleAuthManager', 'AuthManager.Lib/Google');
App::uses('MediaPlatformAuthManager','AuthManager.Lib');


/**
 * Class TagManagerAuthManager
 */
class TagManagerAuthManager extends UpdatedGoogleAuthManager {

	/**
	 * Get the username for the authenticated user.
	 * @return mixed
	 */
	protected function _getUserName() {
		$service = new Google_Service_Plus($this->_client);
		$user = $service->people->get('me');
		return $user['displayName'];
	}

	/**
	 * @return array
	 */
	protected function _getScopes() {
		return array(
			Google_Service_Plus::USERINFO_EMAIL,
			Google_Service_TagManager::TAGMANAGER_READONLY,
		);
	}

	/**
	 * Set the Google Service
	 */
	protected function _setGoogleService() {
		$this->_service = new Google_Service_TagManager($this->_client);
	}

	/**
	 * @return int
	 */
	public function _getPlatformId() {
		return MediaPlatform::TAG_MANAGER;
	}

	/**
	 * @return string
	 */
	protected function _getConfigFilePath() {
		return CakePlugin::path('AuthManager') . 'Config' . DS . 'API' . DS . 'tagManager.json';
	}
}